<?php
/*
 * Schema definition for 'migarefrence_property_addresses'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_property_addresses'] = [
    'migarefrence_property_addresses_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'address' => [
      'type'    => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'longitude' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'latitude' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
