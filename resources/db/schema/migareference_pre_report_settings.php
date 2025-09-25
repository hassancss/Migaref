<?php
/*
 * Schema definition for 'migareference_pre_report_settings'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_pre_report_settings'] = [
    'migareference_pre_report_settings_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'reward_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'commission_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'commission_lable' => [
      'type'    => 'varchar(20)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'default' => '€'
     ],
    'price_range_text_from' => [
      'type'    => 'varchar(50)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'price_range_text_to' => [
      'type'    => 'varchar(50)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'fix_commission_amount' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'percent_commission_amount' => [
      'type' => 'float',
      'default' => '0'
     ],
    'fix_commission_credits' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'percent_commission_credits' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'payable_limit' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'credit_expire' => [
      'type' => 'int(11) unsigned',
      'default' => '365'
     ],
    'read_only' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'check_app_version' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'check_ios_version' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'check_android_version' => [
      'type'    => 'varchar(12)',
      'default' => '2'
     ],
   'ios_store_version' => [
      'type'    => 'varchar(12)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
   'android_store_version' => [
      'type'    => 'text',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'is_unique_mobile' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'enable_unique_address' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'recap_email_enable' => [
      'type' => 'tinyint(1) unsigned',
      'default' => '1'
     ],
    'recap_email_lang' => [
      'type' => 'tinyint(1) unsigned',
      'default' => '1'
     ],
    'enable_webhooks' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'enable_qlf' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
     'qlf_grace_days' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'enable_privacy_global_settings' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'enable_main_address' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'mandatory_main_address' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'enable_sub_address' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
     'enable_credits_api' => [
      'type' => 'tinyint(1) unsigned',
      'default' => '2'
    ],  
    'mandatory_sub_address' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'block_address_report' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'agent_can_see' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'agent_can_manage' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'agent_can_manage_reminder_automation' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'working_days' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'refrence_level' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'address_grace_days' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'grace_days' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'mobile_grace_period_action' => [
      'type' => 'tinyint(4) unsigned',
      'default' => '2'
     ],
    'mobile_grace_period_database' => [
      'type' => 'tinyint(4) unsigned',
      'default' => '1'
     ],
    'notification_type' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],
    'grace_period_warning_message' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'recap_email_bcc' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'grace_period_extrnal_db_url' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'internal_report_note' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'privacy_global_settings' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'external_report_note' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'report_alert_text' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'declined_comment' => [
       'type'    => 'text',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'privacy' => [
       'type'    => 'text',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'terms' => [
       'type'    => 'text',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'term_label_text' => [
       'type'    => 'varchar(200)',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'recap_email_subject' => [
       'type'    => 'varchar(150)',
       'is_null' => false,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'special_terms' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
     ],
    'enable_company' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'mandatory_company' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'enable_legal_address' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
     ],
    'mandatory_legal_address' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'mandatory_sector' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'mandatory_profession' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'extra_one' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'mandatory_extra_one' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'extra_one_label' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'extra_two' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'mandatory_extra_two' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'is_visible_invite_prospectus' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'enable_welcome_email' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'enable_mandatory_agent_selection' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'enable_multi_agent_selection' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'enable_optin_welcome_email' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'enable_only_agent_provinces' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'is_visible_submit_report' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'enable_report_referrer_behalf' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'enable_report_type' => [
       'type' => 'int(11) unsigned',
       'default' => '2'
     ],
    'is_visible_platform_report' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
     ],
    'price_range_from' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
     ],
    'redeem_once' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
     ],
    'price_range_to' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
     ],
    'extra_two_label' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'report_api_webhook_url' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'new_ref_webhook_url' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'invite_message' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'bitly_key' => [
       'type'    => 'varchar(80)',
       'is_null' => true,
       'charset' => 'utf8',
       'default' => '',
       'collation' => 'utf8_unicode_ci'
     ],
    'bitly_login' => [
       'type'    => 'varchar(80)',
       'is_null' => true,
       'charset' => 'utf8',
       'default' => '',
       'collation' => 'utf8_unicode_ci'
     ],
    'confirm_report_privacy_label' => [
       'type'    => 'varchar(150)',
       'is_null' => false,
       'charset' => 'utf8',
       'default' => 'Please read and accept the accept the privacy statement.',
       'collation' => 'utf8_unicode_ci'
     ],
    'confirm_report_privacy_link' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
	 'custom_landing_report_text' => [ //added by imran
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'authorized_call_back_label' => [
       'type'    => 'varchar(150)',
       'is_null' => false,
       'charset' => 'utf8',
       'default' => 'I authorized be call back for commercial purpose.',
       'collation' => 'utf8_unicode_ci'
     ],
	'authorized_call_back_visibility' => [ //added by imran
		'type' => 'tinyint(1) unsigned',
		'default' => '1'
	],  
    'owner_hot_label' => [
       'type'    => 'varchar(150)',
       'is_null' => false,
       'charset' => 'utf8',
       'default' => 'How hot is the contact?',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_pg_header_img' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_pg_header_txt' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'default' => 'Add Report',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_page_bg_color' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_page_form_bg_color' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_page_header_file' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'landing_page_form_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],    
     'consent_info_active' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
     'invite_consent_warning_active' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'invite_consent_warning_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_info_popup_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_col_page_header' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_info_popup_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'invite_consent_warning_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_invit_msg_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_thank_page_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_thank_page_header' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_thank_page_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_col_page_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'consent_col_page_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'sponsor_type' => [
       'type' => 'int(11) unsigned',
       'default' => '1'
      ],
    'consent_collection' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'enable_gdpr_special_settings' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'enable_report_api_webhooks' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'enable_new_ref_webhooks' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'enable_new_ref_webhooks_create' => [
      'type' => 'tinyint(1) unsigned',
      'default' => '1'
    ],
    'enable_new_ref_webhooks_update' => [
      'type' => 'tinyint(1) unsigned',
      'default' => '0'
    ],
    'enable_twillio_notification' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'automation_cron_time' => [
      'type' => 'int(11) unsigned',
      'default' => '5'
    ],
    'enable_automation' => [
      'type' => 'int(11) unsigned',
      'default' => '2'
    ],
    'twillio_sid' => [
      'type'    => 'varchar(50)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'twillio_token' => [
      'type'    => 'varchar(50)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'twillio_sim_id' => [
       'type'    => 'varchar(30)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'default_country_code' => [
       'type'    => 'varchar(30)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
    ],
    'report_api_token' => [
       'type'    => 'varchar(40)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'credits_api_token' => [
       'type'    => 'varchar(40)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'referrer_wellcome_email_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'referrer_wellcome_email_body' => [
       'type'    => 'text',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'referrer_optin_wellcome_email_title' => [
       'type'    => 'varchar(150)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'referrer_optin_wellcome_email_reply_to' => [
       'type'    => 'varchar(160)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'referrer_optin_wellcome_email_bcc_to' => [
       'type'    => 'varchar(60)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'agent_type_label_one' => [
       'type'    => 'varchar(60)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
     'agent_type_label_two' => [
       'type'    => 'varchar(60)',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
     ],
    'referrer_optin_wellcome_email_body' => [
       'type'    => 'text',
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
