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
            'lock_token' => null,
            'locked_at' => null,
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
     * Return the latest running affinity run for an app.
     *
     * @param int $app_id
     * @return array|null
     */
    public function getLatestRunningRun($app_id)
    {
        $rows = $this->_db->fetchAll(
            "SELECT * FROM `migareference_affinity_runs`
             WHERE `app_id` = ? AND `status` = 'running'
             ORDER BY `id` DESC
             LIMIT 1",
            [(int) $app_id]
        );
        if (count($rows)) {
            return $rows[0];
        }
        return null;
    }

    /**
     * Attempt to acquire a lightweight run lock for cron processing.
     *
     * @param int $run_id
     * @param string $lockToken
     * @param int $ttlMinutes
     * @return int
     */
    public function acquireRunLock($run_id, $lockToken, $ttlMinutes = 10)
    {
        $ttlMinutes = (int) $ttlMinutes;
        if ($ttlMinutes <= 0) {
            $ttlMinutes = 10;
        }

        $sql = "UPDATE `migareference_affinity_runs`
          SET `lock_token` = ?, `locked_at` = NOW()
          WHERE `id` = ?
            AND (`locked_at` IS NULL OR `locked_at` < NOW() - INTERVAL {$ttlMinutes} MINUTE)";
        $statement = $this->_db->query($sql, [
            (string) $lockToken,
            (int) $run_id,
        ]);
        return $statement->rowCount();
    }

    /**
     * Release a previously acquired run lock.
     *
     * @param int $run_id
     * @param string|null $lockToken
     * @return int
     */
    public function releaseRunLock($run_id, $lockToken = null)
    {
        $where = ['id = ?' => (int) $run_id];
        if ($lockToken !== null) {
            $where['lock_token = ?'] = (string) $lockToken;
        }

        return $this->_db->update(
            'migareference_affinity_runs',
            [
                'lock_token' => null,
                'locked_at' => null,
            ],
            $where
        );
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
        try {
            $statement = $this->_db->query($sql, [
                (int) $app_id,
                (int) $run_id,
                $low,
                $high,
                (int) $score,
                $raw,
                $now,
                $now,
            ]);
        } catch (Exception $e) {
            throw new Exception('Affinity edge upsert failed: ' . $e->getMessage(), 0, $e);
        }
        return $statement->rowCount();
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

    /**
     * Fetch normalized profile rows for a set of referrer user ids.
     *
     * @param int $appId
     * @param array $referrerIds
     * @return array
     */
    public function getReferrerProfiles($appId, array $referrerIds)
    {
        $referrerIds = array_values(array_filter(array_map('intval', $referrerIds)));
        if (!count($referrerIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($referrerIds), '?'));
        $query = "SELECT inv.user_id AS referrer_id,
            ph.name,
            ph.surname,
            ph.note,
            ph.reciprocity_notes,
            ph.rating,
            jobs.job_title,
            prof.profession_title,
            prov.province,
            inv.address_country_id
          FROM `migarefrence_phonebook` AS ph
          JOIN `migareference_invoice_settings` AS inv
            ON inv.migareference_invoice_settings_id = ph.invoice_id
            AND inv.app_id = ph.app_id
          LEFT JOIN `migareference_jobs` AS jobs
            ON jobs.migareference_jobs_id = ph.job_id
          LEFT JOIN `migareference_professions` AS prof
            ON prof.migareference_professions_id = ph.profession_id
          LEFT JOIN `migareference_geo_provinces` AS prov
            ON prov.migareference_geo_provinces_id = inv.address_province_id
          WHERE ph.app_id = ?
            AND ph.type = 1
            AND inv.user_id IN ($placeholders)
          ORDER BY ph.migarefrence_phonebook_id DESC";

        $params = array_merge([(int) $appId], $referrerIds);
        $rows = $this->_db->fetchAll($query, $params);
        $profiles = [];
        foreach ($rows as $row) {
            $referrerId = (int) $row['referrer_id'];
            if (!isset($profiles[$referrerId])) {
                $profiles[$referrerId] = $row;
            }
        }

        return $profiles;
    }

    /**
     * Return eligible referrer IDs for affinity matching.
     *
     * @param int $appId
     * @return array
     */
    public function getEligibleReferrerIds($appId)
    {
        $query = "SELECT DISTINCT inv.user_id AS referrer_id
          FROM `migareference_invoice_settings` AS inv
          JOIN `migarefrence_phonebook` AS ph
            ON ph.invoice_id = inv.migareference_invoice_settings_id
            AND ph.app_id = inv.app_id
          JOIN `migareference_openai_config` AS cfg
            ON cfg.app_id = ph.app_id
            AND cfg.is_matching_api_enabled = 1
          WHERE ph.app_id = ?
            AND ph.type = 1
            AND ph.rating > 0
            AND ph.job_id > 0
            -- AND (ph.is_matching_call_made IS NULL OR ph.is_matching_call_made = '')
          ORDER BY inv.user_id ASC";
        return $this->_db->fetchAll($query, [(int) $appId]);
    }
}
