<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUploadsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'party_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'original_name' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'stored_name' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'mime_type' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'size' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('party_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('uploads', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('uploads', true);
    }
}
