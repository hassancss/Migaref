<?php
/*
 * Schema definition for 'migareference_ledger'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_ledger'] = [
    'ledger_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
	],
	'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
	'ledger_cron_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
	],
	'report_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
	],
	'app_name' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'report_no' => [
		'type' => 'int(11) unsigned',
		'default' => '0'  
	],
	'referral_name' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'referral_surname' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'owner_name' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'owner_surname' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'encryption_key' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'report_created_at' => [
        'type' => 'datetime'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
];
