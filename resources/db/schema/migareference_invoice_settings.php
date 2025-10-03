<?php
/*
 * Schema definition for 'migareference_invoice_settings'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_invoice_settings'] = [
    'migareference_invoice_settings_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'sponsor_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'province_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'notification_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'blockchain_password' => [
      'type'    => 'varchar(120)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'first_password' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'invoice_name' => [
      'type'    => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'invoice_surname' => [
      'type'    => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'invoice_mobile' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'company' => [
      'type'    => 'varchar(80)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'leagal_address' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'tax_id' => [
      'type'    => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'ext_uid' => [
      'type'    => 'varchar(22)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'optin_form_v' => [
      'type'    => 'varchar(5)',
      'default'=>'v1',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'vat_id' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'extra_one_text' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'extra_two_text' => [
      'type'    => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
     'terms_accepted' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
      ],
     'special_terms_accepted' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
      ],
     'terms_artical_accepted' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
      ],
    'privacy_accepted' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'privacy_artical_accepted' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'leave_date' => [
        'type' => 'datetime',
        'is_null' => true
    ],
    'birth_date' => [
        'type' => 'date',
        'is_null' => true
    ],
    'leave_status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'referrer_source' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'address_province_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'address_country_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'sponsor_one_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'referrer_type' => [
      'type' => 'int(11) unsigned',
      'default' => '2' //1=Customer, 2=Not Customer
    ],
    'sponsor_two_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
    ],
    'address_street' => [
      'type'    => 'varchar(50)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'address_city' => [
      'type'    => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'address_zipcode' => [
      'type'    => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'ref_consent_source' => [
      'type'    => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'ref_consent_ip' => [
      'type'    => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'referrer_ip' => [
      'type'    => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'ref_consent_timestmp' => [
      'type' => 'datetime',
      'is_null' => true
    ],
    'token' => [
      'type'    => 'varchar(200)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ]
];
