<?php

/**
 *
 * Schema definition for 'migareference_processed_referrer'
 *
 * Last update: 2024-04-24
 *
 */

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_processed_referrer'] = [
	'migareference_processed_referrer_id' => [
		'type' => 'int(11) unsigned',
		'auto_increment' => true,
		'primary' => true,
	],
	'app_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application',
            'column' => 'app_id',
            'name' => 'FK_MIGAREFERENCE_PROCESSED_REFERRER_AID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'app_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
	'referrer_id' => [
		'type' => 'int(11) unsigned',
		'default' => '0'
	],
	'processed_date' => [
		'type' => 'datetime',
		'is_null' => true,
	],
	'created_at' => [
		'type' => 'datetime'
	]
];