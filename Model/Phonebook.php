<?php

class Migareference_Model_Phonebook extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Phonebook';
    }
    public function referrerMatching($app_id=0,$phonebook_id=0,$calling_method='') {
        return $this->getTable()->referrerMatching($app_id,$phonebook_id,$calling_method);
    }      
    public function availableMatching($app_id=0,$referrer_id=0) {
        return $this->getTable()->availableMatching($app_id,$referrer_id);
    }   
    public function matchedMatching($app_id=0,$referrer_id=0) {
        return $this->getTable()->matchedMatching($app_id,$referrer_id);
    }   
    public function discardMatching($app_id=0,$referrer_id=0) {
        return $this->getTable()->discardMatching($app_id,$referrer_id);
    }   
    public function lastMatchingCall($app_id=0,$referrer_id=0) {
        return $this->getTable()->lastMatchingCall($app_id,$referrer_id);
    }   
    public function matchCustomer($app_id=0,$matching_network_id=0) {
        return $this->getTable()->matchCustomer($app_id,$matching_network_id);
    }   
    public function discardCustomer($app_id=0,$matching_network_id=0) {
        return $this->getTable()->discardCustomer($app_id,$matching_network_id);
    }   
    public function unmatchcustomer($app_id=0,$matching_network_id=0) {
        return $this->getTable()->unmatchcustomer($app_id,$matching_network_id);
    }   
    public function removecustomer($app_id=0,$matching_network_id=0) {
        return $this->getTable()->removecustomer($app_id,$matching_network_id);
    }   
}
