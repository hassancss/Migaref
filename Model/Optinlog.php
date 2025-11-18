<?php

class Migareference_Model_Optinlog extends Core_Model_Default
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_VALIDATION_FAILED = 'validation_failed';
    const STATUS_SYSTEM_ERROR = 'system_error';

    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Optinlog';
    }

    public function createLog(array $data)
    {
        return $this->getTable()->insertLog($data);
    }

    public function updateLog($logId, array $data)
    {
        return $this->getTable()->updateLog($logId, $data);
    }

    public function fetchLogs($appId, array $filters = [])
    {
        return $this->getTable()->fetchLogs($appId, $filters);
    }
}
