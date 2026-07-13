<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropCustomerCodeUniqueOnUsers extends Migration
{
    // SQLite ne permet pas de retirer une contrainte UNIQUE via ALTER TABLE, et
    // Forge::modifyColumn() ne suffit pas non plus : sa reconstruction de table
    // (SQLite3\Table) reporte les anciens index tels quels sans tenir compte des
    // champs modifiés, donc la contrainte UNIQUE sur customer_code serait recréée
    // à l'identique. On reconstruit donc la table à la main en SQL brut.
    public function up(): void
    {
        $this->rebuildTable(uniqueCustomerCode: false);
    }

    public function down(): void
    {
        $this->rebuildTable(uniqueCustomerCode: true);
    }

    private function rebuildTable(bool $uniqueCustomerCode): void
    {
        $prefix = $this->db->DBPrefix;
        $table  = $prefix . 'users';
        $temp   = $prefix . 'users_rebuild';

        $customerCodeType = 'TEXT' . ($uniqueCustomerCode ? ' UNIQUE' : '');

        $this->db->query('PRAGMA foreign_keys = OFF');
        $this->db->transStart();

        $this->db->query("
            CREATE TABLE {$temp} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                party_id INTEGER NULL,
                party_token TEXT NULL,
                customer_code {$customerCodeType},
                name TEXT,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                phone TEXT,
                phone_mobile TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                email_verified_at TEXT NULL,
                last_login_at TEXT NULL,
                last_login_ip TEXT NULL,
                reset_token TEXT NULL,
                reset_token_expires_at TEXT NULL,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                updated_at TEXT NOT NULL DEFAULT (datetime('now')),
                deleted_at TEXT NULL,
                first_name TEXT NULL,
                password_updated_at TEXT NULL,
                email_pending TEXT NULL,
                email_pending_token TEXT NULL,
                email_pending_expires_at TEXT NULL,
                password_pending_hash TEXT NULL,
                password_pending_token TEXT NULL,
                password_pending_expires_at TEXT NULL,
                locale VARCHAR NULL
            )
        ");

        $this->db->query("INSERT INTO {$temp} SELECT * FROM {$table}");
        $this->db->query("DROP TABLE {$table}");
        $this->db->query("ALTER TABLE {$temp} RENAME TO {$table}");
        $this->db->query("CREATE INDEX {$table}_reset_token ON {$table}(reset_token)");

        $this->db->transComplete();
        $this->db->query('PRAGMA foreign_keys = ON');
    }
}
