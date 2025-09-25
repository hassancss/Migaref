<?php
# Migareference module un-installer.
$name = 'Migareference';
# Clean-up library icons
Siberian_Feature::removeIcons($name);
Siberian_Feature::removeIcons($name . '-flat');
# Clean-up Layouts
$layoutData = [1];
$slug = 'Migareference';
Siberian_Feature::removeLayouts($option->getId(), $slug, $layoutData);
# Clean-up Option(s)/Feature(s)
$code = 'Migareference';
Siberian_Feature::uninstallFeature($code);
# Clean-up DB be really carefull with this.
$tables = [
  'migareference_activity_logs',
  'migareference_app_admins',
  'migareference_how_to',
  'migareference_email_log',
  'migareference_email_template',
  'migareference_notification_event',
  'migareference_invoice_settings',
  'migareference_pre_report_settings',
  'migareference_push_log',
  'migareference_push_template',
  'migareference_report_status',
  'migareference_report',
  'migareference_setting',
  'migareference_user_earnings',
  'migareference_status_comment',
  'migareference_app_agents',
  'migareference_translations'
];
Siberian_Feature::dropTables($tables);
# Clean-up module
Siberian_Feature::uninstallModule($name);
