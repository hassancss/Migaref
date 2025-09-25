<?php
/*
 * Schema definition for 'migareference_qualification'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['migareference_qualifications_referrers'] = [
    'migareference_qualifications_referrers_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
 
    'qualification_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    'referrer_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],

    'created_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
];
