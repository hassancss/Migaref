<?php

class Migareference_Model_Smstemplate extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Smstemplate';
    }
    
    public function insertSmsTemplate($data =[])
    {
      return $this->getTable()->insertSmsTemplate($data);
    }	
}
