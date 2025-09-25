<?php
class Migareference_ReportstatsController extends Application_Controller_Default {

    public function reportAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
	
    public function reportstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
			try { 
				$app_id          =  $data['app_id'];                
				$range_filter    =  $data['range_filter'];                                                        
				$agent_id        =  $data['agent_id'];              
				$date_range      = $data["date_range_params"];                                          
				$referrerstats   = new Migareference_Model_Referrerstats();                
				$migareference   = new Migareference_Model_Migareference();                
				$pre_settings    = $migareference->preReportsettigns($app_id);                                                    
				$tot_params      = $this->formateStatParams(6,'','');                    
				$params          = $this->formateStatParams($range_filter,$date_range['start'],$date_range['end']);
				$no_vairations       = "Prev. Var. <span class='var-nochange'>N/A </i></span>";

				$stats = new Migareference_Model_Stats();
				if (true) {//1. Total Referrers by stars                                                               
					$range_count_ref     = $stats->getTotalReferrersByAgentRatingAndDate($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
					$var_range_count_ref = $stats->getTotalReferrersByAgentRatingAndDate($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']);                  
					// KPIS                                
					$range_count_ref[0]['kpi']                 = $this->kpiForamte(($range_count_ref[0]['five_star']+$range_count_ref[0]['four_star']+$range_count_ref[0]['three_star']),$range_count_ref[0]['all_ref']);                
					$var_range_count_ref[0]['kpi']                 = $this->kpiForamte(($var_range_count_ref[0]['five_star']+$var_range_count_ref[0]['four_star']+$var_range_count_ref[0]['three_star']),$var_range_count_ref[0]['all_ref']);                                        
					// Variations
					$var_range_count_ref[0]['var_all_ref']     = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['all_ref'],$range_count_ref[0]['all_ref']);
					$var_range_count_ref[0]['var_five_star']   = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['five_star'],$range_count_ref[0]['five_star']);                                
					$var_range_count_ref[0]['var_four_star']   = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['four_star'],$range_count_ref[0]['four_star']);                
					$var_range_count_ref[0]['var_three_star']  = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['three_star'],$range_count_ref[0]['three_star']);                
					$var_range_count_ref[0]['var_kpi']         = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['kpi'],$range_count_ref[0]['kpi']);                
				}

				if (true) {//2. Total Reports by stars                                                               
					$range_count_report = $referrerstats->totalCountReportsGnlByRating($app_id, $agent_id, $params['from_date'], $params['to_date']);                                                
					$var_range_count_report = $referrerstats->totalCountReportsGnlByRating($app_id, $agent_id, $params['var_from_date'] ,$params['var_to_date']);                                                         
					// Variations
					$var_range_count_report[0]['var_rep_stats_count_range_total_reports'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_report[0]['total_reports'],$range_count_report[0]['total_reports']);
					$var_range_count_report[0]['var_rep_stats_count_range_5s_reports']   = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_report[0]['five_star_total_reports'],$range_count_report[0]['five_star_total_reports']);                                
					$var_range_count_report[0]['var_rep_stats_count_range_4s_reports']   = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_report[0]['four_star_total_reports'],$range_count_report[0]['four_star_total_reports']);                
					$var_range_count_report[0]['var_rep_stats_count_range_3s_reports']  = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_report[0]['three_star_total_reports'],$range_count_report[0]['three_star_total_reports']); 
					//3. Incubation Days stars
					$range_count_report[0]['three_star_incubation'] = $this->incubationForamte($range_count_report[0]['three_star_total_incubation'],$range_count_report[0]['three_star_success_reports']); 
					$var_range_count_report[0]['three_star_incubation'] = $this->incubationForamte($var_range_count_report[0]['three_star_total_incubation'],$var_range_count_report[0]['three_star_success_reports']); 
					$var_range_count_report[0]['three_star_incubation'] = ($range_filter==6) ? $no_vairations :  $this->variationIncubationForamte($var_range_count_report[0]['three_star_incubation'], $range_count_report[0]['three_star_incubation']);
					$range_count_report[0]['four_star_incubation'] = $this->incubationForamte($range_count_report[0]['four_star_total_incubation'],$range_count_report[0]['four_star_success_reports']); 
					$var_range_count_report[0]['four_star_incubation'] = $this->incubationForamte($var_range_count_report[0]['four_star_total_incubation'],$var_range_count_report[0]['four_star_success_reports']); 
					$var_range_count_report[0]['four_star_incubation'] = ($range_filter==6) ? $no_vairations :  $this->variationIncubationForamte($var_range_count_report[0]['four_star_incubation'], $range_count_report[0]['four_star_incubation']);
					$range_count_report[0]['five_star_incubation'] = $this->incubationForamte($range_count_report[0]['five_star_total_incubation'],$range_count_report[0]['five_star_success_reports']); 
					$var_range_count_report[0]['five_star_incubation'] = $this->incubationForamte($var_range_count_report[0]['five_star_total_incubation'],$var_range_count_report[0]['five_star_success_reports']); 
					$var_range_count_report[0]['five_star_incubation'] = ($range_filter==6) ? $no_vairations :  $this->variationIncubationForamte($var_range_count_report[0]['five_star_incubation'], $range_count_report[0]['five_star_incubation']);
					$range_count_report[0]['total_incubation'] = $this->incubationForamte($range_count_report[0]['total_incubation'],$range_count_report[0]['success_reports']); 
					$var_range_count_report[0]['total_incubation'] = $this->incubationForamte($var_range_count_report[0]['total_incubation'],$var_range_count_report[0]['success_reports']); 
					$var_range_count_report[0]['total_incubation'] = ($range_filter==6) ? $no_vairations :  $this->variationIncubationForamte($var_range_count_report[0]['total_incubation'], $range_count_report[0]['total_incubation']);
					//4. KPI Report Conv.
					$range_count_report[0]['three_star_conv_kpi'] = $this->kpiForamte($range_count_report[0]['three_star_success_reports'],$range_count_report[0]['three_star_total_reports']);
					$var_range_count_report[0]['three_star_conv_kpi'] = $this->kpiForamte($var_range_count_report[0]['three_star_success_reports'],$var_range_count_report[0]['three_star_total_reports']);
					$var_range_count_report[0]['var_rep_stats_range_3s_conv_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['three_star_conv_kpi'],$range_count_report[0]['three_star_conv_kpi']);  
					$range_count_report[0]['four_star_conv_kpi'] = $this->kpiForamte($range_count_report[0]['four_star_success_reports'],$range_count_report[0]['four_star_total_reports']);
					$var_range_count_report[0]['four_star_conv_kpi'] = $this->kpiForamte($var_range_count_report[0]['four_star_success_reports'],$var_range_count_report[0]['four_star_total_reports']);
					$var_range_count_report[0]['var_rep_stats_range_4s_conv_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['four_star_conv_kpi'],$range_count_report[0]['four_star_conv_kpi']); 
					$range_count_report[0]['five_star_conv_kpi'] = $this->kpiForamte($range_count_report[0]['five_star_success_reports'],$range_count_report[0]['five_star_total_reports']);
					$var_range_count_report[0]['five_star_conv_kpi'] = $this->kpiForamte($var_range_count_report[0]['five_star_success_reports'],$var_range_count_report[0]['five_star_total_reports']);
					$var_range_count_report[0]['var_rep_stats_range_5s_conv_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['five_star_conv_kpi'],$range_count_report[0]['five_star_conv_kpi']); 
					$range_count_report[0]['total_conv_kpi'] = $this->kpiForamte($range_count_report[0]['success_reports'],$range_count_report[0]['total_reports']);
					$var_range_count_report[0]['total_conv_kpi'] = $this->kpiForamte($var_range_count_report[0]['total_reports'],$var_range_count_report[0]['total_reports']);
					$var_range_count_report[0]['var_rep_stats_range_total_conv_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['total_conv_kpi'],$range_count_report[0]['total_conv_kpi']); 
					//5. Avg Rep. x Active Ref.
					$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_3s_kpi'] = $this->kpiForamte($range_count_report[0]['three_star_total_reports'], $range_count_report[0]['three_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_3s_kpi'] = $this->kpiForamte($var_range_count_report[0]['three_star_total_reports'], $var_range_count_report[0]['three_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_3s_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_3s_kpi'],$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_3s_kpi']); 
					$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_4s_kpi'] = $this->kpiForamte($range_count_report[0]['four_star_total_reports'], $range_count_report[0]['four_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_4s_kpi'] = $this->kpiForamte($var_range_count_report[0]['four_star_total_reports'], $var_range_count_report[0]['four_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_4s_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_4s_kpi'],$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_4s_kpi']); 
					$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_5s_kpi'] = $this->kpiForamte($range_count_report[0]['five_star_total_reports'], $range_count_report[0]['five_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_5s_kpi'] = $this->kpiForamte($var_range_count_report[0]['five_star_total_reports'], $var_range_count_report[0]['five_star_total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_5s_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_5s_kpi'],$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_5s_kpi']); 
					$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_all_kpi'] = $this->kpiForamte($range_count_report[0]['total_reports'], $range_count_report[0]['total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_all_kpi'] = $this->kpiForamte($var_range_count_report[0]['total_reports'], $var_range_count_report[0]['total_report_ref']);
					$var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_all_kpi'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_report[0]['var_rep_stats_range_avg_rep_x_active_ref_all_kpi'],$range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_all_kpi']); 
					//6. Expected Reports
					if ((in_array($range_filter, [5, 7, 8])) || ($range_filter == 100 && $params['to_date'] < date('Y-m-d'))) {
						$range_count_report[0]['rep_stats_range_expected_reports_3s_kpi'] = 'N/A';
						$range_count_report[0]['rep_stats_range_expected_reports_4s_kpi'] = 'N/A';
						$range_count_report[0]['rep_stats_range_expected_reports_5s_kpi'] = 'N/A';
						$range_count_report[0]['rep_stats_range_expected_reports_all_kpi'] = 'N/A';
					} else {
						$range_count_report[0]['rep_stats_range_expected_reports_3s_kpi'] = round(($range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_3s_kpi'] * $range_count_report[0]['three_star_total_report_na_ref']));
						$range_count_report[0]['rep_stats_range_expected_reports_4s_kpi'] = round(($range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_4s_kpi'] * $range_count_report[0]['four_star_total_report_na_ref']));
						$range_count_report[0]['rep_stats_range_expected_reports_5s_kpi'] = round(($range_count_report[0]['rep_stats_range_avg_rep_x_active_ref_5s_kpi'] * $range_count_report[0]['five_star_total_report_na_ref']));
						$range_count_report[0]['rep_stats_range_expected_reports_all_kpi'] = $range_count_report[0]['total_report_na_ref'];
					}

				}

				//Report Performances Statistics (START)
				if (true) { 
					$var_range_count_report[0]['var_rps_stats_3s_report'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['three_star_total_reports'], $range_count_report[0]['three_star_total_reports']);
					$var_range_count_report[0]['var_rps_stats_4s_report'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['four_star_total_reports'], $range_count_report[0]['four_star_total_reports']);
					$var_range_count_report[0]['var_rps_stats_5s_report'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['five_star_total_reports'], $range_count_report[0]['five_star_total_reports']);
					$var_range_count_report[0]['var_rps_stats_total_report'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_ref[0]['total_reports'], $range_count_report[0]['total_reports']);
				}

				if (true) {
					$range_count_deal_closed     = $stats->getDealClosedByAgentRatingAndDate($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
					$var_range_count_deal_closed = $stats->getDealClosedByAgentRatingAndDate($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']); 

					$var_range_count_deal_closed[0]['var_rps_stats_total_no_of_deal_closed'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['total_deal_closed'], $range_count_deal_closed[0]['total_deal_closed']);
					$var_range_count_deal_closed[0]['var_rps_stats_five_star_total_no_of_deal_closed'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['five_star_total_deal_closed'], $range_count_deal_closed[0]['five_star_total_deal_closed']);
					$var_range_count_deal_closed[0]['var_rps_stats_four_star_total_no_of_deal_closed'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['four_star_total_deal_closed'], $range_count_deal_closed[0]['four_star_total_deal_closed']);
					$var_range_count_deal_closed[0]['var_rps_stats_three_star_total_no_of_deal_closed'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['three_star_total_deal_closed'], $range_count_deal_closed[0]['three_star_total_deal_closed']);

					$var_range_count_deal_closed[0]['var_rps_stats_total_no_of_deal_closed_amount'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['total_deal_closed_amount'], $range_count_deal_closed[0]['total_deal_closed_amount']);
					$var_range_count_deal_closed[0]['var_rps_stats_five_star_total_no_of_deal_closed_amount'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['five_star_total_deal_closed_amount'], $range_count_deal_closed[0]['five_star_total_deal_closed_amount']);
					$var_range_count_deal_closed[0]['var_rps_stats_four_star_total_no_of_deal_closed_amount'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['four_star_total_deal_closed_amount'], $range_count_deal_closed[0]['four_star_total_deal_closed_amount']);
					$var_range_count_deal_closed[0]['var_rps_stats_three_star_total_no_of_deal_closed_amount'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['three_star_total_deal_closed_amount'], $range_count_deal_closed[0]['three_star_total_deal_closed_amount']);

					$var_range_count_deal_closed[0]['var_rps_stats_total_commissions_paid'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['total_commissions_paid'], $range_count_deal_closed[0]['total_commissions_paid']);
					$var_range_count_deal_closed[0]['var_rps_stats_five_star_total_commissions_paid'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['five_star_total_commissions_paid'], $range_count_deal_closed[0]['five_star_total_commissions_paid']);
					$var_range_count_deal_closed[0]['var_rps_stats_four_star_total_commissions_paid'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['four_star_total_commissions_paid'], $range_count_deal_closed[0]['four_star_total_commissions_paid']);
					$var_range_count_deal_closed[0]['var_rps_stats_three_star_total_commissions_paid'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte($var_range_count_deal_closed[0]['three_star_total_commissions_paid'], $range_count_deal_closed[0]['three_star_total_commissions_paid']);

					$var_range_count_deal_closed[0]['var_rps_stats_total_success_reports_avg'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte(($var_range_count_deal_closed[0]['total_reports'] != 0 ? (($var_range_count_deal_closed[0]['total_success_reports'] + $var_range_count_deal_closed[0]['total_deal_closed']) / $var_range_count_deal_closed[0]['total_reports']) : 0), ($range_count_deal_closed[0]['total_reports'] != 0 ? (($range_count_deal_closed[0]['total_success_reports'] + $range_count_deal_closed[0]['total_deal_closed']) / $range_count_deal_closed[0]['total_reports']) : 0));
					
					$var_range_count_deal_closed[0]['var_rps_stats_total_success_reports_5s_avg'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte(
						($var_range_count_deal_closed[0]['total_reports_5s'] != 0 ? 
							(($var_range_count_deal_closed[0]['total_success_reports_5s'] + $var_range_count_deal_closed[0]['five_star_total_deal_closed']) / $var_range_count_deal_closed[0]['total_reports_5s']) : 0), 
						($range_count_deal_closed[0]['total_reports_5s'] != 0 ? 
							(($range_count_deal_closed[0]['total_success_reports_5s'] + $range_count_deal_closed[0]['five_star_total_deal_closed']) / $range_count_deal_closed[0]['total_reports_5s']) : 0)
					);
					
					$var_range_count_deal_closed[0]['var_rps_stats_total_success_reports_4s_avg'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte(
						($var_range_count_deal_closed[0]['total_reports_4s'] != 0 ? 
							(($var_range_count_deal_closed[0]['total_success_reports_4s'] + $var_range_count_deal_closed[0]['four_star_total_deal_closed']) / $var_range_count_deal_closed[0]['total_reports_4s']) : 0), 
						($range_count_deal_closed[0]['total_reports_4s'] != 0 ? 
							(($range_count_deal_closed[0]['total_success_reports_4s'] + $range_count_deal_closed[0]['four_star_total_deal_closed']) / $range_count_deal_closed[0]['total_reports_4s']) : 0)
					);
					
					$var_range_count_deal_closed[0]['var_rps_stats_total_success_reports_3s_avg'] = ($range_filter == 6) ? $no_vairations : $this->variationForamte(
						($var_range_count_deal_closed[0]['total_reports_3s'] != 0 ? 
							(($var_range_count_deal_closed[0]['total_success_reports_3s'] + $var_range_count_deal_closed[0]['three_star_total_deal_closed']) / $var_range_count_deal_closed[0]['total_reports_3s']) : 0), 
						($range_count_deal_closed[0]['total_reports_3s'] != 0 ? 
							(($range_count_deal_closed[0]['total_success_reports_3s'] + $range_count_deal_closed[0]['three_star_total_deal_closed']) / $range_count_deal_closed[0]['total_reports_3s']) : 0)
					);
					
				}
				//Report Performances Statistics (END)

				//Reports Trend by Agent Rating and Date (START)
				if (true) {
					$params_chart = $this->formateStatParamsChart($range_filter, $date_range['start'], $date_range['end']);
					$reports_trend_chart = $stats->getReportsTrendByAgentRatingAndDate($app_id, $agent_id, $data['star_filter'], $params_chart['from_date'], $params_chart['to_date'], $params_chart['label_formate'], $params_chart['group_by_foramt']);
				}
				//Reports Trend by Agent Rating and Date (END)

				$html = [
					'success'         => true,
					'range_count_ref' => $range_count_ref,
					'var_range_count_ref' => $var_range_count_ref,  
					'range_count_report' => $range_count_report,
					'var_range_count_report' => $var_range_count_report,
					'range_count_deal_closed' => $range_count_deal_closed,
					'var_range_count_deal_closed' => $var_range_count_deal_closed,
					'params' => $params,
					'tot_params' => $tot_params,
					'params_chart' => $params_chart,
					'reports_trend_chart' => $reports_trend_chart
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
		$current_temp = ($previous>0 && $current==0) ? 1 : $current ;//Avoid Infinity devision by 0            
		$variation = ($current!=0 || $previous!=0) ? (number_format((($current-$previous)/$current_temp)*100,2)) : 0 ; //Calculate Variation

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
        // $number1 = ($number1==0) ? 1 : $number1 ;    
        return $kpi = ($number1>0 && $number2>0) ? number_format(($number1/$number2),2) : 0 ;                
    }
    public function agentreferrersAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $migareference   = new Migareference_Model_Migareference();                
                    $app_id          = $data['app_id'];                
                    $sponsor_id      = $data['sponsor_id'];                                    
                    if ($sponsor_id==-1) {
                        $referrers  = $migareference->get_referral_users($app_id);
                    }else {
                        $referrers       = $migareference->getAgentReferrerPhonebook($app_id,$sponsor_id);                                                
                    }
                    
                    $html = [
                        'success' => true,
                        'data'    => $referrers                        
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
    private function formateStatParams($range_filter,$date_range_start,$date_range_end)
    {
        $referrerstats       = new Migareference_Model_Referrerstats();                
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
				$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
				$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
				break;     
			case 7: //past month
				$from_date = date("Y-m-d", strtotime("first day of previous month"));
				$to_date = date("Y-m-d", strtotime("last day of previous month"));
				$var_from_date = date("Y-m-d", strtotime($from_date.' -1 MONTH'));
				$var_to_date = date("Y-m-d", strtotime($to_date.' -1 MONTH'));
				$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
				$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
				break;    
			case 8: //past week
				$d = strtotime("today");
				$start_week = strtotime("last sunday midnight", $d);
				$end_week = strtotime("next saturday", $d);
				$from_date = date("Y-m-d", $start_week); 
				$to_date = date("Y-m-d", $end_week); 

				$previous_week = strtotime("-1 week +1 day");
				$start_week = strtotime("last sunday midnight", $previous_week);
				$end_week = strtotime("next saturday", $start_week);
				$var_from_date = date("Y-m-d", $start_week);
				$var_to_date = date("Y-m-d", $end_week);
				$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
				$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
				break;                
			case 2://3 month
				$from_date=date("Y-m-d", strtotime("-3 months"));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-6 months"));
				$var_to_date=date("Y-m-d", strtotime("-3 months"));
				$label_formate="MIN(pr.processed_date) AS labels,";
				$group_by_foramt="GROUP BY DATEDIFF(pr.processed_date, "."'$from_date'".") DIV 7";
				break;                    
			case 3://6 month
				$from_date=date("Y-m-d", strtotime("-6 months"));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-12 months"));
				$var_to_date=date("Y-m-d", strtotime("-6 months"));
				$label_formate="MIN(pr.processed_date) AS labels,";
				$group_by_foramt="GROUP BY DATEDIFF(pr.processed_date, "."'$from_date'".") DIV 7";
				break;                    
			case 4://current Year
				$from_date= date("Y").'-01-01';                        
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-2 months"));
				$var_to_date=date("Y").'-01-01';                 
				$days_in_current_year = $migareference->daysDiffrence($from_date,2);                            
					if ($days_in_current_year<=30) {
						$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
						$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
					} elseif($days_in_current_year>30 && $days_in_current_year<180) {
						$label_formate="MIN(pr.processed_date) AS labels,";
						$group_by_foramt="GROUP BY DATEDIFF(pr.processed_date, "."'$from_date'".") DIV 7";
					}elseif($days_in_current_year>180) {
						$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%M') AS labels,";
						$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M')";
					}                        
				break;                    
			case 5://12 month
				$from_date=date("Y-m-d", strtotime("-12 months"));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-24 months"));
				$var_to_date=date("Y-m-d", strtotime("-12 months"));                
				$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%M') AS labels,";
				$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M')";
				break;                    
			case 6://All (The creation date of Application to today)  
				$from_date = '';   
				$to_date = '';   
				$var_from_date = '';
				$var_to_date = '';       
				$group_by_foramt  = '';  
				$label_formate = '';                            
				/* $from_date=date("Y-m-d", strtotime($this->getApplication()->getCreatedAt()));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date('Y-m-d', strtotime(' -2 day'));
				$var_to_date=$from_date;
				$days_in_current_year = $migareference->daysDiffrence($from_date,2);                            
					if ($days_in_current_year<=30) {
						$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
						$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
					} elseif($days_in_current_year>30 && $days_in_current_year<180) {
						$label_formate="MIN(pr.processed_date) AS labels,";
						$group_by_foramt="GROUP BY DATEDIFF(pr.processed_date, "."'$from_date'".") DIV 7";
					}elseif($days_in_current_year>180) {
						$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%M') AS labels,";
						$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M')";
					} */
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
					$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%m-%d') AS labels,";
					$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M-%d')";
				} elseif($days_diff_custom_date>31 && $days_diff_custom_date<181) {
					$label_formate="MIN(pr.processed_date) AS labels,";
					$group_by_foramt="GROUP BY DATEDIFF(pr.processed_date, "."'$from_date'".") DIV 7";
				}elseif($days_diff_custom_date>181) {
					$label_formate="DATE_FORMAT(pr.processed_date,'%Y-%M') AS labels,";
					$group_by_foramt="GROUP BY DATE_FORMAT(pr.processed_date,'%Y-%M')";
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

	private function variationIncubationForamte($previous=0,$current=0)
    {
                $variation=0;

                $divider = ($previous>$current) ? $previous : $current ;
                $divider = ($divider==0) ? 1 : $divider ;//Avoid Infinity devision by 0            
            
                $variation = ($current!=0 || $previous!=0) ? (number_format((($current-$previous)/$divider)*100,2)) : 0 ; //Calculate Variation

                if ($variation>0) {
                    $formate="Prev. Var. <span class='var-positive'>".abs($variation)."% <i class='fa fa-arrow-up' aria-hidden='true'></i></span>";
                } elseif($variation<0) {
                    $formate="Prev. Var. <span class='var-negative'> ".abs($variation)."% <i class='fa fa-arrow-down' aria-hidden='true'></i></span>";                                   
                }elseif($variation==0){
                    $formate="Prev. Var. <span class='var-nochange'> ".abs($variation)."% <i class='fa fa-stop' aria-hidden='true'></i></span>";                                   
                }
                return $formate;
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

	private function formateStatParamsChart($range_filter,$date_range_start,$date_range_end)
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
			case 8: //past week
				$d = strtotime("today");
				$start_week = strtotime("last sunday midnight", $d);
				$end_week = strtotime("next saturday", $d);
				$from_date = date("Y-m-d", $start_week); 
				$to_date = date("Y-m-d", $end_week); 

				$previous_week = strtotime("-1 week +1 day");
				$start_week = strtotime("last sunday midnight", $previous_week);
				$end_week = strtotime("next saturday", $start_week);
				$var_from_date = date("Y-m-d", $start_week);
				$var_to_date = date("Y-m-d", $end_week);
				$label_formate="DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%m-%d') AS labels,";
				$group_by_foramt="GROUP BY DATE_FORMAT(migareference_stats_interval.setdate,'%Y-%M-%d')";
				break;                
			case 2://3 month
				$from_date=date("Y-m-d", strtotime("-3 months"));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-6 months"));
				$var_to_date=date("Y-m-d", strtotime("-3 months"));
				$label_formate="MIN(migareference_stats_interval.setdate) AS labels,";
				$group_by_foramt="GROUP BY DATEDIFF(migareference_stats_interval.setdate, "."'$from_date'".") DIV 7";
				break;                    
			case 3://6 month
				$from_date=date("Y-m-d", strtotime("-6 months"));
				$to_date=date('Y-m-d', strtotime(' +1 day'));
				$var_from_date=date("Y-m-d", strtotime("-12 months"));
				$var_to_date=date("Y-m-d", strtotime("-6 months"));
				$label_formate="MIN(migareference_stats_interval.setdate) AS labels,";
				$group_by_foramt="GROUP BY DATEDIFF(migareference_stats_interval.setdate, "."'$from_date'".") DIV 7";
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
						$label_formate="MIN(migareference_stats_interval.setdate) AS labels,";
						$group_by_foramt="GROUP BY DATEDIFF(migareference_stats_interval.setdate, "."'$from_date'".") DIV 7";
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
				$from_date = '';   
				$to_date = '';   
				$var_from_date = '';
				$var_to_date = '';                                 
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
					$label_formate="MIN(migareference_stats_interval.setdate) AS labels,";
					$group_by_foramt="GROUP BY DATEDIFF(migareference_stats_interval.setdate, "."'$from_date'".") DIV 7";
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
}

?>