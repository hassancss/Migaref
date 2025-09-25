<?php
/*
 * Schema definition for 'migareference_app_admins'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_app_admins'] = [
    'migareference_app_admins_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'api_admin' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'credits_api_admin' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
