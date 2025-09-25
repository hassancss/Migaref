<?php
class Migareference_AdminstatsController extends Application_Controller_Default{

    public function adminAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function referrerstatsAction() {
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
                    // **********START SECTION 1  (All Refrrers)*******//                                         
                    if (true) {// SubSection 1.1 (All Ref. Counts)                     
                        $tot_count_ref = $referrerstats->countReferrerRatAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                                    
                        $tot_count_ref[0]['five_star_prc']=$this->percntageFormate($tot_count_ref[0]['all_ref'],$tot_count_ref[0]['five_star']);                        
                        $tot_count_ref[0]['four_star_prc']=$this->percntageFormate($tot_count_ref[0]['all_ref'],$tot_count_ref[0]['four_star']);                        
                        $tot_count_ref[0]['three_star_prc']=$this->percntageFormate($tot_count_ref[0]['all_ref'],$tot_count_ref[0]['three_star']);                        
                        // KPIS
                        $tot_count_ref[0]['kpi'] = $this->kpiForamte(($tot_count_ref[0]['five_star']+$tot_count_ref[0]['four_star']),$tot_count_ref[0]['all_ref']);                                     
                    }
                    if (true) {// SubSection 1.2 (Total Pie Charts)                                            
                        // 1.2.1 Ref. PIE Chart (Total)->JOBS                    
                        $tot_count_jobs = $referrerstats->countReferrerJobsAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        // 1.2.2 Ref. PIE Chart (Total)->REGION                    
                        $tot_count_region = $referrerstats->countReferrerRegionsAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                    
                        $tot_count_region = $referrerstats->countReferrerProvincesAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                
                        $region_label=__("");
                        // 1.2.3 Ref. PIE Chart (Total)->AGE                     
                        $tot_count_age_temp = $referrerstats->countReferrerAgeAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                

                        $tot_count_age[0]=['age_count'=>$tot_count_age_temp[0]['under_18'],'labels'=>'<18'];
                        $tot_count_age[1]=['age_count'=>$tot_count_age_temp[0]['under_35'],'labels'=>'19-35'];
                        $tot_count_age[2]=['age_count'=>$tot_count_age_temp[0]['under_55'],'labels'=>'36-55'];
                        $tot_count_age[3]=['age_count'=>$tot_count_age_temp[0]['over_55'],'labels'=>'>55'];
                    }                    
                    // **********END SECTION 1  (All Refrrers)*******//                     
                    // **********START SECTION 2  (Range Referers)*******//                     
                    if (true) {// SubSection 2.1 (Range Graph Referrers)                                                                    
                        $range_graph_reff    = $referrerstats->totalGraphRefAgent($app_id,$agent_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                        $range_graph_refff   = $referrerstats->totalGraphRefAgent($app_id,$agent_id,$params['var_from_date'],$params['var_to_date'],$params['label_formate'],$params['group_by_foramt']);
                        foreach ($range_graph_reff as $key => $value) {
                            $range_graph_reff[$key]['prev_ref']=$range_graph_refff[$key]['total_ref'];
                            $range_graph_reff[$key]['prev_labels']=$range_graph_refff[$key]['labels'];
                        }
                    }
                    if (true) {// SubSection 2.2 (Range Count Referrers)                                                                    
                        $range_count_ref     = $referrerstats->countReferrerRatAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
                        $var_range_count_ref = $referrerstats->countReferrerRatAgent($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']);                  
                        // KPIS                                
                        $range_count_ref[0]['kpi']                 = $this->kpiForamte(($range_count_ref[0]['five_star']+$range_count_ref[0]['four_star']),$range_count_ref[0]['all_ref']);                
                        $var_range_count_ref[0]['kpi']                 = $this->kpiForamte(($var_range_count_ref[0]['five_star']+$var_range_count_ref[0]['four_star']),$var_range_count_ref[0]['all_ref']);                                        
                        // Variations
                        $var_range_count_ref[0]['var_all_ref']     = $this->variationForamte($var_range_count_ref[0]['all_ref'],$range_count_ref[0]['all_ref']);
                        $var_range_count_ref[0]['var_five_star']   = $this->variationForamte($var_range_count_ref[0]['five_star'],$range_count_ref[0]['five_star']);                                
                        $var_range_count_ref[0]['var_four_star']   = $this->variationForamte($var_range_count_ref[0]['four_star'],$range_count_ref[0]['four_star']);                
                        $var_range_count_ref[0]['var_three_star']  = $this->variationForamte($var_range_count_ref[0]['three_star'],$range_count_ref[0]['three_star']);                
                        $var_range_count_ref[0]['var_kpi']         = $this->variationForamte($var_range_count_ref[0]['kpi'],$range_count_ref[0]['kpi']);                
                    }
                    if (true) {// SubSection 2.3 (Range Pie Charts)                                            
                        // 2.3.1 Ref. PIE Chart (Range)->JOBS                    
                        $range_count_jobs = $referrerstats->countReferrerJobsAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
                        // 2.3.2 Ref. PIE Chart (Range)->REGION                    
                        $range_count_region = $referrerstats->countReferrerRegionsAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
                        // 2.3.3 Ref. PIE Chart (Range)->AGE                     
                        $range_count_age_temp = $referrerstats->countReferrerAgeAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                
                    
                        $range_count_age[0]=['age_count'=>$range_count_age_temp[0]['under_18'],'labels'=>'<18'];
                        $range_count_age[1]=['age_count'=>$range_count_age_temp[0]['under_35'],'labels'=>'19-35'];
                        $range_count_age[2]=['age_count'=>$range_count_age_temp[0]['under_55'],'labels'=>'36-55'];
                        $range_count_age[3]=['age_count'=>$range_count_age_temp[0]['over_55'],'labels'=>'>55'];
                    }
                    // **********END SECTION 2  (Range Referers)*******//                     
                    // **********START SECTION 3  (ALL Reports)*******//                     
                    if (true) {  // SubSection 3.1 (Total Graph Reports)                                                                
                        $total_graph_reports = $referrerstats->totalGraphReportsAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date'],$tot_params['label_formate'],$tot_params['group_by_foramt']);
                    }                    
                    if (true) {// SubSection 3.2 (Total Count Reports)                                                                
                        $total_count_reports = $referrerstats->totalCountReportsAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                                        
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
                        $range_graph_reports     = $referrerstats->totalGraphReportsAgent($app_id,$agent_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                    }                    
                    if (true) {// SubSection 4.2 (Range Count Reports)                                                                                            
                        $range_count_reports     = $referrerstats->totalCountReportsAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                                    
                        $var_range_count_reports = $referrerstats->totalCountReportsAgent($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']);                                        
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

                        $var_range_count_reports[0]['var_total_reports']   = $this->variationForamte($var_range_count_reports[0]['total_reports'],$range_count_reports[0]['total_reports']);
                        $var_range_count_reports[0]['var_success_reports'] = $this->variationForamte($var_range_count_reports[0]['success_reports'],$range_count_reports[0]['success_reports']);                
                        $var_range_count_reports[0]['var_incub_reports']   = $this->variationIncubationForamte($var_range_count_reports[0]['incubation'],$range_count_reports[0]['incubation']);                
                        $var_range_count_reports[0]['var_earn_reports']    = $this->variationForamte($var_range_count_reports[0]['earn_amount'],$range_count_reports[0]['earn_amount']);                
                        $var_range_count_reports[0]['var_kpi_reports']     = $this->variationForamte($var_range_count_reports[0]['kpi'],$range_count_reports[0]['kpi']);                
                    }                    
                    // **********END SECTION 4  (Range Reports)*******//                                         
                    // **********END SECTION 5  (Total Reminders )*******//                                         
                    if (true) {  // SubSection 5.1 (Total Graph Reminders)                                                                                    
                        $total_graph_reminders = $referrerstats->totalGraphRemindersAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date'],$tot_params['label_formate'],$tot_params['group_by_foramt']);
                    }      
                    if (true) {  // SubSection 5.2 (Total Graph Reminders)                                                                                        
                        $tottal_count_rem = $referrerstats->totalCountRemidersAgent($app_id,$agent_id,$tot_params['from_date'],$tot_params['to_date']);                                                                    
                        // Managemtn days
                        $tottal_count_rem[0]['management'] = $this->kpiForamte($tottal_count_rem[0]['total_management'],$tottal_count_rem[0]['total_rem_management']);                                     
                        // KPIS
                        $tottal_count_rem[0]['kpi'] = $this->kpiForamte($tottal_count_rem[0]['total_reminders'],$tottal_count_rem[0]['warrnings']);                                     
                    }      
                    // **********END SECTION 5  (Total Reminders )*******//                                         
                    // **********END SECTION 6  (Range Reminders )*******//                                         
                    if (true) {  // SubSection 6.1 (Range Graph Reminders)                                                                
                        $range_graph_reminders = $referrerstats->totalGraphRemindersAgent($app_id,$agent_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                    }      
                    if (true) {  // SubSection 6.2 (Range Graph Reminders)                                                                                        
                        $range_count_rem = $referrerstats->totalCountRemidersAgent($app_id,$agent_id,$params['from_date'],$params['to_date']);                                                                    
                        $var_range_count_rem = $referrerstats->totalCountRemidersAgent($app_id,$agent_id,$params['var_from_date'],$params['var_to_date']);                                                                    
                        // Managemtn days
                        $range_count_rem[0]['management'] = $this->kpiForamte($range_count_rem[0]['total_management'],$range_count_rem[0]['total_rem_management']);                                     
                        $var_range_count_rem[0]['management'] = $this->kpiForamte($var_range_count_rem[0]['total_management'],$var_range_count_rem[0]['total_rem_management']);                                     
                        // KPIS
                        $range_count_rem[0]['kpi'] = $this->kpiForamte($range_count_rem[0]['total_reminders'],$range_count_rem[0]['warrnings']);                                     
                        $var_range_count_rem[0]['kpi'] = $this->kpiForamte($var_range_count_rem[0]['total_reminders'],$var_range_count_rem[0]['warrnings']);                                     
                        // Variations
                        $var_range_count_rem[0]['var_total_reminders']   = $this->variationForamte($var_range_count_rem[0]['total_reminders'],$range_count_rem[0]['total_reminders']);
                        $var_range_count_rem[0]['var_warrnings'] = $this->variationForamte($var_range_count_rem[0]['warrnings'],$range_count_rem[0]['warrnings']);                
                        $var_range_count_rem[0]['var_done'] = $this->variationForamte($var_range_count_rem[0]['done'],$range_count_rem[0]['done']);                
                        $var_range_count_rem[0]['var_potponed']   = $this->variationForamte($var_range_count_rem[0]['potponed'],$range_count_rem[0]['potponed']);                
                        $var_range_count_rem[0]['var_management']    = $this->variationForamte($var_range_count_rem[0]['management'],$range_count_rem[0]['management']);                
                        $var_range_count_rem[0]['var_kpi']     = $this->variationForamte($var_range_count_rem[0]['kpi'],$range_count_rem[0]['kpi']);                
                    }      
                    // **********END SECTION 6  (Range Reminders Reports)*******//                                         
                    // **********START SECTION 8  (Range Agents Table)*******//                     
                    $range_agent_reports=$referrerstats->totalTblAgentsGnl($app_id,$params['from_date'],$params['to_date']);    
                    // **********END SECTION 8  (Range Agents Table)*******//                     
                    $html = [
                        'success'         => true,

                        'tot_count_ref'   => $tot_count_ref,                    
                        'tot_ref_jobs'    => $tot_count_jobs,                    
                        'tot_ref_age'      => $tot_count_age,                    
                        'tot_ref_region'   => $tot_count_region,                    
                        'region_label'     => $region_label,                    

                        'range_graph_ref' => $range_graph_reff,
                        'range_count_ref' => $range_count_ref,
                        'var_range_count_ref' => $var_range_count_ref,                    
                        'range_ref_jobs'  => $range_count_jobs,
                        'range_ref_age'   => $range_count_age,
                        'range_ref_region'=> $range_count_region,                        
                        
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
    public function percntageFormate($total=0, $subtotal=0)
    {        
            $percentag = ($subtotal>0 && $total>0) ? number_format(($subtotal/$total)*100,2) : 0 ;
            return $formate="Pct. Total. <span class='var-nochange'> ".$percentag."% <i class='fa fa-stop' aria-hidden='true'></i></span>";                                   
    }
    public function agenttotalreportsAction()
    {
      if ($data = $this->getRequest()->getQuery()) {
        $referrerstats   = new Migareference_Model_Referrerstats();                                
        $migareference   = new Migareference_Model_Migareference();                
        $app_id          =  $data['app_id'];                
        $sponsor_id      =  $data['agent_id'];  
        $params          = $this->formateStatParams(6,'','');                                                              
        $pre_settings    = $migareference->preReportsettigns($app_id);                                                    
        $referrers       = $referrerstats->agentReferrersTblAgent($app_id,$sponsor_id,$params['from_date'],$params['to_date']);                                                
        $ajax_responce   = [];
        $fin_res         = [];
        if (!empty($referrers)) {
          foreach ($referrers as $key => $value) {
            $earn_amount= ($pre_settings[0]['reward_type']==1) ? number_format($value['total_earn']+$value['mandate_eran'],2) : number_format($value['total_credits']+$value['mandate_eran'],2) ;                                
            $earn_amount= ($earn_amount!=null) ? $earn_amount : 0 ;                                            
            $incubation=$this->incubationForamte($value['total_incubation'],$value['success_reports']);
            $ajax_responce[] = [
                              $value['user_id'],
                              $value['created'],
                              $value['invoice_name']." ".$value['invoice_surname'],
                              $value['email'],
                              $value['mobile'],
                              $value['total_reports'],                              
                              $value['success_reports'],                              
                              number_format($earn_amount,2),                              
                              $incubation,                              
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
        $referrerstats   = new Migareference_Model_Referrerstats();                                
        $migareference   = new Migareference_Model_Migareference();                
        $app_id          =  $data['app_id'];                
        $sponsor_id      =  $data['agent_id'];  
        $range_filter    =  $data['range_filter'];          
        $params          = $this->formateStatParams($range_filter,$data['date_range_start'],$data['date_range_end']);                                                              
        $pre_settings    = $migareference->preReportsettigns($app_id);                                                    
        $referrers       = $referrerstats->agentReferrersTblAgent($app_id,$sponsor_id,$params['from_date'],$params['to_date']);                                                
        $ajax_responce   = [];
        $fin_res         = [];
        if (!empty($referrers)) {
          foreach ($referrers as $key => $value) {
            $earn_amount= ($pre_settings[0]['reward_type']==1) ? number_format($value['total_earn'],2) : number_format($value['total_credits'],2) ;                                
            $earn_amount= ($earn_amount!=null) ? $earn_amount : 0 ;                                
            $earn_amount= $earn_amount+$value['mandate_eran'];
            $incubation=$this->incubationForamte($value['total_incubation'],$value['success_reports']);
			if (isset($data['tab']) && $data['tab'] == 'general') {
				$ajax_responce[] = [
					$value['user_id'],
					$value['invoice_name']." ".$value['invoice_surname'],
					$value['total_reports'],                                                         
					number_format($earn_amount,2),                              
					$incubation,                              
				];
			} else {
				$ajax_responce[] = [
					$value['user_id'],
					$value['created'],
					$value['invoice_name']." ".$value['invoice_surname'],
					$value['email'],
					$value['mobile'],
					$value['total_reports'],                              
					$value['success_reports'],                              
					number_format($earn_amount,2),                              
					$incubation,                              
				];
			}
          }
        }
        $fin_res = [
            "data"=>$ajax_responce,   
            'date_range'=>$date_range         
        ];
        $this->_sendJson($fin_res);
      }
    }
    
 
}
?>