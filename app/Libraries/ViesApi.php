<?php

namespace App\Libraries;

/**
 * Client pour l'API REST VIES (VAT Information Exchange System — Commission Européenne).
 * Vérifie la validité d'un numéro de TVA intracommunautaire.
 *
 * Endpoint : GET https://ec.europa.eu/taxation_customs/vies/rest-api/ms/{country}/vat/{number}
 */
class ViesApi
{
    private const BASE_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms';
    private const TIMEOUT  = 10;

    /**
     * Vérifie un numéro de TVA intracommunautaire.
     *
     * @param  string $vatNumber  Numéro complet avec préfixe pays (ex: FR12345678901)
     * @return array{
     *   valid: bool,
     *   vat_number: string,
     *   country_code: string,
     *   name: string|null,
     *   address: string|null,
     *   error: string|null
     * }
     */
    public function validate(string $vatNumber): array
    {
        $vatNumber = strtoupper(preg_replace('/\s+/', '', $vatNumber));

        if (strlen($vatNumber) < 4) {
            return $this->failure('Numéro de TVA trop court.');
        }

        $countryCode = substr($vatNumber, 0, 2);
        $number      = substr($vatNumber, 2);

        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return $this->failure('Code pays invalide (les 2 premiers caractères doivent être des lettres).');
        }

        $url = self::BASE_URL . '/' . $countryCode . '/vat/' . urlencode($number);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($curlError) {
            log_message('error', '[ViesApi] cURL error: ' . $curlError);
            return $this->failure('Impossible de contacter le service VIES.');
        }

        if ($status === 404) {
            return $this->failure('Numéro de TVA introuvable.');
        }

        if ($status !== 200) {
            log_message('error', '[ViesApi] HTTP ' . $status . ' for ' . $vatNumber);
            return $this->failure('Service VIES indisponible (HTTP ' . $status . ').');
        }

        $data = json_decode($body, true);

        if (! is_array($data)) {
            return $this->failure('Réponse invalide du service VIES.');
        }

        $userError = $data['userError'] ?? '';

        if (! ($data['isValid'] ?? false)) {
            $message = match ($userError) {
                'INVALID'             => 'Numéro de TVA invalide.',
                'NOT_FOUND'           => 'Numéro de TVA introuvable.',
                'SERVICE_UNAVAILABLE' => 'Service VIES temporairement indisponible.',
                'MS_UNAVAILABLE'      => 'L\'État membre est temporairement indisponible.',
                'TIMEOUT'             => 'Le service VIES a mis trop de temps à répondre.',
                'VAT_BLOCKED'         => 'Numéro de TVA bloqué.',
                'IP_BLOCKED'          => 'Accès refusé par le service VIES.',
                default               => 'Numéro de TVA non valide.',
            };

            return $this->failure($message);
        }

        return [
            'valid'        => true,
            'vat_number'   => $countryCode . $number,
            'country_code' => $countryCode,
            'name'         => $data['name']    !== '---' ? ($data['name']    ?? null) : null,
            'address'      => $data['address'] !== '---' ? ($data['address'] ?? null) : null,
            'error'        => null,
        ];
    }

    private function failure(string $message): array
    {
        return [
            'valid'        => false,
            'vat_number'   => null,
            'country_code' => null,
            'name'         => null,
            'address'      => null,
            'error'        => $message,
        ];
    }
}
