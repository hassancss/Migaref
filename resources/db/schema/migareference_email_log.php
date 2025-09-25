<?php
/*
 * Schema definition for 'migareference_email_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_email_log'] = [
    'migareference_email_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'email_customer_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'email_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'calling_method' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'email_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'deliver_method' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
