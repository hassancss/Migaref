<?php
class Migareference_Model_Db_Table_Optinform extends Core_Model_Db_Table
{
	protected $_name = "migareference_optin_form";
	protected $_primary = "migareference_optin_form_id";	
	public function getOptinSettings($app_id=0)
	{
		$query_option = "SELECT * FROM `migareference_optin_form` WHERE `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
	public function getOptinUsers($app_id=0)
	{
		$query_option = "SELECT *, migareference_invoice_settings.created_at AS user_created_at, migareference_invoice_settings.user_id AS invoice_user_id 
		FROM migareference_invoice_settings 
		JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
		LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id
		WHERE migareference_invoice_settings.app_id=$app_id
		AND migareference_invoice_settings.referrer_source=3
		GROUP BY migareference_invoice_settings.migareference_invoice_settings_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
}
