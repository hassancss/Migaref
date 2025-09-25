<?php
/*
 * Schema definition for 'migareference_activity_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_geo_countries'] = [
    'migareference_geo_countries_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'country' => [
      'type' => 'varchar(40)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'country_code' => [
      'type' => 'varchar(4)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
