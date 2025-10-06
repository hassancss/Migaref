<?php
/*
 * Schema definition for 'migareference_app_agents'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_app_agents'] = [
    'migareference_app_agents_id' => [
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
    'admin_user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'full_phonebook' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'paid_status_access' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'agent_type' => [ //1: Customer Agent, 2: Partner Agent
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
