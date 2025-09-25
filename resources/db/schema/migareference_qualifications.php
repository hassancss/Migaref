<?php
/*
 * Schema definition for 'migareference_qualification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['migareference_qualifications'] = [
    'migareference_qualifications_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    'qlf_name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'qlf_file' => [
        'type' => 'varchar(30)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'qlf_credits' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => 0,
    ],
    'qlf_status' => [
        'type' => 'tinyint(1)',
        'is_null' => false,
        'default' => 1, // 1 = Active, 0 = Inactive
    ],
    'created_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
];
