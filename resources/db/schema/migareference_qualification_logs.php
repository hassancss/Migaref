<?php
/*
 * Schema definition for 'migareference_qualification_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_qualification_logs'] = [
    'migareference_qualification_logs_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'referrer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'previous_qualification_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => null
    ],
    'new_qualification_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => null
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'total_credits' => [
        'type' => 'decimal(12,2)',
        'default' => '0.00',
        'is_null' => false
    ],
    'action' => [
        'type' => 'varchar(32)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime',
        'is_null' => false
    ],
];