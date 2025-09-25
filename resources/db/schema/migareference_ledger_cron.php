<?php
/*
 * Schema definition for 'migareference_ledger_cron'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_ledger_cron'] = [
    'ledger_cron_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
	],
	'xml_file_name' => [
        'type' => 'varchar(200)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'xml_payload' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'api_request' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'api_response' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'response' => [
        'type' => 'varchar(10)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'message' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'eth_address' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'ipfs_address' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'eth_sha_hash' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'eth_address_url' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	
	'is_notified' => [
        'type' => 'tinyint(4)',
        'default' => '0'
	],
	'notarization_platform' => [ //Solana
        'type' => 'enum(\'Ethereum\',\'Solana\')',
        'default' => 'Ethereum',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => false,
    ],
	'started_at' => [
		'type' => 'datetime',
		'is_null' => true
	],
	'ended_at' => [
		'type' => 'datetime',
		'is_null' => true
    ],
];
