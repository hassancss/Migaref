<?php

class Migareference_Model_Qualification extends Core_Model_Default
{
    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Qualification';
    }
    public function saveQualification($data = [])
    {
        return $this->getTable()->saveQualification($data);
    }
     public function deleteQualification($id)
    {
      return $this->getTable()->deleteQualification($id);
    }
       public function updateQualification($data = [])
    {
        return $this->getTable()->updateQualification($data);
    }
       public function getFeatureContent($feature_id,$app_id)
    {
        return $this->getTable()->getFeatureContent($feature_id,$app_id);
    }
       public function getQualificationDetails($user_id)
    {
        return $this->getTable()->getQualificationDetails($user_id);
    }

}
