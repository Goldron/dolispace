<?php

if (! function_exists('currency_symbol')) {
    /**
     * Convertit un code devise ISO 4217 (tel que renvoyé par Dolibarr) en symbole d'affichage.
     */
    function currency_symbol(string $code): string
    {
        return match (strtoupper($code)) {
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'CHF' => 'CHF',
            'JPY' => '¥',
            default => strtoupper($code),
        };
    }
}

if (! function_exists('doc_currency')) {
    /**
     * Résout la devise d'affichage d'un document Dolibarr (facture/commande/devis) :
     * EUR par défaut, ou la devise du document si le module multidevise a été utilisé
     * pour l'émettre (champ multicurrency_code renvoyé par l'API).
     */
    function doc_currency(array $doc): array
    {
        $code = strtoupper((string) ($doc['multicurrency_code'] ?? 'EUR'));

        return [
            'code'    => $code,
            'symbol'  => currency_symbol($code),
            'foreign' => $code !== '' && $code !== 'EUR',
        ];
    }
}

if (! function_exists('doc_amount')) {
    /**
     * Lit un montant (HT/TTC/subprice…) d'un document ou d'une ligne Dolibarr, en
     * utilisant la contre-valeur multidevise (champ multicurrency_*) si le document
     * a été émis dans une devise étrangère — cf. doc_currency().
     */
    function doc_amount(array $row, string $field, array $currency): float
    {
        if ($currency['foreign'] && isset($row['multicurrency_' . $field])) {
            return (float) $row['multicurrency_' . $field];
        }

        return (float) ($row[$field] ?? 0);
    }
}

if (! function_exists('fmt_money')) {
    /**
     * Formate un montant avec séparateur de milliers insécable et le symbole de devise.
     */
    function fmt_money(float $amount, string $symbol): string
    {
        return number_format($amount, 2, ',', "\u{A0}") . "\u{A0}" . $symbol;
    }
}
