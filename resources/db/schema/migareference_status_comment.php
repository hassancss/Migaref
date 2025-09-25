<?php
/*
 * Schema definition for 'migareference_status_comment'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_status_comment'] = [
    'migareference_status_comment_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'status_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'comment' => [
      'type' => 'MEDIUMTEXT',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
   ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
