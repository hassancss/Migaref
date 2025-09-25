<?php
/*
 * Schema definition for 'migarefrence_ledger'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_ledger'] = [
    'migarefrence_ledger_id' => [
         'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'self_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false
     ],
    'redeem_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false
     ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'user_type' => [
     'type' => 'int(11) unsigned',
     'default' => '1'
    ],
    'amount' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'entry_type' => [
      'type' => 'varchar(2)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'transection_type' => [
      'type' => 'varchar(2)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'transection_source' => [ //Admin_Custom, Referrer_Report, Prize_Redeem, API
      'type' => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'trsansection_description' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'trsansection_by' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'prize_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
