<?php
/*
 * Schema definition for 'migareference_reserved_sections'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['migareference_qualification_content_setting'] = [
    'migareference_qualification_content_setting_id' => [
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

    'qualification_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    
    'operation' => [
        'type' => 'varchar(20)',
        'default' => 'create',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'non_customer_content_type' => [
        'type' => 'varchar(50)',
        'is_null' => false,
    ],

    'non_customer_list_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],

    'customer_content_type' => [
        'type' => 'varchar(50)',
        'is_null' => false,
    ],

    'customer_list_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
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
