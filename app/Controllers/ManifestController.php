<?php

namespace App\Controllers;

class ManifestController extends BaseController
{
    // Génère le site.webmanifest dynamiquement (name/short_name basés sur company_name)
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $name = (string) cfg('company_name', 'DoliSpace');

        $manifest = [
            'name'             => $name,
            'short_name'       => mb_strlen($name) > 12 ? mb_substr($name, 0, 12) : $name,
            'icons'            => [
                [
                    'src'     => asset_or_default('web-app-manifest-192x192.png'),
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'maskable',
                ],
                [
                    'src'     => asset_or_default('web-app-manifest-512x512.png'),
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'theme_color'      => '#ffffff',
            'background_color' => '#ffffff',
            'display'          => 'standalone',
        ];

        return $this->response
            ->setContentType('application/manifest+json')
            ->setJSON($manifest);
    }
}
