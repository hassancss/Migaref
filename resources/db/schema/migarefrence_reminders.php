<?php
/*
 * Schema definition for 'migarefrence_reminders'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_reminders'] = [
    'migarefrence_reminders_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_day' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_month' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_year' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_hour' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'event_min' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'event_date_time' => [
       'type' => 'datetime',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'reminder_before_type' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
      ],
      'reminder_date_time' => [
        'type' => 'datetime',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
      ],
      'reminder_content' => [
        'type'    => 'text',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
      ],      
      'reminder_current_status' => [
      'type'    => 'varchar(60)',
      'default' => 'Pending',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
      'sending_status' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
       ],
      'is_deleted' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
       ],
      'postpone_days' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
       ],
      'created_at' => [
          'type' => 'datetime'
      ],
      'updated_at' => [
          'type' => 'datetime'
      ]
];
