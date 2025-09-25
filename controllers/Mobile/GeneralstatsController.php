<?php
class Migareference_Mobile_GeneralstatsController extends Application_Controller_Mobile_Default
{    
    public function referrerstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $app_id          =  $data['app_id'];                
                    $range_filter    =  $data['range_filter']; 

                    $referrerstats       = new Migareference_Model_Referrerstats();                
                    $migareference       = new Migareference_Model_Migareference();                
                    $intervas            = $referrerstats->setIntervals($app_id);//this method enures that intervals exist                                                    
                    $pre_settings        = $migareference->preReportsettigns($app_id);                                                    
                    $tot_params          = $this->formateStatParams(6);                    
                    $params              = $this->formateStatParams($range_filter);
                    $no_vairations       = "Prev. Var. <span class='var-nochange'>N/A </i></span>";
                    // **********START SECTION 1  (All Refrrers)*******//                                         
                    if (true) {// SubSection 1.1 (All Ref. Counts)                     
                        $tot_count_ref = $referrerstats->countReferrerRatGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                                    
                        // KPIS
                        $tot_count_ref[0]['kpi'] = $this->kpiForamte(($tot_count_ref[0]['five_star']+$tot_count_ref[0]['four_star']),$tot_count_ref[0]['all_ref']);                                     
                    }
                    if (true) {// SubSection 1.2 (Total Pie Charts)                                            
                        // 1.2.1 Ref. PIE Chart (Total)->JOBS                    
                        $tot_count_jobs = $referrerstats->countReferrerJobsGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        // 1.2.2 Ref. PIE Chart (Total)->REGION                    
                        // $region_label=__("Best 10 Regions");
                        $tot_count_region = $referrerstats->countReferrerRegionsGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        // if ($tot_count_region<2) {
                            $region_label     = __("Best 10 Regions");
                            $tot_count_region = $referrerstats->countReferrerProvincesGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        // }
                        // 1.2.3 Ref. PIE Chart (Total)->AGE                     
                        $tot_count_age_temp = $referrerstats->countReferrerAgeGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                

                        $tot_count_age[0]=['age_count'=>$tot_count_age_temp[0]['under_18'],'labels'=>'<18'];
                        $tot_count_age[1]=['age_count'=>$tot_count_age_temp[0]['under_35'],'labels'=>'19-35'];
                        $tot_count_age[2]=['age_count'=>$tot_count_age_temp[0]['under_55'],'labels'=>'36-55'];
                        $tot_count_age[3]=['age_count'=>$tot_count_age_temp[0]['over_55'],'labels'=>'>55'];
                    }                    
                    // **********END SECTION 1  (All Refrrers)*******//                     
                    // **********START SECTION 2  (Range Referers)*******//                     
                    if (true) {// SubSection 2.1 (Range Graph Referrers)                                                                    
                        $range_graph_reff    = $referrerstats->totalGraphRefGnl($app_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                        $range_graph_refff   = $referrerstats->totalGraphRefGnl($app_id,$params['var_from_date'],$params['var_to_date'],$params['label_formate'],$params['group_by_foramt']);
                        foreach ($range_graph_reff as $key => $value) {
                            $range_graph_reff[$key]['prev_ref']=$range_graph_refff[$key]['total_ref'];
                        }
                    }
                    if (true) {// SubSection 2.2 (Range Count Referrers)                                            
                        $params              = $this->formateStatParams($range_filter);
                        $range_count_ref     = $referrerstats->countReferrerRatGnl($app_id,$params['from_date'],$params['to_date']);                                                
                        $var_range_count_ref = $referrerstats->countReferrerRatGnl($app_id,$params['var_from_date'],$params['var_to_date']);                  
                        // KPIS                                
                        $range_count_ref[0]['kpi']                = $this->kpiForamte(($range_count_ref[0]['five_star']+$range_count_ref[0]['four_star']),$range_count_ref[0]['all_ref']);                
                        $var_range_count_ref[0]['kpi']            = $this->kpiForamte(($var_range_count_ref[0]['five_star']+$var_range_count_ref[0]['four_star']),$var_range_count_ref[0]['all_ref']);                
                        // Variations
                        $var_range_count_ref[0]['var_all_ref']     = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_ref[0]['all_ref'],$range_count_ref[0]['all_ref']);
                        $var_range_count_ref[0]['var_five_star']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_ref[0]['five_star'],$range_count_ref[0]['five_star']);                                
                        $var_range_count_ref[0]['var_four_star']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_ref[0]['four_star'],$range_count_ref[0]['four_star']);                
                        $var_range_count_ref[0]['var_three_star']  = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_ref[0]['three_star'],$range_count_ref[0]['three_star']);                
                        $var_range_count_ref[0]['var_kpi']         = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_ref[0]['kpi'],$range_count_ref[0]['kpi']);                
                    }
                    if (true) {// SubSection 2.3 (Range Pie Charts)                                            
                        // 2.3.1 Ref. PIE Chart (Range)->JOBS                    
                        $range_count_jobs = $referrerstats->countReferrerJobsGnl($app_id,$params['from_date'],$params['to_date']);                                                
                        // 2.3.2 Ref. PIE Chart (Range)->REGION                    
                        $range_count_region = $referrerstats->countReferrerRegionsGnl($app_id,$params['from_date'],$params['to_date']);                                                
                        $range_region_label  = "<i class='fa fa-clock-o' aria-hidden='true' style='padding-right: 7px;'></i>";
                        $range_region_label  .= __("Best 10 Regions");
                        $range_count_region  = $referrerstats->countReferrerProvincesGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        // 2.3.3 Ref. PIE Chart (Range)->AGE                     
                        $range_count_age_temp = $referrerstats->countReferrerAgeGnl($app_id,$params['from_date'],$params['to_date']);                                                
                    
                        $range_count_age[0]=['age_count'=>$range_count_age_temp[0]['under_18'],'labels'=>'<18'];
                        $range_count_age[1]=['age_count'=>$range_count_age_temp[0]['under_35'],'labels'=>'19-35'];
                        $range_count_age[2]=['age_count'=>$range_count_age_temp[0]['under_55'],'labels'=>'36-55'];
                        $range_count_age[3]=['age_count'=>$range_count_age_temp[0]['over_55'],'labels'=>'>55'];
                    }
                    // **********END SECTION 2  (Range Referers)*******//                     
                    // **********START SECTION 3  (ALL Reports)*******//                     
                    if (true) {  // SubSection 3.1 (Total Graph Reports)                                                                
                        $total_graph_reports = $referrerstats->totalGraphReportsGnl($app_id,$tot_params['from_date'],$tot_params['to_date'],$tot_params['label_formate'],$tot_params['group_by_foramt']);
                    }                    
                    if (true) {// SubSection 3.2 (Total Count Reports)                                                                
                        $total_count_reports = $referrerstats->totalCountReportsGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                                        
                        // calculate  Incubations and KPIS                                
                        $total_count_reports[0]['incubation']           = $this->incubationForamte($total_count_reports[0]['total_incubation'],$total_count_reports[0]['success_reports']);                                
                        
                        $total_count_reports[0]['earn_lable']           = ($pre_settings[0]['reward_type']==1) ? __("Commision") : __("Credits") ;
                        $total_count_reports[0]['earn_amount']          = ($pre_settings[0]['reward_type']==1) ? number_format($total_count_reports[0]['total_earn'],2) : number_format($total_count_reports[0]['total_credits'],2) ;                                
                        $total_count_reports[0]['earn_amount']          = ($total_count_reports[0]['earn_amount']!=null) ? $total_count_reports[0]['earn_amount'] : 0 ;                                
                    
                        $total_count_reports[0]['kpi']                  = $this->kpiForamte($total_count_reports[0]['success_reports'],$total_count_reports[0]['total_reports']);                                                            
                    }
                    // **********END SECTION 3  (ALL Reports)*******//                     
                    // **********START SECTION 4  (Range Reports)*******//                     
                    if (true) {  // SubSection 4.1 (Range Graph Reports)                                                                
                        $range_graph_reports     = $referrerstats->totalGraphReportsGnl($app_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                    }                    
                    if (true) {// SubSection 4.2 (Range Count Reports)                                                                                            
                        $range_count_reports     = $referrerstats->totalCountReportsGnl($app_id,$params['from_date'],$params['to_date']);                                                                    
                        $var_range_count_reports = $referrerstats->totalCountReportsGnl($app_id,$params['var_from_date'],$params['var_to_date']);                                        
                        // calculate Variations, Incubations and KPIS                                
                        $range_count_reports[0]['incubation']           = $this->incubationForamte($range_count_reports[0]['total_incubation'],$range_count_reports[0]['success_reports']);                
                        $var_range_count_reports[0]['incubation']       = $this->incubationForamte($var_range_count_reports[0]['total_incubation'],$var_range_count_reports[0]['success_reports']);                
                        
                        $range_count_reports[0]['earn_lable']           = ($pre_settings[0]['reward_type']==1) ? __("Commision") : __("Credits") ;
                        $range_count_reports[0]['earn_amount']          = ($pre_settings[0]['reward_type']==1) ? $range_count_reports[0]['total_earn'] : $range_count_reports[0]['total_credits'] ;                
                        $var_range_count_reports[0]['earn_amount']      = ($pre_settings[0]['reward_type']==1) ? $var_range_count_reports[0]['total_earn'] : $var_range_count_reports[0]['total_credits'] ;                
                        $range_count_reports[0]['earn_amount']          = ($range_count_reports[0]['earn_amount']!=null) ? number_format($range_count_reports[0]['earn_amount'],2) : 0 ;                
                        $var_range_count_reports[0]['earn_amount']      = ($var_range_count_reports[0]['earn_amount']!=null) ? $var_range_count_reports[0]['earn_amount'] : 0 ;                
                    
                        $range_count_reports[0]['kpi']                  = $this->kpiForamte($range_count_reports[0]['success_reports'],$range_count_reports[0]['total_reports']);                
                        $var_range_count_reports[0]['kpi']              = $this->kpiForamte($var_range_count_reports[0]['success_reports'],$var_range_count_reports[0]['total_reports']);                

                        $var_range_count_reports[0]['var_total_reports']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_reports[0]['total_reports'],$range_count_reports[0]['total_reports']);
                        $var_range_count_reports[0]['var_success_reports'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_reports[0]['success_reports'],$range_count_reports[0]['success_reports']);                
                        $var_range_count_reports[0]['var_incub_reports']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_reports[0]['incubation'],$range_count_reports[0]['incubation']);                
                        $var_range_count_reports[0]['var_earn_reports']    = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_reports[0]['earn_amount'],$range_count_reports[0]['earn_amount']);                
                        $var_range_count_reports[0]['var_kpi_reports']     = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_reports[0]['kpi'],$range_count_reports[0]['kpi']);                
                    }                    
                    // **********END SECTION 4  (Range Reports)*******//                                         
                    // **********END SECTION 5  (Total Reminders )*******//                                         
                    if (true) {  // SubSection 5.1 (Total Graph Reminders)                                                                                    
                        $total_graph_reminders = $referrerstats->totalGraphRemindersGnl($app_id,$tot_params['from_date'],$tot_params['to_date'],$tot_params['label_formate'],$tot_params['group_by_foramt']);
                    }      
                    if (true) {  // SubSection 5.2 (Total Graph Reminders)                                                                                        
                        $tottal_count_rem = $referrerstats->totalCountRemidersGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);                                                                    
                        // Managemtn days
                        $tottal_count_rem[0]['management'] = $this->kpiForamte($tottal_count_rem[0]['total_management'],$tottal_count_rem[0]['total_rem_management']);                                     
                        // KPIS
                        $tottal_count_rem[0]['kpi'] = $this->kpiForamte($tottal_count_rem[0]['total_reminders'],$tottal_count_rem[0]['warrnings']);                                     
                    }      
                    // **********END SECTION 5  (Total Reminders )*******//                                         
                    // **********END SECTION 6  (Range Reminders )*******//                                         
                    if (true) {  // SubSection 6.1 (Range Graph Reminders)                                                                
                        $range_graph_reminders = $referrerstats->totalGraphRemindersGnl($app_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                    }      
                    if (true) {  // SubSection 6.2 (Range Graph Reminders)                                                                                        
                        $range_count_rem = $referrerstats->totalCountRemidersGnl($app_id,$params['from_date'],$params['to_date']);                                                                    
                        $var_range_count_rem = $referrerstats->totalCountRemidersGnl($app_id,$params['var_from_date'],$params['var_to_date']);                                                                    
                        // Managemtn days
                        $range_count_rem[0]['management'] = $this->kpiForamte($range_count_rem[0]['total_management'],$range_count_rem[0]['total_rem_management']);                                     
                        $var_range_count_rem[0]['management'] = $this->kpiForamte($var_range_count_rem[0]['total_management'],$var_range_count_rem[0]['total_rem_management']);                                     
                        // KPIS
                        $range_count_rem[0]['kpi'] = $this->kpiForamte($range_count_rem[0]['total_reminders'],$range_count_rem[0]['warrnings']);                                     
                        $var_range_count_rem[0]['kpi'] = $this->kpiForamte($var_range_count_rem[0]['total_reminders'],$var_range_count_rem[0]['warrnings']);                                     
                        // Variations
                        $var_range_count_rem[0]['var_total_reminders']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_rem[0]['total_reminders'],$range_count_rem[0]['total_reminders']);
                        $var_range_count_rem[0]['var_warrnings'] = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_rem[0]['warrnings'],$range_count_rem[0]['warrnings']);                
                        $var_range_count_rem[0]['var_potponed']   = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_rem[0]['potponed'],$range_count_rem[0]['potponed']);                
                        $var_range_count_rem[0]['var_management']    = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_rem[0]['management'],$range_count_rem[0]['management']);                
                        $var_range_count_rem[0]['var_kpi']     = ($range_filter==6) ? $no_vairations :  $this->variationForamte($var_range_count_rem[0]['kpi'],$range_count_rem[0]['kpi']);                
                    }      
                    // **********END SECTION 6  (Range Reminders Reports)*******//                                         
                    // **********START SECTION 8  (Range Agents Table)*******//                     
                    $range_agent_reports=$referrerstats->totalTblAgentsGnl($app_id,$params['from_date'],$params['to_date']);    
                    // **********END SECTION 8  (Range Agents Table)*******//                     
                    $html = [
                        'success'         => true,

                        'tot_count_ref'   => $tot_count_ref,                    
                        'tot_ref_jobs'    => $tot_count_jobs,                    
                        'tot_ref_age'     => $tot_count_age,                    
                        'tot_ref_region'  => $tot_count_region,                    

                        'range_graph_ref' => $range_graph_reff,
                        'range_count_ref' => $range_count_ref,
                        'var_range_count_ref' => $var_range_count_ref,                    
                        'range_ref_jobs'  => $range_count_jobs,
                        'range_ref_age'   => $range_count_age,
                        'range_ref_region'=> $range_count_region,

                        'region_label'    => $region_label,
                        'range_region_label'=> $range_region_label,
                        
                        'total_graph_reports'=> $total_graph_reports,                    
                        'total_count_reports'=> $total_count_reports,

                        'range_graph_reports' => $range_graph_reports,                    
                        'range_count_reports' => $range_count_reports,
                        'var_range_count_reports' => $var_range_count_reports,

                        'total_graph_reminders'=> $total_graph_reminders,                    
                        'total_count_reminders'=> $tottal_count_rem,

                        'range_graph_reminders' => $range_graph_reminders,                    
                        'range_count_reminders' => $range_count_rem,
                        'var_range_count_reminders' => $var_range_count_rem,
                        
                        'total_agent_reports' => $total_agent_reports,
                        'range_agent_reports' => $range_agent_reports,

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
                $current_temp = ($previous>0 && $current==0) ? 1 : $current ;//Avoid Infinity devision by 0            
                $variation = ($current!=0 || $previous!=0) ? (number_format((($current-$previous)/$current_temp)*100,2)) : 0 ; //Calculate Variation

                if ($variation>0) {
                    $formate="Prev. Var. <span class='var-positive'>".$variation."% <i class='fa fa-arrow-up' aria-hidden='true'></i></span>";
                } elseif($variation<0) {
                    $formate="Prev. Var. <span class='var-negative'> ".$variation."% <i class='fa fa-arrow-down' aria-hidden='true'></i></span>";                                   
                }elseif($variation==0){
                    $formate="Prev. Var. <span class='var-nochange'> ".$variation."% <i class='fa fa-stop' aria-hidden='true'></i></span>";                                   
                }
                return $formate;
    }
    private function incubationForamte($total_incubation=0,$success_reports=0)
    {            
        return $incubation = ($success_reports>0) ? number_format(($total_incubation/$success_reports),2) : 0 ;                
    }
    private function kpiForamte($number1=0,$number2=0)
    {        $number1 = ($number1==0) ? 1 : $number1 ;    
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
    private function formateStatParams($range_filter=0)
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
    public function agenttotalreportsAction()
    {
      if ($data = $this->getRequest()->getQuery()) {
        $app_id          =  $data['app_id'];                
        $range_filter    =  $data['range_filter'];                                            
        $referrerstats       = new Migareference_Model_Referrerstats();                
        $migareference       = new Migareference_Model_Migareference();                
        $pre_settings        = $migareference->preReportsettigns($app_id);                                                    
        $tot_params          = $this->formateStatParams(6);                    
        $params              = $this->formateStatParams($range_filter);
        // **********START SECTION 7  (Total Agents Table)*******//                     
        $total_agent_reports = $referrerstats->totalTblAgentsGnl($app_id,$tot_params['from_date'],$tot_params['to_date']);    
        // **********END SECTION 7  (Total Agents Table)*******//                     
        $ajax_responce = [];
        $fin_res = [];
        if (!empty($total_agent_reports)) {
          foreach ($total_agent_reports as $key => $value) {
            $ajax_responce[] = [
                              $value['customer_id'],
                              $value['firstname'].' '.$value['lastname'],
                              $value['total_reports'],
                              $value['total_credits'],
                              $value['total_incubation']
                              ];
          }
        }
        $fin_res = [
            "data"=>$ajax_responce,            
        ];
        $this->_sendJson($fin_res);
      }
    }
    public function agentrangereportsAction()
    {
      if ($data = $this->getRequest()->getQuery()) {
        $app_id          =  $data['app_id'];                
        $range_filter    =  $data['range_filter'];                                            
        $referrerstats       = new Migareference_Model_Referrerstats();                                                            
        $params              = $this->formateStatParams($range_filter);
        // **********START SECTION 8  (rannge Agents Table)*******//                     
        $total_agent_reports = $referrerstats->totalTblAgentsGnl($app_id,$params['from_date'],$params['to_date']);    
        // **********END SECTION 8  (range Agents Table)*******//                     
        $ajax_responce = [];
        $fin_res = [];
        if (!empty($total_agent_reports)) {
          foreach ($total_agent_reports as $key => $value) {
            $ajax_responce[] = [
                              $value['customer_id'],
                              $value['firstname'].' '.$value['lastname'],
                              $value['total_reports'],
                              $value['total_credits'],
                              $value['total_incubation']
                              ];
          }
        }
        $fin_res = [
            "data"=>$ajax_responce,            
        ];
        $this->_sendJson($fin_res);
      }
    }
 
}
?>