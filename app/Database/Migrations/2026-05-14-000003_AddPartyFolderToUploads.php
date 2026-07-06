<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPartyFolderToUploads extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('uploads', [
            'party_folder' => [
                'type'    => 'TEXT',
                'null'    => false,
                'default' => '',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('uploads', 'party_folder');
    }
}
