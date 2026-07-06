<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailPendingToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'email_pending' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'email_pending_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'email_pending_expires_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down(): void
    {
        // SQLite ne supporte pas DROP COLUMN avant 3.35 — on recrée sans les colonnes si besoin
        $this->forge->dropColumn('users', 'email_pending');
        $this->forge->dropColumn('users', 'email_pending_token');
        $this->forge->dropColumn('users', 'email_pending_expires_at');
    }
}
