<?php
class Migareference_Model_Setting extends Core_Model_Default {
    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Setting';
    }
    /**
     * @param string $help_url
     * @return mixed
     */
    public function updateHelpUrl($help_url = "")
    {
        return $this->getTable()->updateHelpUrl($help_url);
    }
    /**
     * @return mixed
     */
    public function getHelpUrl()
    {
        return $this->getTable()->getHelpUrl();
    }
}
