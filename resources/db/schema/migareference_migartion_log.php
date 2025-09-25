<?php
/*
 * Schema definition for 'migareference_migartion_log'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_migartion_log'] = [
    'migareference_migartion_log_id' => [
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
    'report_log' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'invoice_settings_log' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
      'agents_log' => [
         'type'    => 'text',
         'is_null' => true,
         'charset' => 'utf8',
         'collation' => 'utf8_unicode_ci'
        ],
        'admins_log' => [
           'type'    => 'text',
           'is_null' => true,
           'charset' => 'utf8',
           'collation' => 'utf8_unicode_ci'
          ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
