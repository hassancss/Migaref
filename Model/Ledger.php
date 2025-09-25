<?php

class Migareference_Model_Ledger extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Ledger';
    }

	/**
     * @param string $report_id
     * @return mixed
     */
    public function getReportData($report_id = "")
    {
        return $this->getTable()->getReportData($report_id);
    }
}
