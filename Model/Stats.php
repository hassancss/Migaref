<?php
class Migareference_Model_Stats extends Core_Model_Default {
    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Stats';
    }


	public function getTotalReferrersByAgentRatingAndDate($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		return $this->getTable()->getTotalReferrersByAgentRatingAndDate($app_id, $agent_id, $from_date, $to_date);
	}

	public function getDealClosedByAgentRatingAndDate($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		return $this->getTable()->getDealClosedByAgentRatingAndDate($app_id, $agent_id, $from_date, $to_date);
	}

	public function getReportsTrendByAgentRatingAndDate($app_id = 0, $agent_id = 0, $rating = 0, $from_date = '', $to_date = '', $label_format = '', $group_by_format = '') {
		return $this->getTable()->getReportsTrendByAgentRatingAndDate($app_id, $agent_id, $rating, $from_date, $to_date, $label_format, $group_by_format);
	}

	public function getReportReminders($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '', $label_formate ='', $group_by_foramt = '') {
		return $this->getTable()->getReportReminders($app_id, $agent_id, $from_date, $to_date, $label_formate, $group_by_foramt);
	}

	public function getReportRemindersCount($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		return $this->getTable()->getReportRemindersCount($app_id, $agent_id, $from_date, $to_date);
	}
}
