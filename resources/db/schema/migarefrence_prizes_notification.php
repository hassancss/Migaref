<?php
/*
 * Schema definition for 'migarefrence_prizes_notification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_prizes_notification'] = [
    'migarefrence_prizes_notification_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'prz_notification_to_user' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_prz_notification_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_prz_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_push_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_prz_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_prz_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_email_bcc' => [
      'type' => 'varchar(150)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_prz_email_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_push_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_prz_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_prz_notification_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_prz_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_prz_email_text' => [
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
