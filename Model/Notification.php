<?php

class Migareference_Model_Notification extends Core_Model_Default
{
    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Notification';
    }
    public function saveNotification($data = [])
    {
        return $this->getTable()->saveNotification($data);
    }
       public function getNotificationByAppId($app_id) 
    {
        return $this->getTable()->getNotificationByAppId($app_id);
    }
     public function updateNotification($data = [])
    {
        return $this->getTable()->updateNotification($data);
    }

}
