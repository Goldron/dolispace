<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table      = 'logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'action',
        'ip',
        'user_agent',
        'payload',
        'performed_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function record(int $userId, string $action, ?array $payload = null, ?int $performedBy = null): void
    {
        $request = service('request');

        $this->insert([
            'user_id'      => $userId,
            'action'       => $action,
            'ip'           => $request->getIPAddress(),
            'user_agent'   => $request->getUserAgent()->getAgentString(),
            'payload'      => $payload ? json_encode($payload) : null,
            'performed_by' => $performedBy,
        ]);
    }

    public function getForUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
