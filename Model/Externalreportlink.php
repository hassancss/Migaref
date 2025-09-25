
<?php

class Migareference_Model_Externalreportlink extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Externalreportlink';
    }	
     public function agentLink($report_id,$user_id=0)
    {
        return $this->getTable()->agentLink($report_id,$user_id);
    }
     public function links($report_id = 0)
    {
        return $this->getTable()->links($report_id);
    }
     public function matchToken($token = '')
    {
        return $this->getTable()->matchToken($token);
    }
     public function urladmins($app_id=0,$report_id = 0)
    {
        return $this->getTable()->urladmins($app_id,$report_id);
    }
     public function savedata($data=[])
    {
        return $this->getTable()->savedata($data);
    }
}
