<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            // Lien Dolibarr
            'party_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'party_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'customer_code' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Identité
            'first_name' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'name' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Authentification
            'email' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'password' => [
                'type' => 'TEXT',
                'null' => false,
            ],

            // Contact
            'phone' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone_mobile' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Statut
            'is_active' => [
                'type'    => 'INTEGER',
                'null'    => false,
                'default' => 1,
            ],

            // Dates de vérification / connexion
            'email_verified_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'last_login_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'last_login_ip' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Réinitialisation du mot de passe
            'reset_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reset_token_expires_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'password_updated_at' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Timestamps (gérés par le Model CI4)
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
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('customer_code');
        $this->forge->addKey('party_id');
        $this->forge->addKey('reset_token');

        $this->forge->createTable('users', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('users', true);
    }
}
