<?php
class Migareference_Model_Db_Table_Presetting extends Core_Model_Db_Table {
    protected $_name = "migareference_pre_report_settings";
    protected $_primary = "migareference_pre_report_settings_id";
    public function updatePreReport($data=[])
    {
     return "ok";
    }
    public function updatehowto($data=[])
    {
      $app_id             = $data['app_id'];
      $data['updated_at'] = date('Y-m-d H:i:s');
      $res                = $this->_db->update("migareference_pre_report_settings", $data,['app_id = ?' => $app_id]);
      return $res;
    }
}
