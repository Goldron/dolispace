<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocaleToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'locale' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'locale');
    }
}
