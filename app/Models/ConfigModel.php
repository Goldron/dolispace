<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $table         = 'config';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'config_key',
        'config_hook',
        'config_position',
        'config_value',
        'value_type',
        'description',
    ];

    public function getValue(string $key, mixed $default = null): mixed
    {
        $row = $this->where('config_key', $key)->first();

        if (! $row) {
            return $default;
        }

        return $this->cast($row['config_value'], $row['value_type']);
    }

    public function put(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $stored = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        $existing = $this->where('config_key', $key)->first();

        if ($existing) {
            $this->update($existing['id'], ['config_value' => $stored]);
        } else {
            $this->insert([
                'config_key'   => $key,
                'config_value' => $stored,
                'value_type'   => $type,
                'description'  => $description,
            ]);
        }
    }

    public function all(): array
    {
        $rows   = $this->orderBy('config_key', 'ASC')->findAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['config_key']] = $this->cast($row['config_value'], $row['value_type']);
        }

        return $result;
    }

    private function cast(?string $value, string $type): mixed
    {
        return match ($type) {
            'bool'   => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'int'    => (int) $value,
            'float'  => (float) $value,
            'json'   => json_decode($value, true),
            default  => $value,
        };
    }
}
