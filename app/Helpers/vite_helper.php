<?php

if (! function_exists('vite')) {
    /**
     * Injecte les balises HTML pour les assets Vite.
     * En développement : charge depuis le serveur Vite (HMR).
     * En production : lit le manifest et charge les fichiers compilés.
     *
     * @param string|string[] $entries Point(s) d'entrée (ex: 'resources/js/app.js')
     */
    function vite(string|array $entries): string
    {
        $entries = (array) $entries;

        if (config('Vite')->useDevServer) {
            return _vite_dev($entries);
        }

        return _vite_prod($entries);
    }
}

if (! function_exists('_vite_dev')) {
    function _vite_dev(array $entries): string
    {
        /** @var \Config\Vite $config */
        $config = config('Vite');
        $devUrl = rtrim($config->devServerUrl, '/');

        $html  = '<script type="module" src="' . $devUrl . '/@vite/client"></script>' . "\n";

        foreach ($entries as $entry) {
            $html .= '<script type="module" src="' . $devUrl . '/' . ltrim($entry, '/') . '"></script>' . "\n";
        }

        return $html;
    }
}

if (! function_exists('_vite_prod')) {
    function _vite_prod(array $entries): string
    {
        $manifestPath = FCPATH . 'assets/.vite/manifest.json';

        if (! is_file($manifestPath)) {
            return '<!-- Vite manifest introuvable. Lancez "npm run build". -->';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $html     = '';

        foreach ($entries as $entry) {
            $entry = ltrim($entry, '/');

            if (! isset($manifest[$entry])) {
                continue;
            }

            $chunk = $manifest[$entry];

            foreach ($chunk['css'] ?? [] as $css) {
                $html .= '<link rel="stylesheet" href="' . base_url('assets/' . $css) . '">' . "\n";
            }

            $html .= '<script type="module" src="' . base_url('assets/' . $chunk['file']) . '"></script>' . "\n";
        }

        return $html;
    }
}
