<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordPendingToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'password_pending_hash' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'password_pending_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'password_pending_expires_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', [
            'password_pending_hash',
            'password_pending_token',
            'password_pending_expires_at',
        ]);
    }
}
