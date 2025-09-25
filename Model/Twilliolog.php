<?php

class Migareference_Model_Twilliolog extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Twilliolog';
    }	
     public function getwilliologs($app_id = 0)
    {
        return $this->getTable()->getwilliologs($app_id);
    }
}
