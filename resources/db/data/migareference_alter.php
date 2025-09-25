<?php

try {
    $this->query("ALTER TABLE `migareference_ledger_cron` ALTER `notarization_platform` SET DEFAULT 'Solana'");
    $this->query("ALTER TABLE migareference_webhook_logs  MODIFY COLUMN webhook_type ENUM('report', 'referrer', 'reminder', 'centerlized_report')");
    (new Migareference_Model_Db_Table_Migareference)->copySponsor();
    (new Migareference_Model_Db_Table_Migareference)->tempUpdates();//this method will be used for any particular one time updates
} catch (\Exception $e) {
    // Silent!
}