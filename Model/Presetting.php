<?php
class Migareference_Model_Presetting extends Core_Model_Default {
    public function __construct($datas = []) {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Presetting';
    }
    public function updatePreReport($data = [])
    {
      return $this->getTable()->updatePreReport($data);
    }
}
