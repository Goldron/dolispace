<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Hook : app
            ['config_key' => 'company_name',       'config_hook' => 'app',       'config_position' => 1,  'config_value' => 'Espace SpaceClient',           'value_type' => 'string', 'description' => 'Nom de l\'entreprise affiché dans l\'interface',                                         'protected' => 1],
            ['config_key' => 'logo_url',            'config_hook' => 'app',       'config_position' => 2,  'config_value' => '/images/default/logo.svg',     'value_type' => 'string', 'description' => 'URL du logo affiché dans la navbar',                                                     'protected' => 1],
            ['config_key' => 'background_url',      'config_hook' => 'app',       'config_position' => 3,  'config_value' => '/images/default/background.jpg', 'value_type' => 'string', 'description' => 'Image de fond de la page de connexion',                                                  'protected' => 1],
            ['config_key' => 'background_animate', 'config_hook' => 'app',       'config_position' => 4,  'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Activer l\'animation Ken Burns sur l\'image de fond',                                    'protected' => 1],
            ['config_key' => 'label_url',           'config_hook' => 'app',       'config_position' => 5,  'config_value' => '',                             'value_type' => 'string', 'description' => 'Label / badge affiché en bas à droite de la page de connexion',                         'protected' => 1],
            ['config_key' => 'time_cache',          'config_hook' => 'app',       'config_position' => 6,  'config_value' => '5',                            'value_type' => 'int',    'description' => 'Durée du cache des pages en secondes (0 = désactivé)',                                   'protected' => 1],

            // Hook : upload
            ['config_key' => 'uploads_page_enabled',  'config_hook' => 'upload',  'config_position' => 1,  'config_value' => 'true',  'value_type' => 'bool',   'description' => 'Activer la page "Mes fichiers"',                                                          'protected' => 1],
            ['config_key' => 'allow_upload_download', 'config_hook' => 'upload',  'config_position' => 2,  'config_value' => 'true',  'value_type' => 'bool',   'description' => 'Autoriser le téléchargement des fichiers déposés',                                        'protected' => 1],
            ['config_key' => 'allow_upload_delete',   'config_hook' => 'upload',  'config_position' => 3,  'config_value' => 'false', 'value_type' => 'bool',   'description' => 'Autoriser la suppression des fichiers déposés',                                           'protected' => 1],
            ['config_key' => 'max_upload_size',       'config_hook' => 'upload',  'config_position' => 4,  'config_value' => '100',   'value_type' => 'int',    'description' => 'Taille maximale des fichiers uploadés (en Mo)',                                            'protected' => 1],
            ['config_key' => 'allowed_upload_types',  'config_hook' => 'upload',  'config_position' => 5,  'config_value' => 'pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,gif,webp,svg,eps,psd,ai,zip,txt', 'value_type' => 'string', 'description' => 'Extensions de fichiers autorisées à l\'upload (séparées par des virgules)', 'protected' => 1],

            // Hook : otp
            ['config_key' => 'otp_ttl',            'config_hook' => 'otp',        'config_position' => 1,  'config_value' => '900',   'value_type' => 'int',    'description' => 'Durée de validité du code OTP en secondes (900 = 15 min)',                                'protected' => 1],
            ['config_key' => 'otp_rate_limit',     'config_hook' => 'otp',        'config_position' => 2,  'config_value' => '10',    'value_type' => 'int',    'description' => 'Délai minimum entre deux envois de code OTP en secondes (120 = 2 min)',                   'protected' => 1],

            // Hook : antivirus
            ['config_key' => 'clamdscan',          'config_hook' => 'antivirus',  'config_position' => 1,  'config_value' => 'false',              'value_type' => 'bool',   'description' => 'Activer le scan antivirus ClamAV via clamdscan',        'protected' => 1],
            ['config_key' => 'clamdscan_path',     'config_hook' => 'antivirus',  'config_position' => 2,  'config_value' => '/usr/bin/clamdscan', 'value_type' => 'string', 'description' => 'Chemin vers le binaire clamdscan',                      'protected' => 1],

            // Hook : dolibarr
            ['config_key' => 'dolibarr_api_url',                  'config_hook' => 'dolibarr', 'config_position' => 1, 'config_value' => 'https://dolibarr.goldron.fr/', 'value_type' => 'string', 'description' => 'URL de base de l\'API Dolibarr',                                                              'protected' => 1],
            ['config_key' => 'dolibarr_api_token',                'config_hook' => 'dolibarr', 'config_position' => 2, 'config_value' => '',                             'value_type' => 'string', 'description' => 'Jeton d\'authentification API Dolibarr',                                                      'protected' => 1],
            ['config_key' => 'show_drafts',                       'config_hook' => 'dolibarr', 'config_position' => 3, 'config_value' => 'false',                        'value_type' => 'bool',   'description' => 'Afficher les brouillons Dolibarr dans les listes',                                           'protected' => 1],
            ['config_key' => 'rebuild_pdf_on_failure',            'config_hook' => 'dolibarr', 'config_position' => 4, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Régénérer le PDF via Dolibarr si le téléchargement d\'une commande échoue',                  'protected' => 1],
            ['config_key' => 'expedition_enabled',                'config_hook' => 'dolibarr', 'config_position' => 5, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Afficher les expéditions liées aux commandes (nécessite le module Dolibarr expedition)',      'protected' => 1],
            ['config_key' => 'rebuild_shipment_pdf_on_failure',   'config_hook' => 'dolibarr', 'config_position' => 6, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Régénérer le PDF via Dolibarr si le téléchargement d\'une expédition échoue',               'protected' => 1],
            ['config_key' => 'certificatsclients_enabled',        'config_hook' => 'dolibarr', 'config_position' => 7, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Afficher les certificats liés aux commandes (nécessite le module Dolibarr certificatsclients)', 'protected' => 1],
            ['config_key' => 'search_contact_first',              'config_hook' => 'dolibarr', 'config_position' => 8, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Rechercher d\'abord l\'email de contact avant l\'email du tiers lors de la connexion',        'protected' => 1],
            ['config_key' => 'commande_enabled',                  'config_hook' => 'dolibarr', 'config_position' => 9, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Activer les commandes (nécessite le module Dolibarr commande)',                               'protected' => 1],
            ['config_key' => 'propal_enabled',                    'config_hook' => 'dolibarr', 'config_position' => 10, 'config_value' => 'true',                        'value_type' => 'bool',   'description' => 'Activer les devis / propositions commerciales (nécessite le module Dolibarr propal)',        'protected' => 1],
            ['config_key' => 'facture_enabled',                   'config_hook' => 'dolibarr', 'config_position' => 11, 'config_value' => 'true',                        'value_type' => 'bool',   'description' => 'Activer les factures (nécessite le module Dolibarr facture)',                                 'protected' => 1],

            // Hook : email
            ['config_key' => 'smtp_host',          'config_hook' => 'email',      'config_position' => 1,  'config_value' => 'in-v3.mailjet.com', 'value_type' => 'string', 'description' => 'Hôte du serveur SMTP',             'protected' => 1],
            ['config_key' => 'smtp_port',          'config_hook' => 'email',      'config_position' => 2,  'config_value' => '587',               'value_type' => 'int',    'description' => 'Port du serveur SMTP',             'protected' => 1],
            ['config_key' => 'smtp_crypto',        'config_hook' => 'email',      'config_position' => 3,  'config_value' => 'tls',               'value_type' => 'string', 'description' => 'Chiffrement SMTP (tls ou ssl)',    'protected' => 1],
            ['config_key' => 'smtp_user',          'config_hook' => 'email',      'config_position' => 4,  'config_value' => '',                  'value_type' => 'string', 'description' => 'Identifiant SMTP',                'protected' => 1],
            ['config_key' => 'smtp_pass',          'config_hook' => 'email',      'config_position' => 5,  'config_value' => '',                  'value_type' => 'string', 'description' => 'Mot de passe SMTP',               'protected' => 1],
            ['config_key' => 'smtp_from_email',    'config_hook' => 'email',      'config_position' => 6,  'config_value' => 'david@goldron.fr',  'value_type' => 'string', 'description' => 'Adresse expéditrice des emails',  'protected' => 1],
        ];

        foreach ($entries as $entry) {
            $exists = $this->db->table('config')
                ->where('config_key', $entry['config_key'])
                ->countAllResults();

            if ($exists) {
                $this->db->table('config')
                    ->where('config_key', $entry['config_key'])
                    ->update([
                        'config_hook'     => $entry['config_hook'],
                        'config_position' => $entry['config_position'],
                        'protected'       => $entry['protected'],
                    ]);
            } else {
                $this->db->table('config')->insert(array_merge($entry, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
    }
}
