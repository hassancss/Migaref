<?php
/*
 * Schema definition for 'migareference_setting'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_setting'] = [
    'setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'help_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'bitly_login' => [
        'type' => 'varchar(80)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'bitly_key' => [
        'type' => 'varchar(80)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
    'tax_id' => [
        'type' => 'varchar(80)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'migachain_token' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'migachain_client_id' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
     'auto_cron_status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
     'auto_cron_time' => [
      'type' => 'int(11) unsigned',
      'default' => '5'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
