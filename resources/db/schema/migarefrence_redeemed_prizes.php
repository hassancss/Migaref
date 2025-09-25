<?php
/*
 * Schema definition for 'migarefrence_redeemed_prizes'
 redeemed_status 0 pending 1 delivred 2 refused
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_redeemed_prizes'] = [
    'migarefrence_ledger_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'prize_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'redeemed_by' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'redeemed_status' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'redeemed_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
      'type' => 'datetime',
      'is_null' => true
    ]
];
