<?php

class Migareference_Model_Db_Table_Optinlog extends Core_Model_Db_Table
{
    protected $_name = 'migareference_optin_logs';
    protected $_primary = 'migareference_optin_log_id';

    public function insertLog(array $data)
    {
        $now = date('Y-m-d H:i:s');
        if (empty($data['created_at'])) {
            $data['created_at'] = $now;
        }
        if (empty($data['updated_at'])) {
            $data['updated_at'] = $data['created_at'];
        }
        $this->_db->insert($this->_name, $data);
        return (int) $this->_db->lastInsertId();
    }

    public function updateLog($logId, array $data)
    {
        if (!$logId) {
            return false;
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->_db->update(
            $this->_name,
            $data,
            [$this->_primary . ' = ?' => (int) $logId]
        );
    }

    public function fetchLogs($appId, array $filters = [])
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('app_id = ?', (int) $appId)
            ->order($this->_primary . ' DESC');

        if (!empty($filters['status'])) {
            $select->where('status = ?', $filters['status']);
        }

        if (!empty($filters['mismatch_only'])) {
            $select->where('mismatch_flag = 1');
        }

        if (!empty($filters['from'])) {
            $fromTs = strtotime($filters['from']);
            if ($fromTs) {
                $from = date('Y-m-d 00:00:00', $fromTs);
                $select->where('created_at >= ?', $from);
            }
        }

        if (!empty($filters['to'])) {
            $toTs = strtotime($filters['to']);
            if ($toTs) {
                $to = date('Y-m-d 23:59:59', $toTs);
                $select->where('created_at <= ?', $to);
            }
        }

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $clauses = [
                $this->_db->quoteInto('correlation_id LIKE ?', $term),
                $this->_db->quoteInto('ip_address LIKE ?', $term),
                $this->_db->quoteInto('referrer_url LIKE ?', $term),
                $this->_db->quoteInto('status LIKE ?', $term),
            ];
            $select->where('(' . implode(' OR ', $clauses) . ')');
        }

        if (!empty($filters['limit'])) {
            $select->limit((int) $filters['limit']);
        }

        return $this->_db->fetchAll($select);
    }
}
