<?php

class Migareference_Model_OpenaiConfig extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_OpenaiConfig';
    }
    public function setDefaultConfig($app_id=0) {
        return $this->getTable()->setDefaultConfig($app_id);
    }	   
    public function callScriptLog($app_id=0) {
        return $this->getTable()->callScriptLog($app_id);
    }	   
    public function aiMatchingLog($app_id=0) {
        return $this->getTable()->aiMatchingLog($app_id);
    }	   
    public function callscriptLogItem($uid=0) {
        return $this->getTable()->callscriptLogItem($uid);
    }	   
    public function matchingLogItem($uid=0) {
        return $this->getTable()->matchingLogItem($uid);
    }	   
    public function getModels($apiKey=0) {
        return $this->getTable()->getModels($apiKey);
    }	   
    public function addCallScriptLog($data=0) {
        return $this->getTable()->addCallScriptLog($data);
    }	   
}
