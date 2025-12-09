<?php
/*
 * Schema definition for 'migarefrence_app_content_two'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migarefrence_app_content_two'] = [
  'migarefrence_app_content_two_id' => [
    'type' => 'int(11) unsigned',
    'auto_increment' => true,
    'primary' => true,
  ],
  'app_id' => [
    'type' => 'int(11) unsigned',
    'default' => '1'
  ],
  'report_type_pop_title' => [
    'type' => 'varchar(90)',
    'is_null' => true,
    'default'=>'Scegli il tuo Prospect',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_text' => [
    'type' => 'longtext',
    'is_null' => true,
    'default'=>'La nostra azienda cerca opportunità su due fronti. Cerchiamo
potenziali CLIENTI in target per i nostri prodotti o servizi, oppure cerchiamo
COLLABORATORI (agenti, partners). Seleziona il tipo di persona che ci vuoi
presentare',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_one_text' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'default'=>'POTENZIALE CLIENT',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_one_color' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_one_bg_color' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_one_icon_pos' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_two_text' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'default'=>'POTENZIALE COLLABORATORE',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_two_color' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_two_bg_color' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'add_ref_btn_bg_color' => [
    'type' => 'varchar(25)',
    'default' => '#f8d578',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'add_ref_btn_text_color' => [
    'type' => 'varchar(25)',
    'default' => 'black',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_two_icon_pos' => [
    'type' => 'varchar(25)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_two_icon' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_btn_one_icon' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'report_type_pop_cover' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],


//for qualification

  'qlf_box_label' => [
    'type' => 'varchar(100)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_box_bg_color' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_box_text_color' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_cover_file' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_cover' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_title' => [
    'type' => 'varchar(150)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_subtitle' => [
    'type' => 'varchar(200)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_one_cover' => [
    'type' => 'varchar(40)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_one_title' => [
    'type' => 'varchar(150)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_one_subtitle' => [
    'type' => 'varchar(200)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_two_cover' => [
    'type' => 'varchar(40)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_two_title' => [
    'type' => 'varchar(150)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_one_btn_two_subtitle' => [
    'type' => 'varchar(200)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_two_cover' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_two_title' => [
    'type' => 'varchar(150)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'qlf_level_two_subtitle' => [
    'type' => 'varchar(200)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],

  //Enroll URL
   'enroll_url_box_label' => [
    'type' => 'varchar(100)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'enroll_url_box_bg_color' => [
    'type' => 'varchar(30)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'enroll_url_box_text_color' => [
    'type' => 'varchar(20)',
    'is_null' => true,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
  ],
  'enroll_url_cover_file' => [
    'type' => 'varchar(20)',
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
