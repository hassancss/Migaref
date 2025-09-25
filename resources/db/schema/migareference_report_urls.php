<?php
/*
 * Schema definition for 'migareference_activity_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_report_urls'] = [
    'migareference_report_urls_id' => [
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
     'is_agent' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],     
    'long_url' => [
      'type' => 'varchar(280)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'token' => [
      'type' => 'varchar(40)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'short_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ]
];
