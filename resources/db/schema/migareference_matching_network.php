<?php
/*
 * Schema definition for 'migareference_matching_network'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_matching_network'] = [
    'migareference_matching_network_id' => [
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
    'network_referrer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'matching_description' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'status' => [
        'type'=>'enum("available","matched","discard")',
        'default'=>'available'   
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
