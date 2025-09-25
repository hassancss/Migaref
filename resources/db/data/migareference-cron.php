<?php
// Get current module
$module = new Installer_Model_Installer_Module();
$module->prepare('Migareference');
// Install the cron job
Siberian_Feature::installCronjob(
    __('Migareference cron job.'),
    'Migareference_Model_Db_Table_Migareference::cronNotification', // command
    -1, // minute
    -1, // hour
    -1, // month_day
    -1, // month
    -1, // week_day
    true, // is_active
    100, // priority
    false, // standalone (only for specific needs)
    $module->getId() // current module Id
);
Siberian_Feature::installCronjob(
    __('Migareference cron job.'),
    'Migareference_Model_Db_Table_Migareference::refferalUserElimination', // command
    -1, // minute
     23, // hour
    -1, // month_day
    -1, // month
    -1, // week_day
    true, // is_active
    100, // priority
    false, // standalone (only for specific needs)
    $module->getId() // current module Id
);
Siberian_Feature::installCronjob(
    __('Migareference Reminders Fallback cron job.'),
    'Migareference_Model_Db_Table_Migareference::reminderFallback', // command
    -1, // minute
     23, // hour
    -1, // month_day
    -1, // month
    -1, // week_day
    true, // is_active
    100, // priority
    false, // standalone (only for specific needs)
    $module->getId() // current module Id
);
Siberian_Feature::installCronjob(
    __('Migareference notarization cron job.'),
    'Migareference_Model_Db_Table_Cron::xmlCron', // command
    30, // minute
    23, // hour
    -1, // month_day
    -1, // month
    -1, // week_day
    true, // is_active
    100, // priority
    false, // standalone (only for specific needs)
    $module->getId() // current module Id
);
Siberian_Feature::installCronjob(
    __('Migareference Automation cron job.'),
    'Migareference_Model_Db_Table_Migareference::automationTriggerscron', // command
    30, // minute
    7, // hour
    -1, // month_day
    -1, // month
    -1, // week_day
    true, // is_active
    100, // priority
    false, // standalone (only for specific needs)
    $module->getId() // current module Id
);
