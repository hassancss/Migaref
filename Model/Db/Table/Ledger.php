<?php
class Migareference_Model_Db_Table_Ledger extends Core_Model_Db_Table
{
	protected $_name = "migareference_ledger";
	protected $_primary = "ledger_id";

	/**
     * @param string $report_id
     * @return mixed
     */
    public function getReportData($report_id = "")
    {
		//Solana
        return $this->_db->fetchAll("SELECT
											migareference_report.migareference_report_id AS report_id,
											migareference_report.report_no,
											migareference_report.app_id,
											migareference_report.ipfs_address,
											migareference_report.eth_address_url,
											application.name AS app_name,
											migareference_invoice_settings.invoice_name AS referral_name,
											migareference_invoice_settings.invoice_surname AS referral_surname,
											migareference_report.owner_name,
											migareference_report.owner_surname,
											migareference_invoice_settings.blockchain_password AS encryption_key,
											migareference_report.created_at AS report_created_at,
											migareference_ledger_cron.xml_file_name,
											migareference_ledger_cron.notarization_platform,
											migareference_ledger_cron.eth_address
										FROM
											migareference_invoice_settings,
											migareference_report,
											customer,
											application,
											migareference_ledger,
											migareference_ledger_cron
										WHERE
											migareference_invoice_settings.user_id = migareference_report.user_id AND
											migareference_invoice_settings.user_id = customer.customer_id AND
											migareference_report.app_id = application.app_id AND
											migareference_report.migareference_report_id = migareference_ledger.report_id AND
											migareference_ledger_cron.ledger_cron_id = migareference_ledger.ledger_cron_id AND
											migareference_report.is_notarized = 1 AND
											migareference_invoice_settings.blockchain_password IS NOT NULL AND
											migareference_report.migareference_report_id = $report_id");
    }
}
