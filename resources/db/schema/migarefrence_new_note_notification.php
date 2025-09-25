<?php
/*
 * Schema definition for 'migarefrence_new_note_notification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_new_note_notification'] = [
    'migarefrence_new_note_notification_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'new_note_target_notification' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'new_note_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'new_note_push_text' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'new_note_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'new_note_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'new_note_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'new_note_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'new_note_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'new_note_email_text' => [
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
