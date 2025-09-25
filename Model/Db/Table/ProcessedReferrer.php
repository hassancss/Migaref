<?php

class Migareference_Model_Db_Table_ProcessedReferrer extends Core_Model_Db_Table {

    protected $_name = "migareference_processed_referrer";
	protected $_primary = "migareference_processed_referrer_id";

	public function countIstTimeProcessedReferrers($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(pr.processed_date) >= '$from_date' AND DATE(pr.processed_date) <= '$to_date'" : '';
		$query = "SELECT 
					COUNT(pr.referrer_id) AS total_processed_referrers 
					FROM migareference_invoice_settings AS mis                           
					LEFT JOIN migareference_app_admins AS ad ON ad.user_id=mis.user_id 
					LEFT JOIN migarefrence_phonebook AS ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1 
					JOIN customer as cs ON cs.customer_id=mis.user_id 
					JOIN migareference_processed_referrer AS pr ON pr.referrer_id=mis.user_id 
					LEFT JOIN migareference_report AS rep ON rep.user_id = mis.user_id
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = rep.user_id
					LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = rep.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
					WHERE
					mis.app_id = $app_id  
					$agent_filter
					$date_filter 
					GROUP by mis.user_id";     
		$results = $this->_db->fetchAll($query);
		$data[] = ['total_processed_referrers' => count($results)];
		return $data;
	}

	public function countIstTimeProcessedReferrersForCharts($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '', $label_format = '', $group_by_foramt = '') {
		$processed_referrers_all = [];
		$label_range = str_replace('pr.processed_date', 'd.date', $label_format);
		$group_by_range = str_replace('pr.processed_date', 'd.date', $group_by_foramt);
		$range_query = "WITH recursive dates AS (
							SELECT DATE('$from_date') AS date
							UNION ALL
							SELECT date + INTERVAL 1 DAY
							FROM dates
							WHERE date < '$to_date'
						)
						SELECT 
							$label_range
							COUNT(pr.processed_date) AS total_processed_referrers
							FROM dates d 
							LEFT JOIN migareference_processed_referrer pr ON pr.processed_date >= d.date AND pr.processed_date < d.date + INTERVAL 1 DAY AND pr.app_id = $app_id 
							$group_by_range 
							ORDER BY d.date";
		$range_processed_referrers = $this->_db->fetchAll($range_query);

		if (count($range_processed_referrers)) {
			foreach ($range_processed_referrers as $range_processed_referrer) {
				$processed_referrers_all[] = [
					'label' => $range_processed_referrer['labels'],
					'total_processed_referrers' => 0,
				];
			}

			$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
			$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(pr.processed_date) >= '$from_date' AND DATE(pr.processed_date) <= '$to_date'" : '';

			$processed_query = "SELECT ". $label_format;
			$processed_query .= "COUNT(DISTINCT CASE WHEN ph.type=1 THEN pr.referrer_id END) AS total_processed_referrers 
						FROM migareference_invoice_settings AS mis                           
						LEFT JOIN migareference_app_admins AS ad ON ad.user_id=mis.user_id 
						LEFT JOIN migarefrence_phonebook AS ph ON ph.invoice_id=mis.migareference_invoice_settings_id 
						LEFT JOIN customer as cs ON cs.customer_id=mis.user_id 
						LEFT JOIN migareference_processed_referrer AS pr ON pr.referrer_id=mis.user_id 
						LEFT JOIN migareference_report AS rep ON rep.user_id = mis.user_id
						LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = rep.user_id
						LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = rep.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
						WHERE  
						mis.app_id = $app_id  
						$agent_filter
						$date_filter"; 
			$processed_query .= $group_by_foramt;    
			$processed_query .=' ORDER BY pr.processed_date';	
			$processed_referrers = $this->_db->fetchAll($processed_query);
			if (count($processed_referrers)) {
				foreach ($processed_referrers as $processed_referrer) {
					foreach ($processed_referrers_all as $key => $processed_referrer_all) {
						if ($processed_referrer_all['label'] == $processed_referrer['labels']) {
							$processed_referrers_all[$key]['total_processed_referrers'] = $processed_referrer['total_processed_referrers'];
						}
					}
				}
			}
		}

		return $processed_referrers_all;
	}
}