<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Hook : app
            ['config_key' => 'company_name',       'config_hook' => 'app',       'config_position' => 1,  'config_value' => 'Espace SpaceClient',           'value_type' => 'string', 'description' => 'Admin.descCompanyName',           'protected' => 1],
            ['config_key' => 'logo_url',            'config_hook' => 'app',       'config_position' => 2,  'config_value' => '/images/default/logo.svg',     'value_type' => 'string', 'description' => 'Admin.descLogoUrl',               'protected' => 1],
            ['config_key' => 'background_url',      'config_hook' => 'app',       'config_position' => 3,  'config_value' => '/images/default/background.jpg', 'value_type' => 'string', 'description' => 'Admin.descBackgroundUrl',         'protected' => 1],
            ['config_key' => 'background_animate', 'config_hook' => 'app',       'config_position' => 4,  'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descBackgroundAnimate',     'protected' => 1],
            ['config_key' => 'label_url',           'config_hook' => 'app',       'config_position' => 5,  'config_value' => '',                             'value_type' => 'string', 'description' => 'Admin.descLabelUrl',              'protected' => 1],
            ['config_key' => 'time_cache',          'config_hook' => 'app',       'config_position' => 6,  'config_value' => '5',                            'value_type' => 'int',    'description' => 'Admin.descTimeCache',             'protected' => 1],

            // Hook : upload
            ['config_key' => 'uploads_page_enabled',  'config_hook' => 'upload',  'config_position' => 1,  'config_value' => 'true',  'value_type' => 'bool',   'description' => 'Admin.descUploadsPageEnabled',    'protected' => 1],
            ['config_key' => 'allow_upload_download', 'config_hook' => 'upload',  'config_position' => 2,  'config_value' => 'true',  'value_type' => 'bool',   'description' => 'Admin.descAllowUploadDownload',   'protected' => 1],
            ['config_key' => 'allow_upload_delete',   'config_hook' => 'upload',  'config_position' => 3,  'config_value' => 'false', 'value_type' => 'bool',   'description' => 'Admin.descAllowUploadDelete',     'protected' => 1],
            ['config_key' => 'max_upload_size',       'config_hook' => 'upload',  'config_position' => 4,  'config_value' => '100',   'value_type' => 'int',    'description' => 'Admin.descMaxUploadSize',         'protected' => 1],
            ['config_key' => 'allowed_upload_types',  'config_hook' => 'upload',  'config_position' => 5,  'config_value' => 'pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,gif,webp,svg,eps,psd,ai,zip,txt', 'value_type' => 'string', 'description' => 'Admin.descAllowedUploadTypes', 'protected' => 1],

            // Hook : otp
            ['config_key' => 'otp_ttl',            'config_hook' => 'otp',        'config_position' => 1,  'config_value' => '900',   'value_type' => 'int',    'description' => 'Admin.descOtpTtl',                'protected' => 1],
            ['config_key' => 'otp_rate_limit',     'config_hook' => 'otp',        'config_position' => 2,  'config_value' => '10',    'value_type' => 'int',    'description' => 'Admin.descOtpRateLimit',          'protected' => 1],

            // Hook : antivirus
            ['config_key' => 'clamdscan',          'config_hook' => 'antivirus',  'config_position' => 1,  'config_value' => 'false',              'value_type' => 'bool',   'description' => 'Admin.descClamdscan',      'protected' => 1],
            ['config_key' => 'clamdscan_path',     'config_hook' => 'antivirus',  'config_position' => 2,  'config_value' => '/usr/bin/clamdscan', 'value_type' => 'string', 'description' => 'Admin.descClamdscanPath',  'protected' => 1],

            // Hook : dolibarr
            ['config_key' => 'dolibarr_api_url',                  'config_hook' => 'dolibarr', 'config_position' => 1, 'config_value' => 'https://dolibarr.goldron.fr/', 'value_type' => 'string', 'description' => 'Admin.descDolibarrApiUrl',                 'protected' => 1],
            ['config_key' => 'dolibarr_api_token',                'config_hook' => 'dolibarr', 'config_position' => 2, 'config_value' => '',                             'value_type' => 'string', 'description' => 'Admin.descDolibarrApiToken',               'protected' => 1],
            ['config_key' => 'show_drafts',                       'config_hook' => 'dolibarr', 'config_position' => 3, 'config_value' => 'false',                        'value_type' => 'bool',   'description' => 'Admin.descShowDrafts',                     'protected' => 1],
            ['config_key' => 'rebuild_pdf_on_failure',            'config_hook' => 'dolibarr', 'config_position' => 4, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descRebuildPdfOnFailure',            'protected' => 1],
            ['config_key' => 'expedition_enabled',                'config_hook' => 'dolibarr', 'config_position' => 5, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descExpeditionEnabled',              'protected' => 1],
            ['config_key' => 'rebuild_shipment_pdf_on_failure',   'config_hook' => 'dolibarr', 'config_position' => 6, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descRebuildShipmentPdfOnFailure',    'protected' => 1],
            ['config_key' => 'certificatsclients_enabled',        'config_hook' => 'dolibarr', 'config_position' => 7, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descCertificatsclientsEnabled',      'protected' => 1],
            ['config_key' => 'search_contact_first',              'config_hook' => 'dolibarr', 'config_position' => 8, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descSearchContactFirst',             'protected' => 1],
            ['config_key' => 'commande_enabled',                  'config_hook' => 'dolibarr', 'config_position' => 9, 'config_value' => 'true',                         'value_type' => 'bool',   'description' => 'Admin.descCommandeEnabled',                'protected' => 1],
            ['config_key' => 'propal_enabled',                    'config_hook' => 'dolibarr', 'config_position' => 10, 'config_value' => 'true',                        'value_type' => 'bool',   'description' => 'Admin.descPropalEnabled',                  'protected' => 1],
            ['config_key' => 'facture_enabled',                   'config_hook' => 'dolibarr', 'config_position' => 11, 'config_value' => 'true',                        'value_type' => 'bool',   'description' => 'Admin.descFactureEnabled',                 'protected' => 1],
            ['config_key' => 'vat_field_enabled',                 'config_hook' => 'dolibarr', 'config_position' => 12, 'config_value' => 'true',                        'value_type' => 'bool',   'description' => 'Admin.descVatFieldEnabled',                'protected' => 1],

            // Hook : email
            ['config_key' => 'smtp_host',          'config_hook' => 'email',      'config_position' => 1,  'config_value' => 'in-v3.mailjet.com', 'value_type' => 'string', 'description' => 'Admin.descSmtpHost',       'protected' => 1],
            ['config_key' => 'smtp_port',          'config_hook' => 'email',      'config_position' => 2,  'config_value' => '587',               'value_type' => 'int',    'description' => 'Admin.descSmtpPort',       'protected' => 1],
            ['config_key' => 'smtp_crypto',        'config_hook' => 'email',      'config_position' => 3,  'config_value' => 'tls',               'value_type' => 'string', 'description' => 'Admin.descSmtpCrypto',     'protected' => 1],
            ['config_key' => 'smtp_user',          'config_hook' => 'email',      'config_position' => 4,  'config_value' => '',                  'value_type' => 'string', 'description' => 'Admin.descSmtpUser',       'protected' => 1],
            ['config_key' => 'smtp_pass',          'config_hook' => 'email',      'config_position' => 5,  'config_value' => '',                  'value_type' => 'string', 'description' => 'Admin.descSmtpPass',       'protected' => 1],
            ['config_key' => 'smtp_from_email',    'config_hook' => 'email',      'config_position' => 6,  'config_value' => 'david@goldron.fr',  'value_type' => 'string', 'description' => 'Admin.descSmtpFromEmail',  'protected' => 1],
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
                        'description'     => $entry['description'],
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
