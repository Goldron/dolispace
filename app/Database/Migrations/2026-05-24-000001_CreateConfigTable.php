<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfigTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'config_key' => [
                'type'   => 'TEXT',
                'null'   => false,
            ],
            'config_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'value_type' => [
                'type'    => 'TEXT',
                'null'    => false,
                'default' => 'string',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('config_key');
        $this->forge->createTable('config', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('config', true);
    }
}
