<?php
/*
 * Schema definition for 'migareference_professions'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_professions'] = [
    'migareference_professions_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'profession_title' => [
          'type' => 'varchar(200)',
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
