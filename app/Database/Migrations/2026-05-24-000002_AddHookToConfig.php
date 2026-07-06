<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHookToConfig extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('config', [
            'config_hook' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'config_key',
            ],
        ]);

        $this->forge->addColumn('config', [
            'config_position' => [
                'type'    => 'INTEGER',
                'null'    => false,
                'default' => 0,
            ],
        ]);
    }

    public function down(): void
    {
        // SQLite ne supporte pas DROP COLUMN — migration irréversible
    }
}
