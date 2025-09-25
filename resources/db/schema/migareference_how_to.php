<?php
/*
 * Schema definition for 'migareference_how_to'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_how_to'] = [
    'migareference_how_to_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'frame_height' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'how_to_source_unit' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'video_link' => [
      'type'    => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
     'how_to_text' => [
       'type' => 'MEDIUMTEXT',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'how_to_video_source' => [
       'type' => 'MEDIUMTEXT',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'contact_us_link' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'site_link' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'contact_us_email' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'contact_us_phone' => [
      'type' => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'director_name' => [
      'type'    => 'varchar(40)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
     'director_email' => [
      'type'    => 'varchar(40)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
     'director_phone' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
     'director_calendar_url' => [
      'type'    => 'varchar(200)',
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
