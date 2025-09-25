<?php
/*
 * Schema definition for 'migarefrence_prospect'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_prospect'] = [
    'migarefrence_prospect_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
        'start'=>100000
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'name' => [
      'type'    => 'varchar(60)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'surname' => [
      'type'    => 'varchar(60)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'email' => [
      'type'    => 'varchar(60)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'mobile' => [ //will be remain unique
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'note' => [
      'type' => 'MEDIUMTEXT',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'job_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],            
    'rating' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'is_blacklist' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],    
    'password' => [
      'type'    => 'varchar(30)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'gdpr_consent_source' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'gdpr_consent_ip' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'gdpr_consent_timestamp' => [
        'is_null' => true,
        'type' => 'datetime'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
