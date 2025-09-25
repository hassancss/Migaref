<?php
/*
 * Schema definition for 'migareference_reminder_daybook'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_reminder_daybook'] = [
    'migareference_reminder_daybook_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'receiver_id' => [
     'type' => 'int(11) unsigned',
     'is_null' => false
    ],    
    'data' => [
      'type' => 'TEXT',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],      
    'description' => [
      'type' => 'TEXT',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],      
    'title_info' => [
        'type' => 'varchar(200)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
      ],
    'status' => [
        'type' => 'enum("pending", "done", "declined")',
        'default' => 'pending',
        'is_null' => false
      ],
    'type' => [
        'type' => 'enum("referrer", "report")',
        'default' => 'referrer',
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
