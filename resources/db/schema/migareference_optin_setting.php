<?php

/**
 *
 * Schema definition for 'migareference_optin_setting'
 *
 * Last update: 2024-03-18
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_optin_setting'] = [
    'migareference_optin_setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
	'app_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application',
            'column' => 'app_id',
            'name' => 'FK_MIGAREFERENCE_OPTIN_SETTING_AID',
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
	'optin_setting' => [
		'type'    => 'text',
		'is_null' => true,
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci'
	],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];