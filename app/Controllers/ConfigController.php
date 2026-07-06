<?php

namespace App\Controllers;

use App\Models\ConfigModel;

class ConfigController extends BaseController
{
    protected $helpers = ['url', 'vite', 'settings'];

    public function index(): string
    {
        $config = model(ConfigModel::class)
            ->orderBy('config_hook', 'ASC')
            ->orderBy('config_position', 'ASC')
            ->orderBy('config_key', 'ASC')
            ->findAll();

        return view('admin/config', ['config' => $config]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        $model     = model(ConfigModel::class);
        $config    = $model->findAll();
        $overrides = [];

        foreach (['logo_url' => 'logo', 'background_url' => 'background', 'label_url' => 'label'] as $configKey => $basename) {
            $result = $this->handleImageUpload($configKey . '_file', $basename);
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
                return redirect()->to('admin/config')->with('error', $msg);
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

        return redirect()->to('admin/config')->with('success', 'Configuration mise à jour.');
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $key   = trim($this->request->getPost('config_key') ?? '');
        $value = trim($this->request->getPost('config_value') ?? '');
        $type  = $this->request->getPost('value_type') ?? 'string';
        $desc  = trim($this->request->getPost('description') ?? '');

        if (empty($key)) {
            return redirect()->to('admin/config')->with('error', 'La clé est obligatoire.');
        }

        model(ConfigModel::class)->put($key, $value, $type, $desc ?: null);

        return redirect()->to('admin/config')->with('success', "Clé « {$key} » ajoutée.");
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $row = model(ConfigModel::class)->find($id);

        if (! $row || (bool) $row['protected']) {
            return redirect()->to('admin/config')->with('error', 'Cette clé est protégée et ne peut pas être supprimée.');
        }

        model(ConfigModel::class)->delete($id);

        return redirect()->to('admin/config')->with('success', 'Entrée supprimée.');
    }

    public function testEmail(): \CodeIgniter\HTTP\RedirectResponse
    {
        $to = trim($this->request->getPost('test_email_to') ?? '');

        if (empty($to) || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('admin/config')->with('error', 'Adresse email invalide.');
        }

        $email = make_email();
        $email->setTo($to);
        $email->setSubject('Test SMTP — ' . cfg('company_name', 'Espace client'));
        $email->setMessage(view('admin/emails/test_email', ['to' => $to]));

        if (! $email->send(false)) {
            return redirect()->to('admin/config')->with('error', 'Échec de l\'envoi : ' . $email->printDebugger(['headers', 'subject', 'body']));
        }

        return redirect()->to('admin/config')->with('success', "Email de test envoyé à {$to}.");
    }

    private function handleImageUpload(string $inputName, string $basename): string|false|null
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

        $filename = $basename . '.' . $file->getExtension();
        $dest     = FCPATH . 'images';
        $file->move($dest, $filename, true);

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
