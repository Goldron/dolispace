<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProtectedToConfig extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('config', [
            'protected' => [
                'type'    => 'INTEGER',
                'null'    => false,
                'default' => 0,
                'after'   => 'description',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('config', 'protected');
    }
}
