<?php
/*
 * Schema definition for 'migarefrence_welcome_notification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_welcome_notification'] = [
    'migarefrence_welcome_notification_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'welcome_push_enable' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'welcome_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'welcome_push_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'welcome_push_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'welcome_push_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'welcome_push_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'welcome_push_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => true,
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
