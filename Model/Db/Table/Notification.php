<?php

class Migareference_Model_Db_Table_Notification extends Core_Model_Db_Table
{

    protected $_name = 'migareference_qualificationnotifications';
    protected $_primary = 'migareference_qualificationnotifications_id';
    public function saveNotification($data = [])
    {
        // Persist uploaded image from tmp to application folder
        if (!empty($data['logo_url'])) {
            $migareference = new Migareference_Model_Db_Table_Migareference();
            // Returns filename; keeps same name if already copied
            $data['logo_url'] = $migareference->uploadApplicationFile($data['app_id'], $data['logo_url'], 0);
        }

        // Unwanted fields remove karna
        unset($data['migareference_qualificationnotifications_id']);

        // Created at dalna
        $data['created_at'] = date('Y-m-d H:i:s');

        // Insert karna
        $this->_db->insert("migareference_qualificationnotifications", $data);

        // Inserted row ka id return karna
        $id = $this->_db->lastInsertId();
        return $id;
    }
    public function getNotificationByAppId($app_id)
    {
        $select = $this->select()
            ->from($this->_name) // force table name
            ->where('app_id = ?', $app_id)
            ->limit(1);

        $row = $this->fetchRow($select);
        return $row ? $row->toArray() : null;
    }

    public function updateNotification($data = [])
    {
        if (!isset($data['migareference_qualificationnotifications_id']) || !$data['migareference_qualificationnotifications_id']) {
            throw new Exception("Notification ID missing for update");
        }

        $id = $data['migareference_qualificationnotifications_id'];
        unset($data['migareference_qualificationnotifications_id']); // primary key remove
        $data['updated_at'] = date('Y-m-d H:i:s');      // updated_at add

        // Persist uploaded image from tmp to application folder if provided
        if (isset($data['logo_url']) && $data['logo_url'] !== '') {
            $migareference = new Migareference_Model_Db_Table_Migareference();
            $data['logo_url'] = $migareference->uploadApplicationFile($data['app_id'], $data['logo_url'], 0);
        }

        // Update table
        return $this->_db->update("migareference_qualificationnotifications", $data, ['migareference_qualificationnotifications_id = ?' => $id]);
    }
    // public function deleteQualification($id)
    // {
    //     return $this->_db->delete('migareference_qualifications', ['migareference_qualifications_id = ?' => $id]);
    // }
}
