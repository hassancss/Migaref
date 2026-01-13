<?php
/*
 * Schema definition for 'migareference_affinity_edges'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_affinity_edges'] = [
    'id' => [
        'type' => 'int(11)',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'uniq_affinity_edge',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => true
        ]
    ],
    'run_id' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'uniq_affinity_edge',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => true
        ]
    ],
    'referrer_id_low' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'uniq_affinity_edge',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => true
        ]
    ],
    'referrer_id_high' => [
        'type' => 'int(11)',
        'index' => [
            'key_name' => 'uniq_affinity_edge',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => true
        ]
    ],
    'score' => [
        'type' => 'tinyint'
    ],
    'raw_response' => [
        'type' => 'text',
        'is_null' => true
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];
?>
