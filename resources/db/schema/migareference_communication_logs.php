<?php
/*
 * Schema definition for 'migareference_activity_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_communication_logs'] = [
    'migareference_communication_logs_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'phonebook_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],
    'user_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],     
     'reminder_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],    
     'chnage_by' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],    
     'user_type' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],    
    'log_type' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'note' => [
      'type' => 'varchar(300)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ]
];
