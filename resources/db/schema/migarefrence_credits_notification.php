<?php
/*
 * Schema definition for 'migarefrence_credits_notification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_credits_notification'] = [
    'migarefrence_credits_notification_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_credits_api_enable_notification' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'ref_credits_api_notification_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_credits_api_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_push_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_credits_api_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_credits_api_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_email_text' => [
      'type' => 'text',
      'is_null' => true,
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
