<?php
class Migareference_Model_Reportapi extends Core_Model_Default {
  /**
   * Migareference_Model_Reportapi constructor.
   * @param array $param
   * @throws Zend_Exception
   */
  public function __construct($params = [])
  {
      parent::__construct($params);
      $this->_db_table = 'Migareference_Model_Db_Table_Reportapi';
      return $this;
  }
    public function validateToken($token ='')
    {
      return $this->getTable()->validateToken($token);
    }
    public function saveCreditsApiLog($data =[])
    {
      return $this->getTable()->saveCreditsApiLog($data);
    }
    public function validateCreditApiToken($token ='')
    {
      return $this->getTable()->validateCreditApiToken($token);
    }
    public function getApiAdmin($app_id =0)
    {
      return $this->getTable()->getApiAdmin($app_id);
    }
    public function getCreditsApiLog($app_id =0)
    {
      return $this->getTable()->getCreditsApiLog($app_id);
    }
    public function getCreditApiAdmin($app_id =0)
    {
      return $this->getTable()->getCreditApiAdmin($app_id);
    }
    public function updateApiAdmin($admin_id =0)
    {
      return $this->getTable()->updateApiAdmin($admin_id);
    }
    public function updateCreditsApiAdmin ($admin_id =0)
    {
      return $this->getTable()->updateCreditsApiAdmin ($admin_id);
    }
}
