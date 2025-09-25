<?php
/*
 * Schema definition for 'migareference_app_agents'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_agent_provinces'] = [
    'migareference_agent_provinces_id' => [
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
    'country_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'province_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
