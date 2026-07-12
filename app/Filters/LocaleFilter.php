<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

class LocaleFilter implements FilterInterface
{
    // Applique la langue choisie par l'utilisateur (profil client ou menu admin), en override de la négociation automatique du navigateur
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|null
    {
        $locale = session()->get('admin_logged_in')
            ? session()->get('admin_locale')
            : session()->get('user_locale');

        if (is_string($locale) && in_array($locale, config(App::class)->supportedLocales, true)) {
            $request->setLocale($locale);
            service('language')->setLocale($locale);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface|null
    {
        return null;
    }
}
