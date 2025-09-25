<?php
/*
 * Schema definition for 'migareference_webhook_error_template'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_webhook_error_template'] = [
    'migareference_webhook_error_template_id' => [
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
    'webhook_type' => [
        'type' => "enum('report', 'referrer', 'reminder')",
        'default' => 'report'
    ],
    'is_enabled' => [
        'type' => 'tinyint(1) unsigned',
        'default' => '1'
    ],  
    'email_title' => [
        'type' => 'varchar(200)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'email_message' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
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
