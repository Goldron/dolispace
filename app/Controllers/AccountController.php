<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\UserModel;

class AccountController extends BaseController
{
    protected $helpers = ['url', 'vite'];

    // Affiche la page de compte, annule les changements d'email/mot de passe expirés
    public function index(): string
    {
        $users  = model(UserModel::class);
        $userId = (int) session()->get('user_id');
        $user   = $users->find($userId);

        if (! empty($user['email_pending']) && $users->isPendingExpired($user)) {
            $users->cancelEmailChange($userId);
            $user['email_pending']            = null;
            $user['email_pending_token']      = null;
            $user['email_pending_expires_at'] = null;
        }

        if (! empty($user['password_pending_token']) && $users->isPasswordPendingExpired($user)) {
            $users->cancelPasswordChange($userId);
            $user['password_pending_token']      = null;
            $user['password_pending_expires_at'] = null;
        }

        return view('dashboard/account', ['user' => $user]);
    }

    // Met à jour l'identité, et déclenche les liens de confirmation pour email/mot de passe
    public function updateProfile(): \CodeIgniter\HTTP\RedirectResponse
    {
        $users    = model(UserModel::class);
        $userId   = (int) session()->get('user_id');
        $user     = $users->find($userId);

        $firstName = trim($this->request->getPost('first_name') ?? '');
        $name      = trim($this->request->getPost('name') ?? '');
        $newEmail  = strtolower(trim($this->request->getPost('email') ?? ''));
        $password  = $this->request->getPost('new_password') ?? '';
        $confirm   = $this->request->getPost('confirm_password') ?? '';
        $locale    = $this->request->getPost('locale');
        $locale    = in_array($locale, config(\Config\App::class)->supportedLocales, true) ? $locale : null;

        if (empty($firstName) || empty($name)) {
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.firstNameLastNameRequired'));
        }

        $users->update($userId, ['first_name' => $firstName, 'name' => $name, 'locale' => $locale]);
        session()->set('user_name', trim($firstName . ' ' . $name));
        session()->set('user_locale', $locale);
        model(LogModel::class)->record($userId, 'update_profile', ['first_name' => $firstName, 'name' => $name]);

        $emailChanging    = $newEmail && $newEmail !== strtolower($user['email']) && empty($user['email_pending']);
        $passwordChanging = ! empty($password) && empty($user['password_pending_token']);
        $logo             = ($emailChanging || $passwordChanging) ? logo_for_email() : '';

        // Changement d'email (ignoré si modification déjà en attente)
        if ($emailChanging) {
            if (! filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                return redirect()->to('dashboard/account')->with('error', lang('Auth.invalidEmail'));
            }
            if ($users->where('email', $newEmail)->where('id !=', $userId)->first()) {
                return redirect()->to('dashboard/account')->with('error', lang('Dashboard.emailAlreadyUsed'));
            }
            $thirdparty = service('dolibarr')->getThirdpartyByEmail($newEmail);
            if (! empty($thirdparty) && isset($thirdparty['id']) && ! isset($thirdparty['error'])) {
                return redirect()->to('dashboard/account')->with('error', lang('Dashboard.emailAlreadyAssociated'));
            }

            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $users->requestEmailChange($userId, $newEmail, password_hash($token, PASSWORD_BCRYPT), $expires);
            $this->sendEmailConfirmLink($newEmail, $token, $logo);
        }

        // Changement de mot de passe (ignoré si modification déjà en attente)
        if ($passwordChanging) {
            if (strlen($password) < 8) {
                return redirect()->to('dashboard/account')->with('error', lang('Dashboard.passwordMinLength'));
            }
            if ($password !== $confirm) {
                return redirect()->to('dashboard/account')->with('error', lang('Dashboard.passwordsDoNotMatch'));
            }

            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $users->requestPasswordChange($userId, password_hash($password, PASSWORD_BCRYPT), password_hash($token, PASSWORD_BCRYPT), $expires);
            $this->sendPasswordConfirmLink($user['email'], $token, $logo);
        }

        return redirect()->to('dashboard/account')->with('success', lang('Dashboard.profileUpdated'));
    }

    // Valide le lien reçu par email et applique le changement d'adresse
    public function confirmEmail(string $token): \CodeIgniter\HTTP\RedirectResponse
    {
        $users  = model(UserModel::class);
        $userId = (int) session()->get('user_id');
        $user   = $users->find($userId);

        if (empty($user['email_pending']) || empty($user['email_pending_token'])) {
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.invalidOrExpiredLink'));
        }

        if ($users->isPendingExpired($user)) {
            $users->cancelEmailChange($userId);
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.validationLinkExpired'));
        }

        if (! password_verify($token, $user['email_pending_token'])) {
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.invalidLink'));
        }

        $newEmail = $user['email_pending'];
        $users->applyEmailChange($userId, $newEmail);
        session()->set('user_email', $newEmail);
        model(LogModel::class)->record($userId, 'update_email', ['email' => $newEmail]);

        return redirect()->to('dashboard/account')->with('success', lang('Dashboard.emailUpdated'));
    }

    // Valide le lien reçu par email et applique le changement de mot de passe
    public function confirmPassword(string $token): \CodeIgniter\HTTP\RedirectResponse
    {
        $users  = model(UserModel::class);
        $userId = (int) session()->get('user_id');
        $user   = $users->find($userId);

        if (empty($user['password_pending_token'])) {
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.invalidOrExpiredLink'));
        }

        if ($users->isPasswordPendingExpired($user)) {
            $users->cancelPasswordChange($userId);
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.validationLinkExpired'));
        }

        if (! password_verify($token, $user['password_pending_token'])) {
            return redirect()->to('dashboard/account')->with('error', lang('Dashboard.invalidLink'));
        }

        $users->applyPasswordChange($userId);
        model(LogModel::class)->record($userId, 'update_password');

        return redirect()->to('dashboard/account')->with('success', lang('Dashboard.passwordUpdated'));
    }

    // Annule une demande de changement d'email en attente
    public function cancelEmail(): \CodeIgniter\HTTP\RedirectResponse
    {
        model(UserModel::class)->cancelEmailChange((int) session()->get('user_id'));
        return redirect()->to('dashboard/account')->with('success', lang('Dashboard.modificationCancelled'));
    }

    // Annule une demande de changement de mot de passe en attente
    public function cancelPassword(): \CodeIgniter\HTTP\RedirectResponse
    {
        model(UserModel::class)->cancelPasswordChange((int) session()->get('user_id'));
        return redirect()->to('dashboard/account')->with('success', lang('Dashboard.modificationCancelled'));
    }

    // Envoie l'email contenant le lien de confirmation de changement d'adresse
    private function sendEmailConfirmLink(string $to, string $token, string $logo): void
    {
        $confirmUrl = base_url('dashboard/account/confirm-email/' . $token);
        $email      = make_email();
        $email->setTo($to);
        $email->setSubject(lang('Emails.confirmEmailSubject') . ' — ' . cfg('company_name'));
        $email->setMessage(view('dashboard/emails/email_change_link', [
            'confirmUrl' => $confirmUrl,
            'email'      => $to,
            'logo'       => $logo,
        ]));
        $email->send();
    }

    // Envoie l'email contenant le lien de confirmation de changement de mot de passe
    private function sendPasswordConfirmLink(string $to, string $token, string $logo): void
    {
        $confirmUrl = base_url('dashboard/account/confirm-password/' . $token);
        $email      = make_email();
        $email->setTo($to);
        $email->setSubject(lang('Emails.confirmPasswordSubject') . ' — ' . cfg('company_name'));
        $email->setMessage(view('dashboard/emails/password_change_link', [
            'confirmUrl' => $confirmUrl,
            'logo'       => $logo,
        ]));
        $email->send();
    }
}
