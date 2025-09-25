<?php
/*
 * Schema definition for 'migareference_invitation_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_invitation_logs'] = [
    'migareference_invitation_logs_id' => [
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
    'log_type' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ]
];
