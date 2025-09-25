<?php
/*
 * Schema definition for 'migarefrence_report_reminder_auto'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_report_reminder_auto'] = [
    'migarefrence_report_reminder_auto_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'auto_rem_title' => [
      'type' => 'varchar(250)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'auto_rem_trigger' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],    
    'auto_rem_action' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],        
    'auto_rem_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],            
    'auto_rem_status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],            
    'auto_rem_min_rating' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'auto_rem_max_rating' => [
      'type' => 'int(11) unsigned',
      'default' => '5'
    ],    
    'auto_rem_fix_rating' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],    
    'auto_rem_engagement' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'auto_rem_reports' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'auto_rem_days' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'auto_rem_percent' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'auto_rem_add_recap_email' => [
      'type' => 'tinyint(1)',
      'default' => '2'
    ],
    'auto_rem_recap_email_template' => [
      'type'    => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'auto_rem_trigger_hour' => [
      'type' => 'int(11) unsigned',
      'default' => '7'
    ],
    'auto_rem_report_trigger_status' => [
      'type' => 'varchar(50)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'auto_rem_recap_email_header' => [
      'type' => 'varchar(300)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'auto_rem_webhook_url' => [
      'type' => 'varchar(500)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
