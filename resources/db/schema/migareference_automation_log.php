<?php
/*
 * Schema definition for 'migareference_automation_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_automation_log'] = [
    'migareference_automation_log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'trigger_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'report_status_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'report_reminder_auto_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
     'trigger_action_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],                         
     'phonebook_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],           
     'trigger_type_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ], 
     'postpone_days' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ], 
     'reminder_to' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ], 
    'current_reminder_status' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'default' => 'pending',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],          
    'reminder_content' => [
      'type'    => 'varchar(200)',
      'is_null' => true,
      'default' => '',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],          
    'type' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'default' => 'pending',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],          
    'detail' => [
      'type'    => 'varchar(500)',
      'is_null' => true,
      'default' => '',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],  
    'is_deleted' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],         
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
    'auto_event_date_time' => [
        'type' => 'datetime'
    ]
];
