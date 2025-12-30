<?php
/*
 * Schema definition for 'migareference_affinity_runs'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_affinity_runs'] = [
    'id' => [
        'type' => 'int(11)',
        'auto_increment' => true,
        'primary' => true
    ],
    'app_id' => [
        'type' => 'int(11)'
    ],
    'status' => [
        'type' => 'varchar(20)',
        'default' => 'queued'
    ],
    'cursor_i' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'cursor_j' => [
        'type' => 'int(11)',
        'default' => '1'
    ],
    'total_referrers' => [
        'type' => 'int(11)',
        'default' => '0'
    ],
    'total_pairs_estimate' => [
        'type' => 'bigint',
        'default' => '0'
    ],
    'processed_pairs' => [
        'type' => 'bigint',
        'default' => '0'
    ],
    'model' => [
        'type' => 'varchar(64)',
        'is_null' => true
    ],
    'temperature' => [
        'type' => 'decimal(3,2)',
        'is_null' => true
    ],
    'prompt_hash' => [
        'type' => 'varchar(64)',
        'is_null' => true
    ],
    'last_error' => [
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
