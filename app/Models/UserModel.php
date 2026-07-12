<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'party_id',
        'party_token',
        'customer_code',
        'first_name',
        'name',
        'email',
        'password',
        'phone',
        'phone_mobile',
        'locale',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'reset_token',
        'reset_token_expires_at',
        'password_updated_at',
        'email_pending',
        'email_pending_token',
        'email_pending_expires_at',
        'password_pending_hash',
        'password_pending_token',
        'password_pending_expires_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'name'     => 'required|max_length[255]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $hidden = ['password', 'reset_token'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password']           = password_hash($data['data']['password'], PASSWORD_BCRYPT);
            $data['data']['password_updated_at'] = date('Y-m-d H:i:s');
        }

        return $data;
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findByPartyId(int $partyId): ?array
    {
        return $this->where('party_id', $partyId)->first();
    }

    public function findByResetToken(string $token): ?array
    {
        return $this->where('reset_token', $token)
                    ->where('reset_token_expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public function recordLogin(int $id, string $ip): void
    {
        $this->update($id, [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ]);
    }

    public function verifyEmail(int $id): void
    {
        $this->update($id, ['email_verified_at' => date('Y-m-d H:i:s')]);
    }

    public function requestEmailChange(int $id, string $newEmail, string $hashedOtp, string $expires): void
    {
        $this->update($id, [
            'email_pending'            => $newEmail,
            'email_pending_token'      => $hashedOtp,
            'email_pending_expires_at' => $expires,
        ]);
    }

    public function applyEmailChange(int $id, string $newEmail): void
    {
        $this->update($id, [
            'email'                    => $newEmail,
            'email_pending'            => null,
            'email_pending_token'      => null,
            'email_pending_expires_at' => null,
        ]);
    }

    public function cancelEmailChange(int $id): void
    {
        $this->update($id, [
            'email_pending'            => null,
            'email_pending_token'      => null,
            'email_pending_expires_at' => null,
        ]);
    }

    public function isPendingExpired(array $user): bool
    {
        return $this->isExpiredAt($user['email_pending_expires_at'] ?? null);
    }

    public function requestPasswordChange(int $id, string $hashedPassword, string $hashedOtp, string $expires): void
    {
        $this->update($id, [
            'password_pending_hash'       => $hashedPassword,
            'password_pending_token'      => $hashedOtp,
            'password_pending_expires_at' => $expires,
        ]);
    }

    public function applyPasswordChange(int $id): void
    {
        $user = $this->find($id);

        // Bypass du hook hashPassword : on écrit directement le hash déjà bcrypté
        $this->db->table($this->table)->where('id', $id)->update([
            'password'                    => $user['password_pending_hash'],
            'password_updated_at'         => date('Y-m-d H:i:s'),
            'password_pending_hash'       => null,
            'password_pending_token'      => null,
            'password_pending_expires_at' => null,
        ]);
    }

    public function cancelPasswordChange(int $id): void
    {
        $this->update($id, [
            'password_pending_hash'       => null,
            'password_pending_token'      => null,
            'password_pending_expires_at' => null,
        ]);
    }

    public function isPasswordPendingExpired(array $user): bool
    {
        return $this->isExpiredAt($user['password_pending_expires_at'] ?? null);
    }

    private function isExpiredAt(?string $expiresAt): bool
    {
        if (empty($expiresAt)) {
            return true;
        }

        return new \DateTime($expiresAt) <= new \DateTime();
    }
}
