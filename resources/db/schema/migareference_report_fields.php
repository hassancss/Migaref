<?php



/*



 * Schema definition for 'migareference_report_fields'



 */



$schemas = (!isset($schemas)) ? [] : $schemas;



$schemas['migareference_report_fields'] = [



    'migareference_report_fields_id' => [



        'type' => 'int(11) unsigned',



        'auto_increment' => true,



        'primary' => true,



    ],



    'app_id' => [



      'type' => 'int(11) unsigned',



      'default' => '1'



    ],



    'label' => [



      'type'    => 'text',



      'is_null' => false,



      'charset' => 'utf8',



      'collation' => 'utf8_unicode_ci'



    ],



    'field_type' => [



      'type' => 'int(11) unsigned',



      'default' => '1'



    ],



    'is_required' => [



      'type' => 'int(11) unsigned',



      'default' => '1'



    ],



    'is_visible' => [



      'type' => 'int(11) unsigned',



      'default' => '1'



    ],
    'is_visible_status_report' => [



      'type' => 'int(11) unsigned',



      'default' => '2'



    ],



    'field_type_count' => [



      'type' => 'int(11) unsigned',



      'default' => '1'



    ],



    'field_option' => [



      'type'    => 'varchar(300)',



      'is_null' => true,



      'charset' => 'utf8',



      'collation' => 'utf8_unicode_ci'



     ],



     'field_order' => [



       'type' => 'int(11) unsigned',



       'default' => '1'



     ],



     'type' => [



       'type' => 'int(11) unsigned',



       'default' => '1'



     ],



     'options_type' => [
       'type' => 'int(11) unsigned',
       'default' => '0'
     ],
     'default_option_value' => [
       'type'    => 'varchar(20)',
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



