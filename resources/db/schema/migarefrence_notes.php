<?php
/*
 * Schema definition for 'migarefrence_notes'
 * status:1 Active 0 Deleted
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_notes'] = [
    'migarefrence_notes_id' => [
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
    'report_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'notes_content' => [
       'type' => 'MEDIUMTEXT',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'status' => [
        'type' => 'int(11) unsigned',
        'default' => '1'
    ],
    'is_read' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'read_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
