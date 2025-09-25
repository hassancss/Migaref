<?php

class Migareference_Model_Reportnotification extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Reportnotification';
    }
    
    public function reportNotiTagsList()
    {
      return $this->getTable()->reportNotiTagsList();
    }	
    public function sendNotification($app_id=0,$report_id=0,$staus_id=0,$modified_by=0,$call_source='',$report_operation='')
    {
      return $this->getTable()->sendNotification($app_id,$report_id,$staus_id,$modified_by,$call_source,$report_operation);
    }	
}
