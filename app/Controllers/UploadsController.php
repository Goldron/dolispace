<?php

namespace App\Controllers;

use App\Libraries\Clamdscan;
use App\Libraries\DolibarrApi;
use App\Models\LogModel;
use App\Models\UploadModel;

class UploadsController extends BaseController
{
    protected $helpers = ['url', 'vite'];

    private const UPLOAD_DIR = WRITEPATH . 'uploads/';

    private const BLOCKED_MIMES = [
        'text/x-php', 'application/x-httpd-php', 'application/php',
        'text/html', 'text/javascript', 'application/javascript',
        'application/x-sh', 'application/x-perl',
    ];

    // Affiche l'espace de dépôt : fichiers déjà uploadés + devis/commandes pour association
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! cfg('uploads_page_enabled', true)) {
            return redirect()->to('dashboard')->with('error', lang('Dashboard.featureDisabled'));
        }

        $partyId = (int) session()->get('party_id');
        $api     = new DolibarrApi();

        $params    = ['thirdparty_ids' => $partyId, 'limit' => 100, 'sortfield' => 't.rowid', 'sortorder' => 'DESC'];
        $proposals = cfg('propal_enabled', true) ? $this->filterRefs($api->getProposals($params)) : [];
        $orders    = cfg('commande_enabled', true) ? $this->filterRefs($api->getOrders($params)) : [];
        $files     = model(UploadModel::class)->getForParty($partyId);

        return view('dashboard/uploads', compact('files', 'proposals', 'orders'));
    }

    // Valide, scanne puis dépose un fichier dans le dossier du tiers (et de la commande si liée)
    public function upload(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! cfg('uploads_page_enabled', true)) {
            return redirect()->to('dashboard')->with('error', lang('Dashboard.featureDisabled'));
        }

        $partyId = (int) session()->get('party_id');
        $userId  = (int) session()->get('user_id');

        $maxMb   = (int) cfg('max_upload_size', 10);
        $allowed = array_map('trim', explode(',', (string) cfg('allowed_upload_types', 'pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,gif,webp,zip,txt')));

        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid()) {
            $error = $file ? $file->getErrorString() : lang('Dashboard.noFileReceived');
            return redirect()->to('dashboard/uploads')->with('error', $error);
        }

        if ($file->hasMoved()) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileAlreadyProcessed'));
        }

        if ($file->getSizeByUnit('mb') > $maxMb) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileTooLarge', [(string) $maxMb]));
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, $allowed, true)) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.extensionNotAllowed', [$ext]));
        }

        $serverMime = $file->getMimeType();
        if (in_array(strtolower((string) $serverMime), self::BLOCKED_MIMES, true)) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileTypeNotAllowed'));
        }

        $scan = (new Clamdscan())->scan($file->getTempName());
        if (! $scan['clean']) {
            $reason = $scan['virus'] ?? $scan['error'] ?? lang('Dashboard.threatDetected');
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileRejected', [$reason]));
        }

        $api         = new DolibarrApi();
        $thirdparty  = $api->getThirdparty($partyId);
        $companyName = isset($thirdparty['error']) ? '' : (string) ($thirdparty['name'] ?? '');
        $codeClient  = isset($thirdparty['error']) ? '' : preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($thirdparty['code_client'] ?? ''));
        $partyFolder = ($companyName !== '' ? $this->slugify($companyName) . '_' : '') . ($codeClient !== '' ? $codeClient : $partyId);

        // Association devis / commande
        $refType   = '';
        $refId     = 0;
        $refFolder = '';

        $rawRef = trim((string) $this->request->getPost('ref'));
        if ($rawRef !== '') {
            [$refType, $refIdStr] = array_pad(explode(':', $rawRef, 2), 2, '0');
            $refId = (int) $refIdStr;

            $refTypeEnabled = ($refType === 'proposal' && cfg('propal_enabled', true))
                || ($refType === 'order' && cfg('commande_enabled', true));

            if ($refId > 0 && $refTypeEnabled) {
                $record = $refType === 'proposal' ? $api->getProposal($refId) : $api->getOrder($refId);
                if (! isset($record['error']) && ! empty($record['ref'])) {
                    $refFolder = $this->slugify($record['ref']);
                }
            }
        }

        $baseName   = pathinfo($file->getClientName(), PATHINFO_FILENAME);
        $storedName = $this->slugify($baseName) . '-' . date('ymd') . '_' . $this->randomToken(9) . '.' . $ext;
        $dir        = self::UPLOAD_DIR . $partyFolder . ($refFolder !== '' ? '/' . $refFolder : '') . '/';

        if (! is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        $file->move($dir, $storedName);

        model(UploadModel::class)->insert([
            'user_id'       => $userId,
            'party_id'      => $partyId,
            'party_folder'  => $partyFolder,
            'ref_type'      => $refType ?: null,
            'ref_id'        => $refId   ?: null,
            'ref_folder'    => $refFolder,
            'original_name' => $file->getClientName(),
            'stored_name'   => $storedName,
            'mime_type'     => $serverMime,
            'size'          => $file->getSizeByUnit('b'),
        ]);

        model(LogModel::class)->record($userId, 'upload_file', [
            'file'       => $file->getClientName(),
            'size'       => $file->getSizeByUnit('b'),
            'ref_type'   => $refType ?: null,
            'ref_folder' => $refFolder ?: null,
        ]);

        return redirect()->to('dashboard/uploads')->with('success', lang('Dashboard.fileSentSuccess'));
    }

    // Télécharge un fichier uploadé (appartenance vérifiée par utilisateur)
    public function download(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! cfg('uploads_page_enabled', true)) {
            return redirect()->to('dashboard')->with('error', lang('Dashboard.featureDisabled'));
        }

        if (! cfg('allow_upload_download', true)) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.downloadDisabled'));
        }

        $userId = (int) session()->get('user_id');
        $record = model(UploadModel::class)->findOwnedBy($id, $userId);

        if (! $record) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileNotFound'));
        }

        $path = $this->safePath($record['party_folder'], $record['ref_folder'] ?? '', $record['stored_name']);

        if ($path === null || ! is_file($path)) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.physicalFileNotFound'));
        }

        $mime     = mime_content_type($path) ?: 'application/octet-stream';
        $filename = $this->safeFilename($record['original_name']);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents($path));
    }

    // Supprime un fichier uploadé, en base et sur le disque (appartenance vérifiée)
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! cfg('uploads_page_enabled', true)) {
            return redirect()->to('dashboard')->with('error', lang('Dashboard.featureDisabled'));
        }

        if (! cfg('allow_upload_delete', false)) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.deletionDisabledMsg'));
        }

        $userId  = (int) session()->get('user_id');
        $uploads = model(UploadModel::class);
        $record  = $uploads->findOwnedBy($id, $userId);

        if (! $record) {
            return redirect()->to('dashboard/uploads')->with('error', lang('Dashboard.fileNotFound'));
        }

        $path = $this->safePath($record['party_folder'], $record['ref_folder'] ?? '', $record['stored_name']);

        if ($path !== null && is_file($path)) {
            unlink($path);
        }

        $uploads->delete($id);

        return redirect()->to('dashboard/uploads')->with('success', lang('Dashboard.fileDeleted'));
    }

    // Retourne le chemin réel uniquement s'il est dans UPLOAD_DIR (anti path traversal)
    private function safePath(string $partyFolder, string $refFolder, string $filename): ?string
    {
        $base     = realpath(self::UPLOAD_DIR);
        $relative = $refFolder !== ''
            ? $partyFolder . '/' . $refFolder . '/' . $filename
            : $partyFolder . '/' . $filename;
        $path = realpath(self::UPLOAD_DIR . $relative);

        if ($base === false || $path === false) {
            return null;
        }

        return str_starts_with($path, $base . DIRECTORY_SEPARATOR) ? $path : null;
    }

    // Retire les caractères pouvant casser l'en-tête Content-Disposition
    private function safeFilename(string $name): string
    {
        return str_replace(['"', "\r", "\n", "\0"], '', $name);
    }

    // Garde uniquement les entrées avec un champ 'ref' valide, exclut les brouillons si configuré
    private function filterRefs(array $results): array
    {
        if (isset($results['error'])) {
            return [];
        }
        $showDrafts = (bool) cfg('show_drafts', false);
        return array_filter($results, function ($r) use ($showDrafts) {
            if (! is_array($r) || empty($r['ref'])) {
                return false;
            }
            if (! $showDrafts && (int) ($r['statut'] ?? 0) === 0) {
                return false;
            }
            return true;
        });
    }

    // Génère un suffixe aléatoire pour éviter les collisions de noms de fichiers
    private function randomToken(int $length): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max   = strlen($chars) - 1;
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $chars[random_int(0, $max)];
        }
        return $token;
    }

    // Convertit un texte en identifiant de dossier/fichier sûr (accents, minuscules, tirets)
    private function slugify(string $name): string
    {
        $map = [
            'à'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n','À'=>'a','Â'=>'a','Ä'=>'a','É'=>'e','È'=>'e',
            'Ê'=>'e','Ë'=>'e','Î'=>'i','Ï'=>'i','Ô'=>'o','Ö'=>'o','Ù'=>'u',
            'Û'=>'u','Ü'=>'u','Ç'=>'c',
        ];
        $name = strtr($name, $map);
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9]+/', '-', $name);
        return trim($name, '-') ?: 'fichier';
    }
}
