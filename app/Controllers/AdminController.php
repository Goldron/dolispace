<?php

namespace App\Controllers;

use App\Libraries\DolibarrApi;
use App\Models\LogModel;
use App\Models\UploadModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form', 'vite'];

    // Affiche le formulaire de connexion (redirige si déjà connecté)
    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(admin_url());
        }

        if (! $this->adminCredentialsConfigured()) {
            session()->setFlashdata('error', "app.admin_login et app.admin_password doivent être définis dans le fichier .env.");
        }

        return view('admin/login');
    }

    // Vérifie les identifiants depuis le .env et ouvre la session admin
    public function doLogin(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->adminCredentialsConfigured()) {
            return redirect()->to(admin_url('login'))->with('error', "app.admin_login et app.admin_password doivent être définis dans le fichier .env.");
        }

        $login    = trim((string) $this->request->getPost('login'));
        $password = (string) $this->request->getPost('password');

        $validLogin    = env('app.admin_login', '');
        $validPassword = env('app.admin_password', '');

        if ($login !== $validLogin || $password !== $validPassword) {
            return redirect()->to(admin_url('login'))->with('error', 'Identifiants incorrects.');
        }

        session()->set('admin_logged_in', true);
        session()->set('admin_login', $login);

        return redirect()->to(admin_url());
    }

    // app.admin_login / app.admin_password doivent être non vides pour autoriser la connexion
    // (sinon une comparaison à '' === '' laisserait passer un formulaire soumis vide)
    private function adminCredentialsConfigured(): bool
    {
        return trim((string) env('app.admin_login', '')) !== ''
            && trim((string) env('app.admin_password', '')) !== '';
    }

    // Détruit la session admin et redirige vers le login
    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->remove(['admin_logged_in', 'admin_login']);
        return redirect()->to(admin_url('login'));
    }

    // Tableau de bord : statistiques globales, liste des utilisateurs, activité récente paginée
    public function index(): string
    {
        $users   = model(UserModel::class);
        $uploads = model(UploadModel::class);
        $logs    = model(LogModel::class);

        $stats = [
            'users'   => $users->countAllResults(),
            'uploads' => $uploads->countAllResults(),
        ];

        // Jointure avec users pour afficher nom et email dans l'activité
        $recentLogs = $logs->select('logs.*, users.email, users.name AS user_name')
                          ->join('users', 'users.id = logs.user_id', 'left')
                          ->orderBy('logs.created_at', 'DESC')
                          ->paginate(20, 'logs');

        $logPager = $logs->pager;
        $search   = trim((string) $this->request->getGet('q'));

        if ($search !== '') {
            $userList = $users->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('customer_code', $search)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->findAll();
        } else {
            $userList = $users->orderBy('created_at', 'DESC')->limit(20)->findAll();
        }

        $dolibarr = new DolibarrApi();
        foreach ($userList as &$user) {
            $partyId = (int) ($user['party_id'] ?? 0);
            $user['company_name'] = null;

            if ($partyId > 0) {
                $cacheKey = 'thirdparty_name_' . $partyId;
                $companyName = cache($cacheKey);

                if ($companyName === null) {
                    $thirdparty  = $dolibarr->getThirdparty($partyId);
                    $companyName = isset($thirdparty['error']) ? '' : (string) ($thirdparty['name'] ?? '');
                    cache()->save($cacheKey, $companyName, (int) cfg('time_cache', 5));
                }

                $user['company_name'] = $companyName ?: null;
            }
        }
        unset($user);

        return view('admin/index', compact('stats', 'recentLogs', 'logPager', 'userList', 'search'));
    }

    // Supprime un utilisateur (soft delete)
    public function deleteUser(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        model(UserModel::class)->delete($id);

        return redirect()->to(admin_url())->with('success', 'Utilisateur supprimé.');
    }

    // Vide entièrement la table des utilisateurs (hors soft-delete, suppression réelle)
    public function clearUsers(): \CodeIgniter\HTTP\RedirectResponse
    {
        model(UserModel::class)->truncate();

        return redirect()->to(admin_url())->with('success', 'Tous les utilisateurs ont été supprimés.');
    }

    // Vide entièrement le journal d'activité
    public function clearLogs(): \CodeIgniter\HTTP\RedirectResponse
    {
        model(LogModel::class)->truncate();

        return redirect()->to(admin_url())->with('success', 'Le journal d\'activité a été vidé.');
    }

    // Page de diagnostics : ClamAV, SMTP, API Dolibarr et variables .env
    public function status(): string
    {
        $envVars = $this->parseEnvFile();

        // Vérifie disponibilité du binaire indépendamment de l'activation dans l'app
        $clamEnabled   = (bool) cfg('clamdscan', false);
        $binary        = escapeshellcmd((string) cfg('clamdscan_path', '/usr/bin/clamdscan'));
        exec($binary . ' --version 2>&1', $out, $code);
        $clamAvailable = $code === 0;
        $clamVersion   = $clamAvailable ? trim($out[0] ?? '') : null;

        // Vérifie que les variables SMTP minimales sont renseignées dans le .env
        $smtpStatus = [
            'host'   => (string) cfg('smtp_host', ''),
            'port'   => (string) cfg('smtp_port', ''),
            'user'   => (string) cfg('smtp_user', ''),
            'from'   => (string) cfg('smtp_from_email', ''),
            'active' => ! empty(cfg('smtp_host')) && ! empty(cfg('smtp_user')),
        ];

        // Teste chaque endpoint Dolibarr avec une requête minimale (limit=1)
        $api       = new DolibarrApi();
        $apiUrl    = (string) cfg('dolibarr_api_url', '');
        $minParams = ['limit' => 1];

        // Statut général de l'instance Dolibarr (version, environnement…)
        $statusResult  = $api->getStatus();
        $dolibarrInfo  = $statusResult['success'] ?? null;

        $endpoints = [
            'invoices'     => fn() => $api->getInvoices($minParams),
            'orders'       => fn() => $api->getOrders($minParams),
            'proposals'    => fn() => $api->getProposals($minParams),
            'thirdparties' => fn() => $api->getThirdparties($minParams),
        ];

        $dolibarr = [];
        foreach ($endpoints as $name => $call) {
            $result = $call();
            $dolibarr[$name] = [
                'ok'    => ! isset($result['error']),
                'error' => $result['error'] ?? null,
            ];
        }

        // Modules Dolibarr optionnels utilisés par l'espace client
        $dolibarrModules = [
            'certificatsclients' => $api->hasModule('certificatsclients'),
            'expedition'         => $api->hasModule('expedition'),
        ];

        return view('admin/status', compact('envVars', 'clamEnabled', 'clamAvailable', 'clamVersion', 'smtpStatus', 'apiUrl', 'dolibarrInfo', 'dolibarr', 'dolibarrModules'));
    }

    // Lit le fichier .env, groupe les variables par section et masque les valeurs sensibles
    private function parseEnvFile(): array
    {
        $path  = ROOTPATH . '.env';
        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];

        // Mots-clés déclenchant le masquage de la valeur
        $sensitive = ['password', 'pass', 'token', 'secret', 'key', 'smtp'];
        $sections  = [];
        $current   = 'Général';

        foreach ($lines as $line) {
            // Séparateur de section (#-------)
            if (preg_match('/^#-{4,}/', $line)) continue;

            // Titre de section (# NOM DE SECTION)
            if (preg_match('/^#\s+(.+)$/', $line, $m)) {
                $current = trim($m[1]);
                continue;
            }

            // Ligne vide ou commentaire inline
            if (trim($line) === '' || str_starts_with(trim($line), '#')) continue;

            if (str_contains($line, '=')) {
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim(trim($val), "'\"");

                $isSensitive = false;
                foreach ($sensitive as $word) {
                    if (str_contains(strtolower($key), $word)) {
                        $isSensitive = true;
                        break;
                    }
                }

                $sections[$current][] = [
                    'key'   => $key,
                    'value' => $isSensitive ? '••••••••' : $val,
                ];
            }
        }

        return $sections;
    }
}
