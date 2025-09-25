<?php
/*
 * Schema definition for 'migareference_app_agents'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_referrer_agents'] = [
    'migareference_referrer_agents_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'referrer_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agent_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ]
];
