<?php

class Migareference_Model_QualificationReferrer extends Core_Model_Default
{
    public function __construct($datas = [])
    {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_QualificationReferrer';
    }

    public function getCustomersByQualification($qualification_id)
    {
        return $this->getTable()->getCustomersByQualification($qualification_id);
    }
}
