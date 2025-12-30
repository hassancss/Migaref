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
}
