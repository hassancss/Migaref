<?php
/*
 * Schema definition for 'migareference_reminder_reset_logs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_reminder_reset_logs'] = [
    'migareference_reminder_reset_logs_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'admin_id' => [
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],
     'total_count' => [ //number reminders that was reset
     'type' => 'int(11) unsigned',
     'default' => '0'
    ],  
    'created_at' => [
        'type' => 'datetime'
    ]
];
