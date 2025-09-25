
<?php
/*
 * Schema definition for 'migareference_app_socialshares'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_app_socialshares'] = [
    'migareference_app_socialshares_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'user_id' => [
      'type' => 'int(11) unsigned',
      'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime'
    ]
];
