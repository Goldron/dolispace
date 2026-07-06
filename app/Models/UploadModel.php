<?php

namespace App\Models;

use CodeIgniter\Model;

class UploadModel extends Model
{
    protected $table         = 'uploads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'user_id',
        'party_id',
        'party_folder',
        'ref_type',
        'ref_id',
        'ref_folder',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
    ];

    public function getForParty(int $partyId): array
    {
        return $this->where('party_id', $partyId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function findOwnedBy(int $id, int $userId): ?array
    {
        return $this->where('id', $id)
                    ->where('user_id', $userId)
                    ->first();
    }
}
