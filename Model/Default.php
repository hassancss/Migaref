<?php

class Migareference_Model_Default extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Default';
    }

	/**
     * @param string $report_id
     * @return mixed
     */
    public function defaultReminderSettings($app_id = 0)
    {
        return $this->getTable()->defaultReminderSettings($app_id);
    }
    public function prospectTransform($app_id = 0)
    {
        return $this->getTable()->prospectTransform($app_id);
    }
    public function copyAgents($app_id = 0)
    {
        return $this->getTable()->copyAgents($app_id);
    }
    public function copyReftoPhonebook($app_id = 0)
    {
        return $this->getTable()->copyReftoPhonebook($app_id);
    }
    public function setDefaultCreditsApiNotification($app_id = 0)
    {
        return $this->getTable()->setDefaultCreditsApiNotification($app_id);
    }
}
