<?php
/*
 * Schema definition for 'migareference_webhook_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_webhook_logs'] = [
    'migareference_webhook_logs_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'webhook_type' => [
        'type' => "enum('report', 'referrer')",
        'default' => 'report'
    ],
    'retry_count' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],  
    'response_type' => [
      'type' => "enum('success', 'faill')",
      'default' => 'success'
    ],  
    'response_message' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'http_code' => [
      'type' => 'smallint(3) unsigned',
      'is_null' => true, 
      'default' => null
  ],
    'created_at' => [
        'type' => 'timestamp',
        'default' => 'CURRENT_TIMESTAMP'
    ],
    'updated_at' => [
        'type' => 'timestamp',
        'default' => 'CURRENT_TIMESTAMP',
        'on_update' => 'CURRENT_TIMESTAMP'
    ],
];
