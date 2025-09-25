<?php
/*
 * Schema definition for 'migareference_sms_template'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_sms_template'] = [
    'migareference_sms_template_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'event_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'is_sms_ref' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'is_sms_agt' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],    
    'ref_sms_text' => [
      'type' => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'agt_sms_text' => [
      'type' => 'text',
      'is_null' => false,
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
