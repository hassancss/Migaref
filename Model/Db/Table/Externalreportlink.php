<?php
class Migareference_Model_Db_Table_Externalreportlink extends Core_Model_Db_Table
{
	protected $_name = "migareference_report_urls";
	protected $_primary = "migareference_report_urls_id";	
	public function savedata($data=[])
      {
        $this->_db->insert("migareference_report_urls", $data);
      }
	public function links($report_id = 0)
      {
        $query_option = "SELECT * FROM `migareference_report_urls` 
                         JOIN customer ON customer.customer_id=migareference_report_urls.user_id
                         WHERE `report_id` = $report_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
	public function matchToken($token = '')
      {
        $query_option = "SELECT *,migareference_report.user_id as report_by 
                         FROM `migareference_report_urls` 
                         JOIN customer ON customer.customer_id=migareference_report_urls.user_id
                         JOIN migareference_report ON migareference_report.migareference_report_id=migareference_report_urls.report_id
                         WHERE migareference_report_urls.`token` = '$token'";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
	public function agentLink($report_id,$user_id = 0)
      {
        $query_option = "SELECT * FROM `migareference_report_urls`                          
                         WHERE `report_id`=$report_id AND  `user_id` = $user_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
	public function urladmins($app_id=0,$report_id = 0)
      {
        $query_option = "SELECT *
                        FROM `migareference_app_admins` 
                        JOIN customer ON customer.customer_id=migareference_app_admins.user_id
                        LEFT JOIN migareference_report_urls ON migareference_report_urls.user_id=migareference_app_admins.user_id AND migareference_report_urls.report_id=$report_id
                        WHERE migareference_app_admins.app_id=$app_id AND migareference_report_urls.migareference_report_urls_id IS NULL ORDER BY customer.firstname";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
}
