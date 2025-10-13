<?php

class Migareference_Model_QualificationContentSetting extends Core_Model_Default
{
    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_QualificationContentSetting';
    }
    public function saveContentSetting($data = [])
    {
        return $this->getTable()->saveContentSetting($data);
    }
    public function updateContentSetting($id)
    {
      return $this->getTable()->updateContentSetting($id);
    }
    
   
    public function getByAppValueQualification($appId, $valueId, $qualificationId)
    {
        $row = $this->getTable()->getByAppValueQualification($appId, $valueId, $qualificationId);

        return $row ? $row->toArray() : null;  
    }
    public function fetchVisibleFeaturesForApp($appId)
    {
        $rows = $this->getTable()->fetchVisibleFeaturesForApp($appId);
        return $rows ? $rows->toArray() : [];
    }

   
    public function fetchFoldersForApp($appId)
    {
        $rows = $this->getTable()->fetchFoldersForApp($appId);
        return $rows ? $rows->toArray() : [];
    }
    
}
