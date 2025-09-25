<?php
/*
 * Schema definition for 'migareference_user_earnings'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_user_earnings'] = [
    'migareference_user_earning_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'refferral_user_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'sold_user_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
     'user_type' => [
     'type' => 'int(11) unsigned',
     'default' => '1'
    ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'earn_amount' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
     'platform' => [
       'type' => 'varchar(15)',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
