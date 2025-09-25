<?php

class Migareference_Model_Optin_Captcha extends Core_Model_Default
{
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Optin_Captcha';
    }   
    public function createCaptchaImages($app_id=0) {
        return $this->getTable()->createCaptchaImages($app_id);
    }
}
