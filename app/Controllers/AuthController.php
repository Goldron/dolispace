<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $helpers = ['form', 'url', 'vite'];

    protected UserModel $users;
    protected LogModel  $logs;

    // Initialise les modèles utilisés par tous les endpoints du contrôleur
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->users = model(UserModel::class);
        $this->logs  = model(LogModel::class);
    }

    // GET /auth
    public function index(): string
    {
        return view('auth/email');
    }

    // POST /auth/check-email
    public function checkEmail(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $email = strtolower(trim($this->request->getPost('email') ?? ''));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('auth')->with('error', lang('Auth.invalidEmail'));
        }

        $user = $this->users->findByEmail($email);

        // Utilisateur local avec mot de passe récent → formulaire de connexion
        if ($user && $this->hasRecentPassword($user)) {
            session()->set('auth_email', $email);

            return redirect()->to('auth/password');
        }

        // Email enregistré sur plusieurs tiers/contacts Dolibarr : Dolibarr n'impose pas l'unicité,
        // on refuse plutôt que de rattacher arbitrairement au premier tiers trouvé
        if ($this->hasDuplicateEmailInDolibarr($email)) {
            return redirect()->to('auth')->with('error', lang('Auth.duplicateEmailContactSupport'));
        }

        // Vérification dans Dolibarr : contact d'abord, tiers ensuite
        $partyId = $this->resolvePartyIdFromEmail($email);

        // Dolibarr injoignable (cURL en échec ou erreur serveur) : ne pas confondre avec "email inconnu"
        if ($partyId === false) {
            return view('auth/maintenance');
        }

        if ($partyId !== null) {
            // Un compte local est déjà rattaché à ce tiers → orienter vers le bon email
            $existing = $this->users->findByPartyId($partyId);
            if ($existing) {
                return redirect()->to('auth')->with('error', lang('Auth.accountAlreadyExists'));
            }

            $emailKey = 'pending_lock_' . hash('sha256', $email);

            if (! cache()->get($emailKey)) {
                $token = bin2hex(random_bytes(32));

                cache()->save('pending_' . hash('sha256', $token), [
                    'email'    => $email,
                    'party_id' => $partyId,
                ], 86400);

                cache()->save($emailKey, true, 1800);

                $this->sendVerificationEmail($email, $token);
            }

            return redirect()->to('auth/pending')->with('pending_email', $email);
        }

        return view('auth/denied');
    }

    // Vrai si l'email correspond à plus d'un tiers ou plus d'un contact dans Dolibarr
    private function hasDuplicateEmailInDolibarr(string $email): bool
    {
        $dolibarr = service('dolibarr');

        return $dolibarr->countThirdpartiesByEmail($email) > 1
            || $dolibarr->countContactsByEmail($email) > 1;
    }

    // Résout l'ID du tiers Dolibarr à partir d'un email : contact d'abord, tiers ensuite.
    // Retourne l'ID du tiers, null si aucune correspondance, ou false si Dolibarr est injoignable.
    private function resolvePartyIdFromEmail(string $email): int|false|null
    {
        $dolibarr = service('dolibarr');

        if (cfg('search_contact_first', true)) {
            $contact = $dolibarr->getContactByEmail($email);

            if (isset($contact['error'])) {
                $status = (int) ($contact['status'] ?? 0);
                if ($status === 0 || $status >= 500) {
                    return false;
                }
            } elseif (! empty($contact) && isset($contact['id']) && ! empty($contact['socid'])) {
                return (int) $contact['socid'];
            }
        }

        $thirdparty = $dolibarr->getThirdpartyByEmail($email);

        if (isset($thirdparty['error'])) {
            $status = (int) ($thirdparty['status'] ?? 0);

            return ($status === 0 || $status >= 500) ? false : null;
        }

        if (! empty($thirdparty) && isset($thirdparty['id'])) {
            return (int) $thirdparty['id'];
        }

        return null;
    }

    // GET /auth/pending
    public function showPending(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $email = session()->getFlashdata('pending_email');

        if (! $email) {
            return redirect()->to('auth');
        }

        return view('auth/pending', ['email' => $email]);
    }

    // GET /auth/password
    public function showPassword(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $email = session()->get('auth_email');

        if (! $email) {
            return redirect()->to('auth');
        }

        return view('auth/password', ['email' => $email]);
    }

    // POST /auth/login
    public function login(): \CodeIgniter\HTTP\RedirectResponse
    {
        $email    = session()->get('auth_email');
        $password = $this->request->getPost('password') ?? '';

        if (! $email) {
            return redirect()->to('auth');
        }

        $user = $this->users->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->to('auth/password')->with('error', lang('Auth.incorrectPassword'));
        }

        if (! $user['is_active']) {
            return redirect()->to('auth/password')->with('error', lang('Auth.accountDisabled'));
        }

        session()->remove('auth_email');
        session()->set([
            'user_id'     => $user['id'],
            'user_name'   => trim(($user['first_name'] ?? '') . ' ' . $user['name']),
            'user_email'  => $user['email'],
            'user_locale' => $user['locale'] ?? null,
            'party_id'    => $user['party_id'],
            'logged_in'   => true,
        ]);

        $this->users->recordLogin($user['id'], $this->request->getIPAddress());
        $this->logs->record($user['id'], 'login');

        return redirect()->to('dashboard');
    }

    // GET /auth/verify/{token}
    public function verify(string $token): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $data = cache()->get('pending_' . hash('sha256', $token));

        if (! $data) {
            return view('auth/denied', [
                'message' => lang('Auth.invalidOrExpiredLink'),
            ]);
        }

        return view('auth/register', [
            'email'    => $data['email'],
            'party_id' => $data['party_id'],
            'token'    => $token,
        ]);
    }

    // POST /auth/register
    public function doRegister(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $token = $this->request->getPost('token') ?? '';
        $data  = cache()->get('pending_' . hash('sha256', $token));

        if (! $data) {
            return redirect()->to('auth')->with('error', lang('Auth.sessionExpired'));
        }

        $rules = [
            'first_name' => 'required|max_length[100]',
            'name'       => 'required|max_length[100]',
            'password'   => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/register', [
                'email'      => $data['email'],
                'party_id'   => $data['party_id'],
                'token'      => $token,
                'validation' => $this->validator,
            ]);
        }

        // withDeleted() inclut les comptes soft-deletés (même email → unique DB)
        $existing  = $this->users->withDeleted()->findByEmail($data['email']);
        $isDeleted = $existing && $existing['deleted_at'] !== null;

        $payload = [
            'first_name'        => $this->request->getPost('first_name'),
            'name'              => $this->request->getPost('name'),
            'password'          => $this->request->getPost('password'), // hashPassword hook
            'email_verified_at' => date('Y-m-d H:i:s'),
        ];

        if ($isDeleted) {
            // Réactive d'abord le compte (le modèle ignore les lignes soft-deletées dans update())
            db_connect()->table('users')->where('id', $existing['id'])->update(['deleted_at' => null]);
        }

        if ($existing) {
            // Compte existant (actif ou restauré) → mise à jour
            $this->users->update($existing['id'], array_merge($payload, ['is_active' => 1]));
            $userId = $existing['id'];
        } else {
            // Nouveau compte
            $userId = $this->users->insert(array_merge($payload, [
                'party_id'  => $data['party_id'],
                'email'     => $data['email'],
                'is_active' => 1,
            ]));

            if (! $userId) {
                log_message('error', '[doRegister] insert failed for ' . $data['email'] . ': ' . implode(', ', $this->users->errors()));
                return redirect()->to('auth')->with('error', lang('Auth.accountCreationFailed'));
            }
        }

        cache()->delete('pending_' . hash('sha256', $token));
        cache()->delete('pending_lock_' . hash('sha256', $data['email']));

        $this->logs->record($userId, $existing ? 'password_renewed' : 'register');

        session()->set([
            'user_id'    => $userId,
            'user_name'  => trim($this->request->getPost('first_name') . ' ' . $this->request->getPost('name')),
            'user_email' => $data['email'],
            'party_id'   => $data['party_id'],
            'logged_in'  => true,
        ]);

        return redirect()->to('dashboard');
    }

    // POST /auth/request-otp
    public function requestOtp(): \CodeIgniter\HTTP\RedirectResponse
    {
        $email = session()->get('auth_email');

        if (! $email) {
            return redirect()->to('auth');
        }

        $user = $this->users->findByEmail($email);

        if (! $user || ! $user['is_active']) {
            return redirect()->to('auth/password')->with('error', lang('Auth.unableToSendCode'));
        }

        $lockKey = 'login_otp_lock_' . hash('sha256', $email);

        if (cache()->get($lockKey)) {
            return redirect()->to('auth/otp')->with('error', lang('Auth.codeAlreadySent'));
        }

        $logo = logo_for_email();

        $otp    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpKey = 'login_otp_' . hash('sha256', $email);

        cache()->save($otpKey,  password_hash($otp, PASSWORD_BCRYPT), (int) cfg('otp_ttl', 900));
        cache()->save($lockKey, true, (int) cfg('otp_rate_limit', 120));

        $emailSvc = make_email();
        $emailSvc->setTo($email);
        $emailSvc->setSubject(lang('Emails.loginCodeSubject') . ' — ' . cfg('company_name'));
        $emailSvc->setMessage(view('auth/emails/login_otp', ['otp' => $otp, 'logo' => $logo]));
        $emailSvc->send();

        return redirect()->to('auth/otp');
    }

    // GET /auth/otp
    public function showOtp(): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $email = session()->get('auth_email');

        if (! $email) {
            return redirect()->to('auth');
        }

        return view('auth/otp', ['email' => $email]);
    }

    // POST /auth/verify-otp
    public function verifyOtp(): \CodeIgniter\HTTP\RedirectResponse
    {
        $email = session()->get('auth_email');

        if (! $email) {
            return redirect()->to('auth');
        }

        $otp    = trim($this->request->getPost('otp') ?? '');
        $otpKey = 'login_otp_' . hash('sha256', $email);
        $hash   = cache()->get($otpKey);

        if (! $hash || ! password_verify($otp, $hash)) {
            return redirect()->to('auth/otp')->with('error', lang('Auth.incorrectOrExpiredCode'));
        }

        $user = $this->users->findByEmail($email);

        if (! $user || ! $user['is_active']) {
            return redirect()->to('auth')->with('error', lang('Auth.accountNotFoundOrDisabled'));
        }

        cache()->delete($otpKey);
        cache()->delete('login_otp_lock_' . hash('sha256', $email));

        session()->remove('auth_email');
        session()->set([
            'user_id'     => $user['id'],
            'user_name'   => trim(($user['first_name'] ?? '') . ' ' . $user['name']),
            'user_email'  => $user['email'],
            'user_locale' => $user['locale'] ?? null,
            'party_id'    => $user['party_id'],
            'logged_in'   => true,
        ]);

        $this->users->recordLogin($user['id'], $this->request->getIPAddress());
        $this->logs->record($user['id'], 'login_otp');

        return redirect()->to('dashboard');
    }

    // GET /auth/logout
    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $this->logs->record($userId, 'logout');
            cache()->delete('dashboard_' . $userId);
        }

        session()->destroy();

        return redirect()->to('auth');
    }

    // Vrai si le mot de passe a été défini/renouvelé il y a moins d'un an
    private function hasRecentPassword(array $user): bool
    {
        if (empty($user['password_updated_at'])) {
            return false;
        }

        return new \DateTime($user['password_updated_at']) > new \DateTime('-1 year');
    }

    // Envoie l'email contenant le lien de finalisation d'inscription
    private function sendVerificationEmail(string $to, string $token): void
    {
        $link  = site_url('auth/verify/' . $token);
        $email = make_email();
        $email->setTo($to);
        $email->setSubject(lang('Emails.accessSubject') . ' — ' . cfg('company_name'));
        $email->setMessage(view('auth/emails/verify', [
            'link'  => $link,
            'email' => $to,
            'logo'  => logo_for_email(),
        ]));

        $email->send();
    }

}
