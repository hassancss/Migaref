<?php
/*
 * Schema definition for 'migareference_openai_config'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_openai_config'] = [
    'migareference_openai_config_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'gpt_api' => [
      'type' => 'enum("openai", "perplexity")',
      'default' => 'openai',
      'is_null' => false
    ],

    'perplexity_apikey' => [
      'type' => 'varchar(255)',
      'is_null' => true
    ],
    'openai_apikey' => [
      'type' => 'varchar(255)',
      'is_null' => true
    ],
    'matching_preffered_lan' => [
      'type' => 'varchar(50)',
      'is_null' => false
    ],
    'call_script_ai_model' => [
      'type' => 'varchar(50)',
      'default' => 'gpt-4o',
      'is_null' => false,
    ],
    'matching_ai_model' => [
      'type' => 'varchar(50)',
      'default' => 'gpt-4o-mini',
      'is_null' => false,
    ],
    'is_api_enabled' => [
      'type' => 'tinyint(1)',
      'default' => '2',
      'comment' => '1 for enabled, 2 for disabled'
    ],
    'is_full_referrer_list_enabled' => [
      'type' => 'tinyint(1)',
      'default' => '2',
      'comment' => '1 for enabled, 2 for disabled'
    ],
    'is_matching_api_enabled' => [
      'type' => 'tinyint(1)',
      'default' => '2',
      'comment' => '1 for enabled, 2 for disabled'
    ],
    'openai_temperature' => [
      'type' => 'float',
      'default' => '1'
    ],
    'openai_token' => [
      'type' => 'int(11) unsigned',
      'default' => '350',
      'comment' => 'Max tokens for the script generation'
    ],
    'max_matches' => [
      'type' => 'int(11) unsigned',
      'default' => '3'      
    ],
    'grace_period_matches' => [
      'type' => 'int(11) unsigned',
      'default' => '30'      
    ],
    'system_prompt' => [
      'type' => 'text',
      'is_null' => false
    ],
    'user_prompt' => [
      'type' => 'text',
      'is_null' => false
    ],
    'created_at' => [
        'type' => 'datetime',
        'default' => 'CURRENT_TIMESTAMP'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'default' => 'CURRENT_TIMESTAMP',
        'on_update' => 'CURRENT_TIMESTAMP'
    ]
];
?>
