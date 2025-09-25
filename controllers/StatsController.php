<?php
class Migareference_StatsController extends Application_Controller_Default{

    public function referrerAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function referrerstatsAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {                
                    $app_id          =  $data['app_id'];                
                    $range_filter    =  $data['range_filter'];                
                    $user_id         =  $data['referrer_filter'];                                                                                           
                    $new_vs_uninstall_devices = [];
                    $referrerstats       = new Migareference_Model_Referrerstats();                
                    $migareference       = new Migareference_Model_Migareference();                
                    $pre_settings        = $migareference->preReportsettigns($app_id);                                
                    // Section 1
                    $params=$this->formateStatParams(6);//6 for all stats
                    $total_count_reports = $referrerstats->totalCountReports($app_id,$user_id,$params['from_date'],$params['to_date']);                                                
                    $total_graph_reports = $referrerstats->totalGraphReports($app_id,$user_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);

                    // calculate  Incubations and KPIS                                
                    $total_count_reports[0]['incubation']           = $this->incubationForamte($total_count_reports[0]['total_incubation'],$total_count_reports[0]['success_reports']);                                
                    
                    $total_count_reports[0]['earn_lable']           = ($pre_settings[0]['reward_type']==1) ? __("Commision") : __("Credits") ;
                    $total_count_reports[0]['earn_amount']          = ($pre_settings[0]['reward_type']==1) ? $total_count_reports[0]['total_earn'] : $total_count_reports[0]['total_credits'] ;                                
                    $total_count_reports[0]['earn_amount']          = ($total_count_reports[0]['earn_amount']!=null) ? $total_count_reports[0]['earn_amount'] : 0 ;                                
                
                    $total_count_reports[0]['kpi']                  = $this->kpiForamte($total_count_reports[0]['success_reports'],$total_count_reports[0]['total_reports']);                                
                    // Time Range Referrer Reports
                    // Section 2
                    $params=$this->formateStatParams($range_filter);
                    $range_count_reports     = $referrerstats->totalCountReports($app_id,$user_id,$params['from_date'],$params['to_date']);                                                
                    $range_graph_reports     = $referrerstats->totalGraphReports($app_id,$user_id,$params['from_date'],$params['to_date'],$params['label_formate'],$params['group_by_foramt']);
                    $var_range_count_reports = $referrerstats->totalCountReports($app_id,$user_id,$params['var_from_date'],$params['var_to_date']);                                        
                    // calculate Variations, Incubations and KPIS                                
                    $range_count_reports[0]['incubation']           = $this->incubationForamte($range_count_reports[0]['total_incubation'],$range_count_reports[0]['success_reports']);                
                    $var_range_count_reports[0]['incubation']       = $this->incubationForamte($var_range_count_reports[0]['total_incubation'],$var_range_count_reports[0]['success_reports']);                
                    
                    $range_count_reports[0]['earn_lable']           = ($pre_settings[0]['reward_type']==1) ? __("Commision") : __("Credits") ;
                    $range_count_reports[0]['earn_amount']          = ($pre_settings[0]['reward_type']==1) ? $range_count_reports[0]['total_earn'] : $range_count_reports[0]['total_credits'] ;                
                    $var_range_count_reports[0]['earn_amount']      = ($pre_settings[0]['reward_type']==1) ? $var_range_count_reports[0]['total_earn'] : $var_range_count_reports[0]['total_credits'] ;                
                    $range_count_reports[0]['earn_amount']          = ($range_count_reports[0]['earn_amount']!=null) ? $range_count_reports[0]['earn_amount'] : 0 ;                
                    $var_range_count_reports[0]['earn_amount']      = ($var_range_count_reports[0]['earn_amount']!=null) ? $var_range_count_reports[0]['earn_amount'] : 0 ;                
                
                    $range_count_reports[0]['kpi']                  = $this->kpiForamte($range_count_reports[0]['success_reports'],$range_count_reports[0]['total_reports']);                
                    $var_range_count_reports[0]['kpi']              = $this->kpiForamte($var_range_count_reports[0]['success_reports'],$var_range_count_reports[0]['total_reports']);                

                    $var_range_count_reports[0]['var_total_reports']   = $this->variationForamte($var_range_count_reports[0]['total_reports'],$range_count_reports[0]['total_reports']);
                    $var_range_count_reports[0]['var_success_reports'] = $this->variationForamte($var_range_count_reports[0]['success_reports'],$range_count_reports[0]['success_reports']);                
                    $var_range_count_reports[0]['var_incub_reports']   = $this->variationForamte($var_range_count_reports[0]['incubation'],$range_count_reports[0]['incubation']);                
                    $var_range_count_reports[0]['var_earn_reports']    = $this->variationForamte($var_range_count_reports[0]['earn_amount'],$range_count_reports[0]['earn_amount']);                
                    $var_range_count_reports[0]['var_kpi_reports']     = $this->variationForamte($var_range_count_reports[0]['kpi'],$range_count_reports[0]['kpi']);                
                    // // Section 3
                    $params=$this->formateStatParams($range_filter);
                    $range_count_prospects = $referrerstats->totalCountLandPrpct($app_id,$user_id,$params['from_date'],$params['to_date']);                                                
                    $var_range_count_prospects = $referrerstats->totalCountLandPrpct($app_id,$user_id,$params['var_from_date'],$params['var_to_date']);                  

                    $range_count_prospects[0]['total_visit']    = ($range_count_prospects[0]['total_visit']!=null) ? $range_count_prospects[0]['total_visit'] : 0 ;                
                    $range_count_prospects[0]['total_share']    = ($range_count_prospects[0]['total_share']!=null) ? $range_count_prospects[0]['total_share'] : 0 ;                
                    $range_count_prospects[0]['total_report']   = ($range_count_prospects[0]['total_report']!=null) ? $range_count_prospects[0]['total_report'] : 0 ;                
                    // calculate Variations, and KPIS                                
                    $range_count_prospects[0]['kpi']                = $this->kpiForamte($range_count_prospects[0]['total_report'],$range_count_prospects[0]['total_share']);                
                    $var_range_count_prospects[0]['kpi']            = $this->kpiForamte($var_range_count_prospects[0]['total_report'],$var_range_count_prospects[0]['total_share']);                
    
                    $var_range_count_prospects[0]['var_total_visit']  = $this->variationForamte($var_range_count_prospects[0]['total_visit'],$range_count_prospects[0]['total_visit']);
                    $var_range_count_prospects[0]['var_total_share']  = $this->variationForamte($var_range_count_prospects[0]['total_share'],$range_count_prospects[0]['total_share']);                                
                    $var_range_count_prospects[0]['var_total_report'] = $this->variationForamte($var_range_count_prospects[0]['total_report'],$range_count_prospects[0]['total_report']);                
                    $var_range_count_prospects[0]['var_kpi']          = $this->variationForamte($var_range_count_prospects[0]['kpi'],$range_count_prospects[0]['kpi']);                
                    // Formate Dates and intervals                
                    $html = [
                        'success' => true,
                        'data'             => $total_graph_reports,
                        'range_data'       => $range_graph_reports,                    
                        'count_data'       => $total_count_reports,
                        'range_count_data' => $range_count_reports,
                        'range_count_prospects'     => $range_count_prospects,
                        'var_range_count_reports'   => $var_range_count_reports,
                        'var_range_count_prospects' => $var_range_count_prospects,                    
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
                $variation = ($current!=0 || $previous!=0) ? ((($current-$previous)/$current_temp)*100) : 0 ; //Calculate Variation

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
                        $var_from_date=date("Y-m-d", strtotime($this->getApplication()->getCreatedAt()));
                        $var_to_date=date('Y-m-d', strtotime(' +1 day'));
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
}
?>