<?php
/*
 * Schema definition for 'migarefrence_report_reminder_types'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_report_reminder_types'] = [
    'migarefrence_report_reminder_types_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'rep_rem_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'rep_rem_target_notification' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'rep_rem_badge' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],        
    'rep_rem_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],        
    'rep_rem_icon_file' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_before_email_hours' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'rep_rem_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'rep_rem_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'rep_rem_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_custom_file' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_before_push_hours' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'rep_rem_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'rep_rem_email_text' => [
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
