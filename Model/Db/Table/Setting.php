<?php
class Migareference_Model_Db_Table_Setting extends Core_Model_Db_Table {
    protected $_name = "migareference_setting";
    protected $_primary = "setting_id";
    /**
     * @return string
     */
    public function getHelpUrl()
    {
        $help_url = "";
        $query_help_url = "SELECT help_url FROM migareference_setting WHERE help_url <> '' OR help_url IS NOT NULL";
        $res_help_url = $this->_db->fetchAll($query_help_url);
        if (count($res_help_url) && !empty($res_help_url[0]['help_url'])) {
            $help_url = $res_help_url[0]['help_url'];
        }
        return $help_url;
    }
    /**
     * @param string $help_url
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    public function updateHelpUrl($help_url = "")
    {
        $res_help_url = $this->_db->fetchAll("SELECT help_url FROM appmetropolis_setting WHERE help_url <> '' OR help_url IS NOT NULL");
        if (count($res_help_url)) {
            $this->_db->update('migareference_setting', [
                'help_url' => $help_url,
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'setting_id > ?' => 0
            ]);
        } else {
            $this->_db->insert("migareference_setting", [
                'help_url' => $help_url,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        return $this;
    }
}
