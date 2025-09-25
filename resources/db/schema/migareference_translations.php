<?php
/*
 * Schema definition for 'migareference_translations'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_translations'] = [
    'migareference_translations_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'text_field' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
