<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('admin_url')) {
    /**
     * Segment d'URL de l'espace admin, configurable via app.admin_path (défaut : 'admin').
     * Permet de changer le chemin d'accès à l'administration par mesure de sécurité.
     */
    function admin_url(string $path = ''): string
    {
        $prefix = trim((string) env('app.admin_path', 'admin'), '/');

        return trim($prefix . '/' . ltrim($path, '/'), '/');
    }
}
