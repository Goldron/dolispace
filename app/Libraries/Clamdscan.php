<?php

namespace App\Libraries;

class Clamdscan
{
    /**
     * Scanne un fichier via clamdscan.
     *
     * @return array{ clean: bool, virus: string|null, error: string|null }
     */
    public function scan(string $filePath): array
    {
        if (! cfg('clamdscan', false)) {
            return ['clean' => true, 'virus' => null, 'error' => null];
        }

        if (! file_exists($filePath)) {
            return $this->failure('Fichier introuvable : ' . $filePath);
        }

        // Le démon clamd tourne sous un utilisateur système distinct (ex: clamav) et ne
        // partage pas forcément de groupe avec PHP : sans lecture publique, il refuse
        // le fichier temporaire (0600 par défaut) avec "Access denied".
        @chmod($filePath, 0644);

        $binary  = (string) cfg('clamdscan_path', '/usr/bin/clamdscan');
        $command = escapeshellcmd($binary) . ' --no-summary ' . escapeshellarg($filePath) . ' 2>&1';
        $output  = [];
        $code    = 0;

        exec($command, $output, $code);

        // clamdscan exit codes : 0 = sain, 1 = virus trouvé, 2 = erreur
        if ($code === 0) {
            return ['clean' => true, 'virus' => null, 'error' => null];
        }

        if ($code === 1) {
            $virus = $this->parseVirus($output);
            log_message('warning', '[Clamdscan] Virus détecté dans ' . $filePath . ' : ' . $virus);
            return ['clean' => false, 'virus' => $virus, 'error' => null];
        }

        $message = implode(' ', $output) ?: 'Erreur inconnue de clamdscan.';
        log_message('error', '[Clamdscan] Erreur lors du scan de ' . $filePath . ' : ' . $message);
        return $this->failure($message);
    }

    private function parseVirus(array $output): string
    {
        foreach ($output as $line) {
            if (preg_match('/FOUND$/', trim($line))) {
                // format : /chemin/fichier: NomDuVirus FOUND
                if (preg_match('/:\s+(.+?)\s+FOUND$/', $line, $matches)) {
                    return $matches[1];
                }
            }
        }

        return 'Virus inconnu';
    }

    private function failure(string $message): array
    {
        return ['clean' => false, 'virus' => null, 'error' => $message];
    }
}
