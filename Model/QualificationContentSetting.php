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
    
    /**
     * Fetch a single content setting by app/value/qualification.
     * Controller should call this instead of building joins.
     */
  public function getByAppValueQualification($appId, $valueId, $qualificationId)
{
    $row = $this->getTable()->getByAppValueQualification($appId, $valueId, $qualificationId);

    return $row ? $row->toArray() : null;  
}

    
}
