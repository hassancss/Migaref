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
    unset($data['migareference_qualifications_id']);  
    $data['updated_at'] = date('Y-m-d H:i:s');       

    // Purana record le lo
    $oldData = $this->_db->fetchRow(
        $this->_db->select()
            ->from("migareference_qualifications")
            ->where("migareference_qualifications_id = ?", $id)
    );

    // Agar new image di gayi to upload karo, warna purana hi rakho
    if (!empty($data['qlf_file'])) {
        // ✅ sirf filename save karo
        $data['qlf_file'] = basename($data['qlf_file']);  

        $migareference = new Migareference_Model_Db_Table_Migareference();
        $migareference->uploadApplicationFile($data['app_id'], $data['qlf_file'], 0);
    } else {
        $data['qlf_file'] = $oldData['qlf_file'];  // old image restore
    }

    // Update table
    return $this->_db->update(
        "migareference_qualifications",
        $data,
        ['migareference_qualifications_id = ?' => $id]
    );
}


    public function deleteQualification($id)
    {
        return $this->_db->delete('migareference_qualifications', ['migareference_qualifications_id = ?' => $id]);
    }
    public function getFeatureContent($feature_id,$app_id)
    {
        $query_option = "SELECT application_option_value.value_id, application_option.model
        FROM `application_option_value` 
        JOIN application_option ON application_option.option_id=application_option_value.option_id 
        WHERE  `app_id` = $app_id AND application_option_value.`value_id` = $feature_id";
        return $this->_db->fetchAll($query_option);
    } 
    public function getQualificationDetails($user_id)
    {
        $query_option = "SELECT * 
        FROM `migareference_invoice_settings` 
        JOIN migareference_qualifications_referrers ON migareference_qualifications_referrers.referrer_id=migareference_invoice_settings.user_id 
        JOIN migareference_qualification_content_setting ON migareference_qualification_content_setting.qualification_id=migareference_qualifications_referrers.qualification_id
        WHERE `user_id` = $user_id";
        return $this->_db->fetchAll($query_option);
    } 
}
