<?php

class Migareference_Model_Db_Table_QualificationReferrer extends Core_Model_Db_Table
{

    protected $_name = 'migareference_qualifications_referrers';
    protected $_primary = 'migareference_qualifications_referrers_id';
    public function getCustomersByQualification($qualification_id)
    {
        $db = $this->_db; // ya $this->getAdapter() depending tumhare base class par

        $sql = "
        SELECT 
    c.customer_id AS id, 
    c.firstname, 
    c.lastname, 
    c.email, 
    c.mobile,
    qr.created_at AS referrer_created_at
FROM migareference_qualifications_referrers qr
INNER JOIN customer c ON qr.referrer_id = c.customer_id
WHERE qr.qualification_id = ?
    ";

        return $db->fetchAll($sql, [$qualification_id]);
    }
}
