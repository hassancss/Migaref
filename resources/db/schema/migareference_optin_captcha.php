<?php
/*
 * Schema definition for 'migareference_optin_captcha'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_optin_captcha'] = [
    'migareference_optin_captcha_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'image_name' => [
          'type' => 'varchar(20)',
          'is_null' => false,
          'charset' => 'utf8',
          'collation' => 'utf8_unicode_ci'
  	],
    'image_uid' => [
          'type' => 'varchar(20)',
          'is_null' => false,
          'charset' => 'utf8',
          'collation' => 'utf8_unicode_ci'
  	],
    'sum' => [
          'type' => 'int(11) unsigned',
          'default' => '0'          
  	],
    'created_at' => [
        'type' => 'timestamp',
        'default' => 'CURRENT_TIMESTAMP'
    ],
];
