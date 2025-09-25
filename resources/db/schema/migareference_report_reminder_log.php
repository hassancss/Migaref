<?php
/*
 * Schema definition for 'migareference_report_reminder_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_report_reminder_log'] = [
    'migareference_report_reminder_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],                
     'push_log_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],           
     'email_log_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],           
     'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],  
    'receipent' => [
      'type' => 'varchar(15)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],        
    'created_at' => [
        'type' => 'datetime'
    ]
];
