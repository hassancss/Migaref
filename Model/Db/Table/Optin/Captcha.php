<?php

class Migareference_Model_Db_Table_Optin_Captcha extends Core_Model_Db_Table {

    protected $_name = 'migareference_optin_captcha';
    protected $_primary = 'migareference_optin_captcha_id';    

    public function createCaptchaImages($app_id=0)
    {
        $sum = [6, 9, 8, 9, 5, 6, 9, 8, 8, 4];
        for ($i=1; $i <11 ; $i++) {      
            $data['app_id'] = $app_id;
            $data['image_name'] = $i.'.png';
            $data['image_uid'] = md5($i);
            $data['sum'] = $sum[$i-1];
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->_db->insert("migareference_optin_captcha", $data);            
        }
        // Return the whole list
        $sql = $this->_db->select()
            ->from($this->_name)
            ->where('app_id = ?', $app_id);
        return $this->_db->fetchAll($sql);
    }
}
