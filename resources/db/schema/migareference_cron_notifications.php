<?php
/*
 * Schema definition for 'migareference_cron_notifications'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_cron_notifications'] = [
    'migareference_cron_notifications_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'notification_event_id' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
     'trigger_start_time' => [
         'type' => 'datetime'
     ],
    'push_delay_hours' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'email_delay_hours' => [
      'type' => 'int(11) unsigned',
      'is_null' => false,
     ],
    'is_push_deliverd' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_email_deliverd' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],

    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
