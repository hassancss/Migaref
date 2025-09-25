<?php

class Migareference_Model_Optinform extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Optinform';
    }	
     public function getOptinSettings($app_id = 0)
    {
        return $this->getTable()->getOptinSettings($app_id);
    }
     public function getOptinUsers($app_id = 0)
    {
        return $this->getTable()->getOptinUsers($app_id);
    }
}
