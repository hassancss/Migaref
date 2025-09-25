<?php
class Migareference_Model_ProcessedReferrer extends Core_Model_Default {

	public function __construct($datas = []) {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_ProcessedReferrer';
	}

	public function countIstTimeProcessedReferrers($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		return $this->getTable()->countIstTimeProcessedReferrers($app_id, $agent_id, $from_date, $to_date);
	}

	public function countIstTimeProcessedReferrersForCharts($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '', $label_format = '', $group_by_foramt = '') {
		return $this->getTable()->countIstTimeProcessedReferrersForCharts($app_id, $agent_id, $from_date, $to_date, $label_format, $group_by_foramt);
	}
	
}