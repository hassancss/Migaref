<?php

class Migareference_Model_Db_Table_QualificationContentSetting extends Core_Model_Db_Table
{

    protected $_name = 'migareference_qualification_content_setting';
    protected $_primary = 'migareference_qualification_content_setting_id';
      public function saveContentSetting($data = [])
    {
        // unwanted fields remove
        unset($data['migareference_qualification_content_setting_id']);

        // created_at set
        $data['created_at'] = date('Y-m-d H:i:s');

        // insert record
        $this->_db->insert("migareference_qualification_content_setting", $data);
        $id = $this->_db->lastInsertId();

        return $id;
    }

    public function updateContentSetting($data = [])
    {
        if (!isset($data['migareference_qualification_content_setting_id']) || !$data['migareference_qualification_content_setting_id']) {
            throw new Exception("Content Setting ID missing for update");
        }

        $id = $data['migareference_qualification_content_setting_id'];

        // primary key remove
        unset($data['migareference_qualification_content_setting_id']);

        // updated_at set
        $data['updated_at'] = date('Y-m-d H:i:s');

        // update query
        return $this->_db->update(
            "migareference_qualification_content_setting",
            $data,
            ['migareference_qualification_content_setting_id = ?' => $id]
        );
    }
    
    public function getByAppValueQualification($appId, $valueId, $qualificationId)
{
    $select = $this->select()
        ->from($this->_name, [
              'migareference_qualification_content_setting_id',
            'non_customer_content_type',
            'non_customer_list_id',
            'customer_content_type',
            'customer_list_id',
             
        ])
        ->where('app_id = ?', (int)$appId)
        ->where('value_id = ?', (int)$valueId)
        ->where('qualification_id = ?', (int)$qualificationId)
        ->limit(1);

    return $this->fetchRow($select);
}
 

 
public function fetchVisibleFeaturesForApp($appId)
{
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(['aov' => 'application_option_value'], [
            'value_id'    => 'aov.value_id',
            'tabbar_name' => 'aov.tabbar_name',
            'position'    => 'aov.position',
            'option_id'   => 'aov.option_id',
        ])
        ->join(['ao' => 'application_option'], 'ao.option_id = aov.option_id', [
            'name'       => 'ao.name',    
            'code'       => 'ao.code',   
            'is_enabled' => 'ao.is_enabled',
        ])
        ->where('aov.app_id = ?', (int) $appId)
        ->where('aov.is_visible = ?', 1)
        ->where('ao.is_enabled = ?', 1)
        ->where('(ao.code IS NULL OR ao.code != ?)', 'folder_v2')
        ->order('aov.position ASC')
        ->order('aov.value_id ASC');

    return $this->fetchAll($select);
}

 
public function fetchFoldersForApp($appId)
{
    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(['aov' => 'application_option_value'], [
            'value'       => 'aov.value_id',   
            'tabbar_name' => 'aov.tabbar_name',
            'position'    => 'aov.position',
            'option_id'   => 'aov.option_id',
            'folder_id'   => 'aov.folder_id',  
            'is_visible'  => 'aov.is_visible',
        ])
        ->join(['ao' => 'application_option'], 'ao.option_id = aov.option_id', [
            'name'       => 'ao.name',
            'code'       => 'ao.code',
            'is_enabled' => 'ao.is_enabled',
        ])
        ->where('aov.app_id = ?', (int) $appId)
        
        ->where('ao.code = ?', 'folder_v2')
        
        ->where('ao.is_enabled = ?', 1)
        ->where('aov.is_visible = ?', 1)
        ->order('aov.position ASC')
        ->order('aov.value_id ASC');

    return $this->fetchAll($select);
}


}
