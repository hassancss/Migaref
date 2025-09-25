<?php
class Migareference_Model_Db_Table_Reportapi extends Core_Model_Db_Table {
    public function validateToken($token='')
    {
      $sql    ="SELECT *
                FROM `migareference_pre_report_settings`
                WHERE `report_api_token`='$token'";
      return  $this->_db->fetchAll($sql);
    }
    public function getCreditsApiLog($app_id=0)
    {
      $sql    ="SELECT *
                FROM `migareference_credits_api_log`
                WHERE `app_id`=$app_id";
      return  $this->_db->fetchAll($sql);
    }
    public function validateCreditApiToken($token='')
    {
      $sql    ="SELECT *
                FROM `migareference_pre_report_settings`
                WHERE `credits_api_token`='$token'";
      return  $this->_db->fetchAll($sql);
    }
    public function getApiAdmin($app_id=0)
    {
      $query_option = "SELECT * FROM `migareference_app_admins`
                       JOIN customer ON customer.customer_id=migareference_app_admins.user_id
                       WHERE migareference_app_admins.app_id=$app_id AND migareference_app_admins.api_admin=1";
      $res_option   = $this->_db->fetchAll($query_option);
      return $res_option;
    }
    public function getCreditApiAdmin($app_id=0)
    {
      $query_option = "SELECT * FROM `migareference_app_admins`
                       JOIN customer ON customer.customer_id=migareference_app_admins.user_id
                       WHERE migareference_app_admins.app_id=$app_id AND migareference_app_admins.credits_api_admin=1";
      $res_option   = $this->_db->fetchAll($query_option);
      return $res_option;
    }
    public function updateApiAdmin($admin_id=0)
    {
      $data['api_admin']=0;
      $this->_db->update("migareference_app_admins", $data,['api_admin = ?' =>0]);
      $data['api_admin']=1;
      return $this->_db->update("migareference_app_admins", $data,['migareference_app_admins_id = ?' => $admin_id]);
    }
    public function updateCreditsApiAdmin($admin_id=0)
    {
      // rest previos admin
      $data['credits_api_admin']=0;
      $this->_db->update("migareference_app_admins", $data,['credits_api_admin = ?' =>0]);
      // set new admin
      $data['credits_api_admin']=1;
      return $this->_db->update("migareference_app_admins", $data,['migareference_app_admins_id = ?' => $admin_id]);
    }
    public function saveCreditsApiLog($data=[])
    {
      $data['created_at']    = date('Y-m-d H:i:s');
      $this->_db->insert("migareference_credits_api_log", $data);
      return $this->_db->lastInsertId();
    }
}
