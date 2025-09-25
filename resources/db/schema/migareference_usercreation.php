<?php
/*
 * Schema definition for 'migareference_usercreation'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_usercreation'] = [
    'migareference_usercreation_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'user_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],
    'key' => [
      'type' => 'varchar(120)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
