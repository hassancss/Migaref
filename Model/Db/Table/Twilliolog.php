<?php
class Migareference_Model_Db_Table_Twilliolog extends Core_Model_Db_Table
{
	protected $_name = "migareference_twillio_log";
	protected $_primary = "migareference_twillio_log_id";	
	public function getwilliologs($app_id = 0)
      {
        $query_option = "SELECT migareference_twillio_log.*,customer.firstname,customer.lastname,customer.email 
                        FROM `migareference_twillio_log` 
                        JOIN customer ON customer.customer_id=migareference_twillio_log.user_id
                        WHERE migareference_twillio_log.app_id = $app_id AND migareference_twillio_log.created_At > now() - interval 1 week";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
}
