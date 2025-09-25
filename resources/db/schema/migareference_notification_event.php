<?php
/*
 * Schema definition for 'migareference_notification_event'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_notification_event'] = [
    'migareference_notification_event_id' => [
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
    'event_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'push_template_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'email_template_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'sms_template_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'email_delay_days' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'push_delay_days' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'email_delay_hours' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'push_delay_hours' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'is_active' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
