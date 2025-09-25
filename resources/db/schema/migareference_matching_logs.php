<?php
$schemas['migareference_matching_logs'] = [
    'migareference_matching_logs_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => false
    ],
    'referrer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => false
    ],   
    'calling_method' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'response_type' => [
        'type' => 'enum("success", "no matches", "error")',
        'default' => 'success',
        'is_null' => false
    ],
    'prompt' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'response' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'token_used' => [
        'type' => 'int(11) unsigned',
        'default' => '0',
        'is_null' => false
    ],
    'created_at' => [
        'type' => 'datetime',
        'default' => 'CURRENT_TIMESTAMP'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'default' => 'CURRENT_TIMESTAMP',
        'on_update' => 'CURRENT_TIMESTAMP'
    ]
];

?>