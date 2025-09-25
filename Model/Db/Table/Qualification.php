<?php

class Migareference_Model_Db_Table_Qualification extends Core_Model_Db_Table
{

    protected $_name = 'migareference_qualifications';
    protected $_primary = 'migareference_qualifications_id';
    public function saveQualification($data = [])
    {
        //image/file 
        $migareference=new Migareference_Model_Db_Table_Migareference();
        $migareference->uploadApplicationFile($data['app_id'],$data['qlf_file'],0);
        // Unwanted fields remove
        unset($data['migarefrence_qualifications_id']);

        // Created at 
        $data['created_at'] = date('Y-m-d H:i:s');

        // Insert 
        $this->_db->insert("migareference_qualifications", $data);        
        $id = $this->_db->lastInsertId();
        return $id;
    }

    public function updateQualification($data = [])
    {
        if (!isset($data['migareference_qualifications_id']) || !$data['migareference_qualifications_id']) {
            throw new Exception("Qualification ID missing for update");
        }

        $id = $data['migareference_qualifications_id'];
        unset($data['migareference_qualifications_id']); // primary key remove
        $data['updated_at'] = date('Y-m-d H:i:s');      // updated_at add

        // Update table
        return $this->_db->update("migareference_qualifications", $data, ['migareference_qualifications_id = ?' => $id]);
    }
    public function deleteQualification($id)
    {
        return $this->_db->delete('migareference_qualifications', ['migareference_qualifications_id = ?' => $id]);
    }
}
