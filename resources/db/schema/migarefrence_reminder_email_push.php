<?php
/*
 * Schema definition for 'migareference_push_template'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_reminder_email_push'] = [
    'migarefrence_reminder_email_push_id' => [
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
    'reminder_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_email_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
