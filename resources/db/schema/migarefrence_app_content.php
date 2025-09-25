<?php
/*
 * Schema definition for 'migarefrence_app_content'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_app_content'] = [
    'migarefrence_app_content_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'top_home_header_bg' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_save_btn_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_save_btn_text' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_save_card_title' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_cover_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_invite_btn_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'add_property_invite_btn_text' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'add_property_invite_card_title' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],    
    'top_home_header_text' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'how_it_works_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'how_it_works_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'how_it_works_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'how_it_works_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_invite_btn_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_save_btn_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'add_property_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_form_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_form_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'landing_page_header_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reportconfirm_page_title' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reportconfirm_page_message' => [
       'type'    => 'longtext',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'crmreport_page_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_page_form_title' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_page_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_nav_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_page_form_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_page_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreport_page_header_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreportconfirm_page_title' => [
      'type' => 'varchar(100)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'crmreportconfirm_page_message' => [
       'type'    => 'longtext',
       'is_null' => true,
       'charset' => 'utf8',
       'collation' => 'utf8_unicode_ci'
      ],
    'referre_report_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'referre_report_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'referre_report_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'referre_report_file' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'report_status_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'report_status_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'referre_report_file' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'report_status_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'report_status_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prizes_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prizes_box_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prizes_box_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'prizes_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminders_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminders_box_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminders_box_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'reminders_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'statistics_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'statistics_box_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'statistics_box_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'statistics_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebooks_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebooks_start_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'default'=>'#f8d578',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebooks_box_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebooks_box_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebooks_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'settings_box_label' => [
      'type' => 'varchar(100)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'settings_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'settings_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'settings_file' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'top_prize_header_bg' => [
      'type' => 'varchar(25)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'top_prize_header_text' => [
      'type' => 'varchar(50)',
      'is_null' => false,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'page_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_add_btn_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_add_btn_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_reset_btn_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_reset_btn_text_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_reference_btn_bg_color' => [
      'type' => 'varchar(25)',
      'is_null' => true,
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'
    ],
    'phonebook_reference_btn_text_color' => [
      'type' => 'varchar(25)',
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
