<?php
/*
 * Schema definition for 'migareference_report'
 * report_source 1: From APP end 2: From Labding Page 3 From backoffice or Owner end, 4 for API End
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_report'] = [
    'migareference_report_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'report_no' => [
      'type' => 'int(11) unsigned',
      'default' => '10000'
     ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'property_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'report_custom_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1',
      'is_null' => true
     ],
    'sales_expectations' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'commission_type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'reward_type' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'consent_bitly' => [
       'type'    => 'varchar(50)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'commission_fee' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'is_reminder_sent' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'address' => [
      'type'    => 'varchar(120)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'longitude' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'latitude' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'owner_name' => [
      'type'    => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'owner_surname' => [
      'type'    => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'note' => [
      'type' => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'owner_mobile' => [
      'type'    => 'varchar(20)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
     'owner_hot' => [
       'type' => 'int(11) unsigned',
       'default' => '3'
      ],
     'owner_job' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
      ],
     'owner_dob' => [
       'type' => 'date',
   		 'is_null' => true
      ],
    'last_modification' => [
      'type'    => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'last_modification_by' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
     'user_type' => [
     'type' => 'int(11) unsigned',
     'default' => '1'
    ],
    'last_modification_at' => [
      'type' => 'datetime',
      'is_null' => true
     ],
    'currunt_report_status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'certificate_status' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'status' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'report_source' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'confirm_report_privacy' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'authorized_call_back' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
	 ],
	//added by imran start
	'is_notarized' => [
		'type' => 'tinyint(4)',
		'default' => '0'
	],
	'eth_address' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'ipfs_address' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'eth_sha_hash' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'eth_address_url' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
	],
	'notarized_at' => [
		'type' => 'datetime',
		'is_null' => true
	],
	//added by imran end
  'extra_dynamic_fields' => [
    'type' => 'text',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'extra_dynamic_field_settings' => [
    'type' => 'text',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'consent_source' => [
    'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
  ],
  'consent_ip' => [
    'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
  ],
  'consent_timestmp' => [
    'type' => 'datetime',
    'is_null' => true
  ],
  'prospect_id' => [
    'type' => 'int(11) unsigned',
    'default' => '0'
  ],
  'created_by' => [
    'type' => 'int(11) unsigned',
    'default' => '0'
   ],
  'created_at' => [
    'type' => 'datetime'
  ],
  'updated_at' => [
    'type' => 'datetime',
    'is_null' => true
  ]
];
