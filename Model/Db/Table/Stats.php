<?php
class Migareference_Model_Db_Table_Stats extends Core_Model_Db_Table {
    public function getTotalReferrersByAgentRatingAndDate($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';
		$sql = "SELECT 
					COUNT(DISTINCT CASE WHEN ph.rating < 6 THEN mis.user_id END) all_ref,
					COUNT(DISTINCT CASE WHEN ph.rating = 5 THEN mis.user_id END) five_star,
					COUNT(DISTINCT CASE WHEN ph.rating = 4 THEN mis.user_id END) four_star,
					COUNT(DISTINCT CASE WHEN ph.rating = 3 THEN mis.user_id END) three_star
				FROM 
					migareference_invoice_settings as mis                               
                	LEFT JOIN migareference_app_admins as ad ON ad.user_id = mis.user_id
                	LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id = mis.migareference_invoice_settings_id AND ph.type = 1 
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = mis.user_id
         			LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = mis.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
                	JOIN customer as cs ON cs.customer_id = mis.user_id
                WHERE 
					ad.user_id IS NULL AND
					mis.app_id = $app_id AND 
					ph.name NOT LIKE '%*%' 
					$agent_filter 
					$date_filter";
					
		return $this->_db->fetchAll($sql);
	}

	public function getDealClosedByAgentRatingAndDate($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(migareference_report.created_at) >= '$from_date' AND DATE(migareference_report.created_at) <= '$to_date'" : '';
		$sql = "SELECT 
                    COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2 THEN migareference_report.migareference_report_id END) total_deal_closed,
					COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 5 THEN migareference_report.migareference_report_id END) five_star_total_deal_closed,
					COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 4 THEN migareference_report.migareference_report_id END) four_star_total_deal_closed,
					COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 3 THEN migareference_report.migareference_report_id END) three_star_total_deal_closed,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2 THEN migareference_report.commission_fee END) total_deal_closed_amount,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 5 THEN migareference_report.commission_fee END) five_star_total_deal_closed_amount,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 4 THEN migareference_report.commission_fee END) four_star_total_deal_closed_amount,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 3 THEN migareference_report.commission_fee END) three_star_total_deal_closed_amount,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 THEN migareference_report.commission_fee END) total_commissions_paid,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 5 THEN migareference_report.commission_fee END) five_star_total_commissions_paid,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 4 THEN migareference_report.commission_fee END) four_star_total_commissions_paid,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 3 THEN migareference_report.commission_fee END) three_star_total_commissions_paid,
					COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
					COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 THEN migareference_report.migareference_report_id END) total_success_reports,
					COUNT(DISTINCT CASE WHEN ph.rating = 5 THEN migareference_report.migareference_report_id END) AS total_reports_5s,
					COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 5 THEN migareference_report.migareference_report_id END) total_success_reports_5s,
					COUNT(DISTINCT CASE WHEN ph.rating = 4 THEN migareference_report.migareference_report_id END) AS total_reports_4s,
					COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 4 THEN migareference_report.migareference_report_id END) total_success_reports_4s,
					COUNT(DISTINCT CASE WHEN ph.rating = 3 THEN migareference_report.migareference_report_id END) AS total_reports_3s,
					COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 AND ph.rating = 3 THEN migareference_report.migareference_report_id END) total_success_reports_3s,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 3 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS three_star_total_incubation,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 4 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS four_star_total_incubation,
					SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 5 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS five_star_total_incubation,
					SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
				FROM 
					migareference_report 
					JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status 
					LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id = le.user_id AND migareference_report.migareference_report_id = le.report_id 
					LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id = ue.refferral_user_id AND migareference_report.migareference_report_id = ue.report_id 
					JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id = migareference_report.user_id 
					LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id = migareference_invoice_settings.migareference_invoice_settings_id AND ph.type = 1 
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = migareference_report.user_id
         			LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = migareference_report.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
				WHERE 
					migareference_report.app_id = $app_id 
					AND migareference_report.status = 1                         
					$agent_filter 
					$date_filter";
					
		return $this->_db->fetchAll($sql);
	}

	public function getReportsTrendByAgentRatingAndDate($app_id = 0, $agent_id = 0, $rating = 0, $from_date = '', $to_date = '', $label_format = '', $group_by_format = '') {
		$filters = [];
		if (!empty($agent_id) && !is_null($agent_id)) {
			$filters[] = "(refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")";
		}
		if (!empty($rating) && !is_null($rating)) {
			$filters[] = "ph.rating = " . $rating;
		}
		if (!empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date)) {
			$filters[] = "(migareference_stats_interval.setdate >= '$from_date' AND migareference_stats_interval.setdate <= '$to_date')";
		}

		$filter_string = '';
		if ($filters && count($filters) == 1) {
			$filter_string = implode('', $filters);
		} else if ($filters && count($filters) > 1) {
			$filter_string = implode(' AND ', $filters);
		}

		$where = ' WHERE ';
		if (empty($filter_string)) {
			$where = '';
		}
		
		$sql = "SELECT 
					$label_format 
					COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
					COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2 THEN migareference_report.migareference_report_id END) total_deal_closed
				FROM 
					migareference_stats_interval 
					LEFT JOIN migareference_report ON migareference_report.app_id = $app_id AND 
						migareference_report.status = 1 AND 
						date(migareference_report.created_at) = migareference_stats_interval.setdate 
					LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status 
					JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id = migareference_report.user_id 
					LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id = migareference_invoice_settings.migareference_invoice_settings_id AND ph.type = 1 
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = migareference_report.user_id
         			LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = migareference_report.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id 
				$where 
					$filter_string 
					$group_by_format 
					ORDER BY migareference_stats_interval.setdate";

		return $this->_db->fetchAll($sql);			
	}

	public function getReportReminders($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '', $label_formate ='', $group_by_foramt = '') {
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$sql = "SELECT
					$label_formate 
					COUNT(rm.migarefrence_reminders_id) AS total_reminders,
					COUNT(CASE WHEN rm.reminder_current_status = 'cancele' OR rm.reminder_current_status='canceled' THEN rm.migarefrence_reminders_id END) canceled,
					COUNT( CASE WHEN rm.reminder_current_status = 'Postpone' THEN rm.migarefrence_reminders_id END) potponed
				FROM 
					migarefrence_reminders AS rm 
					JOIN migareference_report as mr ON mr.migareference_report_id = rm.report_id 
					JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id = rm.event_type 
					JOIN migareference_invoice_settings AS inv ON inv.user_id = mr.user_id
					JOIN migareference_stats_interval ON rm.app_id = $app_id AND rm.is_deleted = 0 AND date(rm.created_at) = migareference_stats_interval.setdate 
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = inv.user_id
         			LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = inv.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
					JOIN customer ON customer.customer_id = inv.user_id 
				WHERE  
					(DATE(migareference_stats_interval.setdate) >= '$from_date' AND 
					DATE(migareference_stats_interval.setdate) <= '$to_date') 
					$agent_filter 
					$group_by_foramt 
					ORDER BY migareference_stats_interval.setdate";

		return $this->_db->fetchAll($sql);
	}

	public function getReportRemindersCount($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '') {
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$sql = "SELECT
					COUNT(alg.migareference_automation_log_id) AS total_reminders,
					COUNT(CASE WHEN alg.trigger_id = 4 THEN alg.migareference_automation_log_id END) warrnings,
					COUNT(CASE WHEN alg.current_reminder_status = 'cancele' OR alg.current_reminder_status = 'canceled' THEN alg.migareference_automation_log_id END) total_canceled,
					COUNT(CASE WHEN alg.current_reminder_status = 'Postpone' THEN alg.migareference_automation_log_id END) potponed,
					COUNT(CASE WHEN alg.current_reminder_status = 'done' OR alg.current_reminder_status = 'cancele' THEN alg.migareference_automation_log_id END) total_rem_management,
					COUNT(CASE WHEN alg.current_reminder_status = 'done' THEN alg.migareference_automation_log_id END) done,
                    SUM( CASE WHEN alg.current_reminder_status = 'done' OR alg.current_reminder_status='cancele' THEN DATEDIFF(date(alg.updated_at), date(alg.created_at)) ELSE 0 END) AS total_management,
					COUNT(CASE WHEN (alg.current_reminder_status = 'cancele' OR alg.current_reminder_status = 'canceled') AND cl.log_type = 'Automation' AND cl.note LIKE '%7 Days Fallback%' THEN alg.migareference_automation_log_id END) total_fallback
				FROM 
					migarefrence_reminders AS rm 
					LEFT JOIN migareference_automation_log AS alg ON rm.report_id = alg.report_id 
					JOIN migareference_report as mr ON mr.migareference_report_id = rm.report_id 
					JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id = rm.event_type 
					JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id = alg.report_reminder_auto_id
					JOIN migareference_invoice_settings AS inv ON inv.user_id = mr.user_id
					LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = inv.user_id
         			LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = inv.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
					JOIN customer ON customer.customer_id = inv.user_id 
					LEFT JOIN migareference_communication_logs AS cl ON cl.reminder_id = rmat.migarefrence_report_reminder_auto_id
				WHERE 
					alg.app_id = $app_id AND 
					alg.is_deleted = 0 AND 
					(DATE(alg.created_at) >= '$from_date' AND 
					DATE(alg.created_at) <= '$to_date') 
					$agent_filter";

        return $this->_db->fetchAll($sql);

	}
}
