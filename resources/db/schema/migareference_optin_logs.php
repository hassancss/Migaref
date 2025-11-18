<?php
/*
 * Schema definition for 'migareference_optin_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_optin_logs'] = [
    'migareference_optin_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
    ],
    'sponsor_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
    ],
    'province_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
    ],
    'ip_address' => [
        'type' => 'varchar(45)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'referrer_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'correlation_id' => [
        'type' => 'varchar(64)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'request_payload' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'validation_errors' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'downstream_response' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'stack_trace' => [
        'type' => 'longtext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'status' => [
        'type' => 'varchar(32)',
        'default' => 'pending',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'mismatch_flag' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '0',
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
];
