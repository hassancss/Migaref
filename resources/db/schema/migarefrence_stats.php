<?php
/*
 * Schema definition for 'migarefrence_stats'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_stats'] = [
    'migarefrence_stats_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'total_apps' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'total_gcm' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'total_apns' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'total_tokens' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'total_users' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'refrel_users' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'admins' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'net_refreal' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'total_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'active_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'declined_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'paid_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'payable_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'per_report_dump' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
