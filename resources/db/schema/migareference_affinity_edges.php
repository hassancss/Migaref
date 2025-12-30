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
        'type' => 'int(11)'
    ],
    'run_id' => [
        'type' => 'int(11)'
    ],
    'referrer_id_low' => [
        'type' => 'int(11)'
    ],
    'referrer_id_high' => [
        'type' => 'int(11)'
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
    ],
    'indexes' => [
        'uniq_migareference_affinity_edges' => [
            'columns' => [
                'app_id',
                'run_id',
                'referrer_id_low',
                'referrer_id_high'
            ],
            'unique' => true
        ],
        'idx_migareference_affinity_edges_app_run' => [
            'columns' => [
                'app_id',
                'run_id'
            ]
        ]
    ]
];
?>
