<?php
/*
 * Schema definition for 'migareference_activity_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_activity_logs'] = [
    'migareference_activity_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'user_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],
     'user_type' => [
     'type' => 'int(11) unsigned',
     'default' => '1'
    ],
    'report_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],
    'log_source' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'log_type' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'log_detail' => [
      'type' => 'varchar(120)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
