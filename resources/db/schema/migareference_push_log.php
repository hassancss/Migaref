<?php
/*
 * Schema definition for 'migareference_push_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_push_log'] = [
    'migareference_push_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'push_message_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'deliver_method' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'notification_title' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'calling_method' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
     'notification_text' => [
        'type'    => 'text',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
       ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
