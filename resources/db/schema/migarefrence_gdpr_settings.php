<?php
/*
 * Schema definition for 'migarefrence_prizes'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_gdpr_settings'] = [
    'migarefrence_gdpr_settings_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'value_id' => [
      'type' => 'int(11) unsigned',
      'default' => '0'
     ],
    'consent_info_active' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],    
    'invite_consent_warning_active' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
     ],    
    'consent_info_popup_title' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_warning_title' => [
      'type' => 'varchar(80)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_doyou_text' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reportconfirm_page_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reportconfirm_page_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_proceed_text' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_proceed_background' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_page_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_page_header' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_proceed_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_agree_btn_background' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_agree_btn_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_page_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_col_page_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_doyou_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_discard_text' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_discard_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_title' => [
      'type' => 'varchar(40)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_form_title' => [
      'type' => 'varchar(40)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_header_file' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_warning_active' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'report_page_popup_file' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_report_popup_file' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_bg_color' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_text_color' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_form_bg_color' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reportconfirm_page_title' => [
      'type' => 'varchar(40)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_submit_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_submit_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_submit_text' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_discard_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_under_text' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_under_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'invite_consent_btn_under_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_popup_doyou_background' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_thank_page_bg' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_thank_page_font' => [
      'type' => 'varchar(30)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_thank_page_title' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_thank_page_header' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'consent_info_popup_body' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'consent_col_page_body' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'consent_col_agree_btn_text' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'consent_invit_msg_body' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'consent_thank_page_body' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'invite_consent_warning_body' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'invite_message' => [
      'type'    => 'text',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'reportconfirm_page_message' => [
      'type'    => 'text',
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
