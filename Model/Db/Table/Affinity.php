<?php
class Migareference_Model_Db_Table_Affinity extends Core_Model_Db_Table
{
    /**
     * Insert a new affinity run row with optional metadata.
     *
     * @param int $app_id
     * @param array $meta
     * @return int
     */
    public function createAffinityRun($app_id, $meta=[])
    {
        $now = date('Y-m-d H:i:s');
        $defaults = [
            'app_id' => (int) $app_id,
            'status' => 'queued',
            'cursor_i' => 0,
            'cursor_j' => 1,
            'total_referrers' => 0,
            'total_pairs_estimate' => 0,
            'processed_pairs' => 0,
            'model' => null,
            'temperature' => null,
            'prompt_hash' => null,
            'last_error' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $allowed_meta = array_intersect_key($meta, $defaults);
        $data = array_merge($defaults, $allowed_meta);
        $this->_db->insert('migareference_affinity_runs', $data);
        return $this->_db->lastInsertId();
    }

    /**
     * Return affinity run row data for a given id.
     *
     * @param int $run_id
     * @return array|null
     */
    public function getAffinityRun($run_id)
    {
        $rows = $this->_db->fetchAll(
            "SELECT * FROM `migareference_affinity_runs` WHERE `id` = ?",
            [(int) $run_id]
        );
        if (count($rows)) {
            return $rows[0];
        }
        return null;
    }

    /**
     * Update selected fields for a run and bump updated_at.
     *
     * @param int $run_id
     * @param array $data
     * @return int
     */
    public function updateAffinityRun($run_id, $data=[])
    {
        $allowed = [
            'status' => true,
            'cursor_i' => true,
            'cursor_j' => true,
            'total_referrers' => true,
            'total_pairs_estimate' => true,
            'processed_pairs' => true,
            'model' => true,
            'temperature' => true,
            'prompt_hash' => true,
            'last_error' => true,
        ];
        $update = array_intersect_key($data, $allowed);
        $update['updated_at'] = date('Y-m-d H:i:s');
        return $this->_db->update(
            "migareference_affinity_runs",
            $update,
            ['id = ?' => (int) $run_id]
        );
    }

    /**
     * Upsert a scored edge for a pair of referrers.
     *
     * @param int $app_id
     * @param int $run_id
     * @param int $a
     * @param int $b
     * @param int $score
     * @param string|null $raw
     * @return bool
     */
    public function upsertAffinityEdge($app_id,$run_id,$a,$b,$score,$raw=null)
    {
        $low = min((int) $a, (int) $b);
        $high = max((int) $a, (int) $b);
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `migareference_affinity_edges`
          (`app_id`,`run_id`,`referrer_id_low`,`referrer_id_high`,`score`,`raw_response`,`created_at`,`updated_at`)
          VALUES (?,?,?,?,?,?,?,?)
          ON DUPLICATE KEY UPDATE
            `score` = VALUES(`score`),
            `raw_response` = VALUES(`raw_response`),
            `updated_at` = VALUES(`updated_at`)";
        $this->_db->query($sql, [
            (int) $app_id,
            (int) $run_id,
            $low,
            $high,
            (int) $score,
            $raw,
            $now,
            $now,
        ]);
        return true;
    }

    /**
     * List scored edges for a referrer in a run ordered by score.
     *
     * @param int $app_id
     * @param int $run_id
     * @param int $referrer_id
     * @param int $limit
     * @return array
     */
    public function listEdgesForReferrer($app_id,$run_id,$referrer_id,$limit=10)
    {
        $limit = (int) $limit;
        if ($limit <= 0) {
            $limit = 10;
        }
        $query = "SELECT * FROM `migareference_affinity_edges`
          WHERE `app_id` = ? AND `run_id` = ?
          AND (`referrer_id_low` = ? OR `referrer_id_high` = ?)
          ORDER BY `score` DESC, `id` DESC
          LIMIT $limit";
        return $this->_db->fetchAll($query, [
            (int) $app_id,
            (int) $run_id,
            (int) $referrer_id,
            (int) $referrer_id,
        ]);
    }
}
