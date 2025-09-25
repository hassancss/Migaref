<?php
/*
 * Schema definition for 'migarefrence_prizes'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_prizes'] = [
    'migarefrence_prizes_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'credits_number' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'redeemed_once' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'prize_name' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_description' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_icon' => [
      'type' => 'varchar(120)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_link1' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_link1_btn_text' => [
      'type' => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_link1_enable' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'prize_link2' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_link2_btn_text' => [
      'type' => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prize_link2_enable' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'prize_start_date' => [
        'type' => 'datetime'
    ],
    'prize_expire_date' => [
        'type' => 'datetime'
    ],
    'prize_status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
