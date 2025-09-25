<?php
/*
 * Schema definition for 'migarefrence_property_addresses'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_blacklist_phone_numbers'] = [
    'migarefrence_blacklist_phone_numbers_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'phone_number' => [
      'type'    => 'varchar(20)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
