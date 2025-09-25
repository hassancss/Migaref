<?php
/*
 * Schema definition for 'migarefrence_phonebook'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_phonebook'] = [
    'migarefrence_phonebook_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
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
    'mobile' => [
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
    'reciprocity_notes' => [
      'type' => 'MEDIUMTEXT',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'job_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'profession_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'invoice_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'report_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'type' => [
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
    'engagement_level' => [
      'type' => 'int(11) unsigned',
      'default' => '5'
     ],
    'is_exclude' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'first_password' => [
      'type'    => 'varchar(30)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'last_contact_at' => [
      'type' => 'date',
      'is_null' => true
    ],    
    'is_matching_call_made' => [
        'type'=>'enum("pending","error","success","completed")',
        'default'=>'pending'   
     ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ]
];
