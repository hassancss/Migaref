<?php
/*
 * Schema definition for 'migareference_optin_form'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_optin_form'] = [
    'migareference_optin_form_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'button_width' => [
     'type' => 'int(11) unsigned',
     'default' => '100'
    ],
     'button_label' => [
     'type' => 'varchar(100)',
      'is_null' => false,
      'default'=>'SI! Voglio candidarmi',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'button_text_color' => [
      'type' => 'varchar(20)',
      'is_null' => false,
      'default'=>'#ffffff',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'button_background_color' => [
      'type' => 'varchar(20)',
      'is_null' => false,
      'default'=>'#000000',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
     'on_submit_button_label' => [
     'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'on_submit_button_text_color' => [
      'type' => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'on_submit_button_background_color' => [
      'type' => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],   
    'redirect_url' => [
      'type' => 'varchar(150)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'confirmation_message' => [
      'type' => 'varchar(150)',
      'is_null' => true,
      'default'=>'Complimenti, Candidatura Ricevuta!',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
