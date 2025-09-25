<?php
/*
 * Schema definition for 'migareference_push_template'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_push_template'] = [
    'migareference_push_template_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'event_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'is_push_ref' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'is_push_agt' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'ref_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_cover_image' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agt_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_cover_image' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_is_push_ref' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'reminder_is_push_agt' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'reminder_ref_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_ref_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_ref_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_ref_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_ref_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_ref_cover_image' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_push_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_push_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_open_feature' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_agt_feature_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_agt_custom_url' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_cover_image' => [
      'type' => 'varchar(100)',
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
