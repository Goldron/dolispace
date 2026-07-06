<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    /**
     * Active le serveur de développement Vite (HMR).
     * Surchargeable via .env : vite.useDevServer = true
     */
    public bool $useDevServer = false;

    /**
     * URL du serveur de développement Vite.
     * Surchargeable via .env : vite.devServerUrl = http://client.goldron.fr:5173
     */
    public string $devServerUrl = 'http://client.goldron.fr:5173';
}
