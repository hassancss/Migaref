<?php
class Migareference_Model_Db_Table_Referrerstats extends Core_Model_Db_Table
{	
        public function setIntervals($app_id=0)
        {
                $query_option = "SELECT *
                        FROM  migareference_stats_interval                       
                        WHERE 1";
                $res_option   = $this->_db->fetchAll($query_option);        
                if (!count($res_option)) {
                        $start_date = "2019-01-01";
                        do {
                                $interval_date=date('Y-m-d', strtotime($start_date. ' + 1 days'));         
                                $start_date=$interval_date;                        
                                $data['setdate']=$start_date;
                                $this->_db->insert("migareference_stats_interval", $data);                
                        } while ($interval_date < '2028-12-31');
                }                        
        }
        // Referrer
	public function totalCountReports($app_id=0,$user_id=0,$from_date='',$to_date='')
	{
		$query_option = "SELECT 
                        COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN migareference_report.migareference_report_id END) success_reports,
                        ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                        ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                        SUM( CASE WHEN migareference_report_status.standard_type=2   THEN migareference_report.commission_fee ELSE 0 END) mandate_eran,
                        SUM(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
                        FROM migareference_report        
                        JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id=le.user_id AND migareference_report.migareference_report_id=le.report_id
                        LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id=ue.refferral_user_id AND migareference_report.migareference_report_id=ue.report_id
                        WHERE migareference_report.app_id = $app_id 
                        AND migareference_report.status=1"; 
                        $query_option.= ($user_id!=0) ? " AND migareference_report.user_id=".$user_id : " " ;
                        $query_option.=" AND migareference_report.created_at>='$from_date'
                        AND migareference_report.created_at<='$to_date'";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function totalGraphReports($app_id = 0,$user_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
	//Use $user_id param only if $user_id!=0

        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2 THEN migareference_report.migareference_report_id END) AS success_reports
                        FROM `migareference_stats_interval` 
                        LEFT JOIN migareference_report ON migareference_report.app_id = $app_id 
                        AND migareference_report.status=1 AND date(migareference_report.created_at)=migareference_stats_interval.setdate";
        $query_option.= ($user_id!=0) ? " AND migareference_report.user_id=".$user_id : " " ; 
        $query_option.="LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' ";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
	public function totalCountLandPrpct($app_id = 0,$user_id=0,$from_date='',$to_date='')
	{
	//Use $user_id param only if $user_id!=0	        
        $query_option .= "SELECT 
        SUM(IF ( migareference_invitation_logs.log_type='visit', 1, 0)) total_visit,
        SUM(IF ( migareference_invitation_logs.log_type='share', 1, 0)) total_share,
        SUM(IF ( migareference_invitation_logs.log_type='report', 1, 0)) total_report
        FROM `migareference_invitation_logs` 
        WHERE migareference_invitation_logs.app_id=$app_id";
        $query_option.= ($user_id!=0) ? " AND migareference_invitation_logs.user_id=".$user_id : " " ;
        $query_option .="AND DATE(migareference_invitation_logs.created_at)>='$from_date' 
        AND DATE(migareference_invitation_logs.created_at)<='$to_date'";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        // General
	public function totalCountReportsGnl($app_id=0,$from_date='',$to_date='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(migareference_report.created_at) >= '$from_date' AND DATE(migareference_report.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
                        COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
			COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 THEN migareference_report.migareference_report_id END) total_success_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN migareference_report.migareference_report_id END) success_reports,
			SUM( CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2 THEN migareference_report.commission_fee END) total_deal_closed_amount,
		        SUM( CASE WHEN migareference_report_status.standard_type = 3 THEN migareference_report.commission_fee END) total_commissions_paid,
                        ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) - Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                        ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                        SUM( CASE WHEN migareference_report_status.standard_type=2 THEN migareference_report.commission_fee ELSE 0 END) mandate_eran,
                        SUM(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
                        FROM migareference_report        
                        JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id=le.user_id AND migareference_report.migareference_report_id=le.report_id
                        LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id=ue.refferral_user_id AND migareference_report.migareference_report_id=ue.report_id
                        JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=migareference_report.user_id       
                        WHERE migareference_report.app_id = $app_id 
                        AND migareference_report.status=1                         
                        $date_filter";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function totalCountRemidersGnl($app_id=0,$from_date='',$to_date='')
	{
		$query_option = "SELECT 
                        COUNT(migareference_automation_log.migareference_automation_log_id) AS total_reminders,
                        COUNT( CASE WHEN migareference_automation_log.trigger_id=4  THEN migareference_automation_log.migareference_automation_log_id END) warrnings,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='Postpone'  THEN migareference_automation_log.migareference_automation_log_id END) potponed,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='done' OR migareference_automation_log.current_reminder_status='cancele'  THEN migareference_automation_log.migareference_automation_log_id END) total_rem_management,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='done'  THEN migareference_automation_log.migareference_automation_log_id END) done,
                        SUM( CASE WHEN migareference_automation_log.current_reminder_status='done' OR migareference_automation_log.current_reminder_status='cancele'  THEN DATEDIFF(date(migareference_automation_log.updated_at), date(migareference_automation_log.created_at)) ELSE 0 END) AS total_management
                        FROM `migareference_automation_log` 
                        WHERE migareference_automation_log.app_id = $app_id
                        AND migareference_automation_log.is_deleted=0
                        AND DATE(migareference_automation_log.created_at)>='$from_date'
                        AND DATE(migareference_automation_log.created_at)<='$to_date'";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function totalTblAgentsGnl($app_id=0,$from_date='',$to_date='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(migareference_report.created_at) >= '$from_date' AND DATE(migareference_report.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
						customer.customer_id,
						customer.firstname,
						customer.lastname,
						COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2 THEN migareference_report.migareference_report_id END) success_reports,
                        ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) - Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                        ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                        SUM( CASE WHEN migareference_report_status.standard_type=2 THEN migareference_report.commission_fee ELSE 0 END) mandate_eran,
						SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 THEN migareference_report.commission_fee END) total_commissions_paid,
						SUM(DISTINCT CASE WHEN migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
						FROM 
							migareference_report 
							JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status 
							LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id = le.user_id AND migareference_report.migareference_report_id = le.report_id 
							LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id = ue.refferral_user_id AND migareference_report.migareference_report_id = ue.report_id 
							JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id = migareference_report.user_id 
							LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = migareference_report.user_id
         					LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = migareference_report.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id 
							JOIN migareference_app_agents ON (migareference_app_agents.user_id = refag_one.agent_id OR migareference_app_agents.user_id = refag_two.agent_id)
							JOIN customer ON customer.customer_id = migareference_app_agents.user_id
						WHERE 
							migareference_app_agents.app_id = $app_id 
							AND migareference_report.status = 1                         
							$date_filter 
							GROUP BY migareference_app_agents.user_id";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;

		/* FROM migareference_app_agents
                        LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.sponsor_id=migareference_app_agents.user_id
                        LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id 
                        AND migareference_report.status=1                         
                        $date_filter 
                        JOIN customer ON customer.customer_id=migareference_app_agents.user_id
                        LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id=le.user_id AND migareference_report.migareference_report_id=le.report_id
                        LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id=ue.refferral_user_id AND migareference_report.migareference_report_id=ue.report_id
                        WHERE migareference_app_agents.app_id = $app_id
                        GROUP BY migareference_app_agents.user_id */


						/* FROM migareference_report	
						LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = migareference_report.user_id
         				LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = migareference_report.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id 
						JOIN customer ON (customer.customer_id = refag_one.agent_id OR customer.customer_id = refag_two.agent_id)  
						LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status 
						LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id = le.user_id AND migareference_report.migareference_report_id = le.report_id
                        LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id = ue.refferral_user_id AND migareference_report.migareference_report_id = ue.report_id 
						JOIN customer ON customer.customer_id=migareference_app_agents.user_id */
	}
	public function totalGraphReportsGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2 THEN migareference_report.migareference_report_id END) AS success_reports
                        FROM `migareference_stats_interval` 
                        LEFT JOIN migareference_report ON migareference_report.app_id = $app_id 
                        AND migareference_report.status=1 AND date(migareference_report.created_at)=migareference_stats_interval.setdate
                        LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status                        
                        LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=migareference_report.user_id       
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' ";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
	public function totalGraphRemindersGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT(migareference_automation_log.migareference_automation_log_id) AS total_reminders,
                        COUNT( CASE WHEN migareference_automation_log.trigger_id=4  THEN migareference_automation_log.migareference_automation_log_id END) warrnings,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='Postpone'  THEN migareference_automation_log.migareference_automation_log_id END) potponed
                        FROM `migareference_stats_interval` 
                        LEFT JOIN migareference_automation_log ON migareference_automation_log.app_id = $app_id AND migareference_automation_log.is_deleted=0 AND date(migareference_automation_log.created_at)=migareference_stats_interval.setdate
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' ";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
	public function totalGraphRefGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(migareference_stats_interval.setdate) >= '$from_date' AND DATE(migareference_stats_interval.setdate) <= '$to_date'" : '';
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT( mis.user_id) AS total_ref
                        FROM `migareference_stats_interval`
                        LEFT JOIN migareference_invoice_settings AS mis ON mis.app_id=$app_id AND date(mis.created_at)=migareference_stats_interval.setdate
                        LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
                        LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id 
                        LEFT JOIN customer as cs ON cs.customer_id=mis.user_id
                        WHERE ad.user_id IS NULL 
						$date_filter";
        $query_option .=" ".$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
	public function countReferrerRatGnl($app_id = 0,$from_date='',$to_date='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
			SUM(IF ( ph.rating<6, 1, 0)) all_ref,
			SUM(IF ( ph.rating=5, 1, 0)) five_star,
			SUM(IF ( ph.rating=4, 1, 0)) four_star,
			SUM(IF ( ph.rating=3, 1, 0)) three_star
			FROM migareference_invoice_settings as mis                               
			LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
			LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
			JOIN customer as cs ON cs.customer_id=mis.user_id
			WHERE ad.user_id IS NULL 
			AND mis.app_id=$app_id 
			$date_filter";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function countReferrerJobsGnl($app_id = 0,$from_date='',$to_date='')
	{
	    $date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
			COUNT(jb.job_title) job_count, job_title as labels
			FROM migareference_invoice_settings as mis                               
			LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
			LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
			LEFT JOIN migareference_jobs as jb ON jb.migareference_jobs_id=ph.job_id
			JOIN customer as cs ON cs.customer_id=mis.user_id
			WHERE ad.user_id IS NULL AND mis.app_id=$app_id 
			$date_filter 
			GROUP BY jb.migareference_jobs_id  
		ORDER BY `job_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}

	public function countReferrerSectorsGnl($app_id = 0, $from_date = '', $to_date = '')
	{
			$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';
			$query_option = "SELECT 
                COUNT(pro.profession_title) profession_count, profession_title as labels
                FROM migareference_invoice_settings as mis                               
                LEFT JOIN migareference_app_admins as ad ON ad.user_id = mis.user_id
                LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id = mis.migareference_invoice_settings_id AND ph.type = 1
                LEFT JOIN migareference_professions as pro ON pro.migareference_professions_id = ph.profession_id
                JOIN customer as cs ON cs.customer_id = mis.user_id
                WHERE ad.user_id IS NULL AND mis.app_id = $app_id 
				$date_filter 
                GROUP BY pro.migareference_professions_id  
				HAVING profession_count > 0
	        ORDER BY `profession_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function countReferrerRegionsGnl($app_id = 0,$from_date='',$to_date='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';      
         $query_option = "SELECT 
                COUNT(cu.migareference_geo_countries_id)as country_count,cu.country as labels
                FROM migareference_invoice_settings as mis                               
                JOIN migareference_geo_countries as cu ON cu.migareference_geo_countries_id=mis.address_country_id
                WHERE mis.app_id=$app_id              
                $date_filter 
				GROUP BY mis.address_country_id
	        ORDER BY `country_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function countReferrerProvincesGnl($app_id = 0,$from_date='',$to_date='')
	{
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';  
		        
        $query_option = "SELECT 
                COUNT(prov.migareference_geo_provinces_id)as country_count,prov.province as labels
                FROM migareference_invoice_settings as mis                               
                JOIN migareference_geo_provinces as prov ON prov.migareference_geo_provinces_id=mis.province_id
                WHERE mis.app_id=$app_id              
                $date_filter 
                GROUP BY mis.address_country_id
	        ORDER BY `country_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}	
	public function countReferrerAgeGnl($app_id = 0,$from_date='',$to_date='')
	{
			$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : ''; 
            $query_option = "SELECT 
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 0 AND 18
                THEN '<18'                          
                END) AS under_18,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 19 AND 35
                THEN '19-35'                          
                END) AS under_35,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 36 AND 55
                THEN '36-55'                          
                END) AS under_55,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) >55
                THEN '>55'                          
                END) AS over_55
                FROM migareference_invoice_settings as mis                               
                JOIN customer as cu ON cu.customer_id=mis.user_id
                WHERE mis.app_id=$app_id  
                $date_filter";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        // Admin/Agent Stats
    public function countReferrerRatAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(mis.created_at) >= '$from_date' AND DATE(mis.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
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
				mis.app_id=$app_id  
				$agent_filter 
				$date_filter";        
		$res_option   = $this->_db->fetchAll($query_option);        
		return $res_option;
	}
    public function countReferrerProvincesAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
		        
                $query_option = "SELECT 
                COUNT(prov.migareference_geo_provinces_id)as country_count,prov.province as labels
                FROM migareference_invoice_settings as mis                               
                JOIN migareference_geo_provinces as prov ON prov.migareference_geo_provinces_id=mis.province_id
                WHERE mis.app_id=$app_id
                AND mis.sponsor_id=$agent_id              
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'
                GROUP BY mis.address_country_id
	        ORDER BY `country_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        public function totalGraphReportsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2 THEN migareference_report.migareference_report_id END) AS success_reports
                        FROM `migareference_stats_interval` 
                        LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.app_id=$app_id AND migareference_invoice_settings.sponsor_id=$agent_id
                        LEFT JOIN migareference_report ON migareference_report.app_id = migareference_invoice_settings.app_id 
                        AND migareference_report.user_id=migareference_invoice_settings.user_id
                        AND migareference_report.status=1 AND date(migareference_report.created_at)=migareference_stats_interval.setdate
                        LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status                        
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' ";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
    public function totalGraphRemindersAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
        /* $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT(migareference_automation_log.migareference_automation_log_id) AS total_reminders,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='cancele' OR migareference_automation_log.current_reminder_status='canceled' THEN migareference_automation_log.migareference_automation_log_id END) canceled,
                        COUNT( CASE WHEN migareference_automation_log.current_reminder_status='Postpone'  THEN migareference_automation_log.migareference_automation_log_id END) potponed
                        FROM `migareference_stats_interval` 
                        LEFT JOIN migareference_automation_log ON migareference_automation_log.app_id = $app_id AND migareference_automation_log.is_deleted=0 AND date(migareference_automation_log.created_at)=migareference_stats_interval.setdate
                        LEFT JOIN migareference_invoice_settings AS mis ON mis.user_id=migareference_automation_log.user_id 
						LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = mis.user_id
						LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = mis.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id
                		JOIN customer as cs ON cs.customer_id = mis.user_id
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' 
						$agent_filter";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate'; */
		$sql = "SELECT
					$label_formate 
					COUNT(alg.migareference_automation_log_id) AS total_reminders,
					COUNT(CASE WHEN alg.current_reminder_status = 'cancele' OR alg.current_reminder_status='canceled' THEN alg.migareference_automation_log_id END) canceled,
					COUNT( CASE WHEN alg.current_reminder_status = 'Postpone' THEN alg.migareference_automation_log_id END) potponed
				FROM 
					migareference_automation_log AS alg
					JOIN migareference_invoice_settings AS inv ON inv.user_id = alg.user_id
					LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id = inv.sponsor_id
					JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id = inv.migareference_invoice_settings_id
					JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
					JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
					JOIN migareference_stats_interval ON alg.app_id = $app_id AND alg.is_deleted = 0 AND date(alg.created_at) = migareference_stats_interval.setdate 
					LEFT JOIN migareference_report AS rp ON rp.migareference_report_id = alg.report_id 
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
        public function totalCountReportsAgent($app_id=0,$agent_id=0,$from_date='',$to_date='')
	{
		$query_option = "SELECT 
                        COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
                        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN migareference_report.migareference_report_id END) success_reports,
                        ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                        ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                        SUM( CASE WHEN migareference_report_status.standard_type=2   THEN migareference_report.commission_fee ELSE 0 END) mandate_eran,
                        SUM(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
                        FROM migareference_report        
                        JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id=le.user_id AND migareference_report.migareference_report_id=le.report_id
                        LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id=ue.refferral_user_id AND migareference_report.migareference_report_id=ue.report_id
                        JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=migareference_report.user_id AND migareference_invoice_settings.sponsor_id=$agent_id AND migareference_invoice_settings.app_id=$app_id      
                        WHERE migareference_report.app_id = migareference_invoice_settings.app_id 
                        AND migareference_report.status=1                         
                        AND DATE(migareference_report.created_at)>='$from_date'
                        AND DATE(migareference_report.created_at)<='$to_date'";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
    public function totalCountRemidersAgent($app_id=0,$agent_id=0,$from_date='',$to_date='')
	{
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
					migareference_automation_log AS alg
					JOIN migareference_invoice_settings AS inv ON inv.user_id = alg.user_id
					LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id = inv.sponsor_id
					JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id = inv.migareference_invoice_settings_id
					JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
					JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
					LEFT JOIN migareference_report AS rp ON rp.migareference_report_id = alg.report_id 
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
        public function totalGraphRefAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
	{
		
        $query_option = "SELECT ".$label_formate;
        $query_option .= "COUNT( mis.user_id) AS total_ref
                        FROM `migareference_stats_interval`
                        LEFT JOIN migareference_invoice_settings AS mis ON mis.app_id=$app_id AND date(mis.created_at)=migareference_stats_interval.setdate AND mis.sponsor_id=$agent_id
                        LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
                        LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id 
                        LEFT JOIN customer as cs ON cs.customer_id=mis.user_id
                        WHERE migareference_stats_interval.setdate>='$from_date' AND migareference_stats_interval.setdate<='$to_date' AND ad.user_id IS NULL ";
        $query_option .=$group_by_foramt;
        $query_option .=' ORDER BY migareference_stats_interval.setdate';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
	}
        public function countReferrerJobsAgent($app_id = 0,$agent_id=0, $from_date='',$to_date='')
	{
		        
                $query_option .= "SELECT 
                COUNT(jb.job_title) job_count, job_title as labels
                FROM migareference_invoice_settings as mis                               
                LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
                LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
                LEFT JOIN migareference_jobs as jb ON jb.migareference_jobs_id=ph.job_id
                JOIN customer as cs ON cs.customer_id=mis.user_id
                WHERE ad.user_id IS NULL 
                AND mis.app_id=$app_id 
                AND mis.sponsor_id=$agent_id            
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'
                GROUP BY jb.migareference_jobs_id  
	        ORDER BY `job_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        public function countReferrerRegionsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
		        
                $query_option .= "SELECT 
                COUNT(cu.migareference_geo_countries_id)as country_count,cu.country as labels
                FROM migareference_invoice_settings as mis                               
                JOIN migareference_geo_countries as cu ON cu.migareference_geo_countries_id=mis.address_country_id
                WHERE mis.app_id=$app_id              
                AND mis.sponsor_id=$agent_id            
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'
                GROUP BY mis.address_country_id
	        ORDER BY `country_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        public function countReferrerProvinceAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
                $query_option .= "SELECT 
                COUNT(prov.migareference_geo_provinces_id)as country_count,prov.province as labels
                FROM migareference_invoice_settings as mis                               
                JOIN migareference_geo_provinces as prov ON prov.migareference_geo_provinces_id=mis.province_id
                WHERE mis.app_id=$app_id              
                AND mis.sponsor_id=$agent_id            
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'
                GROUP BY mis.address_country_id
	        ORDER BY `country_count` DESC LIMIT 10";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
        public function agentReferrersTblAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
                $query_option .= "SELECT mis.*,cu.email,cu.mobile,DATE(mis.created_at) as created,
                COUNT(DISTINCT rep.migareference_report_id) AS total_reports,
                COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2   THEN rep.migareference_report_id END) success_reports,
                ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                SUM( CASE WHEN migareference_report_status.standard_type=2   THEN rep.commission_fee ELSE 0 END) mandate_eran,
                SUM(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN DATEDIFF(date(rep.last_modification_at), date(rep.created_at)) ELSE 0 END) AS total_incubation
                FROM migareference_invoice_settings AS mis
                LEFT JOIN migareference_report AS rep ON rep.user_id=mis.user_id
                JOIN customer as cu ON cu.customer_id=mis.user_id
                LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=rep.currunt_report_status
                LEFT JOIN migarefrence_ledger AS le ON rep.user_id=le.user_id AND rep.migareference_report_id=le.report_id
                LEFT JOIN migareference_user_earnings AS ue ON rep.user_id=ue.refferral_user_id AND rep.migareference_report_id=ue.report_id                
                WHERE mis.app_id=$app_id 
                AND mis.sponsor_id=$agent_id
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'
                GROUP BY mis.user_id";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
	public function countReferrerAgeAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
	{
		        
                $query_option .= "SELECT 
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 0 AND 18
                THEN '<18'                          
                END) AS under_18,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 19 AND 35
                THEN '19-35'                          
                END) AS under_35,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) BETWEEN 36 AND 55
                THEN '36-55'                          
                END) AS under_55,
                COUNT( CASE WHEN TIMESTAMPDIFF(YEAR, DATE_FORMAT(FROM_UNIXTIME(cu.`birthdate`), '%Y-%m-%d'), CURDATE()) >55
                THEN '>55'                          
                END) AS over_55
                FROM migareference_invoice_settings as mis                               
                JOIN customer as cu ON cu.customer_id=mis.user_id
                WHERE mis.app_id=$app_id  
                AND mis.sponsor_id=$agent_id            
                AND DATE(mis.created_at)>='$from_date' 
                AND DATE(mis.created_at)<='$to_date'";        
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}

	public function totalCountReportsGnlByRating($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '')
	{
		$agent_filter = !empty($agent_id) && !is_null($agent_id) ? "AND (refag_one.agent_id = " . $agent_id . " OR refag_two.agent_id = " . $agent_id . ")"  : '';
		$date_filter = !empty($from_date) && !is_null($from_date) && !empty($to_date) && !is_null($to_date) ? "AND DATE(migareference_report.created_at) >= '$from_date' AND DATE(migareference_report.created_at) <= '$to_date'" : '';
		$query_option = "SELECT 
						COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
						COUNT(DISTINCT CASE WHEN ph.rating = 5 THEN migareference_report.migareference_report_id END) five_star_total_reports,
						COUNT(DISTINCT CASE WHEN ph.rating = 4 THEN migareference_report.migareference_report_id END) four_star_total_reports,
						COUNT(DISTINCT CASE WHEN ph.rating = 3 THEN migareference_report.migareference_report_id END) three_star_total_reports,
						COUNT(DISTINCT CASE WHEN ph.rating = 3 AND ph.type = 1 AND migareference_report.user_id IS NOT NULL THEN migareference_report.user_id ELSE 0 END) AS three_star_total_report_ref,
						COUNT(DISTINCT CASE WHEN ph.rating = 3 AND ph.type = 1 AND migareference_report.user_id IS NULL THEN migareference_report.user_id ELSE 0 END) AS three_star_total_report_na_ref,
						COUNT(DISTINCT CASE WHEN ph.rating = 4 AND ph.type = 1 AND migareference_report.user_id IS NOT NULL THEN migareference_report.user_id ELSE 0 END) AS four_star_total_report_ref,
						COUNT(DISTINCT CASE WHEN ph.rating = 4 AND ph.type = 1 AND migareference_report.user_id IS NULL THEN migareference_report.user_id ELSE 0 END) AS four_star_total_report_na_ref,
						COUNT(DISTINCT CASE WHEN ph.rating = 5 AND ph.type = 1 AND migareference_report.user_id IS NOT NULL THEN migareference_report.user_id ELSE 0 END) AS five_star_total_report_ref,
						COUNT(DISTINCT CASE WHEN ph.rating = 5 AND ph.type = 1 AND migareference_report.user_id IS NULL THEN migareference_report.user_id ELSE 0 END) AS five_star_total_report_na_ref,
						COUNT(DISTINCT CASE WHEN ph.rating < 6 AND ph.type = 1 AND migareference_report.user_id IS NOT NULL THEN migareference_report.user_id ELSE 0 END) AS total_report_ref,
						SUM(IF (ph.rating < 6 AND ph.type = 1 AND migareference_report.user_id IS NULL, 1, 0)) total_report_na_ref,
						SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 3 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS three_star_total_incubation,
						SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 3 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS three_star_total_incubation,
						COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2) AND ph.rating = 3 THEN migareference_report.migareference_report_id END) three_star_success_reports,
						SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 4 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS four_star_total_incubation,
						COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2) AND ph.rating = 4 THEN migareference_report.migareference_report_id END) four_star_success_reports,
						SUM(DISTINCT CASE WHEN (migareference_report_status.standard_type = 3 OR migareference_report_status.standard_type = 2) AND ph.rating = 5 THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS five_star_total_incubation,
						COUNT(DISTINCT CASE WHEN (migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2) AND ph.rating = 5 THEN migareference_report.migareference_report_id END) five_star_success_reports,
                                                COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2 THEN migareference_report.migareference_report_id END) success_reports,
                                                ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
                                                ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn,
                                                SUM( CASE WHEN migareference_report_status.standard_type=2   THEN migareference_report.commission_fee ELSE 0 END) mandate_eran,
                                                SUM(DISTINCT CASE WHEN migareference_report_status.standard_type=3 OR migareference_report_status.standard_type=2  THEN DATEDIFF(date(migareference_report.last_modification_at), date(migareference_report.created_at)) ELSE 0 END) AS total_incubation
                                                FROM migareference_report 
                                                JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status 
                                                LEFT JOIN migarefrence_ledger AS le ON migareference_report.user_id = le.user_id AND migareference_report.migareference_report_id = le.report_id 
                                                LEFT JOIN migareference_user_earnings AS ue ON migareference_report.user_id = ue.refferral_user_id AND migareference_report.migareference_report_id = ue.report_id 
                                                JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id = migareference_report.user_id 
                                                LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id = migareference_report.user_id
         					LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id = migareference_report.user_id && refag_two.migareference_referrer_agents_id != refag_one.migareference_referrer_agents_id 
                                                LEFT JOIN migareference_app_agents ON (migareference_app_agents.user_id = refag_one.agent_id OR migareference_app_agents.user_id = refag_two.agent_id)
                                                -- JOIN customer ON customer.customer_id = migareference_app_agents.user_id
                                                LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id AND ph.type=1 
                                                WHERE migareference_report.app_id = $app_id 
                                                AND migareference_report.status=1       
						$agent_filter 
						$date_filter";
        $res_option   = $this->_db->fetchAll($query_option);        
        return $res_option;
	}
}
