<?php
/*
 * Schema definition for 'migareference_notification_form'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['migareference_qualificationnotifications'] = [
    'migareference_qualificationnotifications_id' => [
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
        'default' => '0'
    ],
    'operation' => [
        'type' => 'varchar(20)',
        'default' => 'create',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'webhook' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'email_subject' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'bcc' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'email_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'push_title' => [
        'type' => 'varchar(150)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'push_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'logo_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'ref_credits_api_open_feature' => [
        'type' => 'tinyint(1)',
        'default' => '0'
    ],
    'ref_credits_api_feature_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'ref_credits_api_custom_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
];
