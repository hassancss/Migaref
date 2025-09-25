<?php
/*
 * Schema definition for 'migareference_optin_firewall'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_optin_firewall'] = [
    'migareference_optin_firewall_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'ip_address' => [
          'type' => 'varchar(20)',
          'is_null' => false,
          'charset' => 'utf8',
          'collation' => 'utf8_unicode_ci'
  	],
    'created_at' => [
        'type' => 'timestamp',
        'default' => 'CURRENT_TIMESTAMP'
    ],
];
