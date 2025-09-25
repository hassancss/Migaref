<?php
class Migareference_FollowupstatsController extends Application_Controller_Default{

    public function followupAction() {
        $this->loadPartials();
    }

	public function followupstatsAction() {
		if ($data = $this->getRequest()->getPost()) {
			try {           
				$app_id          =  $data['app_id'];                
				$range_filter    =  $data['range_filter'];                                                        
				$agent_id        =  $data['agent_id'];              
				$date_range      = $data["date_range_params"];                                          
				$referrerstats   = new Migareference_Model_Referrerstats();                
				$migareference   = new Migareference_Model_Migareference();  
				$stats = new Migareference_Model_Stats();              
				$pre_settings    = $migareference->preReportsettigns($app_id);                                                    
				$tot_params      = $this->formateStatParams(6,'','');                    
				$params          = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']); 
				$no_vairations   = "Prev. Var. <span class='var-nochange'>N/A </i></span>";    

				if (true) {                                                             
					$range_graph_reminders = $referrerstats->totalGraphRemindersAgent($app_id, $agent_id, $params['from_date'], $params['to_date'], $params['label_formate'], $params['group_by_foramt']);
				}   

				if (true) {                                                                                
					$range_count_rem = $referrerstats->totalCountRemidersAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                                    
					$var_range_count_rem = $referrerstats->totalCountRemidersAgent($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']);                                                                    
					// Managemtn days
					$range_count_rem[0]['management'] = $this->kpiForamte($range_count_rem[0]['total_management'],$range_count_rem[0]['total_rem_management']);                                     
					$var_range_count_rem[0]['management'] = $this->kpiForamte($var_range_count_rem[0]['total_management'],$var_range_count_rem[0]['total_rem_management']);                                     
					// KPIS
					$range_count_rem[0]['kpi'] = $this->kpiForamte($range_count_rem[0]['total_reminders'],$range_count_rem[0]['warrnings']);                                     
					$var_range_count_rem[0]['kpi'] = $this->kpiForamte($var_range_count_rem[0]['total_reminders'],$var_range_count_rem[0]['warrnings']);                                     
					// Variations
					$var_range_count_rem[0]['var_total_canceled']   = $this->variationForamte($var_range_count_rem[0]['total_canceled'],$range_count_rem[0]['total_canceled']);
					$var_range_count_rem[0]['var_total_reminders']   = $this->variationForamte($var_range_count_rem[0]['total_reminders'],$range_count_rem[0]['total_reminders']);
					$range_count_rem[0]['total_processed'] = ($range_count_rem[0]['total_reminders'] - $range_count_rem[0]['total_fallback']);
					$var_range_count_rem[0]['var_total_processed'] = $this->variationForamte(($var_range_count_rem[0]['total_reminders'] - $var_range_count_rem[0]['total_fallback']), $range_count_rem[0]['total_processed']);
					$var_range_count_rem[0]['var_total_fallback'] = $this->variationForamte($var_range_count_rem[0]['total_fallback'],$range_count_rem[0]['total_fallback']);                
					$var_range_count_rem[0]['var_warrnings'] = $this->variationForamte($var_range_count_rem[0]['warrnings'],$range_count_rem[0]['warrnings']);                
					$var_range_count_rem[0]['var_done'] = $this->variationForamte($var_range_count_rem[0]['done'],$range_count_rem[0]['done']);                
					$var_range_count_rem[0]['var_potponed']   = $this->variationForamte($var_range_count_rem[0]['potponed'],$range_count_rem[0]['potponed']);                
					$var_range_count_rem[0]['var_management']    = $this->variationForamte($var_range_count_rem[0]['management'],$range_count_rem[0]['management']);                
					$var_range_count_rem[0]['var_kpi']     = $this->variationForamte($var_range_count_rem[0]['kpi'],$range_count_rem[0]['kpi']);                
				}      
				
				if (true) {
					$range_count_rem[0]['avg_processed'] = ($range_count_rem[0]['total_reminders'] > 0 && $range_count_rem[0]['total_processed'] > 0) ? number_format(($range_count_rem[0]['total_processed'] / $this->__countDaysBetweenDates($params['from_date'], $params['to_date'], $pre_settings[0]['working_days'] == 1 ? true : false)) * 100, 2) : 0;
					$var_range_count_rem[0]['var_avg_processed'] = $no_vairations;
					if ($range_filter != 6) {
						$var_range_count_rem[0]['var_avg_processed'] = ($var_range_count_rem[0]['total_reminders'] > 0 && $var_range_count_rem[0]['total_processed'] > 0) ? number_format(($var_range_count_rem[0]['total_processed'] / $this->__countDaysBetweenDates($params['from_date'], $params['to_date'], $pre_settings[0]['working_days'] == 1 ? true : false)) * 100, 2) : 0;
						$var_range_count_rem[0]['var_avg_processed'] = $this->variationForamte($range_count_rem[0]['avg_processed'], $var_range_count_rem[0]['var_avg_processed']);
					}
				}

				if (true) {                                                             
					$range_graph_report_reminders = $stats->getReportReminders($app_id, $agent_id, $params['from_date'], $params['to_date'], $params['label_formate'], $params['group_by_foramt']);
				}

				if (true) {                                                                                
					$range_report_ref_reminders = $stats->getReportRemindersCount($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                                    
					$var_range_report_ref_reminders = $stats->getReportRemindersCount($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']); 

					// Managemtn days
					$range_report_ref_reminders[0]['management'] = $this->kpiForamte($range_report_ref_reminders[0]['total_management'],$range_report_ref_reminders[0]['total_rem_management']);                                     
					$var_range_report_ref_reminders[0]['management'] = $this->kpiForamte($var_range_report_ref_reminders[0]['total_management'],$var_range_report_ref_reminders[0]['total_rem_management']);                                     
					// KPIS
					$range_report_ref_reminders[0]['kpi'] = $this->kpiForamte($range_report_ref_reminders[0]['total_reminders'],$range_report_ref_reminders[0]['potponed']);                                     
					$var_range_report_ref_reminders[0]['kpi'] = $this->kpiForamte($var_range_report_ref_reminders[0]['total_reminders'],$var_range_report_ref_reminders[0]['potponed']);                                     
					// Variations
					$var_range_report_ref_reminders[0]['var_total_canceled']   = $this->variationForamte($var_range_report_ref_reminders[0]['total_canceled'],$range_report_ref_reminders[0]['total_canceled']);
					$var_range_report_ref_reminders[0]['var_total_reminders']   = $this->variationForamte($var_range_report_ref_reminders[0]['total_reminders'],$range_report_ref_reminders[0]['total_reminders']);
					$range_report_ref_reminders[0]['total_processed'] = ($range_report_ref_reminders[0]['total_reminders'] - $range_report_ref_reminders[0]['total_fallback']);
					$var_range_report_ref_reminders[0]['var_total_processed'] = $this->variationForamte(($var_range_report_ref_reminders[0]['total_reminders'] - $var_range_report_ref_reminders[0]['total_fallback']), $range_report_ref_reminders[0]['total_processed']);
					$var_range_report_ref_reminders[0]['var_total_fallback'] = $this->variationForamte($var_range_report_ref_reminders[0]['total_fallback'],$range_report_ref_reminders[0]['total_fallback']);                
					$var_range_report_ref_reminders[0]['var_warrnings'] = $this->variationForamte($var_range_report_ref_reminders[0]['warrnings'],$range_report_ref_reminders[0]['warrnings']);                
					$var_range_report_ref_reminders[0]['var_done'] = $this->variationForamte($var_range_report_ref_reminders[0]['done'],$range_report_ref_reminders[0]['done']);                
					$var_range_report_ref_reminders[0]['var_potponed']   = $this->variationForamte($var_range_report_ref_reminders[0]['potponed'],$range_report_ref_reminders[0]['potponed']);                
					$var_range_report_ref_reminders[0]['var_management']    = $this->variationForamte($var_range_report_ref_reminders[0]['management'],$range_report_ref_reminders[0]['management']);                
					$var_range_report_ref_reminders[0]['var_kpi']     = $this->variationForamte($var_range_report_ref_reminders[0]['kpi'],$range_report_ref_reminders[0]['kpi']);
				} 
				

				$html = [
					'success'         => true,
					'range_graph_reminders' => $range_graph_reminders, 
					'range_count_reminders' => $range_count_rem,
                    'var_range_count_reminders' => $var_range_count_rem,
					'range_graph_report_reminders' => $range_graph_report_reminders,
					'range_report_ref_reminders' => $range_report_ref_reminders,
					'var_range_report_ref_reminders' => $var_range_report_ref_reminders,
					'params' => $params,
					'tot_params' => $tot_params,
					
				];
			} catch (Exception $e) {
				$html = [
					'error' => true,
					'message' => $e->getMessage(),
					'message_button' => 1,                    
					'message_loader' => 1
				];
			}

			$this->getLayout()->setHtml(Zend_Json::encode($html));
		}
	}

	private function variationForamte($previous=0,$current=0)
    {
                $variation=0;

                $divider = ($previous>$current) ? $previous : $current ;
                $divider = ($divider==0) ? 1 : $divider ;//Avoid Infinity devision by 0            
            
                $variation = ($current!=0 || $previous!=0) ? (number_format((($current-$previous)/$divider)*100,2)) : 0 ; //Calculate Variation

                if ($variation>0) {
                    $formate="Prev. Var. <span class='var-positive'>".$variation."%<i class='fa fa-arrow-up' aria-hidden='true'></i></span>";
                } elseif($variation<0) {
                    $variation=abs($variation);
                    $formate="Prev. Var. <span class='var-negative'> ".$variation."%<i class='fa fa-arrow-down' aria-hidden='true'></i></span>";                                   
                }elseif($variation==0){
                    $formate="Prev. Var. <span class='var-nochange'> ".$variation."%<i class='fa fa-stop' aria-hidden='true'></i></span>";                                   
                }
                return $formate;
    }
    private function variationIncubationForamte($previous=0,$current=0)
    {
                $variation=0;

                $divider = ($previous>$current) ? $previous : $current ;
                $divider = ($divider==0) ? 1 : $divider ;//Avoid Infinity devision by 0            
            
                $variation = ($current!=0 || $previous!=0) ? (number_format((($current-$previous)/$divider)*100,2)) : 0 ; //Calculate Variation

                if ($variation<0) {
                    $formate="Prev. Var. <span class='var-positive'>".$variation."% <i class='fa fa-arrow-up' aria-hidden='true'></i></span>";
                } elseif($variation>0) {
                    $formate="Prev. Var. <span class='var-negative'> ".$variation."% <i class='fa fa-arrow-down' aria-hidden='true'></i></span>";                                   
                }elseif($variation==0){
                    $formate="Prev. Var. <span class='var-nochange'> ".$variation."% <i class='fa fa-stop' aria-hidden='true'></i></span>";                                   
                }
                return $formate;
    }
    private function incubationForamte($total_incubation=0,$success_reports=0)
    {            
        $incubation = ($success_reports>0) ? number_format(($total_incubation/$success_reports),2) : 0 ;                
        if ($success_reports>0 && $incubation<1) {
            $incubation=number_format(1,2);
        }
        return $incubation;
    }
    private function kpiForamte($number1=0,$number2=0)
    {
                // $number1 = ($number1==0) ? 0 : $number1 ;    
        return $kpi = ($number2>0) ? number_format(($number1/$number2),2) : 0 ;                
    }

	private function formateStatParams($range_filter,$date_range_start,$date_range_end)
    {            
        $migareference       = new Migareference_Model_Migareference();                
        $from_date='';
        $to_date='';
        $label_formate='';
        $group_by_foramt='';
        switch ($range_filter) {
                    case 1: //1 Month
                        $from_date=date("Y-m-d", strtotime("-1 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-2 months"));
                        $var_to_date=date("Y-m-d", strtotime("-1 months"));
                        $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                        $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
                        break;  
					case 7: //past month
						$from_date = date("Y-m-d", strtotime("first day of previous month"));
						$to_date = date("Y-m-d", strtotime("last day of previous month"));
						$var_from_date = date("Y-m-d", strtotime($from_date.' -1 MONTH'));
						$var_to_date = date("Y-m-d", strtotime($to_date.' -1 MONTH'));
						$label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
						$group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
						break;                    
                    case 2://3 month
                        $from_date=date("Y-m-d", strtotime("-3 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-6 months"));
                        $var_to_date=date("Y-m-d", strtotime("-3 months"));
                        $label_formate="MIN(setdate) AS labels,";
                        $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                        break;                    
                    case 3://6 month
                        $from_date=date("Y-m-d", strtotime("-6 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-12 months"));
                        $var_to_date=date("Y-m-d", strtotime("-6 months"));
                        $label_formate="MIN(setdate) AS labels,";
                        $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                        break;                    
                    case 4://current Year
                        $from_date= date("Y").'-01-01';                        
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-2 months"));
                        $var_to_date=date("Y").'-01-01';                 
                        $days_in_current_year = $migareference->daysDiffrence($from_date,2);                            
                            if ($days_in_current_year<=30) {
                                $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                                $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
                            } elseif($days_in_current_year>30 && $days_in_current_year<180) {
                                $label_formate="MIN(setdate) AS labels,";
                                $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                            }elseif($days_in_current_year>180) {
                                $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M') AS labels,";
                                $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M')";
                            }                        
                        break;                    
                    case 5://12 month
                        $from_date=date("Y-m-d", strtotime("-12 months"));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date("Y-m-d", strtotime("-24 months"));
                        $var_to_date=date("Y-m-d", strtotime("-12 months"));                
                        $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M') AS labels,";
                        $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M')";
                        break;                    
                    case 6://All (The creation date of Application to today)                                                
                        $from_date=date("Y-m-d", strtotime($this->getApplication()->getCreatedAt()));
                        $to_date=date('Y-m-d', strtotime(' +1 day'));
                        $var_from_date=date('Y-m-d', strtotime(' -2 day'));
                        $var_to_date=$from_date;
                        $days_in_current_year = $migareference->daysDiffrence($from_date,2);                            
                            if ($days_in_current_year<=30) {
                                $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                                $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
                            } elseif($days_in_current_year>30 && $days_in_current_year<180) {
                                $label_formate="MIN(setdate) AS labels,";
                                $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                            }elseif($days_in_current_year>180) {
                                $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M') AS labels,";
                                $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M')";
                            }
                        break;     
                    case 100://For custom date
                        $from_date  = date('Y-m-d', $date_range_start);
                        $from_date  = date('Y-m-d', strtotime("+1 days",strtotime($from_date)));
                        $to_date    = date('Y-m-d', $date_range_end);                                                                          
                        $datediff   = $date_range_end-$date_range_start;
                        $days_diff_custom_date  = round($datediff / (60 * 60 * 24));
                        $var_from_date=date('Y-m-d', strtotime("-".$days_diff_custom_date." days",strtotime($from_date)));
                        $var_to_date=$from_date;                                         
                        if ($days_diff_custom_date<=31) {
                            $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
                            $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
                        } elseif($days_diff_custom_date>31 && $days_diff_custom_date<181) {
                            $label_formate="MIN(setdate) AS labels,";
                            $group_by_foramt="GROUP BY DATEDIFF(setdate, "."'$from_date'".") DIV 7";
                        }elseif($days_diff_custom_date>181) {
                            $label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M') AS labels,";
                            $group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M')";
                        }
                        break;                                   
                }
                $data=[ 
                        'from_date'=>$from_date,
                        'to_date'=>$to_date,
                        'label_formate'=>$label_formate,
                        'group_by_foramt'=>$group_by_foramt,
                        'var_from_date'=>$var_from_date,
                        'var_to_date'=>$var_to_date
                    ];
                return $data;
    }

	private function __countDaysBetweenDates($start_date, $end_date, $exclude_weekends = true) {
		$start = new DateTime($start_date);
		$end = new DateTime($end_date);
		// otherwise the  end date is excluded (bug?)
		$end->modify('+1 day');
		$interval = $end->diff($start);
		// total days
		$days = $interval->days;
		if ($exclude_weekends) {
			// create an iterateable period of date (P1D equates to 1 day)
			$period = new DatePeriod($start, new DateInterval('P1D'), $end);
			foreach($period as $dt) {
				$curr = $dt->format('D');
				// substract if Saturday or Sunday
				if ($curr == 'Sat' || $curr == 'Sun') {
					$days--;
				}
			}
		}

		return $days;
	}
}
?>