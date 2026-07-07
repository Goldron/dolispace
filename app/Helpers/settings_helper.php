<?php

if (! function_exists('cfg')) {
    /**
     * Lit une valeur depuis la table config (avec cast automatique).
     * Cache toutes les entrées en mémoire au premier appel.
     */
    function cfg(string $key, mixed $default = null): mixed
    {
        static $cache = null;

        if ($cache === null) {
            $cache = [];
            try {
                $rows = db_connect()
                    ->table('config')
                    ->select('config_key, config_value, value_type')
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    $cache[$row['config_key']] = match ($row['value_type']) {
                        'bool'  => filter_var($row['config_value'], FILTER_VALIDATE_BOOLEAN),
                        'int'   => (int) $row['config_value'],
                        'float' => (float) $row['config_value'],
                        'json'  => json_decode($row['config_value'], true),
                        default => $row['config_value'],
                    };
                }
            } catch (\Throwable) {
                // DB pas encore disponible (migrations, etc.)
            }
        }

        return array_key_exists($key, $cache) ? $cache[$key] : $default;
    }
}

if (! function_exists('versioned_asset')) {
    /**
     * Ajoute ?v=filemtime à un chemin d'asset public pour invalider le cache navigateur
     * après un remplacement (logo, fond, label, icônes…).
     */
    function versioned_asset(string $path): string
    {
        if ($path === '') {
            return $path;
        }

        $abs = FCPATH . ltrim($path, '/');

        return $path . (is_file($abs) ? '?v=' . filemtime($abs) : '');
    }
}

if (! function_exists('image_to_base64')) {
    function image_to_base64(string $imagePath, bool $withMime = true): string|false
    {
        $imageData = @file_get_contents($imagePath);

        if ($imageData === false) {
            return false;
        }

        $base64 = base64_encode($imageData);

        if ($withMime) {
            return 'data:' . mime_content_type($imagePath) . ';base64,' . $base64;
        }

        return $base64;
    }
}

if (! function_exists('logo_for_email')) {
    function logo_for_email(): string
    {
        return image_to_base64(FCPATH . ltrim(cfg('logo_url', ''), '/')) ?: '';
    }
}

if (! function_exists('make_email')) {
    /**
     * Retourne une instance du service email configurée depuis la table config.
     */
    function make_email(): \CodeIgniter\Email\Email
    {
        $email = service('email', null, false);
        $email->initialize([
            'protocol'   => 'smtp',
            'SMTPHost'   => cfg('smtp_host', ''),
            'SMTPPort'   => (int) cfg('smtp_port', 587),
            'SMTPCrypto' => cfg('smtp_crypto', 'tls'),
            'SMTPUser'   => cfg('smtp_user', ''),
            'SMTPPass'   => cfg('smtp_pass', ''),
            'fromEmail'  => cfg('smtp_from_email', ''),
            'fromName'   => cfg('company_name', ''),
            'mailType'   => 'html',
        ]);

        return $email;
    }
}
