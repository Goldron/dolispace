<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsTable extends Migration
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
            'action' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'ip' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'performed_by' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('action');

        $this->forge->createTable('logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('logs', true);
    }
}
