<?php
class Migareference_Model_Affinity extends Core_Model_Default
{
    public function __construct($datas = []) {
        parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Affinity';
    }

    /**
     * Create a new affinity scoring run for an app.
     *
     * @param int $app_id
     * @param array $meta
     * @return int
     */
    public function createAffinityRun($app_id, $meta=[])
    {
        return $this->getTable()->createAffinityRun($app_id, $meta);
    }

    /**
     * Create a new affinity run row for an app (alias for createAffinityRun).
     *
     * @param int $app_id
     * @param array $meta
     * @return int
     */
    public function createRun($app_id, $meta=[])
    {
        return $this->createAffinityRun($app_id, $meta);
    }

    /**
     * Fetch a single affinity run row by id.
     *
     * @param int $run_id
     * @return array|null
     */
    public function getAffinityRun($run_id)
    {
        return $this->getTable()->getAffinityRun($run_id);
    }

    /**
     * Fetch the most recent running affinity run for an app.
     *
     * @param int $app_id
     * @return array|null
     */
    public function getLatestRunningRun($app_id)
    {
        return $this->getTable()->getLatestRunningRun($app_id);
    }

    /**
     * Acquire a run lock for cron processing.
     *
     * @param int $run_id
     * @param string $lockToken
     * @param int $ttlMinutes
     * @return int
     */
    public function acquireRunLock($run_id, $lockToken, $ttlMinutes = 10)
    {
        return $this->getTable()->acquireRunLock($run_id, $lockToken, $ttlMinutes);
    }

    /**
     * Release a run lock.
     *
     * @param int $run_id
     * @param string|null $lockToken
     * @return int
     */
    public function releaseRunLock($run_id, $lockToken = null)
    {
        return $this->getTable()->releaseRunLock($run_id, $lockToken);
    }

    /**
     * Update fields for a specific affinity run.
     *
     * @param int $run_id
     * @param array $data
     * @return int
     */
    public function updateAffinityRun($run_id, $data=[])
    {
        return $this->getTable()->updateAffinityRun($run_id, $data);
    }

    /**
     * Insert or update an affinity edge between two referrers.
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
        return $this->getTable()->upsertAffinityEdge($app_id,$run_id,$a,$b,$score,$raw);
    }

    /**
     * List scored edges for a given referrer in a run.
     *
     * @param int $app_id
     * @param int $run_id
     * @param int $referrer_id
     * @param int $limit
     * @return array
     */
    public function listEdgesForReferrer($app_id,$run_id,$referrer_id,$limit=10)
    {
        return $this->getTable()->listEdgesForReferrer($app_id,$run_id,$referrer_id,$limit);
    }

    /**
     * Fetch profile rows for a set of referrer ids.
     *
     * @param int $appId
     * @param array $referrerIds
     * @return array
     */
    public function getReferrerProfiles($appId, array $referrerIds)
    {
        return $this->getTable()->getReferrerProfiles($appId, $referrerIds);
    }

    /**
     * Fetch eligible referrer IDs for affinity matching.
     *
     * @param int $appId
     * @return array
     */
    public function getEligibleReferrerIds(int $appId): array
    {
        $rows = $this->getTable()->getEligibleReferrerIds($appId);
        $ids = [];
        foreach ($rows as $row) {
            if (isset($row['referrer_id'])) {
                $ids[] = (int) $row['referrer_id'];
            }
        }
        return $ids;
    }

    /**
     * Get the next batch of pairs for a run and update cursors/progress.
     *
     * @param int $app_id
     * @param int $run_id
     * @param int $batch_pairs
     * @return array|null
     */
    public function getNextPairs($app_id, $run_id, $batch_pairs = 20)
    {
        $run = $this->getAffinityRun($run_id);
        if (!$run || (int) $run['app_id'] !== (int) $app_id) {
            return null;
        }

        $batch_pairs = (int) $batch_pairs;
        if ($batch_pairs <= 0) {
            $batch_pairs = 20;
        }
        if ($batch_pairs > 200) {
            $batch_pairs = 200;
        }

        $eligible_ids = $this->getEligibleReferrerIds($app_id);
        $eligible_ids = array_values($eligible_ids);
        $total_referrers = count($eligible_ids);
        $total_pairs_estimate = ($total_referrers * ($total_referrers - 1)) / 2;

        $pairs = [];
        $status = $run['status'];
        $cursor_i = (int) $run['cursor_i'];
        $cursor_j = (int) $run['cursor_j'];
        $processed_pairs = (int) $run['processed_pairs'];

        if ($total_referrers < 2) {
            $status = 'completed';
            $cursor_i = 0;
            $cursor_j = 0;
        } else {
            $i = $cursor_i;
            $j = $cursor_j;
            $generated = 0;
            $max_i = $total_referrers - 1;
            while ($i < $max_i && $generated < $batch_pairs) {
                if ($j <= $i) {
                    $j = $i + 1;
                }
                if ($j >= $total_referrers) {
                    $i++;
                    $j = $i + 1;
                    continue;
                }
                $pairs[] = [
                    'a' => $eligible_ids[$i],
                    'b' => $eligible_ids[$j],
                    'i' => $i,
                    'j' => $j,
                ];
                $generated++;
                $j++;
            }
            $processed_pairs += $generated;
            $cursor_i = $i;
            $cursor_j = $j;
            if ($cursor_i >= $max_i) {
                $status = 'completed';
            } else {
                $status = 'running';
            }
        }

        $this->updateAffinityRun($run_id, [
            'status' => $status,
            'cursor_i' => $cursor_i,
            'cursor_j' => $cursor_j,
            'total_referrers' => $total_referrers,
            'total_pairs_estimate' => $total_pairs_estimate,
            'processed_pairs' => $processed_pairs,
        ]);

        return [
            'pairs' => $pairs,
            'batch_pairs' => $batch_pairs,
            'status' => $status,
            'cursor_i' => $cursor_i,
            'cursor_j' => $cursor_j,
            'processed_pairs' => $processed_pairs,
            'total_pairs_estimate' => $total_pairs_estimate,
            'total_referrers' => $total_referrers,
        ];
    }
}
