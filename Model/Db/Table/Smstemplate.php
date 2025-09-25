<?php
class Migareference_Model_Db_Table_Smstemplate extends Core_Model_Db_Table
{
	protected $_name = "migareference_sms_template";
	protected $_primary = "migareference_sms_template_id";	
	public function insertSmsTemplate($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_sms_template", $data);
        return $this->_db->lastInsertId();
      }
}
