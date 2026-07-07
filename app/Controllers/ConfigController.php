<?php

namespace App\Controllers;

use App\Libraries\DolibarrApi;
use App\Models\ConfigModel;

class ConfigController extends BaseController
{
    protected $helpers = ['url', 'vite', 'settings'];

    // Clé de config => module Dolibarr requis, pour la carte "Fonctionnalités"
    private const FEATURE_MODULES = [
        'expedition_enabled'         => 'expedition',
        'certificatsclients_enabled' => 'certificatsclients',
        'commande_enabled'           => 'commande',
        'propal_enabled'             => 'propal',
        'facture_enabled'            => 'facture',
    ];

    // Icônes générées à partir de l'upload (nom de fichier => taille en pixels)
    private const ICON_SIZES = [
        'web-app-manifest-512x512.png' => 512,
        'web-app-manifest-192x192.png' => 192,
        'apple-touch-icon.png'         => 180,
        'favicon-96x96.png'            => 96,
    ];

    // Tailles embarquées dans favicon.ico (format multi-résolution)
    private const FAVICON_ICO_SIZES = [16, 32, 48];

    // Affiche la liste des clés de configuration, triées par hook/position/clé
    public function index(): string
    {
        $config = model(ConfigModel::class)
            ->orderBy('config_hook', 'ASC')
            ->orderBy('config_position', 'ASC')
            ->orderBy('config_key', 'ASC')
            ->findAll();

        $dolibarr = new DolibarrApi();
        $featureModulesAvailable = [];

        foreach (self::FEATURE_MODULES as $key => $moduleName) {
            $featureModulesAvailable[$key] = $dolibarr->hasModule($moduleName);
        }

        return view('admin/config', [
            'config'                   => $config,
            'featureModulesAvailable'  => $featureModulesAvailable,
        ]);
    }

    // Met à jour toutes les clés de configuration en une seule soumission (valeurs, hooks, images)
    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        $model     = model(ConfigModel::class);
        $config    = $model->findAll();
        $overrides = [];

        // logo/background : nom unique par upload (jamais écrasés) — label : comportement inchangé
        $imageKeys = [
            'logo_url'       => ['logo', true],
            'background_url' => ['background', true],
            'label_url'      => ['label', false],
        ];

        foreach ($imageKeys as $configKey => [$basename, $unique]) {
            $result = $this->handleImageUpload($configKey . '_file', $basename, $unique);
            if ($result === false) {
                $file = $this->request->getFile($configKey . '_file');
                $phpCode = $file ? $file->getError() : UPLOAD_ERR_NO_FILE;
                $phpErrors = [
                    UPLOAD_ERR_INI_SIZE  => sprintf('Le fichier dépasse la limite serveur (%s).', ini_get('upload_max_filesize')),
                    UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la limite du formulaire.',
                    UPLOAD_ERR_PARTIAL   => "L'envoi du fichier a été interrompu.",
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire serveur manquant.',
                    UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
                ];
                $msg = $phpErrors[$phpCode] ?? sprintf(
                    "Format non autorisé pour « %s ». Formats acceptés : PNG, JPG, WebP, GIF%s.",
                    $configKey,
                    $configKey === 'logo_url' ? ', SVG' : ''
                );
                return redirect()->to(admin_url('config'))->with('error', $msg);
            }
            if ($result !== null) {
                $overrides[$configKey] = $result;
            }
        }

        $hookUpdates = $this->request->getPost('_hook') ?? [];
        $clearKeys   = $this->request->getPost('_clear') ?? [];

        $db = db_connect();
        $db->transStart();

        foreach ($config as $row) {
            $key   = $row['config_key'];
            $value = isset($overrides[$key])
                ? $overrides[$key]
                : (isset($clearKeys[$key]) ? '' : $this->request->getPost($key));
            $payload = [];

            if ($value !== null) {
                if ($row['value_type'] === 'bool' && ! isset($overrides[$key])) {
                    $value = $this->request->getPost($key) ? 'true' : 'false';
                }
                $payload['config_value'] = $value;
            }

            if (isset($hookUpdates[$row['id']])) {
                $payload['config_hook'] = trim($hookUpdates[$row['id']]) ?: null;
            }

            if (! empty($payload)) {
                $model->update($row['id'], $payload);
            }
        }

        $db->transComplete();

        return redirect()->to(admin_url('config'))->with('success', 'Configuration mise à jour.');
    }

    // Ajoute une nouvelle clé de configuration
    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $key   = trim($this->request->getPost('config_key') ?? '');
        $value = trim($this->request->getPost('config_value') ?? '');
        $type  = $this->request->getPost('value_type') ?? 'string';
        $desc  = trim($this->request->getPost('description') ?? '');

        if (empty($key)) {
            return redirect()->to(admin_url('config'))->with('error', 'La clé est obligatoire.');
        }

        model(ConfigModel::class)->put($key, $value, $type, $desc ?: null);

        return redirect()->to(admin_url('config'))->with('success', "Clé « {$key} » ajoutée.");
    }

    // Supprime une clé de configuration (refusé si elle est protégée)
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $row = model(ConfigModel::class)->find($id);

        if (! $row || (bool) $row['protected']) {
            return redirect()->to(admin_url('config'))->with('error', 'Cette clé est protégée et ne peut pas être supprimée.');
        }

        model(ConfigModel::class)->delete($id);

        return redirect()->to(admin_url('config'))->with('success', 'Entrée supprimée.');
    }

    // Régénère toutes les icônes de l'application à partir d'une image source (512×512 minimum)
    public function updateIcon(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('icon_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->to(admin_url('config'))->with('error', "Aucun fichier valide reçu.");
        }

        $allowed = ['image/png', 'image/jpeg', 'image/webp'];

        if (! in_array($file->getMimeType(), $allowed, true)) {
            return redirect()->to(admin_url('config'))->with('error', "Format non autorisé pour l'icône. Formats acceptés : PNG, JPG, WebP.");
        }

        $tempPath   = $file->getTempName();
        $dimensions = @getimagesize($tempPath);

        if (! $dimensions || $dimensions[0] < 512 || $dimensions[1] < 512) {
            return redirect()->to(admin_url('config'))->with('error', "L'image doit faire au moins 512×512 pixels.");
        }

        foreach (self::ICON_SIZES as $filename => $size) {
            try {
                \Config\Services::image()
                    ->withFile($tempPath)
                    ->fit($size, $size, 'center')
                    ->save(FCPATH . 'images/' . $filename);
            } catch (\Throwable) {
                return redirect()->to(admin_url('config'))->with('error', "Échec de la génération de l'icône « {$filename} ».");
            }
        }

        try {
            $this->generateFaviconIco($tempPath);
        } catch (\Throwable) {
            return redirect()->to(admin_url('config'))->with('error', 'Échec de la génération de favicon.ico.');
        }

        return redirect()->to(admin_url('config'))->with('success', 'Icônes régénérées avec succès.');
    }

    // Construit un favicon.ico multi-résolution (16/32/48 px) à partir d'images PNG encapsulées
    // (format ICO moderne accepté par tous les navigateurs actuels, pas de BMP legacy nécessaire)
    private function generateFaviconIco(string $sourcePath): void
    {
        $images = [];

        foreach (self::FAVICON_ICO_SIZES as $size) {
            $tmp = tempnam(sys_get_temp_dir(), 'ico') . '.png';

            \Config\Services::image()
                ->withFile($sourcePath)
                ->fit($size, $size, 'center')
                ->save($tmp);

            $images[$size] = file_get_contents($tmp);
            unlink($tmp);
        }

        $count   = count($images);
        $offset  = 6 + ($count * 16);
        $header  = pack('vvv', 0, 1, $count);
        $entries = '';
        $data    = '';

        foreach ($images as $size => $bytes) {
            $entries .= pack(
                'C4vvVV',
                $size >= 256 ? 0 : $size,
                $size >= 256 ? 0 : $size,
                0,
                0,
                1,
                32,
                strlen($bytes),
                $offset
            );
            $data   .= $bytes;
            $offset += strlen($bytes);
        }

        file_put_contents(FCPATH . 'images/favicon.ico', $header . $entries . $data);
    }

    // Envoie un email de test avec la configuration SMTP actuelle
    public function testEmail(): \CodeIgniter\HTTP\RedirectResponse
    {
        $to = trim($this->request->getPost('test_email_to') ?? '');

        if (empty($to) || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to(admin_url('config'))->with('error', 'Adresse email invalide.');
        }

        $email = make_email();
        $email->setTo($to);
        $email->setSubject('Test SMTP — ' . cfg('company_name', 'Espace client'));
        $email->setMessage(view('admin/emails/test_email', ['to' => $to]));

        if (! $email->send(false)) {
            return redirect()->to(admin_url('config'))->with('error', 'Échec de l\'envoi : ' . $email->printDebugger(['headers', 'subject', 'body']));
        }

        return redirect()->to(admin_url('config'))->with('success', "Email de test envoyé à {$to}.");
    }

    // Traite l'upload d'une image de config : valide, déplace, redimensionne si besoin.
    // $unique = true : nom de fichier unique par upload, l'ancien fichier n'est jamais touché.
    private function handleImageUpload(string $inputName, string $basename, bool $unique = false): string|false|null
    {
        $file = $this->request->getFile($inputName);

        if (! $file || $file->hasMoved()) {
            return null;
        }

        // Aucun fichier sélectionné — comportement normal
        if ($file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // Erreur PHP lors de l'upload (taille, permis, etc.)
        if (! $file->isValid()) {
            return false;
        }

        $allowed = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp', 'image/gif'];
        $mime     = $file->getMimeType();

        if (! in_array($mime, $allowed, true)) {
            return false;
        }

        $filename = $unique
            ? $basename . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $file->getExtension()
            : $basename . '.' . $file->getExtension();
        $dest = FCPATH . 'images';
        $file->move($dest, $filename, ! $unique);

        // Redimensionne si largeur > 2000 px (SVG ignoré — format vectoriel)
        if ($mime !== 'image/svg+xml') {
            $path = $dest . DIRECTORY_SEPARATOR . $filename;
            try {
                $image = \Config\Services::image()->withFile($path);
                if (($image->getProperties(true)['width'] ?? 0) > 2000) {
                    $image->resize(2000, 2000, true, 'width')->save($path);
                }
            } catch (\Throwable) {
                // Redimensionnement échoué — le fichier original est conservé
            }
        }

        return '/images/' . $filename;
    }
}
