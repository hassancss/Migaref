<?php
/*
 * Schema definition for 'migareference_credits_api_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_credits_api_log'] = [
    'migareference_credits_api_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'request_ip' => [
      'type'    => 'varchar(30)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'response' => [
      'type'    => 'varchar(10)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'description' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
