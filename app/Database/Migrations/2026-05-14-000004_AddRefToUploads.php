<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRefToUploads extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('uploads', [
            'ref_type' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ref_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'ref_folder' => [
                'type'    => 'TEXT',
                'null'    => false,
                'default' => '',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('uploads', ['ref_type', 'ref_id', 'ref_folder']);
    }
}
