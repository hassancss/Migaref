<?php
/*
 * Schema definition for 'migareference_report_status'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_report_status'] = [
    'migareference_report_status_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'status_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'status_icon' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'is_standard' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'is_optional' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'optional_type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_comment' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_acquired' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_pause_sending' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'standard_type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'order_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'is_declined' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'declined_grace_days' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'declined_to' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_reminder' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'reminder_grace_days' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'auto_fallabck_comment' => [
       'type' => 'varchar(300)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ]
,
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ]
];
