<?php

class Migareference_Model_Webhook extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Webhook';
    }	
     public function triggerReportWebhook($app_id=0,$report_id=0,$call_type='',$event_type='')
    {
        return $this->getTable()->triggerReportWebhook($app_id,$report_id,$call_type,$event_type);
    }
     public function trigerNewReferrerWebhook($app_id=0,$referrer_id=0)
    {
        return $this->getTable()->trigerNewReferrerWebhook($app_id,$referrer_id);
    }
     public function referrerWebhookParamsTemplate($app_id=0,$referrer_id=0,$call_type='')
    {
        return $this->getTable()->referrerWebhookParamsTemplate($app_id,$referrer_id,$call_type);
    }
}
