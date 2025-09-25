<?php
/*
 * Schema definition for 'migareference_twillio_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_twillio_log'] = [
    'migareference_twillio_log_id' => [
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
      'default' => '0'
    ],    
    'sms_text' => [
      'type' => 'varchar(300)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'api_response' => [
      'type' => 'varchar(300)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ]
];
