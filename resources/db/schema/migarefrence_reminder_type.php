<?php
/*
 * Schema definition for 'migarefrence_reminder_type'
 * status: 1 Active 2 disable 3 delete
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_reminder_type'] = [
    'migarefrence_reminder_type_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'reminder_type_text' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'status' => [
        'type' => 'int(11) unsigned',
        'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
