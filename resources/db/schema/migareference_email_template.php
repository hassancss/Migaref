<?php
/*
 * Schema definition for 'migareference_email_template'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_email_template'] = [
    'migareference_email_template_id' => [
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
    'is_email_ref' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'is_email_agt' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ref_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'ref_email_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'agt_email_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_is_email_ref' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_is_email_agt' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reminder_ref_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_ref_email_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_email_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminder_agt_email_text' => [
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
