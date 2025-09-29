<?php
/**
 * Class Migareference_Mobile_ViewController
 */
  use Siberian\Currency;
class Migareference_Mobile_ViewController extends Application_Controller_Mobile_Default
{
  public function fetchsettingsAction (){
      try {
          // Set default settings
          $defaults = [
              "default_page" => (string) "places",
              "default_layout" => (string) "place-100",
              "distance_unit" => (string) "km",
              "listImagePriority" => (string) "thumbnail",
              "defaultPin" => (string) "pin"
          ];
          $payload = [
              "success" => true,
              "settings" => $defaults,
          ];
      } catch (\Exception $e) {
          $payload = [
              "error" => true,
              "message" => $e->getMessage(),
          ];
      }
      $this->_sendJson($payload);
  }
  public function loadAction(){
      if ($value_id = $this->getRequest()->getParam('value_id')) {
          try {
              $app_id            = $this->getApplication()->getId();
              $migareference     = new Migareference_Model_Migareference();
              $stasus            = $migareference->getReportStatus();
              $pre_report        = $migareference->preReportsettigns($app_id);
              $app_short_version = $this->getRequest()->getParam('app_short_version');
              $os_type           = $this->getRequest()->getParam('platform');
              $local_anroid_version = explode(".",$pre_report[0]['android_store_version']);
              $local_ios_version    = explode(".",$pre_report[0]['ios_store_version']);
              $local_anroid_version = $local_anroid_version[1].".".$local_anroid_version[2];
              settype($local_anroid_version,'float');
              $local_ios_version    = $local_ios_version[1].".".$local_ios_version[2];
              settype($local_ios_version,'float');
              if ($pre_report[0]['check_app_version']==1) {
                if (!isset($app_short_version) || !isset($os_type)) {
                  throw new Exception(__("Please update your app"), 1);
                }
                if ($pre_report[0]['check_ios_version']==1 && $os_type=='IOS' && $app_short_version<$local_ios_version) {
                  throw new Exception(__("Please update your app"), 1);
                }
                if ($pre_report[0]['check_android_version']==1 && $os_type=='Android' && $app_short_version<$local_anroid_version) {
                  throw new Exception(__("Please update your app"), 1);
                }
              }
              $payload = [
                  "page_title" => __($migareference->getPageTitle($app_id, $value_id)),
                  "stasus"     => $stasus
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error'   => true,
                  'message' => $e->getMessage()
              ];
          }
      } else {
          $payload = [
              'error'   => true,
              'message' => __('An error occurred during process. Please try again later.')
          ];
      }
      $this->_sendJson($payload);
  }
  public function loadhomecontentAction(){
    $migareference = new Migareference_Model_Migareference();
    $default       = new Core_Model_Default();
    $base_url      = $default->getBaseUrl();
    $app_id        = $this->getApplication()->getId();    
    $app_content   = $migareference->get_app_content($app_id);
    if (!count($app_content)) {
      $migareference->save_app_content($app_id);
      $app_content   = $migareference->get_app_content($app_id);
    }
    // // Temp Method 3/25/2021
    if (empty($app_content[0]['enroll_url_box_label'])) {
      $migareference->temp_upate_app_content($app_id);
      $app_content = $migareference->get_app_content($app_id);
    }
    // // End Temp
    $applicationBase=$base_url."/images/application/".$app_id."/features/migareference/";

    $iconsBase      = $base_url."/app/local/modules/Migareference/resources/appicons/";
    $app_content[0]['qlf_cover_file']=$applicationBase.$app_content[0]['qlf_cover_file'];
    $app_content[0]['qlf_level_one_cover']=$applicationBase.$app_content[0]['qlf_level_one_cover'];
    $app_content[0]['qlf_level_one_btn_one_cover']=$applicationBase.$app_content[0]['qlf_level_one_btn_one_cover'];
    $app_content[0]['qlf_level_one_btn_two_cover']=$applicationBase.$app_content[0]['qlf_level_one_btn_two_cover'];
    $app_content[0]['qlf_level_two_cover']=$applicationBase.$app_content[0]['qlf_level_two_cover'];
    $app_content[0]['enroll_url_cover_file']=$applicationBase.$app_content[0]['enroll_url_cover_file'];
    $app_content[0]['how_it_works_file']=$applicationBase.$app_content[0]['how_it_works_file'];
    $app_content[0]['referre_report_file']=$applicationBase.$app_content[0]['referre_report_file'];
    $app_content[0]['add_property_file']=$applicationBase.$app_content[0]['add_property_file'];
    $app_content[0]['report_status_file']=$applicationBase.$app_content[0]['report_status_file'];
    $app_content[0]['prizes_file']=$applicationBase.$app_content[0]['prizes_file'];
    $app_content[0]['settings_file']=$applicationBase.$app_content[0]['settings_file'];
    $app_content[0]['reminders_file']=$applicationBase.$app_content[0]['reminders_file'];
    $app_content[0]['phonebooks_file']=$applicationBase.$app_content[0]['phonebooks_file'];
    $app_content[0]['statistics_file']=$applicationBase.$app_content[0]['statistics_file'];
    $app_content[0]['report_type_pop_cover']=$applicationBase.$app_content[0]['report_type_pop_cover'];
    $app_content[0]['report_type_pop_btn_one_icon']=$applicationBase.$app_content[0]['report_type_pop_btn_one_icon'];
    $app_content[0]['report_type_pop_btn_two_icon']=$applicationBase.$app_content[0]['report_type_pop_btn_two_icon'];
    $app_content[0]['disable_file']=$iconsBase.'disable.png';    
    $app_content[0]["logo_whatsapp"]=$iconsBase."whatsapp-green.png";
    $app_content[0]["email"]=$iconsBase."mail.png";
    $app_content[0]["sms"]=$iconsBase."sms.png";
    $app_content[0]["copy"]=$iconsBase."copy.png";
    $app_content[0]['whatsapp_icon']=$iconsBase.'whatsapp-green.png';    
    $app_content[0]['notepad']=$iconsBase.'notepad.png';    
    $app_content[0]['call_icon']=$iconsBase.'phone-call-green.png';    
    $app_content[0]['phonebook_icon']=$iconsBase.'open-phonebook.png';    
    $app_content[0]['chatbot_icon']=$iconsBase.'chatbot.png';    
    $app_content[0]['optional_one']=$iconsBase.'optional_one.png';    
    $app_content[0]['add_property_cover_file']=$applicationBase.$app_content[0]['add_property_cover_file'];                
    $app_content[0]['report_detail_form_card_title']=__("Report Detail");                
    $app_content[0]['report_detail_agent_card_title']=__("Agent/Sponsor Detail");                
    $app_content[0]['report_detail_referrer_card_title']=__("Referrer Detail");                
    $app_content[0]['report_detail_status_card_title']=__("Report Status");                
    $app_content[0]['admin_dob_confirm_warning']=__("The Date of Birth is a very important excuse to hear from your Referrer and build relationships. Try your best to add it to the system!");                
    $app_content[0]['admin_dob_confirm_understoodbtn_text']=__("OK I understood");                
    $app_content[0]['admin_dob_confirm_getdob_text']=__("Oh yes! I add it now");                
    $app_content[0]['admin_dob_confirm_understoodbtn_style']="";                
    $app_content[0]['admin_dob_confirm_getdob_style']="";                
      $payload = [
          'success'   => true,
          'message'   => __('An error occurred during process. Please try again later.'),
          'app_content' => $app_content[0],
          "stas"     => $app_id
      ];
      $this->_sendJson($payload);
  }
  public function loadactivereportscontentAction(){ //Deprecated
    try {      
      
    $migareference     = new Migareference_Model_Migareference();
    $application       = $this->getApplication();
    $app_id            = $application->getId();
    $data              = Siberian_Json::decode($this->getRequest()->getRawBody());
    $default           = new Core_Model_Default();
    $base_url          = $default->getBaseUrl();
    
    $filter_array      = [];
    $report_collection = [];
    $user_id           = $data['user_id'];
    $filter_string     = "";
    $is_admin          = 0;
    $is_agetn          = 0;
    $invoic_string     = "";
    $iconsBase         = $base_url."/app/local/modules/Migareference/resources/appicons/";
    $consentBase       = $base_url . "/migareference/consent?appid=".$app_id.'&rep=';
    $applicationBase   = $base_url."/images/application/".$app_id."/features/migareference/";
    $warrning_icon     = $iconsBase."warrning.png";
    $phone_icon        = $iconsBase."phone.png";
    $note_icon         = $iconsBase."note.png";
    $block_icon        = $iconsBase."certificate.png";
      
      
    $filter_string.= ($data['status_id'] && $data['status_id']>0) ? " AND migareference_report.currunt_report_status=".$data['status_id'] : "" ;
     // Create a DateTime object from the input string
     $dateTime = new DateTime($data['from_date']);
       // Add one day to the date
      $dateTime->modify('+1 day');
     // Format the DateTime object to get only the date part (Y-m-d)
     $fromdate = $dateTime->format('Y-m-d');
    $filter_string .= ($data['from_date']) ? " AND DATE(migareference_report.created_at) >= '".$fromdate."'" : "";
 // Create a DateTime object from the input string
    $dateTime = new DateTime($data['to_date']);
    // Add one day to the date
    $dateTime->modify('+1 day');
    // Format the DateTime object to get only the date part (Y-m-d)
    $todate = $dateTime->format('Y-m-d');
    $filter_string .= ($data['to_date']) ? " AND DATE(migareference_report.created_at) <= '".$todate."'" : "";

    $admin_data    = $migareference->is_admin($app_id,$user_id);    
    $agent_data    = $migareference->is_agent($app_id,$user_id);
    $pre_settings  = $migareference->preReportsettigns($app_id);
    $bitly_crede   = $migareference->getBitlycredentails($app_id);
    $app_content   = $migareference->get_app_content($app_id);
    $report_type_one=$app_content[0]['report_type_pop_btn_one_text'];
    $report_type_two=$app_content[0]['report_type_pop_btn_two_text'];
    
    $report_type_icon_one=$applicationBase.$app_content[0]['report_type_pop_btn_one_icon'];
    $report_type_icon_two=$applicationBase.$app_content[0]['report_type_pop_btn_two_icon'];    
    $is_agent=0;
    $agent_type=0;
    $enable_gdpr   = ($pre_settings[0]['consent_collection']==1) ? true : false ;   
    $status_string=''; 
    if (!empty($admin_data)) {
      $is_admin =1;
    }elseif(!empty($agent_data) && ($pre_settings[0]['agent_can_manage']==1 OR $pre_settings[0]['agent_can_see']==1)) {
      $is_agent =1;
      if ($filter_string=='' && $data['search']=='' && ($data['status_id']==-2 || $data['status_id']==0)) { //it mean this is first call for admin load
        $status_string.=" AND migareference_report_status.standard_type!=3 AND migareference_report_status.standard_type!=4";
      }
      $agent_type=$agent_data[0]['agent_type'];
      if ($agent_type==1) {
        $filter_string.=" AND "."(migareference_report.report_custom_type=".$agent_type." OR "."migareference_report.report_custom_type=0)";
      }else {
        $filter_string.=" AND "."migareference_report.report_custom_type=".$agent_type;        
      }
      $filter_string.=" AND "."("."refag_one.agent_id=".$user_id;
      $filter_string.=" OR refag_two.agent_id=".$user_id;
      $filter_string.=" OR migareference_report.user_id=".$user_id;
      $filter_string.=" OR migareference_report.created_by=".$user_id.")";
    }else {
      $filter_string.=" AND migareference_report.user_id=".$user_id;
    }
    
    
    if ($filter_string=='' && $data['search']=='' && $is_admin==1) { //it mean this is first call for admin load
      $status_string.=" AND migareference_report_status.standard_type!=3 AND migareference_report_status.standard_type!=4";
    }
    
    $filter_array['filter_string']= $filter_string;
    $filter_array['invoic_string']= $invoic_string;
    $filter_array['status_string']= $status_string;
    $filter_array['app_id']       = $app_id;        
    $status = $migareference->getReportStatus($app_id);
    $all_reports  = $migareference->getReportList($filter_array);    
    $statusdata = array_merge([
      ['migareference_report_status_id' => -2, 'status_title' => __("Exclude Declined and Paid")]
      ,['migareference_report_status_id' => -1, 'status_title' => __("Show All")]], $status);
  
    $now            = time(); // or your date as well
    $tags           = [
                        '@@report_owner@@',
                        '@@referrer_name@@',
                        '@@agent_name@@',
                        '@@consent_link@@'
                      ];
    $commission_label=__("Value");
    $fmt                     = numfmt_create( $application->getLocale(), NumberFormatter::CURRENCY );
    $currency=$application->getCurrency();
    $no_gdpr=$iconsBase.'no_gdpr.png';
    $gdpr=$iconsBase.'gdpr.png';
    foreach ($all_reports as $key => $value) {
      $gdpr_icon             = ($value['gdpr_consent_timestamp']==NULL) ? $no_gdpr : $gdpr ;
      $gdpr_text             = ($value['gdpr_consent_timestamp']==NULL) ? __("No GDPR") : __("GDPR") ;
      $commission_fee_set    = ($value['commission_fee']>0) ? true : false ;            
      $report_custom_type=$report_type_one;
      $report_custom_type_icon=$report_type_icon_one;
      if ($value['report_custom_type']==2) {
        $report_custom_type=$report_type_two;
        $report_custom_type_icon=$report_type_icon_two;
      }
      // Managed By      
      $managed_by="";          
      if ($value['sponsor_one_id']!=null || $value['sponsor_two_id']!=null) { //Manged by Agent
          if ($value['sponsor_one_id']!=null) {            
            $managed_by=$value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'];                                                         
          }
          if ($value['sponsor_two_id'] != null) {
            if (!empty($managed_by)) {$managed_by .= ' & ';}
            $managed_by .= $value['sponsor_two_lastname'] . " " . $value['sponsor_two_firstname'];                                             
        }       
      }else if($value['is_standard']==1 && $value['standard_type']==1){ //Report is new and not managed by anyone
        $managed_by=__("Unknown");
      }else { 
        $all_logs = $migareference->getReportlog($value['app_id'],$value['migareference_report_id']);
        $all_logs = end($all_logs);                    
        if ($all_logs['user_type']==1) { //Managed by Admin->as an app customer
          $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['cutomerfirstname']." ".$all_logs['cutomerlastname'];
        }else {//Managed by Admin->as an app Admin
          $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['adminfirstname']." ".$all_logs['adminlastname'];
        }                    
      }                 
                  
      $commission_type_label = __("Credits") ;
      if ($value['reward_type']==1) {
        $formate_commission_fee  = numfmt_format_currency($fmt, $value['commission_fee'], $currency);        
        $commission_type_label   = $currency ;
      }
      $can_manage  = ($is_admin|| ($is_agent && $pre_settings[0]['agent_can_manage']==1 )) ? true : false ;      
      // $is_warrning = ($value['current_reminder_status']!="") ? true : false ;      
      $is_warrning = true ;      
      if ($data['search']!="") {
        if (stripos($value['owner_name'], $data['search']) !== false OR stripos($value['owner_surname'], $data['search']) !== false OR stripos($value['invoice_name'], $data['search']) !== false OR stripos($value['invoice_surname'], $data['search']) !== false) {
              $report_collection[]=array(
                'migareference_report_id' => $value['migareference_report_id'],
                'report_id'               => $value['migareference_report_id'],
                'report_no'              => $value['report_no'],
                'created_at'             => $value['report_created_at'],
                'refrale_mobile'         => $value['mobile'],
                'refrale_c_name'         => $value['invoice_name']." ".$value['invoice_surname'],
                'owner_c_name'           => $value['owner_name']." ".$value['owner_surname'],
                'owner_mobile'           => $value['owner_mobile'],
                'last_modification_at'   => $value['report_modified_at'],
                'report_status'          => __($value['status_title'])." ".$status_img,
                'commission_fee'         => $value['commission_fee'],
                'property_type'          => $value['property_type'],
                'sales_expectations'     => $value['sales_expectations'],
                'owner_hot'              => $value['owner_hot'],
                'address'                => $value['address'],
                'longitude'              => $value['longitude'],
                'latitude'               => $value['latitude'],
                'can_manage'             => $can_manage,
                'report_custom_type'     => $report_custom_type,
                'report_custom_type_icon'=> $report_custom_type_icon,
                'managed_by'             => $managed_by,
                'is_warrning'            => $is_warrning,
                'warrning_icon'          => $warrning_icon,
                'phone_icon'             => $phone_icon,
                'status_icon'            => $applicationBase.$value['status_icon'],
                'block_icon'             => $block_icon,
                'note_icon'              => $note_icon,
                'gdpr_icon'              => $gdpr_icon,
                'gdpr_text'              => $gdpr_text,
                'commission_label'       => $commission_label,
                'commission_fee_set'     => $commission_fee_set,
                'commission_type_label'  => $commission_type_label,
                'formate_commission_fee' => $formate_commission_fee,
                'consent_invit_msg_body' => '',
                'is_notarized' => $value['is_notarized'] ? true : false,
          );
        }
      }else {
        // Build Consent Message
        $consent_invit_msg_body=NULL;
        if ($value['consent_timestmp']==NULL) {          
          $consent_link         = $value['consent_bitly'];
          if ($value['consent_bitly']=='') {
            $utilities = new Migareference_Model_Utilities();
            $consent_link         = $consentBase.$value['migareference_report_id'];
            $consent_link         = $utilities->shortLink($consent_link);
            $datas['app_id']									= $app_id;
            $datas['migareference_report_id'] = $value['migareference_report_id'];
            $datas['consent_bitly']					  = $consent_link;
            $migareference->updatepropertyreport($datas);
          }
          $strings              = [
                                    $value['owner_name']." ".$value['owner_surname'],
                                    $value['invoice_name']." ".$value['invoice_surname'],
                                    $value['sponsor_firstname']." ".$value['sponsor_lastname'],
                                    $consent_link
                                  ];
          $consent_invit_msg_body=str_replace($tags, $strings, $pre_settings[0]['consent_invit_msg_body']);
        }
              //Fro admin we have to exclude Declined,Not sold Reports
              if ($is_admin==0 || $filter_string!="") {
                    $report_collection[]=array(
                      'migareference_report_id' => $value['migareference_report_id'],
                      'report_id'               => $value['migareference_report_id'],
                      'report_no'              => $value['report_no'],
                      'created_at'             => $value['report_created_at'],
                      'refrale_mobile'         => $value['mobile'],
                      'refrale_c_name'         => $value['invoice_name']." ".$value['invoice_surname'],
                      'owner_c_name'           => $value['owner_name']." ".$value['owner_surname'],
                      'owner_mobile'           => $value['owner_mobile'],
                      'last_modification_at'   => $value['report_modified_at'],
                      'report_status'          => __($value['status_title'])." ".$status_img,
                      'report_status_id'       => $value['report_status_id'],
                      'commission_fee'         => $value['commission_fee'],//Pending will look on later
                      'property_type'          => $value['property_type'],
                      'sales_expectations'     => $value['sales_expectations'],
                      'owner_hot'              => $value['owner_hot'],
                      'address'                => $value['address'],
                      'longitude'              => $value['longitude'],
                      'latitude'               => $value['latitude'],
                      'report_custom_type'     => $report_custom_type,
                      'report_custom_type_icon'=> $report_custom_type_icon,
                      'block_icon'             => $block_icon,
                      'phone_icon'             => $phone_icon,
                      'can_manage'             => $can_manage,
                      'managed_by'             => $managed_by,
                      'is_warrning'            => $is_warrning,
                      'warrning_icon'          => $warrning_icon,
                      'status_icon'            => $applicationBase.$value['status_icon'],
                      'block_icon'             => $block_icon,
                      'gdpr_icon'              => $gdpr_icon,
                      'gdpr_text'              => $gdpr_text,
                      'commission_label'       => $commission_label,
                      'commission_fee_set'     => $commission_fee_set,
                      'commission_type_label'  => $commission_type_label,
                      'formate_commission_fee' => $formate_commission_fee,
                      'consent_invit_msg_body'=> $consent_invit_msg_body,
                      'is_notarized' => $value['is_notarized'] ? true : false,
                );
              }elseif ($value['standard_type']!=4 && $value['standard_type']!=3) {                                        
                    $report_collection[]=array(
                      'migareference_report_id' => $value['migareference_report_id'],
                      'report_id'               => $value['migareference_report_id'],
                      'report_no'              => $value['report_no'],
                      'created_at'             => $value['report_created_at'],
                      'refrale_mobile'         => $value['mobile'],
                      'refrale_c_name'         => $value['invoice_name']." ".$value['invoice_surname'],
                      'owner_c_name'           => $value['owner_name']." ".$value['owner_surname'],
                      'owner_mobile'           => $value['owner_mobile'],
                      'last_modification_at'   => $value['report_modified_at'],
                      'report_status'          => __($value['status_title'])." ".$status_img,
                      'report_status_id'       => $value['report_status_id'],
                      'commission_fee'         => $value['commission_fee'],//Pending will look on later
                      'property_type'          => $value['property_type'],
                      'sales_expectations'     => $value['sales_expectations'],
                      'block_icon'             => $block_icon,
                      'owner_hot'              => $value['owner_hot'],
                      'address'                => $value['address'],
                      'longitude'              => $value['longitude'],
                      'latitude'               => $value['latitude'],
                      'can_manage'             => $can_manage,
                      'report_custom_type_icon'=> $report_custom_type_icon,
                      'report_custom_type'     => $report_custom_type,
                      'phone_icon'             => $phone_icon,
                      'managed_by'             => $managed_by,
                      'is_warrning'            => $is_warrning,
                      'warrning_icon'          => $warrning_icon,
                      'status_icon'            => $applicationBase.$value['status_icon'],
                      'block_icon'             => $block_icon,
                      'gdpr_icon'              => $gdpr_icon,
                      'gdpr_text'              => $gdpr_text,
                      'commission_label'       => $commission_label,
                      'commission_fee_set'     => $commission_fee_set,
                      'commission_type_label'  => $commission_type_label,
                      'formate_commission_fee' => $formate_commission_fee,
                      'consent_invit_msg_body'=> $consent_invit_msg_body,
                      'is_notarized' => $value['is_notarized'] ? true : false,
                );
              }
      }
    }
    $payload = [
            'success'           => true,
            'status'            => $statusdata,            
            'is_admin'          => $is_admin,
            'is_agent'          => $is_agent,
            'user_group_ids'    => $user_group_ids,
            'agent_can_see'     => $pre_settings[0]['agent_can_see'],
            'agent_can_manage'  => $pre_settings[0]['agent_can_manage'],
            'all_reports'       => $report_collection,                               
            'raw_reports_count'         => COUNT($all_reports),                               
            'raw_reports_list'         => $all_reports,                               
            'filter_array'     => $filter_array,                               
            'enable_gdpr'       => $enable_gdpr,                   
            'agent_list'       => $agent_list,                   
            'agent_data'       => $agent_data,                   
    ];
      } catch (\Exception $e) {
              $payload = [
                  'error'   => true,
                  'message' => $e->getMessage()
              ];
          }
    $this->_sendJson($payload);
  }
  public function manageinputypevaluesignatureAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$field_value="",$longitude="",$latitude="",$option_type=0,$option_default="",$country_id=0)
  {
    $extra_input_template="";
    $migareference  = new Migareference_Model_Migareference();
    if ($type==1) {
      $extra_input_template.=str_replace(",", "", $field_value);      
    } else if($type==2) {
      $extra_input_template.=str_replace(",", "", $field_value);      
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
        $temp_options=explode('@',$options);        
        foreach ($temp_options as $key => $value) {
          $option_value++;          
          if ($option_value==$field_value) {            
            $extra_input_template.=str_replace(",", "", $value); 
          }
        }        
          break;
        case 1://Country List
          $geoCountries              = $migareference->getGeoCountries($app_id);
          // $df_opt=explode("@",$option_default);          
          foreach ($geoCountries as $key => $value) {
            if ($field_value==$value['migareference_geo_countries_id']) {
             $extra_input_template.=str_replace(",", "", __($value['country']));  
            }
          }          
          break;
        case 2:
        // $df_opt=explode("@",$option_default);
        $dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$country_id);        
        foreach ($dataGeoConPro as $key => $value) {
          if ($field_value==$value['migareference_geo_provinces_id']) {
            $extra_input_template.=str_replace(",", "", __($value['province']));  
          }
        }        
          break;
        default:
          // $extra_input_template.="<option value=''></option>";
          break;
      }
    }else if($type==4) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }else if($type==5) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }else if($type==6) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }
    return $extra_input_template;
  }
  
  /*
  *Sensitive (In Terms of Optimization or speed) be carefull
  */
  public function loadactivereportsAction(){
    try {      
      
    $migareference     = new Migareference_Model_Migareference();  
    $application       = $this->getApplication();  
    $app_id            = $this->getApplication()->getId();
    $data              = Siberian_Json::decode($this->getRequest()->getRawBody());
    $base_url          = (new Core_Model_Default())->getBaseUrl();  
    
    $user_id           = $data['user_id'];
    $filter_array      = [];
    $report_collection = [];
    $filter_string     = "";
    $status_string     = "";
    $is_admin          = 0;
    $is_agent          = 0;
    $agent_type        = 0;    
    $iconsBase         = $base_url."/app/local/modules/Migareference/resources/appicons/";
    $consentBase       = $base_url . "/migareference/consent?appid=".$app_id.'&rep=';
    $applicationBase   = $base_url."/images/application/".$app_id."/features/migareference/";
    $phone_icon        = $iconsBase."phone.png";
    $note_icon         = $iconsBase."note.png";
    $block_icon        = $iconsBase."certificate.png";
      
    $pre_settings  = $migareference->preReportsettigns($app_id);
    $app_content   = $migareference->get_app_content($app_id);
    $report_type_one=$app_content[0]['report_type_pop_btn_one_text'];
    $report_type_two=$app_content[0]['report_type_pop_btn_two_text'];
    
    $report_type_icon_one=$applicationBase.$app_content[0]['report_type_pop_btn_one_icon'];
    $report_type_icon_two=$applicationBase.$app_content[0]['report_type_pop_btn_two_icon'];
 
    $enable_gdpr   = ($pre_settings[0]['consent_collection']==1) ? true : false ;
    //Report Status List for filter  
    $status = $migareference->getReportStatus($app_id);
    $statusdata = array_merge(
      [
        ['migareference_report_status_id' => "-2", 'status_title' => __("Exclude Declined and Paid")],
        ['migareference_report_status_id' => "-1", 'status_title' => __("Show All")]
      ], $status);
    //::Report Status List for filter
    /*Set User type */
    $admin_data    = $migareference->is_admin($app_id,$user_id);    
    $agent_data    = $migareference->is_agent($app_id,$user_id);
    $is_admin = (!empty($admin_data)) ? 1 : 0 ;
    $is_agent = (!empty($agent_data)) ? 1 : 0 ;
    $is_referrer = (empty($agent_data) && empty($admin_data)) ? 1 : 0 ;
    /* Status Filter */
    if ($data['status_id']>0) {//Apply specific status
      $filter_string.= ($data['status_id'] && $data['status_id']>0) ? " AND migareference_report.currunt_report_status=".$data['status_id'] : "" ;
    }else if($data['status_id']==-2){ //-2 to exclude Paid and Declined
      $status_string.=" AND migareference_report_status.standard_type!=3 AND migareference_report_status.standard_type!=4";
    }//-1 Show all or nothing to apply
    /* Filter as [er user type */
    if($is_agent && ($pre_settings[0]['agent_can_manage']==1 OR $pre_settings[0]['agent_can_see']==1)) { //Filter reports for agnet
      $agent_type=$agent_data[0]['agent_type'];
      if ($agent_type==1) {
        $filter_string.=" AND "."(migareference_report.report_custom_type=".$agent_type." OR "."migareference_report.report_custom_type=0)";
      }else {
        $filter_string.=" AND "."migareference_report.report_custom_type=".$agent_type;        
      }
      $filter_string.=" AND "."("."refag_one.agent_id=".$user_id;
      $filter_string.=" OR refag_two.agent_id=".$user_id;
      $filter_string.=" OR migareference_report.user_id=".$user_id;
      $filter_string.=" OR migareference_report.created_by=".$user_id.")";
    }elseif(!$is_admin) { //Filter reports for referrer or agent(if its not allowed to manage os he can see only self reports as a referrer)
      $filter_string.=" AND migareference_report.user_id=".$user_id;
    }
    
  
    $filter_array['filter_string']= $filter_string;
    $filter_array['invoic_string']= '';
    $filter_array['status_string']= $status_string;
    $filter_array['app_id']       = $app_id;        
    $all_reports  = $migareference->getReportList($filter_array);  
    
    $fmt = numfmt_create( $application->getLocale(), NumberFormatter::CURRENCY );
    $currency=$application->getCurrency();
    $no_gdpr=$iconsBase.'no_gdpr.png';
    $gdpr=$iconsBase.'gdpr.png';
    // Ceck if their is any field set to visible on card
    $dynamic_visible_list = (new Migareference_Model_Reportfields())->findAll(['app_id'=> $app_id, 'is_visible_status_report' => 1,'is_visible' => 1 ])->toArray();
    foreach ($all_reports as $key => $value) {
      /*GDPR icon and text */
      $gdpr_icon             = ($value['gdpr_consent_timestamp']==NULL) ? $no_gdpr : $gdpr ;
      $gdpr_text             = ($value['gdpr_consent_timestamp']==NULL) ? __("No GDPR") : __("GDPR") ;
       //Report Source icon
        // report_source 1: From APP end 2: From Labding Page 3 From backoffice or Owner end, 4 for API End
        $report_source_icon='';
        $report_source_title='';
        if ($value['report_source']==1) {
          $report_source_icon=$iconsBase.'report_app.png';
          $report_source_title=__("App");
        }elseif ($value['report_source']==2) {
          $report_source_icon=$iconsBase.'report_landing.png';
          $report_source_title=__("Landing Page");
        }elseif ($value['report_source']==3) {
          $report_source_icon=$iconsBase.'report_owner.png'; 
          $report_source_title=__("Editor"); 
        }elseif ($value['report_source']==4) {
          $report_source_icon=$iconsBase.'report_api.png'; 
          $report_source_title=__("API"); 
        }   
      /*Define if commission is given or not */
      $commission_fee_set    = ($value['commission_fee']>0) ? true : false ;            
      /*Define custom report type ICON and Text lable */
      $report_custom_type=$report_type_one;
      $report_custom_type_icon=$report_type_icon_one;
      if ($value['report_custom_type']==2) {
        $report_custom_type=$report_type_two;
        $report_custom_type_icon=$report_type_icon_two;
      }
      /*Define MANAGED BY lable && Sponso one & two if exist*/   
      $managed_by="";          
      if ($value['sponsor_one_id']!=null || $value['sponsor_two_id']!=null) { //Manged by Agent
          if ($value['sponsor_one_id']!=null) {            
            $managed_by=$value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'];                                             
          }
          if ($value['sponsor_two_id'] != null) {
            if (!empty($managed_by)) {$managed_by .= ' & ';}
            $managed_by .= $value['sponsor_two_lastname'] . " " . $value['sponsor_two_firstname'];                                             
        }       
      }else if($value['is_standard']==1 && $value['standard_type']==1){ //Report is new and not managed by anyone
        $managed_by=__("Unknown");
      }else { 
        $all_logs = $migareference->getReportlog($app_id,$value['migareference_report_id']);
        $all_logs = end($all_logs);                    
        if ($all_logs['user_type']==1) { //Managed by Admin->as an app customer
          $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['cutomerfirstname']." ".$all_logs['cutomerlastname'];
        }else {//Managed by Admin->as an app Admin
          $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['adminfirstname']." ".$all_logs['adminlastname'];
        }                    
      }                  
      /*Define commission lable */         
      $commission_type_label = __("Credits") ;
      if ($value['reward_type']==1) {
        $formate_commission_fee  = numfmt_format_currency($fmt, $value['commission_fee'], $currency);        
        $commission_type_label   = $currency ;
      }
      /*Define either the report could be managed by user or not */ 
      $can_manage  = ($is_admin|| ($is_agent && $pre_settings[0]['agent_can_manage']==1)) ? true : false ;             
      /*Define Consent invitaion message if already not consent is not given */   
      $consent_invit_msg_body = ($value['consent_timestmp']==NULL) ? NULL : $this->getConsentInvitationMessage($app_id,$value,$consentBase, $pre_settings[0]['consent_invit_msg_body']);
      /* Build content for dynamic fields set to visible */
      $dynamic_visible_fields_raw = "";
      $static_fields[1]['name']="property_type";
      $static_fields[2]['name']="sales_expectations";
      $static_fields[3]['name']="address";
      $static_fields[4]['name']="owner_name";
      $static_fields[5]['name']="owner_surname";
      $static_fields[6]['name']="owner_mobile";
      $static_fields[7]['name']="note";
      if (COUNT($dynamic_visible_list)) {
        $actual_dynamic_filed=unserialize($value['extra_dynamic_fields']);
        foreach ($dynamic_visible_list as $field_key => $field_value) {          
          if ($field_value['type']==1) {            
              $name=$static_fields[$field_value['field_type_count']]['name'];
              $field_val = (!empty($value[$name])) ? $value[$name] : "" ;
              $field_text=$this->manageinputypevaluesignatureAction($app_id,$field_value['field_type'],$name,$field_value['field_option'],0,$field_val,0,0,$field_value['option_type'],$field_value['default_option_value'],0);
          }else {
            $name="extra_".$field_value['field_type_count'];
            $field_val = (!empty($actual_dynamic_filed[$name])) ? $actual_dynamic_filed[$name] : "" ;      
            $field_text=$this->manageinputypevaluesignatureAction($app_id,$field_value['field_type'],$name,$field_value['field_option'],0,$field_val,0,0,$field_value['option_type'],$field_value['default_option_value'],0);     
          }
          $dynamic_visible_fields_raw .= "<div class='row p-6'>";
            $dynamic_visible_fields_raw .= "<div class='col'>";
              $dynamic_visible_fields_raw .= "<h2 class='h2-color'>";
                $dynamic_visible_fields_raw .= $field_value['label'].":";
              $dynamic_visible_fields_raw .= "</h2>";
            $dynamic_visible_fields_raw .= "</div>";                
            $dynamic_visible_fields_raw .= "<div class='col'>";
              $dynamic_visible_fields_raw .= "<h2 class='h2-right-align'>";
                $dynamic_visible_fields_raw .= $field_text;
              $dynamic_visible_fields_raw .= "</h2>";
            $dynamic_visible_fields_raw .= "</div>";
          $dynamic_visible_fields_raw .= "</div>";
        }
      }
                    $report_collection[]=array(
                      'migareference_report_id' => $value['migareference_report_id'],
                      'report_id'               => $value['migareference_report_id'],
                      'report_no'              => $value['report_no'],
                      'referrer_id'             => $value['user_id'],
                      'created_at'             => $value['report_created_at'],
                      'refrale_mobile'         => $value['mobile'],
                      'refrale_c_name'         => $value['invoice_surname']." ".$value['invoice_name'],
                      'owner_c_name'           => $value['owner_surname']." ".$value['owner_name'],
                      'owner_mobile'           => $value['owner_mobile'],
                      'last_modification_at'   => $value['report_modified_at'],
                      'report_modified_at_filter'   => $value['report_modified_at_filter'],
                      'report_status'          => __($value['status_title'])." ".$status_img,
                      'report_status_id'       => $value['report_status_id'],
                      'commission_fee'         => $value['commission_fee'],
                      'property_type'          => $value['property_type'],
                      'sales_expectations'     => $value['sales_expectations'],
                      'owner_hot'              => $value['owner_hot'],
                      'address'                => $value['address'],
                      'longitude'              => $value['longitude'],
                      'latitude'               => $value['latitude'],
                      'note_unread_count'      => $value['note_unread_count'],
                      'report_custom_type'     => $report_custom_type,
                      'dynamic_visible_fields_raw'=> $dynamic_visible_fields_raw,
                      'dynamic_visible_list'=> $dynamic_visible_list,
                      'actual_dynamic_filed'=> $actual_dynamic_filed,
                      'report_custom_type_icon'=> $report_custom_type_icon,
                      'report_source_icon'=> $report_source_icon,
                      'report_source_title'=> $report_source_title,
                      'block_icon'             => $block_icon,
                      'sponsor_one'            => $value['sponsor_one_id'],
                      'sponsor_two'            => $value['sponsor_two_id'],
                      'phone_icon'             => $phone_icon,
                      'can_manage'             => $can_manage,
                      'managed_by'             => $managed_by,                      
                      'warrning_icon'          => $warrning_icon,
                      'status_icon'            => $applicationBase.$value['status_icon'],
                      'block_icon'             => $block_icon,
                      'gdpr_icon'              => $gdpr_icon,
                      'note_icon'              => $note_icon,
                      'gdpr_text'              => $gdpr_text,                      
                      'commission_fee_set'     => $commission_fee_set,
                      'commission_type_label'  => $commission_type_label,
                      'formate_commission_fee' => $formate_commission_fee,
                      'consent_invit_msg_body'=> $consent_invit_msg_body,
                      'is_notarized' => $value['is_notarized'] ? true : false,
                );                                                                                     
              }
    $reports_sort_by_filter[]=['filter_id'=>'-report_modified_at_filter','sort_by'=>__("Last Modification")];              
    $reports_sort_by_filter[]=['filter_id'=>'-created_at','sort_by'=>__("New to Old")];              
    $reports_sort_by_filter[]=['filter_id'=>'created_at','sort_by'=>__("Old to New")];              
    $reports_sort_by_filter[]=['filter_id'=>'-report_no','sort_by'=>__("Report NO")];     
    
    
    $reports_filter_date_range[]=['filter_id'=>1,'date_range'=>__("Past 7 Days")];              
    $reports_filter_date_range[]=['filter_id'=>2,'date_range'=>__("Past 30 Days")];                            
    $reports_filter_date_range[]=['filter_id'=>3,'date_range'=>__("Past 3 Months")];              
    $reports_filter_date_range[]=['filter_id'=>4,'date_range'=>__("Past 6 Months")];              
    $reports_filter_date_range[]=['filter_id'=>5,'date_range'=>__("Past 12 Months")];  
    $reports_filter_date_range[]=['filter_id'=>1000,'date_range'=>__("Custom date")];  
    $payload = [
            'success'           => true,
            'status'            => $statusdata,            
            'is_admin'          => $is_admin,
            'is_agent'          => $is_agent,
            'is_referrer'       => $is_referrer,
            'user_group_ids'    => $user_group_ids,
            'agent_can_see'     => $pre_settings[0]['agent_can_see'],
            'agent_can_manage'  => $pre_settings[0]['agent_can_manage'],
            'all_reports'       => $report_collection,                               
            'raw_reports_count' => COUNT($all_reports),                               
            'raw_reports_list'  => $all_reports,                               
            'filter_array'      => $filter_array,                               
            'enable_gdpr'       => $enable_gdpr,                                               
            'agent_data'        => $agent_data,                   
            'reports_sort_by_filter'        => $reports_sort_by_filter,                   
            'reports_filter_date_range'        => $reports_filter_date_range,                   
    ];
      } catch (\Exception $e) {
              $payload = [
                  'error'   => true,
                  'message' => $e->getMessage()
              ];
          }
    $this->_sendJson($payload);
  }
  public function randomTaxid() {
    $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 6; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    // return implode($pass); //turn the array into a string
    return "";
}
public function getConsentInvitationMessage($app_id=0,$value=[],$consentBase='',$consent_invit_msg_body=''){
  $tags           = ['@@report_owner@@','@@referrer_name@@','@@agent_name@@','@@consent_link@@'];
  $migareference     = new Migareference_Model_Migareference();
  $utilities = new Migareference_Model_Utilities();
  $bitly_crede   = $migareference->getBitlycredentails($app_id);
      $consent_link         = $value['consent_bitly'];
      if ($consent_link=='') { //if link is not already created create new link
          $long_url             = $consentBase.$value['migareference_report_id'];
          $short_url         = $utilities->shortLink($long_url);
          $consent_link=$short_url;
          if ($short_url==$long_url) {
            $short_url='';
          }
          $datas['app_id']									= $app_id;
          $datas['migareference_report_id'] = $value['migareference_report_id'];
          $datas['consent_bitly']					  = $short_url;
          $migareference->updatepropertyreport($datas);
        }
        $strings              = [
                                  $value['owner_name']." ".$value['owner_surname'],
                                  $value['invoice_name']." ".$value['invoice_surname'],
                                  $value['sponsor_firstname']." ".$value['sponsor_lastname'],
                                  $consent_link
                                ];
        $consent_invit_msg_body=str_replace($tags, $strings,$consent_invit_msg_body);
        return $consent_invit_msg_body;
}
public function loadereportdataAction(){
  $default           = new Core_Model_Default();
  $migareference     = new Migareference_Model_Migareference();
  $utilities         = new Migareference_Model_Utilities();
  $report_id         = $this->getRequest()->getParam('report_id');
  $app_id            = $this->getApplication()->getId();
  $base_url          = $default->getBaseUrl();
  $status            = $migareference->getReportStatus($app_id);
  $all_reports       = $migareference->getReportItem($app_id,$report_id);
  $pre_settings      = $migareference->preReportsettigns($app_id);
  $app_content       = $migareference->get_app_content($app_id);
  $report_collection = [];
  $enable_gdpr       = ($pre_settings[0]['consent_collection']==1) ? true : false ;
  $default_model[0]  = "";
  $default_model[1]  = "";
  $field_data        = unserialize($all_reports[0]['extra_dynamic_field_settings']);
  $field_data        = $migareference->getreportfield($app_id);
  $version           = ($this->getRequest()->getParam('version')!==null) ? $this->getRequest()->getParam('version') : 0 ;
  if ($version>0) {    
    $label_classes="item item-input item-custom item-stacked-label";
    $label_style="padding:12px;";
    $span_style="width:100%;max-width:100%";
  } else {
    $label_classes="item item-input item-stacked-label"; 
    $label_style="padding-right:16px;";
    $span_style="";
  }
  $label_classes="item item-input item-custom item-stacked-label";
  // Dynamic Logic Start
  $field='';
  $static_fields[1]['name']="property_type";
  $static_fields[2]['name']="sales_expectations";
  $static_fields[3]['name']="address";
  $static_fields[4]['name']="owner_name";
  $static_fields[5]['name']="owner_surname";
  $static_fields[6]['name']="owner_mobile";
  $static_fields[7]['name']="note";

  // Add Manually Custom Report type field
  if ($pre_settings[0]['enable_report_type']==1 && $version>=4.30) {    
    $field.="<label style='".$label_style."' class='".$label_classes."'>";
    $field.="<span class='input-label' style='".$span_style."' >";
    $field.=$app_content[0]['report_type_pop_title']." ".'*';
    $field.="</span><br>";
    $field.="<select  style='width:100%;padding:8px;border-radius:3px;font-size:medium;border: 1px solid rgb(169, 169, 169);'   data-ng-model='migareferenceformchange.report_custom_type'>";    
    $field.="<option value='1'>".$app_content[0]['report_type_pop_btn_one_text']."</option>";    
    $field.="<option value='2'>".$app_content[0]['report_type_pop_btn_two_text']."</option>";           
    $field.="</select></label>";
  }
    
  $count=-1;
  $margin_setting=0;
  $whatsapp_icon = $base_url."/app/local/modules/Migareference/resources/appicons/whatsapp-green.png";            
  foreach ($field_data as $key => $value) {
    $disable="";
    if ($value['is_visible']==1) {
      $count++; 
      $display="";     
    }else {
      $display="display:none;";
    }    
    $label_top=($key==0) ? "margin-top:1px;" : "" ;
    $required = ($value['is_required']==1) ? "*" : "" ;
    
    if ($value['type']==1) {
          $field.="<label style='".$label_style.$display.$label_top."' class='".$label_classes."'>";
          $field.="<span class='input-label' style='".$span_style."' >";
          $field.=__($value['label'])." ".$required;
          $field.="</span><br>";
          if ($static_fields[$value['field_type_count']]['name']=='owner_mobile') { //add Phone Call and whatsapp call action buttons to mobile            
            $margin_setting=($count*91)+123;
            $margin_setting=$margin_setting."px";
            $mobile_field=$this->manageinputypeAction($app_id,$value['field_type'],$static_fields[$value['field_type_count']]['name'],$value['field_option'],0,$disable,$value['options_type'],$value['default_option_value']);
            //Remove </label> from mobile field
            $mobile_field = str_replace("</label>", "", $mobile_field);            
            $mobile_field_html = '
            <div class="row" style="padding: 0;" bis_skin_checked="1">
              <div class="col-60" bis_skin_checked="1">
                ' . $mobile_field . '
              </div>             
            </div></label>';
            $field.=$mobile_field_html;
          }else {        
            $field.=$this->manageinputypeAction($app_id,$value['field_type'],$static_fields[$value['field_type_count']]['name'],$value['field_option'],0,$disable,$value['options_type'],$value['default_option_value']);
          }
    }else {
      $name="extra_".$value['field_type_count'];
      $field.="<label style='".$label_style.$display.$label_top."' class='".$label_classes."'>";
      $field.="<span class='input-label' style='".$span_style."' >";
      $field.=__($value['label'])." ".$required;
      $field.="</span><br>";
      $field.=$this->manageinputypeAction($app_id,$value['field_type'],$name,$value['field_option'],$value['field_type_count'],$disable,$value['options_type'],$value['default_option_value']);
    }
  }
    // Dynamic Logic End
    foreach ($all_reports as $key => $value) {
      $gdpr_icon = ($value['gdpr_consent_timestamp']==NULL) ? 'no_gdpr.png' : 'gdpr.png' ;
      $gdpr_text = ($value['gdpr_consent_timestamp']==NULL) ? __("No GDPR") : 'GDPR' ;
      $gdpr_icon = $base_url."/app/local/modules/Migareference/resources/appicons/".$gdpr_icon;
      $owner_dob = ($value['owner_dob']==NULL) ? '' : $value['owner_dob'] ;
      // Build Consent Message
      $consent_invit_msg_body=NULL;
      if ($value['gdpr_consent_timestamp']==NULL) {
        $bitly_crede          = $migareference->getBitlycredentails($app_id);
        $agent                = $migareference->getSponsorList($app_id,$value['user_id']);
        $consent_link         = $base_url . "/migareference/consent?appid=".$app_id.'&rep='.$value['migareference_report_id'];
        $consent_link         = $utilities->shortLink($consent_link);
        $tags                 = [
                                  '@@report_owner@@',
                                  '@@referrer_name@@',                                  
                                  '@@consent_link@@'
                                ];
        $strings              = [
                                  $value['owner_name']." ".$value['owner_surname'],
                                  $value['invoice_name']." ".$value['invoice_surname'],                                  
                                  $consent_link
                                ];
        $consent_invit_msg_body=str_replace($tags, $strings, $pre_settings[0]['consent_invit_msg_body']);
      }
      $status_res  = $migareference->reportStatusByKey($value['migareference_report_status_id'],$value['migareference_report_id']);
      $now         = time(); // or your date as well
      $your_date   = strtotime($value['last_modification_at']);
      $datediff    = $now - $your_date;
      $days        = round($datediff / (60 * 60 * 24));
      $new_date    = date('d-m-Y', strtotime($value['report_created_at']));
      $modi_date   = date('d-m-Y', strtotime($value['last_modification_at']));
      $warrning_icon = $base_url."/app/local/modules/Migareference/resources/appicons/warrning.png";
      $block_icon    = $base_url."/app/local/modules/Migareference/resources/appicons/certificate.png";
      $note_icon    = $base_url."/app/local/modules/Migareference/resources/appicons/note.png";
      $sponsorid     = ($value['sponsor_id']) ? true : false ;
      // Manage Warring icon
      $is_warrning =false;      
      $report_collection[]=array(
        'migareference_report_id'        => $value['migareference_report_id'],
        'migareference_report_status_id' => $value['migareference_report_status_id'],
        'report_no'              => $value['report_no'],
        'created_at'             => date('d-m-Y', strtotime($value['report_created_at'])),
        'refrale_mobile'         => $value['mobile'],
        'sponsor_firstname'      => $value['sponsor_firstname'],
        'sponsor_lastname'       => $value['sponsor_lastname'],
        'sponsor_email'          => $value['sponsor_email'],
        'report_custom_type'     => $value['report_custom_type'],
        'standard_type'          => $status_res[0]['standard_type'],
        'invoice_name'           => $value['invoice_name'],
        'invoice_surname'        => $value['invoice_surname'],
        'refrale_c_name'         => $value['invoice_name']." ".$value['invoice_surname'],
        'owner_c_name'           => $value['owner_name']." ".$value['owner_surname'],
        'owner_name'             => $value['owner_name'],
        'owner_surname'          => $value['owner_surname'],
        'owner_mobile'           => $value['owner_mobile'],
        'prospect_id'            => $value['prospect_id'],
        'refferal_mobile'        => $value['invoice_mobile'],
        'last_modification_at'   => date('d-m-Y', strtotime($value['last_modification_at'])),
        'report_status'          => __($value['status_title']),
        'report_status_id'       => $value['report_status_id'],
        'reward_type'            => $value['reward_type'],
        'commission_fee'         => $value['commission_fee'],
        'commission_type'        => $value['commission_type'],
        'property_type'          => $value['property_type'],
        'sales_expectations'     => $value['sales_expectations'],
        'owner_hot'              => $value['owner_hot'],
        'owner_dob'              => $owner_dob,
        'order_id'               => $value['order_id'],
        'comment'                => $value['comment'],
        'address'                => $value['address'],
        'longitude'              => $value['longitude'],
        'latitude'               => $value['latitude'],
        'acquired'               => $value['acquired'],
        'note'                   => $value['note'],
        'is_mandate_acquired'    => $value['is_mandate_acquired'],
        'phonebook_id'           => $value['migarefrence_phonebook_id'],
        'is_warrning'            => $is_warrning,
        'warrning_icon'          => $warrning_icon,
        'status_icon'            => $base_url."/images/application/".$app_id."/features/migareference/".$value['status_icon'],
        'block_icon'             => $block_icon,
        'note_icon'             => $note_icon,
        'sponsorid'              => $sponsorid,
        'extra_dynamic_fields'   => unserialize($value['extra_dynamic_fields']),
        'extra_dynamic_field_settings' => unserialize($value['extra_dynamic_field_settings']),
        'gdpr_icon'             => $gdpr_icon,
        'gdpr_text'             => $gdpr_text,
        'consent_invit_msg_body'=> $consent_invit_msg_body,
        'is_notarized' => $value['is_notarized'] ? true : false,
      );
      $extra_dynamic_filed=unserialize($value['extra_dynamic_field_settings']);
      $actual_dynamic_filed=unserialize($value['extra_dynamic_fields']);
      $country_id=0;
      foreach ($extra_dynamic_filed as $keyyy => $valueee) {
        if ($valueee['type']==2 && $valueee['options_type']!=0) {
          $name="extra_".$valueee['field_type_count'];
          $field_value = (!empty($actual_dynamic_filed[$name])) ? $actual_dynamic_filed[$name] : "" ;
        }
        $name="extra_".$valueee['field_type_count'];
        $field_value = (!empty($actual_dynamic_filed[$name])) ? $actual_dynamic_filed[$name] : "" ;
        if ($valueee['field_type']==3 && $valueee['options_type']==1 && $valueee['type']==2) {
          $country_id=$field_value;
          $df_opt[]=$country_id;
          $default_model[$valueee['field_type_count']]=$field_value."@".$valueee['options_type'];
        }
        // Province
        $provinceCollection = [];
        if ($valueee['field_type']==3 && $valueee['options_type']==2) {
          // $df_opt             = explode("@",$value['default_option_value']);
          $dataGeoConPro      = $migareference->getGeoCountryProvicnes($app_id,$country_id);
          $provinceCollection = [];
          $df_opt[]           = $field_value;
          foreach ($dataGeoConPro as $provkey => $provvalue) {
            $provinceCollection[$provvalue['migareference_geo_provinces_id']]=[
              "migareference_geo_provinces_id"=>$provvalue['migareference_geo_provinces_id'],
              "province"=>$provvalue['province']
            ];
          }
          $default_model[$valueee['field_type_count']]=$field_value."@".$valueee['options_type'];
        }
      }
    }
    $geoCountries     = $migareference->getGeoCountries($app_id);
    $countryCollection=[];
    foreach ($geoCountries as $key => $value) {
      $countryCollection[$value['migareference_geo_countries_id']]=[
        "id"=>$value['migareference_geo_countries_id'],
        "name"=>$value['country']
      ];
    }
    $report_collection[0]['extra_dynamic_fields']['extra_10']=$report_collection[0]['note'];
    $payload = [
        'success'       => true,
        'status'        => $status,        
        'margin_setting' => $margin_setting,        
        'whatsapp_icon' => $whatsapp_icon,        
        'all_reports'   => $report_collection,
        'all_reports_raw_data'   => $all_reports,
        'form_builder'  => $field,
        'field_data'  => $field_data,
        "geoCountries"  => $countryCollection,
        "proviceitems"  => $provinceCollection,
        "default"       => $df_opt,
        "default_model" => $default_model,
        "enable_gdpr"   => $enable_gdpr
    ];
    $this->_sendJson($payload);
  }

  public function buildmessageAction(){
    $user_id           = $this->getRequest()->getParam('user_id');
    $agent_id          = $this->getRequest()->getParam('agent_id');
    $type              = $this->getRequest()->getParam('type');//1 Referrer,2 Agnet, 3 Admin
    $report_by         = $this->getRequest()->getParam('report_by');
    $report_custom_type= $this->getRequest()->getParam('report_custom_type', 1); // New parameter with default value 1
    $migareference     = new Migareference_Model_Migareference();    
    $default           = new Core_Model_Default();
    $utilities = new Migareference_Model_Utilities();
    $base_url          = $default->getBaseUrl();
    $app_id            = $this->getApplication()->getId();
    $referer           = $migareference->getSingleuser($app_id,$report_by);
    $agent_data        = $migareference->getAgentdata($user_id);
    $bitly_crede       = $migareference->getBitlycredentails($app_id);
    $pre_report        = $migareference->preReportsettigns($app_id);
    $social_sahre      = $migareference->getSocialsharesUser($user_id);
    $gdpr_settings     = $migareference->get_gdpr_settings($app_id);
    $is_allow_socialshare = (count($social_sahre)) ? 2 : 1 ;        
    $long_url = $base_url."/migareference/landingreport?app_id=".$app_id."&user_id=".$user_id."&agent_id=".$agent_id."&report_by=".$report_by."&type=".$type."&report_custom_type=".$report_custom_type;        
    $short_url = $utilities->shortLink($long_url);
    $datas['app_url'] = $short_url;    
    $invite_message=$gdpr_settings[0]['invite_message'];
    $invite_message = $gdpr_settings[0]['invite_message'];
    $invite_message = str_replace('@@referrer_name@@', $referer[0]['firstname'] . ' ' . $referer[0]['lastname'], $invite_message);
    $invite_message = str_replace('@@landing_link@@', $datas['app_url'], $invite_message);

    $datas['agent_data']  = $agent_data[0];    
    $datas['is_allow_socialshare']  = $is_allow_socialshare;
    $payload = [
        'success'     => true,
        'data'      => $datas,
        'invite_message'=> $invite_message,
        'gdpr_settings'=> $gdpr_settings,        
        'short_url'=>$short_url,        
    ];
    $this->_sendJson($payload);
  }
  public function markasdoneAction(){
    try {      
      $log_id           = $this->getRequest()->getParam('log_id');
      $migareference    = new Migareference_Model_Migareference();       
      $migareference->markasDon($log_id,'done');      
      $payload = [
        "success" => true,
        "message" => __("Status successfully updated."),        
      ];      
    } catch (\Throwable $th) {
      $payload = [
        "success" => false,
        "message" => __("Something went wrong, Please try again later."),        
    ];
    }
   
    $this->_sendJson($payload);
  }
  public function gethowtoAction(){
          $migareference = new Migareference_Model_Migareference();
          $application  = $this->getApplication();
          $youtube_key  = $application->getYoutubeKey();
          $app_id       = $application->getId();
          $default      = new Core_Model_Default();
          $base_url     = $default->getBaseUrl();
          $how_to_result= $migareference->gethowto($app_id);
          $bitly_crede  = $migareference->getBitlycredentails($app_id);
          $customer_id  = $this->getRequest()->getParam('customer_id');
          $utilities = new Migareference_Model_Utilities();
          //Build Referral Link
          $referrer_link = $base_url."/migareference/landingreport?app_id=".$app_id."&user_id=".$customer_id."&agent_id=0&report_by=0&type=1&report_custom_type=1";
          $referrer_link = $utilities->shortLink($referrer_link);
          $how_to_result[0]['referrer_link']=$referrer_link;
          $how_to_result[0]['how_to_text'] = str_replace('span', 'p', $how_to_result[0]['how_to_text']);
          // Video link
          $temp_source=$how_to_result[0]['how_to_video_source'];
          $source='';
          $source.='<div id="Container" style="padding-bottom:56.25%; position:relative; display:block; width: 100%">';
          $source.= str_replace('<iframe ', ' <iframe style="position:absolute; top:0; left: 0;height:100% !important;width:100% !important"', $temp_source);
          $source.='</div>';
          $how_to_result[0]['how_to_video_source']=$source;
          $how_to_result[0]['fm_height']='40';
          $how_to_result[0]['how_to_source_unit']='vh';
          $video_id   = $this->parseyoutubetokenbyuriAction($how_to_result[0]['video_link']);
          $search     = $this->getRequest()->getParam('search');
          $json_url   = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=".urlencode($video_id)."&key=".$youtube_key;
          $json       = file_get_contents($json_url);
          $value      = json_decode($json, TRUE);
          $data       = array("videos" => array());
          for ($i = 0; $i < sizeof($value['items']); $i++) {
              if ($video_id) {
                  $data["videos"][] = array(
                      "offset"        => $i + 1,
                      "video_id"      => $video_id,
                      "is_visible"    => false,                      
                      "url"           => 'https://www.youtube.com/embed/'.$video_id,
                      "url_embed"     => 'https://www.youtube.com/embed/'.$video_id,
                      "cover_url"     => $value['items'][$i]['snippet']['thumbnails']['default']['url'],
                      "title"         => $value['items'][$i]['snippet']['title'],
                      "description"   => $value['items'][$i]['snippet']['description'],
                      "button"        => 'Show Related',
                  );
              }
      }
          if (!empty($how_to_result[0]['site_link'])) {
            if (strpos($how_to_result[0]['site_link'], 'http') !== false) {
                  $a=true;
                }else {
                  $how_to_result[0]['site_link']='https://'.$how_to_result[0]['site_link'];
                }
          }

          $payload  = [
              'success' => true,
              'gethowto'=> $how_to_result[0],
              'collection'=>$data,
              'app_id'=>$app_id,
              'other'=>$json,
              "source"        => $source,
              'ther'=>$json_url
          ];
      $this->_sendJson($payload);
  }
  public function chrckmandateAction(){
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $pkid          = $this->getRequest()->getParam('pkid');
          $report_id     = $this->getRequest()->getParam('report_id');
          $checkmandate  = $migareference->reportStatusByKey($pkid,$report_id);
          $pre_report    = $migareference->preReportsettigns($app_id);
          $report        = $migareference->get_report_by_key($report_id);
          $payload  = [
              'success' => true,
              'status_data'=> $checkmandate[0],
              'pre_report'=>$pre_report,
              'report'=>$report[0]
          ];
      $this->_sendJson($payload);
  }

  private function sortByname($a,$b)
  {
      return strcmp( $a['name'], $b['name'] );
  }
  public function getpropertysettingsAction(){
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $user_id       = $this->getRequest()->getParam('user_id');

          $property_settings_result = $migareference->getpropertysettings($app_id,$user_id);
          $pre_property_settings    = $migareference->preReportsettigns($app_id);
          $customer                 = $migareference->getSingleuser($app_id,$user_id);
          $total_earn               = $migareference->get_earnings($app_id,$user_id);
          $customer_agents          = $migareference->get_customer_agents($app_id);
          $partner_agents           = $migareference->get_partner_agents($app_id);
          $admins                   = $migareference->getAdmins($app_id);
          $is_admin                 = $migareference->is_admin($app_id,$user_id);
          $standard_status          = $migareference->get_standard($app_id);
          $all_jobs                 = $migareference->getJobs($app_id);
          $all_professions          = $migareference->getProfessions($app_id);
          $user_account_settings    = $migareference->useraccountSettings($app_id);          
          $countries                = $migareference->geoProvinceCountries($app_id);//Countries which have provinces 
          $address_province_list    = [];
          $customer_agent_list      = [];
          $partner_agent_list       = [];
          $agent_province_list      = [];
          $is_agent_list            = false;
          $is_geo_list              = false;
          $user_account_settings    = json_decode($user_account_settings[0]['settings']);

          $pre_property_settings[0]['enable_birthdate']    = ($user_account_settings->extra_birthdate) ? 1 : 2 ;
          $pre_property_settings[0]['mandatory_birthdate'] = ($user_account_settings->extra_birthdate_required) ? 1 : 2 ;
          $jobs_collection[]=['name'=>__("Non classifiable"),'job_id'=> 0];          
          foreach ($all_jobs as $key => $value) {
              $jobs_collection[$value['migareference_jobs_id']]=[
                                'name'=>$value['job_title'],
                                'job_id'=>$value['migareference_jobs_id']
                            ];
            }            
          $professions_collection=[];          
          foreach ($all_professions as $key => $value) {
              $professions_collection[$value['migareference_professions_id']]=[
                                'name'=>$value['profession_title'],
                                'profession_id'=>$value['migareference_professions_id']
                            ];
            }          
          $professions_collection[]=['name'=>__("N/A"),'professions_id'=> 0];          

          if (count($customer_agents) || count($partner_agents)) {
            if ($pre_property_settings[0]['sponsor_type']==1) {
              $is_agent_list=true;
            }else {
              $property_settings_result[0]['province_id']=$property_settings_result[0]['sponsor_id']."@".$property_settings_result[0]['province_id'];
              $is_geo_list=true;
            }
            if ($pre_property_settings[0]['enable_only_agent_provinces']==2) {
              $agentProvinces=$migareference->getGeoCountrieProvinces($app_id,0);
              foreach ($agentProvinces as $prov_key => $prove_value) {
                $agent_province_list[]=[
                    'id'=>$prove_value['user_id'],
                    'province'=>$prove_value['province'],
                    'province_id'=>"0"."@".$prove_value['migareference_geo_provinces_id']
                ];
              }
            }
            foreach ($customer_agents as $key => $value) {
              if ($pre_property_settings[0]['enable_only_agent_provinces'] == 1) {
                  $agentProvinces = $migareference->agentProvinces($app_id, $value['customer_id']);
                  foreach ($agentProvinces as $keyy => $valuee) {
                      $agent_province_list[] = [
                          'id' => $valuee['user_id'],
                          'province' => $valuee['province'],
                          'province_id' => $valuee['user_id'] . "@" . $valuee['porvince_id']
                      ];
                  }
              }
              $customer_agent_list[$value['customer_id']] = [
                  'id' => $value['customer_id'],
                  'name' => $value['lastname'] . ' ' . $value['firstname']
              ];
            }
            
            foreach ($partner_agents as $key => $value) {
                if ($pre_property_settings[0]['enable_only_agent_provinces'] == 1) {
                    $agentProvinces = $migareference->agentProvinces($app_id, $value['customer_id']);
                    foreach ($agentProvinces as $keyy => $valuee) {
                        $agent_province_list[] = [
                            'id' => $valuee['user_id'],
                            'province' => $valuee['province'],
                            'province_id' => $valuee['user_id'] . "@" . $valuee['porvince_id']
                        ];
                    }
                }
                $partner_agent_list[$value['customer_id']] = [
                    'id' => $value['customer_id'],
                    'name' => $value['lastname'] . ' ' . $value['firstname']
                ];
            }
          }
          // Sort customer_agent_list by name
          uasort($customer_agent_list, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
          });

          // Sort partner_agent_list by name
          uasort($partner_agent_list, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
          });
          if ($pre_property_settings[0]['enable_mandatory_agent_selection']==2) {                        
            $customer_agent_list = ['0' => ['id' => 0,'name' => __("I don’t know")]] + $customer_agent_list;
            $partner_agent_list = ['0' => ['id' => 0,'name' => __("I don’t know")]] + $partner_agent_list;
        } else {            
            $customer_agent_list = ['0' => ['id' => 0,'name' => __("Select Sponsor")]] + $customer_agent_list;
            $partner_agent_list = ['0' => ['id' => 0,'name' => __("Select Sponsor")]] + $partner_agent_list;
        }     
                             
          $read_only      = 2;          
          $earning        = ($total_earn[0]['total_earn']>0) ? $total_earn[0]['total_earn'] : 0 ;          
          $is_blocked     = ($property_settings_result[0]['status']==0) ? 0 : 0 ;                       
          $setup_settings = (count($pre_property_settings) && $property_settings_result[0]['terms_accepted']==0) ? 0 : 1 ;

          //Build birth date
          if (!empty($customer[0]['birthdate']) && $customer[0]['birthdate']!=0 && $customer[0]['birthdate']!=-3600) {
            $birht_date = date('d-m-Y', $customer[0]['birthdate']);
            $timestamp  = strtotime($birht_date);
            $property_settings_result[0]['birth_day']=date("d", $timestamp);
            $property_settings_result[0]['birth_month']=date("m", $timestamp);
            $property_settings_result[0]['birth_year']=date("Y", $timestamp);
              if ($property_settings_result[0]['birth_day']=='01' && $property_settings_result[0]['birth_month']=='01' && $property_settings_result[0]['birth_year']=='1970') {
                $property_settings_result[0]['birth_day']="00";
                $property_settings_result[0]['birth_month']="00";
                $property_settings_result[0]['birth_year']="0000";    
              }
          }else {
            $property_settings_result[0]['birth_day']="00";
            $property_settings_result[0]['birth_month']="00";
            $property_settings_result[0]['birth_year']="0000";
          }                    
          if (!empty($customer[0]['mobile'])) {
            $property_settings_result[0]['invoice_mobile']=$customer[0]['mobile'];
          }
          // Status counter
          $is_standard=0;
          if (count($pre_property_settings)) {
            $standard_limit    = 3;
            $standard_index    = count($standard_status)+1;
            if ($pre_property_settings[0]['commission_type']==1 || $pre_property_settings[0]['commission_type']==3) {
              $standard_limit++;
            }
            if ($standard_limit!=count($standard_status)) {
              $is_standard=0;
            }
          }
          // read_only//1,2//Yes,No
          if (!empty($pre_property_settings) && $pre_property_settings[0]['read_only']==1) {
            $read_only=1;
            $read_only_err="This Option is only readable. Please contact to App Owner for new reports.";
          }else {
            $read_only=2;
            $read_only_err="This Option is only readable. Please contact to App Owner for new reports.";
          }
          if ($read_only==2 && !count($admins)) {
            $read_only=1;
            $read_only_err="Their is no Admin user exist.";
          }
          $is_no_admin = (count($admins)) ? 0 : 1 ;   //This app any single admin or not       
          $is_admin    = (count($is_admin)) ? true : false ; //The current user is admin or not
          // Work on LIMIT
          $is_need_vat_id=0;
          if (count($pre_property_settings) && count($property_settings_result)) {
            if ($earning>=$pre_property_settings[0]['payable_limit'] && $property_settings_result[0]['vat_id']=="") {
              $is_need_vat_id=1;
            }
          }          
          $agent_province_list =  array_map("unserialize", array_unique(array_map("serialize", $agent_province_list)));
          sort($agent_province_list);          
          // Main and Sub Address settings
          $default_country_id=0;
          foreach ($countries as $key => $value) {                   
            $countries_list[]=[
              'country'=>$value['country'],
              'country_id'=>$value['migareference_geo_countries_id']
            ];
          }
          $countries_list =  array_map("unserialize", array_unique(array_map("serialize", $countries_list)));
          sort($countries_list);
            if (count($countries_list)) {
            $default_country_id=$countries_list[0]['country_id'];
          }
            if (count($property_settings_result) && count($countries_list)>1) {
            $default_country_id=$property_settings_result[0]['address_country_id'];
          }
          $country_provinces        = $migareference->getGeoCountrieProvinces($app_id,$default_country_id);
          foreach ($country_provinces as $key => $value) {
            $address_province_list[]=[
              'province'=>$value['province'],
              'province_id'=>$value['migareference_geo_provinces_id']
            ];
          }
          $address_province_list =  array_map("unserialize", array_unique(array_map("serialize", $address_province_list)));
          sort($address_province_list);
          if (count($property_settings_result)) {
            // Compatible logic: $property_settings_result[0]['province_id'] is dprecated and in new version
            $property_settings_result[0]['province_id']=$property_settings_result[0]['address_province_id'];
          }
          // END Address Settings
          $property_settings_result[0]['sponsor_id'] = (Integer)($property_settings_result[0]['sponsor_one_id'] > 0) ? $property_settings_result[0]['sponsor_one_id'] : 0;
          $payload  = [
              'success'             => true,
              'getPropertysettings' => $property_settings_result,
              'agent_list'          => $customer_agent_list,
              'partner_agent_list'  => $partner_agent_list,
              'job_list'            => $jobs_collection,
              'profession_list'     => $professions_collection,
              'agnet_province_list' => $agent_province_list,
              'pre_report'          => $pre_property_settings,
              'setup_settings'      => $setup_settings,
              'read_only'           => $read_only,
              'is_blocked'          => $is_blocked,
              'is_standard'         => $is_standard,
              'is_no_admin'         => $is_no_admin,
              'is_admin'            => $is_admin,
              'is_agent_list'       => $is_agent_list,
              'is_geo_list'         => $is_geo_list,
              'is_need_vat_id'      => $is_need_vat_id,
              'notif_data_option'   => $notif_data_option,
              'customer'            => $customer,
              'default_country_id'  => $default_country_id,
              'countries_list'      => $countries_list,
              'countries_count'     => count($countries_list),
              'address_province_list'=> $address_province_list,
              'error_text'          => __('Warning'),
              'is_need_vat_id_err'  => __('Your Earning max Limit meet. You must setup the VAT-ID in settings to proceed farther.'),
              'is_blocked_err'      => __('Only Authorized user allowed.'),
              'read_only_err'       => __($read_only_err),
              'setup_settings_err'  => __('You need to first accept the terms in Settings area.'),
              'is_admin_err'        => __('Admin User cannot Submit Report.'),
              'is_agent_err'        => __('Agent User cannot Submit Report.'),
              'is_standard_err'     => __('Status settings Missed. Please contact to admin.'),
          ];
      $this->_sendJson($payload);
  }
  public function savepropertysettingsAction(){
    try {
            $application   = $this->getApplication();
            $app_id        = $application->getId();
            $migareference = new Migareference_Model_Migareference();
            $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
            $default        = new Core_Model_Default();
            $base_url       = $default->getBaseUrl();
            $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $data['app_id']= $app_id;
            $errors        = "";
            $pre_report    = $migareference->preReportsettigns($app_id);
            $user_account_settings = $migareference->useraccountSettings($app_id);
            $user_account_settings = json_decode($user_account_settings[0]['settings']);
            if (isset($data['sponsor_id']['id'])) {//from some version we get this formate
              $data['sponsor_id'] = ($data['sponsor_id']['id'] > 0) ? $data['sponsor_id']['id'] : 0;
            }
            if (empty($data['notification_type'])) {
                $errors .= __('You must select Notification Type.') . "<br/>";
            }
            if ($data['sponsor_id']==0 && $pre_report[0]['sponsor_type']==1 && $pre_report[0]['enable_mandatory_agent_selection']==1) {
              $errors .= __('You must answer sponsor rule.') . "<br/>";
            }
            if (empty($data['province_id']) && $pre_report[0]['sponsor_type']==2 && $data['app_short_version']>=1.148 && isset($data['app_short_version'])) {
               $errors .= __('You must select province.') . "<br/>";
            }
            if (empty($data['blockchain_password'])) {
               $errors .= __('Block Chain Password cannot be empty.') . "<br/>";
            }            
            if (empty($data['invoice_name'])) {
               $errors .= __('Invoice Name cannot be empty.') . "<br/>";
            }
            if (empty($data['invoice_surname'])) {
               $errors .= __('Invoice Surname cannot be empty.') . "<br/>";
            }
            if (isset($data['app_short_version']) && $data['app_short_version']>=1.148 && $user_account_settings->extra_birthdate==true && $user_account_settings->extra_birthdate_required==true && (empty($data['birth_day']) || empty($data['birth_month']) || empty($data['birth_year']))) {
               $errors .= __('Birth Date cannot be empty.') . "<br/>";
            }
            if (empty($data['company']) && $pre_report[0]['mandatory_company']==1) {
               $errors .= __('Company name cannot be empty.') . "<br/>";
            }
            if (empty($data['job_id']) && $pre_report[0]['mandatory_profession']==1) {
               $errors .= __('Profession cannot be empty.') . "<br/>";
            }
            if (empty($data['profession_id']) && $pre_report[0]['mandatory_sector']==1) {
               $errors .= __('Sector cannot be empty.') . "<br/>";
            }
            if (empty($data['extra_one_text']) && $pre_report[0]['mandatory_extra_one']==1 && $pre_report[0]['extra_one']==1) {
               $errors .= __('You should fill all mandatory fields.') . "<br/>";
            }
            if (empty($data['extra_two_text']) && $pre_report[0]['mandatory_extra_two']==1 && $pre_report[0]['extra_two']==1) {
               $errors .= __('You should fill all mandatory fields.') . "<br/>";
            }
            if (empty($data['tax_id'])) {
               $errors .= __('Tax ID cannot be empty.') . "<br/>";
            }
            if ($data['terms_accepted']==false || $data['terms_accepted']!=1) {
               $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
            }
            if (isset($data['special_terms_accepted']) && !empty($pre_report[0]['term_label_text'])) {
              if ($data['special_terms_accepted']==false || $data['special_terms_accepted']!=1) {
                 $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
              }
            }
            if ($data['privacy_accepted']==false || $data['privacy_accepted']!=1) {
               $errors .= __('You must Accept Privacy conditions to save settings.').$data['privacy_accepted'] . "<br/>";
            }            
            if ($pre_report[0]['mandatory_main_address']==1 && empty($data['province_id'])) {
              $errors .= __('You must select province.') . "<br/>";
            }            
            $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
            $birth_date = date('Y-m-d',strtotime($b_date));
            $chnage_by=$data['chnage_by'];
            
            unset($data['birth_day']);
            unset($data['birth_month']);
            unset($data['birth_year']);
            unset($data['app_short_version']);
            unset($data['chnage_by']);
            if (!empty($errors)) {
            throw new Exception($errors);
          }else{
            $id = ($data['operation']=='save') ? 999999 : $data['migareference_invoice_settings_id'] ;            
            // For geo location use sponsor id
            // 03/09/2023 commeent out for Customer reported bug in versions
            // $data['sponsor_id'] = ($data['sponsor_id']['id']>0) ? $data['sponsor_id']['id'] : 0;                                                
            // Manage Agent keys while agent type is geolocation
            $data['partner_sponsor_id']=0;//partner sponsor is only used when type is
            if ($pre_report[0]['sponsor_type']==2) {                      
              $agent_provonces=$migareference->agentMultiGeoProvince($data['app_id'],$data['province_id']);
              $agent_count=COUNT($agent_provonces);
              $data['sponsor_id']=0;              
              if($agent_count==1){
                $data['sponsor_id']=$agent_provonces[0]['user_id'];                        
              }else if($agent_count==2){
                $data['sponsor_id']=$agent_provonces[0]['user_id'];
                $data['partner_sponsor_id']=$agent_provonces[1]['user_id'];
              }
            }                 
                        
            $duplicate_data= $migareference->checkDuplication($data,$id);
            $data['address_province_id']=$data['province_id'];
            // Manage Job & Profession            
            
            if (!isset($data['add_job'])) {
              $data['job_id']=0;
            }
            if (isset($data['job_id']['job_id'])) {
              $data['job_id']=$data['job_id']['job_id'];
            }           
            $data['profession_id']=$data['profession_id']['profession_id'];   
            $phonne_book['profession_id']=$data['profession_id'];
            $phonne_book['job_id']=$data['job_id'];            
                       
            if ($data['operation']=='save' && empty($duplicate_data)) {                                 
              unset($data['terms']);
              unset($data['operation']);
              $gcmdata=$migareference->checkGcm($data['user_id'],$data['app_id']);
              if (count($gcmdata)) {
                $data['token']=$gcmdata[0]['registration_id'];
              }else {
                $apnsdata=$migareference->checkApns($data['user_id'],$data['app_id']);
                $data['token']=$gcmdata[0]['device_token'];
              }
              
                unset($data['add_job']);
                $data['birth_date']=$birth_date;
                $customer['birthdate']= strtotime($data['birth_date']);
                $migareference->updateCustomerdob($data['user_id'],$customer);
                $data['referrer_source']=1;
                                           
                unset($data['birthdate']);
                unset($data['mobile']);
                unset($data['firstname']);
                unset($data['lastname']);
                unset($data['email']);
                $migareference->savePropertysettings($data);  
                
                // Send Welcome Email to referrer
                if ($pre_report[0]['enable_welcome_email']==1
                  && !empty($pre_report[0]['referrer_wellcome_email_title'])
                  && !empty($pre_report[0]['referrer_wellcome_email_body']))
                {
                  $notificationTags=$migareference->welcomeEmailTags();
                  if (isset($data['customer_sponsor_id']) && !empty($data['customer_sponsor_id']) ) {
                    $agent_user=$migareference->getSingleuser($app_id,$data['customer_sponsor_id']);
                  }
                  $customer=$migareference->getSingleuser($app_id,$data['user_id']);
                  $notificationStrings = [
                    $customer[0]['firstname']." ".$customer[0]['lastname'],
                    $customer[0]['email'],
                    $data['first_password'],
                    $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                    $app_link
                  ];
                  $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_report[0]['referrer_wellcome_email_title']);
                  $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_report[0]['referrer_wellcome_email_body']);
                  $email_data['type']        = 2;//type 2 for wellcome log
                  $migareference->sendMail($email_data,$app_id,$data['user_id']);
                }  
                           
              }else if($data['operation']=='update' && !empty($duplicate_data) && $duplicate_data[0]['migareference_invoice_settings_id']==$id) {                                      
                $referrer_previous_data=$migareference->getpropertysettings($app_id,$data['user_id']);                        
                $gcmdata=$migareference->checkGcm($data['user_id'],$data['app_id']);
                if (count($gcmdata)) {
                  $data['token']=$gcmdata[0]['registration_id'];
                }else {
                  $apnsdata=$migareference->checkApns($data['user_id'],$data['app_id']);
                  $data['token']=$gcmdata[0]['device_token'];
                }
                /* Update Phonebook if their is any change in phonebook
                * job_id
                * profession_id
                */
                $phobook_item=$migareference->getInvoicePhonebook($data['migareference_invoice_settings_id']);
                if (count($phobook_item)) {                                    
                  $phone_return=$migareference->update_phonebook($phonne_book,$phobook_item[0]['migarefrence_phonebook_id'],$chnage_by,1);//Also save log if their is change in Rating,Job,Notes                  
                }      
               /* Update Sib Customer Table
                * mobile
                * birthdate
                */
                $data['birth_date']   = $birth_date;
                $customer['mobile']   = $data['invoice_mobile'];
                $customer['birthdate']= strtotime($data['birth_date']);
                $migareference->updateCustomerdob($data['user_id'],$customer);    
                // Manage Referrer Agetns
                $invoice_item=$migareference->getReferrerByKey($data['migareference_invoice_settings_id']);//get prev item before update                                        
                $migareference->deleteSponsor($data['user_id']);                          
                $referrer_agent['app_id']=$data['app_id'];
                $referrer_agent['referrer_id']=$data['user_id'];
                $referrer_agent['created_at']=date('Y-m-d H:i:s');
                if ($pre_report[0]['sponsor_type']==2) {  //sponsor type is Geo Location                  
                  if ($invoice_item[0]['address_province_id']!=$data['address_province']) {                                                                              
                    $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                    if ($referrer_agent['agent_id']!=0) {
                      $migareference->addSponsor($referrer_agent);
                    }                
                    $referrer_agent['agent_id']= (isset($data['partner_sponsor_id'])) ? $data['partner_sponsor_id'] : 0 ;
                    if ($referrer_agent['agent_id']!=0) {
                      $migareference->addSponsor($referrer_agent);        
                    }                          
                  }                        
                }else if($pre_report[0]['sponsor_type']==1){ //sponsor type is Standard                   
                  $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                    if ($referrer_agent['agent_id']!=0) {
                      $migareference->addSponsor($referrer_agent);
                    } 
                }
                //  Update Referrer Record
                $invoice_data['app_id']                   = $data['app_id'];
                $invoice_data['user_id']                  = $data['user_id'];
                $invoice_data['province_id']              = $data['province_id'];                
                $invoice_data['blockchain_password']      = $data['blockchain_password'];
                $invoice_data['invoice_name']             = $data['invoice_name'];
                $invoice_data['invoice_surname']          = $data['invoice_surname'];
                $invoice_data['invoice_mobile']           = $data['invoice_mobile'];
                $invoice_data['company']                  = $data['company'];
                $invoice_data['leagal_address']           = $data['leagal_address'];
                $invoice_data['tax_id']                   = $data['tax_id'];
                $invoice_data['vat_id']                   = $data['vat_id'];
                $invoice_data['extra_one_text']           = $data['extra_one_text'];
                $invoice_data['extra_two_text']           = $data['extra_two_text'];
                $invoice_data['terms_accepted']           = $data['terms_accepted'];
                $invoice_data['special_terms_accepted']   = $data['special_terms_accepted'];
                $invoice_data['privacy_accepted']         = $data['privacy_accepted'];
                $invoice_data['privacy_artical_accepted'] = $data['privacy_artical_accepted'];
                $invoice_data['terms_artical_accepted']   = $data['terms_artical_accepted'];
                $invoice_data['birth_date']               = $data['birth_date'];
                $invoice_data['address_province_id']      = $data['address_province_id'];
                $invoice_data['address_country_id']       = $data['address_country_id'];
                $invoice_data['address_street']           = $data['address_street'];
                $invoice_data['address_city']             = $data['address_city'];
                $invoice_data['address_zipcode']          = $data['address_zipcode'];
                $migareference->updatePropertysettings($invoice_data,$data['migareference_invoice_settings_id']);    
                // Trigger webhook if their is change in Referrer
                $referrer_new_data=$migareference->getpropertysettings($app_id,$data['user_id']);                                        
                $changes_detect=(new Migareference_Model_Utilities())->detectReferrerChanges($referrer_previous_data,$referrer_new_data,$app_id);//This will detect changes and trigger webhook if changes found                                                                                 
              }
          }
          $payload = [
              'success' => true,
              'message' => __("Successfully Settings saved."),                            
              'phonne_book'=>$phonne_book,
              'phone_return'=>$phone_return,
            ];
          } catch (Exception $e) {
            $payload = [
              'error' => true,
              'message' => __($e->getMessage()),          
              'received'=>$duplicate_data,
              'agent_provonces'=>$agent_provonces,
      ];
    }
    $this->_sendJson($payload);
  }
  public function savephonedetailAction(){
    try {            
            $migareference = new Migareference_Model_Migareference();
            $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
            $app_id        = $this->getApplication()->getId();
            $pre_settings  = $migareference->preReportsettigns($app_id);
            $data['app_id']= $app_id;
            $errors        = "";            
            if (empty($data['name'])){
              $errors .= __('Please add a valid Name.') . "<br/>";
            }
            if (empty($data['surname'])){
              $errors .= __('Please add a valid Surname.') . "<br/>";
            }
            if (strlen($data['mobile']) < 10 || strlen($data['mobile']) > 14 || empty($data['mobile']) || preg_match('@[a-z]@', $data['mobile'])
            || (substr($data['mobile'], 0, 1)!='+' && substr($data['mobile'], 0, 2)!='00')){
              $errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
            }
            $temp_email=$data['email'];
            if (empty($temp_email)) {
              $temp_email="@gmail.com";
            }
            if (!empty($data['mobile'])) {
              $data['mobile'] = str_replace(' ', '', $data['mobile']);
              $phone_email_exist=$migareference->isPhoneEmailExist($data['app_id'],$temp_email,$data['mobile'],$data['type']);
              if ($data['operation']=='update' && count($phone_email_exist) && $phone_email_exist[0]['migarefrence_phonebook_id']!=$data['migarefrence_phonebook_id']) {
                $errors .= __('Email or Mobile already exist.') . "<br/>";
              }
            }
          if (!empty($errors)) {
            throw new Exception($errors);
          }else{
            // Build DOB
            $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
            $birth_date = date('Y-m-d',strtotime($b_date));
            // Define Phonebook or Prospect Common entries
            $phonebook_item["name"]         = $data['name'];
            $phonebook_item["surname"]      = $data['surname'];
            $phonebook_item["email"]        = $data['email'];
            $phonebook_item["mobile"]       = $data['mobile'];
            $phonebook_item["note"]         = $data['note'];
            $phonebook_item["reciprocity_notes"] = $data['reciprocity_notes'];
            $phonebook_item["job_id"]       = $data['job_id'];
            $phonebook_item["profession_id"]= $data['profession_id'];
            $phonebook_item["rating"]       = $data['rating'];
            $phonebook_item["is_blacklist"] = $data['is_blacklist'];            
            // For Referrer Phonebook Type ==1
            if ($data['type']==1) {
              // Update Referrer or Invoice Table              
              $invoice_data['address_country_id']  = $data['address_country_id'];
              $invoice_data['address_street']      = $data['address_street'];
              $invoice_data['address_zipcode']     = $data['address_zipcode'];
              $invoice_data['address_city']        = $data['address_city'];
              $invoice_data['address_province_id'] = $data['address_province_id'];
              $migareference->updatePropertysettings($invoice_data,$data['invoice_id']);
              // Manage Agents        
              $invoiceDataItem=$migareference->getInvoiceItem($data['invoice_id']);
              $migareference->deleteSponsor($invoiceDataItem[0]['user_id']);                          
              $referrer_agent['app_id']=$app_id;
              $referrer_agent['referrer_id']=$invoiceDataItem[0]['user_id'];
              $referrer_agent['created_at']=date('Y-m-d H:i:s');                      
             if ($pre_settings[0]['enable_multi_agent_selection']==1) {                                                                           
                  $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                  if ($referrer_agent['agent_id']!=0) {
                    $migareference->addSponsor($referrer_agent);
                  }                
                  // $referrer_agent['agent_id']= (isset($data['partner_sponsor_id'])) ? $data['partner_sponsor_id'] : 0 ;
                  // if ($referrer_agent['agent_id']!=0) {
                  //   $migareference->addSponsor($referrer_agent);        
                  // }                                                     
              }else{                        
                $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                  if ($referrer_agent['agent_id']!=0) {
                    $migareference->addSponsor($referrer_agent);
                  } 
              }    
              // Update Customer Table to Sync DOB
              $customer['birthdate']= strtotime($birth_date);
              $migareference->updateCustomerdob($invoiceDataItem[0]['user_id'],$customer);
              // Update Phonebook Table
              $migareference->update_phonebook($phonebook_item,$data['migarefrence_phonebook_id'],$data['change_by'],1);//Also save log if their is change in Rating,Job,Notes
			  
            }else {                            
              $response=$migareference->update_prospect($phonebook_item,$data['migarefrence_prospect_id'],$data['change_by'],2);//Also save log if their is change in Rating,Job,Notes                    
              $report_prospect["owner_name"]   = $data['name'];
              $report_prospect["owner_surname"]= $data['surname'];
              $report_prospect['owner_mobile'] = $data['mobile'];
              $report_prospect['updated_at']   = date('Y-m-d H:i:s');
              $report_prospect['owner_dob']    = $birth_date;              
              $migareference->updateReportProspect($report_prospect,$data['migarefrence_prospect_id']);
            }            
          }
          $payload = [
              'success' => true,
              'message' => __("Successfully data saved."),
              'data' => $data,
              'res_tes' => $res_tes,
              'res_t' => $data['change_by'],
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function savephoneentryAction(){
    try {
            $data           = Siberian_Json::decode($this->getRequest()->getRawBody());
            $migareference  = new Migareference_Model_Migareference();
            $application    = $this->getApplication();
            $app_id         = $application->getId();
            $pre_settings   = $migareference->preReportsettigns($app_id);
            $data['app_id'] = $app_id;
            $default        = new Core_Model_Default();
            $base_url       = $default->getBaseUrl();
            $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $errors         = "";
            $birth_date     = 0;
            if (empty($data['name'])){
              $errors .= __('Please add a valid Name.') . "<br/>";
            }
            if (empty($data['surname'])){
              $errors .= __('Please add a valid Surname.') . "<br/>";
            }
            //Their was type mistake 10/28/21 to make it compatible with previous apps exchange type <=2.2.11
            if ($data['type']==1) {
              // if($pre_settings[0]['enable_birthdate']==1 && $pre_settings[0]['mandatory_birthdate']==1 && (empty($data['birth_day']) || empty($data['birth_month']) || empty($data['birth_year']))){
              //   $errors .= __('Please add a valid Birthdate.') . "<br/>";
              // }
              $data['type']==2;
              if(empty($data['password'])){
                // Disable Mandatory because app is not update to minimum version for this 2.4.2
                // $errors .= __('Please add a valid Password.') . "<br/>";
              }
              if(empty($data['email'])){
                $errors .= __('Please add a valid Email.') . "<br/>";
              }else{
                $customer=$migareference->getCustomer($app_id,$data['email']);
                $user_id=$customer[0]['customer_id'];
              }
            }else {
              $data['type']==1;
            }
            if (strlen($data['mobile']) < 10
            || strlen($data['mobile']) > 14
            || empty($data['mobile'])
            || preg_match('@[a-z]@', $data['mobile'])
            || (
              substr($data['mobile'], 0, 1)!='+'
              && substr($data['mobile'], 0, 2)!='00')
              ){
              $errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
            }
            if (isset($data['job_id']['job_id'])) {
                $data['job_id']=$data['job_id']['job_id'];
            }
            if (isset($data['profession_id']['profession_id'])) {
                $data['profession_id']=$data['profession_id']['profession_id'];
            }
            $data['mobile'] = str_replace(' ', '', $data['mobile']);
            $phone_email_exist=$migareference->isPhoneEmailExist($data['app_id'],'',$data['mobile'],$data['type']);
            if ($data['operation']=='update' && count($phone_email_exist)) {
              $errors .= __('Mobile already exist.') . "<br/>";
            }
          if (!empty($errors)) {
            throw new Exception($errors);
          }else{                  
                  $password = $data['password'];
                  if(!empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])){
                    if ($b_date === '00-00-0000') {
                      $birth_date = '0';
                    } else {
                      $birth_date = date('Y-m-d',strtotime($b_date));
                      $birth_date = strtotime($birth_date);
                    }
                  }
                  $change_by=$data['change_by'];
                  unset($data['birth_day']);
                  unset($data['birth_month']);
                  unset($data['birth_year']);
                  unset($data['password']);
                  unset($data['change_by']);                  
                  if (!count($phone_email_exist)) {
                    // create Customer
                    if(!count($customer)){
                      $customer['app_id']         = $app_id;
                      $customer['firstname']      = $data['name'];
                      $customer['lastname']       = $data['surname'];
                      $customer['email']          = $data['email'];
                      $customer['mobile']         = $data['mobile'];
                      $customer['birthdate']      = $birth_date;
                      $customer['password']       = sha1($password);
                      $customer['privacy_policy'] = 1;
                      $user_id=$migareference->createUser($customer);
                    }                    
                    // Save Invoice
                $inv_settings['app_id']                 = $app_id;
                $inv_settings['user_id']                = $user_id;
                $inv_settings['blockchain_password']    = $this->randomPassword();
                $inv_settings['invoice_name']           = $data['name'];
                $inv_settings['sponsor_id']             = $data['sponsor_id'];
                $inv_settings['job_id']                 = $data['job_id'];
                $inv_settings['profession_id']          = $data['profession_id'];
                $inv_settings['partner_sponsor_id']     = $data['partner_sponsor_id'];
                $inv_settings['invoice_surname']        = $data['surname'];
                $inv_settings['invoice_mobile']         = $data['mobile'];
                $inv_settings['note']                   = $data['note'];
                $inv_settings['reciprocity_notes']      = $data['reciprocity_notes'];
                $inv_settings['tax_id']                 = $this->randomTaxid();;
                $inv_settings['terms_accepted']         = 1;
                $inv_settings['special_terms_accepted'] = 1;
                $inv_settings['privacy_accepted']       = 1;
                $inv_settings['address_country_id']     = $data['address_country_id'];
                $inv_settings['address_street']         = $data['address_street'];
                $inv_settings['address_zipcode']        = $data['address_zipcode'];
                $inv_settings['address_city']           = $data['address_city'];
                $inv_settings['address_province_id']    = $data['address_province_id'];                
                
                $migareference->savePropertysettings($inv_settings); //This method also save phonebook entry if previously not exist
                
                
              // Send Welcome Email to referrer
                if ($pre_settings[0]['enable_welcome_email']==1
                    && !empty($pre_settings[0]['referrer_wellcome_email_title'])
                    && !empty($pre_settings[0]['referrer_wellcome_email_body']))
                  {
                  $notificationTags=$migareference->welcomeEmailTags();
                  // $agent_user=$migareference->getSingleuser($app_id,$data['sponsor_id']);
                  $notificationStrings = [
                    $customer['firstname']." ".$customer['lastname'],
                    $customer['email'],
                    $password,
                    $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                    $app_link
                  ];
                  $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_title']);
                  $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_body']);
                  $email_data['type']        = 2;//type 2 for wellcome log
                  
                  $migareference->sendMail($email_data,$app_id,$user_id);
                }
            }

          }
          $payload = [
              'success' => true,
              'message' => __("Successfully detail saved.")
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => __($e->getMessage()),
          'other'=>$customer
      ];
    }
    $this->_sendJson($payload);
  }
  public function savepropertyreportAction(){
    try {
            $application   = $this->getApplication();
            $app_id        = $application->getId();
            $migareference = new Migareference_Model_Migareference();
            $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
            $app_link      = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $migareference = new Migareference_Model_Migareference();
            $utilities     = new Migareference_Model_Utilities();
            $pre_report_settings = $migareference->preReportsettigns($app_id);
            $field_data    = $migareference->getreportfield($app_id);
            $is_admin      = $migareference->is_admin($app_id,$data['user_id']);
            $is_agent      = $migareference->is_agent($app_id,$data['user_id']);
            $gdpr_settings  = $migareference->get_gdpr_settings($app_id);
            $created_by     = $data['user_id'];
            $sponsorid=0;
            if (count($is_agent)) {
              $sponsorid=$data['user_id'];
            }
            $address_error =false;
            $errors          = "";
            $static_fields[1]="property_type";
            $static_fields[2]="sales_expectations";
            $static_fields[3]="address";
            $static_fields[4]="owner_name";
            $static_fields[5]="owner_surname";
            $static_fields[6]="owner_mobile";
            $static_fields[7]="note";
            // validation rules for dynamic report Fileds
            foreach ($field_data as $key => $value) {
              $name="extra_".$value['field_type_count'];
              // Empry Validation
              if ($value['field_type']==6 && !empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])) {
                $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
                $birth_date = date('Y-m-d',strtotime($b_date));
                unset($data['birth_day']);
                unset($data['birth_month']);
                unset($data['birth_year']);
                $data[$name]=$birth_date;
                $report_entry['owner_dob']=$birth_date;
              }
              if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$name])) {
                // file_type==6 for DOB validation rules
                if ($value['field_type']==6) {
                  if (empty($data['birth_day']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label']. __("Day")." <br/>";
                    $data[$name]='';
                  }
                  if (empty($data['birth_month']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label'].__("Month")."  <br/>";
                    $data[$name]='';
                  }
                  if (empty($data['birth_year']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label'].__("Year")." <br/>";
                    $data[$name]='';
                  }
                }elseif ($value['field_type']==7) { // Email Validation only for Referrer Report
                  if ($data['report_type']==1) {
                    $errors.= __('You must add valid value for')." ".$value['label']." <br/>";                    
                  }
                }else {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }
              }elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$static_fields[$value['field_type_count']]])) {
                $errors.= __('You must add valid value for')." ".$value['label']. " <br/>";
              }
                // Explicitly check for email
                if ($value['type']==2 && $value['is_visible']==1 && $value['field_type']==7 && !empty($data[$name]) && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                  $errors .= __('Email is not correct. Please add a valid email address') . "<br/>";
                }
              // Country and Province value managment and validation
              // COUNTRY
              if ($value['field_type']==3 && $value['options_type']==1 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['id'];
                }
              }
              // Provinces
              if ($value['field_type']==3 && $value['options_type']==2 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['migareference_geo_provinces_id'];
                }
              }
              // Address Validations
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && $static_fields[$value['field_type_count']]=='address' && $pre_report_settings[0]['enable_unique_address']==1) {
                  $address['address']  = $data['address'];
                  $address['longitude']= $data['longitude'];
                  $address['latitude'] = $data['latitude'];
                  $days=$pre_report_settings[0]['address_grace_days'];
                  $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                  $internal_address_duplication=$migareference->isinternalAddressunique($app_id,$address,$date);
                  $external_address_duplication=$migareference->isexternalAddressunique($app_id,$address);
                  if (count($internal_address_duplication) || count($external_address_duplication)) {
                    if ($pre_report_settings[0]['block_address_report']==2) {
                      $address_error=true;
                    }else {
                      $errors .= __('This Property Address already exists.') . "<br/>";
                    }
                  }
              }
              // Owner Mobile Validations
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && $static_fields[$value['field_type_count']]=='owner_mobile') {
                $days=$pre_report_settings[0]['grace_days'];
                $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));              
      					if (strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])
                || (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')){
      						$errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
      					}elseif ($pre_report_settings[0]['is_unique_mobile']==1) {                  
                  $mobile_validation=$migareference->validateProspectMobile($app_id,$grace_date,$data['owner_mobile'],$data['user_id']);//return true/false
                  if ($mobile_validation) {
                    if ($pre_report_settings[0]['mobile_grace_period_action']==1) {
                      $errors .= __('This Mobile Number already exists.') . "<br/>";
                    }else {
                      $duplicate_warning=__($pre_report_settings[0]['grace_period_warning_message']);
                    }
                  }
                }
              }
            }
            // End dynamic filed validation rules
            // Rules for report added by Admin or Agent on behalf of refrel
            if ($data['report_type']==2 && (count($is_admin) || count($is_agent))) {
              if (empty($data['refreral_user_id']) || $data['refreral_user_id']==0) {
                $errors.= __('Please select user.');
              }else {
                $temp_options         = explode('@',$data['refreral_user_id']);
                $data['user_id']      = $temp_options[0];
                $taxID                = $this->randomTaxid();
              }
            }
            if (!empty($errors)) {
              throw new Exception($errors);
            }else{
              $user_data  = $migareference->getAgentdata($data['user_id']);
              $password   = $this->randomPassword();
              // if only siberian user save agrrement settings with default tax_id
              if ($temp_options[1]==1) {
                  $inv_settings['app_id']=$app_id;
                  $inv_settings['user_id']=$data['user_id'];
                  $inv_settings['blockchain_password']=$password;
                  $inv_settings['invoice_name']=$user_data[0]['firstname'];
                  $inv_settings['sponsor_id']=$sponsorid;
                  $inv_settings['invoice_surname']=$user_data[0]['lastname'];
                  $inv_settings['invoice_mobile']=$user_data[0]['mobile'];
                  $inv_settings['tax_id']=$taxID;
                  $inv_settings['terms_accepted']=0;
                  $inv_settings['special_terms_accepted']=0;
                  $inv_settings['privacy_accepted']=0;
                  $migareference->savePropertysettings($inv_settings);
                }
              $repo_data           = $migareference->get_last_report_no();
              $invoice_settings    = $migareference->getpropertysettings($app_id,$data['user_id']);
              $agent_data          = $migareference->is_agent($app_id,$data['user_id']);
              if (!count($invoice_settings)) {
                $error=__("You must setup setting first");
                      throw new Exception($error);
              }
              // If owner not set Settings default will be
              $data['commission_type']= 0;
              $data['commission_fee'] = 5000;
              if (count($pre_report_settings) && $pre_report_settings[0]['reward_type']==1) {
                  $data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_amount'] : 0 ;
              } else {
                $data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_credits'] : 0 ;
              }
              $staus_data = $migareference->get_one_standard_status($app_id,1);//Standard index
              $data['report_no'] = (!count($repo_data)) ? 1000 : $repo_data[0]['report_no']+1 ;

                $report_entry['report_no']=$data['report_no'];
                $report_entry['app_id']=$app_id;
                $report_entry['user_id']=$data['user_id'];
                $report_entry['user_type']=(!empty($agent_data)) ? 3 : 1;
                $report_entry['property_type']=(empty($data['property_type'])) ? 1 : $data['property_type'];
                $report_entry['sales_expectations']=(empty($data['sales_expectations'])) ? "" : $data['sales_expectations'];
                $report_entry['commission_type']=$pre_report_settings[0]['commission_type'];
                $report_entry['reward_type']=$pre_report_settings[0]['reward_type'];
                $report_entry['commission_fee']=$data['commission_fee'];
                $report_entry['report_custom_type']= ($data['report_custom_type']===null) ? 1 : $data['report_custom_type'] ;
                $report_entry['is_reminder_sent']=0;
                $report_entry['address']=(empty($data['address'])) ? "" : $data['address'];
                $report_entry['longitude']=(empty($data['longitude'])) ? "" : $data['longitude'];
                $report_entry['latitude']=(empty($data['latitude'])) ? "" : $data['latitude'];
                $report_entry['owner_name']=$data['owner_name'];
                $report_entry['owner_surname']=$data['owner_surname'];
                $report_entry['owner_mobile']=$data['owner_mobile'];
                $report_entry['owner_hot']=$data['owner_hot'];
                $report_entry['note']=$data['extra_10'];
                $report_entry['currunt_report_status']=$staus_data[0]['migareference_report_status_id'];
                $report_entry['last_modification']=$status_data[0]['status_title'];
                $report_entry['last_modification_by']=$data['user_id'];
                $report_entry['last_modification_at']=date('Y-m-d H:i:s');
                $report_entry['extra_dynamic_fields']=serialize($data);
                $report_entry['extra_dynamic_field_settings']=serialize($field_data);
                $report_entry['created_by']=$created_by;
                $report_id = $migareference->savepropertyreport($report_entry);
                // Build Consent Message
                $default              = new Core_Model_Default();
                $base_url             = $default->getBaseUrl();
                $bitly_crede          = $migareference->getBitlycredentails($app_id);
                $consent_link         = $base_url . "/migareference/consent?appid=".$app_id.'&rep='.$report_id;
                $agent                = $migareference->getSponsorList($app_id,$report_entry['user_id']);
                $consent_link         = $utilities->shortLink($consent_link);
                $consent_bitly['app_id']									= $app_id;
                $consent_bitly['migareference_report_id'] = $report_id;
                $consent_bitly['consent_bitly']					  = $consent_link;
                $consent_bitly['ignore_webhook']					  = true;
                $migareference->updatepropertyreport($consent_bitly);
                $tags                 = [
                                          '@@report_owner@@',
                                          '@@referrer_name@@',                                          
                                          '@@consent_link@@'
                                        ];
                $strings              = [
                                          $report_entry['owner_name']." ".$report_entry['owner_surname'],
                                          $user_data[0]['firstname']." ".$user_data[0]['lastname'],                                          
                                          $consent_link
                                        ];
                $consent_invit_msg_body = (empty($gdpr_settings[0]['consent_invit_msg_body'])) ?  $pre_report_settings[0]['consent_invit_msg_body']: $gdpr_settings[0]['consent_invit_msg_body'];                                         
                $consent_invit_msg_body=str_replace($tags, $strings, $consent_invit_msg_body);
                //*************Send Notification***************
                if ($report_id>0) {
                  // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
                  $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_id,$report_entry['currunt_report_status'],$report_entry['last_modification_by'],'APP-END','create');                                              
                }
            }
            $success_message=__("Successfully Report saved.")."<br>";
            if ($address_error) {
              $success_message.=__("Warning! Address already used in another report. Please be aware that it is possible someone else already submitted the same report.");
            }
          $payload = [
              'success' => true,
              'message' =>$success_message,
              'consent_invit_msg_body'=>$consent_invit_msg_body,
              'notifcation_response'=>$notifcation_response
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage(),
          'others' => $field_data,
          'oths' => $data
      ];
    }
    $this->_sendJson($payload);
  }
  public function prevalidatesubmitreportAction(){
    try {
            $application   = $this->getApplication();
            $app_id        = $application->getId();
            $migareference = new Migareference_Model_Migareference();
            $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
            $app_link      = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $migareference = new Migareference_Model_Migareference();
            $pre_report_settings = $migareference->preReportsettigns($app_id);
            $field_data    = $migareference->getreportfield($app_id);
            $is_admin      = $migareference->is_admin($app_id,$data['user_id']);
            $is_agent      = $migareference->is_agent($app_id,$data['user_id']);
            $gdpr_settings  = $migareference->get_gdpr_settings($app_id);
            $created_by     = $data['user_id'];
            $sponsorid=0;
            if (count($is_agent)) {
              $sponsorid=$data['user_id'];
            }
            $address_error =false;
            $errors          = "";
            $static_fields[1]="property_type";
            $static_fields[2]="sales_expectations";
            $static_fields[3]="address";
            $static_fields[4]="owner_name";
            $static_fields[5]="owner_surname";
            $static_fields[6]="owner_mobile";
            $static_fields[7]="note";
            // validation rules for dynamic report Fileds
            foreach ($field_data as $key => $value) {
              $name="extra_".$value['field_type_count'];
              // Empry Validation
              if ($value['field_type']==6 && !empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])) {
                $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
                $birth_date = date('Y-m-d',strtotime($b_date));
                unset($data['birth_day']);
                unset($data['birth_month']);
                unset($data['birth_year']);
                $data[$name]=$birth_date;
                $report_entry['owner_dob']=$birth_date;
              }
              if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$name])) {
                // file_type==6 for DOB validation rules
                if ($value['field_type']==6) {
                  if (empty($data['birth_day']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label']. __("Day")." <br/>";
                    $data[$name]='';
                  }
                  if (empty($data['birth_month']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label'].__("Month")."  <br/>";
                    $data[$name]='';
                  }
                  if (empty($data['birth_year']) ) {
                    $errors.= __('You must add valid value for')." ".$value['label'].__("Year")." <br/>";
                    $data[$name]='';
                  }
                }else {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }
              }elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$static_fields[$value['field_type_count']]])) {
                $errors.= __('You must add valid value for')." ".$value['label']. " <br/>";
              }
              // Country and Province value managment and validation
              // COUNTRY
              if ($value['field_type']==3 && $value['options_type']==1 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['id'];
                }
              }
              // Provinces
              if ($value['field_type']==3 && $value['options_type']==2 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['migareference_geo_provinces_id'];
                }
              }
              // Address Validations
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && $static_fields[$value['field_type_count']]=='address' && $pre_report_settings[0]['enable_unique_address']==1) {
                  $address['address']  = $data['address'];
                  $address['longitude']= $data['longitude'];
                  $address['latitude'] = $data['latitude'];
                  $days=$pre_report_settings[0]['address_grace_days'];
                  $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                  $internal_address_duplication=$migareference->isinternalAddressunique($app_id,$address,$date);
                  $external_address_duplication=$migareference->isexternalAddressunique($app_id,$address);
                  if (count($internal_address_duplication) || count($external_address_duplication)) {
                    if ($pre_report_settings[0]['block_address_report']==2) {
                      $address_error=true;
                    }else {
                      $errors .= __('This Property Address already exists.') . "<br/>";
                    }
                  }
              }
              // Owner Mobile Validations              
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && $static_fields[$value['field_type_count']]=='owner_mobile') {                
                $days=$pre_report_settings[0]['grace_days'];
                $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));              
      					if (strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])
                || (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')){
      						$errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
      					}elseif ($pre_report_settings[0]['is_unique_mobile']==1) {                  
                  $mobile_validation=$migareference->validateProspectMobile($app_id,$grace_date,$data['owner_mobile'],$data['user_id']);//return true/false
                  if ($mobile_validation) {
                    if ($pre_report_settings[0]['mobile_grace_period_action']==1) {
                      $errors .= __('This Mobile Number already exists.') . "<br/>";
                    }else {
                      $duplicate_warning=__($pre_report_settings[0]['grace_period_warning_message']);
                    }
                  }
                }
              }
            }
            // End dynamic filed validation rules
            // Rules for report added by Admin or Agent on behalf of refrel
            if ($data['report_type']==2 && (count($is_admin) || count($is_agent))) {
              if (empty($data['refreral_user_id']) || $data['refreral_user_id']==0) {
                $errors.= __('Please select user.');
              }else {
                $temp_options         = explode('@',$data['refreral_user_id']);
                $data['user_id']      = $temp_options[0];
                $taxID                = $this->randomTaxid();
              }
            }
            if (!empty($errors)) {
              throw new Exception($errors);
            }
            $success_message=__("Successfully Report saved.")."<br>";
            if ($address_error) {
              $success_message.=__("Warning! Address already used in another report. Please be aware that it is possible someone else already submitted the same report.");
            }
          $payload = [
              'success' => true,
              'message' =>$success_message,
              'consent_invit_msg_body'=>$consent_invit_msg_body
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage(),
          'others' => $field_data,
          'oths' => $data
      ];
    }
    $this->_sendJson($payload);
  }
  public function updatepropertyreportAction(){
    try {
            $application    = $this->getApplication();
            $app_id         = $application->getId();
            $migareference  = new Migareference_Model_Migareference();
            $data           = Siberian_Json::decode($this->getRequest()->getRawBody());
            $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $pre_report     = $migareference->preReportsettigns($app_id);            
            $previous_item  = $migareference->getReport($app_id,$data['migareference_report_id']);
            $field_data     = unserialize( $previous_item[0]['extra_dynamic_field_settings']);
            $standard_type  = $data['standard_type'];
            $admin_push_response=[];
            $agent_push_response=[];
            unset($data['standard_type']);            
            $errors       = "";
            $commision_fee= (empty($data['commission_fee'])) ? "" : $data['commission_fee'] ;
            //*This is user id of Admin who is updating not Referral who is submitting Report in case of agent it will be agent user_id who updating
            $user_id      = $data['user_id'];
            unset($data['user_id']);
            if ($data['is_acquired']==1 && $standard_type!=4) {
                if (empty($data['commission_fee'])) {
                  $errors .= __('You must add commission fee.') . "<br/>";
                }
            }
            if ($data['new_order_id']<$data['order_id'] && $previous_item[0]['standard_type']==3 && ($data['reward_type']==2 || $data['reward_type']==1)) {
                  $errors .= __('You can only move to SUPERIOR status') . "<br/>";
            }elseif ($previous_item[0]['standard_type']==3) {
              $errors .= __('You can only move to SUPERIOR status') . "<br/>";
            }else {
              unset($data['new_order_id']);
              unset($data['order_id']);
            }
            if ($data['comment_required']==true && empty($data['comment'])) {
                  $errors .= __('You must add Comment for Referral.') . "<br/>";
            }
            if ($commision_fee>0) { //if user enter commission for Range and % commission
              $com_fee_report=$commision_fee;
            }else { // for fixed commission
              $com_fee_report=$data['commission_fee_report'];
            }
            if ($standard_type==3 && $com_fee_report<1) {
              $errors .= __('You must add commission fee to make it Paid.') . "<br/>";
            }
            $static_fields[1]="property_type";
            $static_fields[2]="sales_expectations";
            $static_fields[3]="address";
            $static_fields[4]="owner_name";
            $static_fields[5]="owner_surname";
            $static_fields[6]="owner_mobile";
            $static_fields[7]="note";
            // validation rules for dynamic report Fileds
            foreach ($field_data as $key => $value) {
              $name="extra_".$value['field_type_count'];
              // Empry Validation
              if ($value['field_type']==6 && !empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])) {
                $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
                $birth_date = date('Y-m-d',strtotime($b_date));
                unset($data['birth_day']);
                unset($data['birth_month']);
                unset($data['birth_year']);
                $data[$name]=$birth_date;
                $report_item['owner_dob']=$birth_date;
              }
              if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && $value['field_type']!=7 && empty($data[$name])) {
                $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
              }elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($static_fields[$value['field_type_count']])) {
                $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
              }
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='address' && $pre_report_settings[0]['enable_unique_address']==1) {
                  $address['address']  = $data['address'];
                  $address['longitude']= $data['longitude'];
                  $address['latitude'] = $data['latitude'];
                  $days=$pre_report_settings[0]['address_grace_days'];
                  $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                  $internal_address_duplication=$migareference->isinternalAddressunique($app_id,$address,$date);
                  $external_address_duplication=$migareference->isexternalAddressunique($app_id,$address);
                  if (count($address_duplication) || count($external_address_duplication)) {
                    $errors .= __('This Property Address already exists.') . "<br/>";
                  }
              }
              if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='owner_mobile' && $pre_report_settings[0]['is_unique_mobile']==1) {
                $days=$pre_report_settings[0]['grace_days'];
                $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                $mobile_duplication=$migareference->isMobileunique($app_id,$date,$data['owner_mobile']);
                $mobile_blacklist=$migareference->isBlackList($app_id,$data['owner_mobile']);
      					if (strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])
               || (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')){
      						$errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
      					}elseif(count($mobile_duplication)) {
      						$errors .= __('This Mobile Number already exists.') . "<br/>";
      					}elseif (count($mobile_blacklist)) {
                  $errors .= __('This Mobile Number has been Blacklisted.') . "<br/>";
                }
              }
              // Explicitly check for email
              if ($value['type']==2 && $value['is_visible']==1 && $value['field_type']==7 && !empty($data[$name]) && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                $errors .= __('Email is not correct. Please add a valid email address') . "<br/>";
              }
              // Country and Province value managment and validation
              // COUNTRY
              if ($value['field_type']==3 && $value['options_type']==1 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['id'];
                }
              }
              // Provinces
              if ($value['field_type']==3 && $value['options_type']==2 && $value['type']==2) {
                $province_item=$data[$name];
                if ($value['is_required']==1 && empty($data[$name])) {
                  $errors.= __('You must add valid value for')." ".$value['label']. "<br/>";
                }else {
                  $data[$name]=$province_item['migareference_geo_provinces_id'];
                }
              }
            }
            // End dynamic filed validation rules
            if (!empty($errors)) {
            throw new Exception($errors);
            }else{
              if ($commision_fee>0) { //if user enter commission for Range and % commission
                $com_fee_report=$commision_fee;
              }else { // for fixed commission
                $com_fee_report=$data['commission_fee_report'];
              }
              unset($data['commission_fee_report']);
              // START Comment section
              $comment_tb['app_id']    = $app_id;
              $comment_tb['report_id'] = $data['migareference_report_id'];
              $comment_tb['status_id'] = $data['currunt_report_status'];
              $comment_tb['comment']   = $data['comment'];
              if ($data['comment_required']==true) {
                $commnent_id             = $migareference->saveComment($comment_tb);
              }
              unset($data['comment_required']);
              unset($data['comment']);
              unset($data['is_acquired']);
              unset($data['acquired']);
                // End Comment section
                $status_data                  = $migareference->getStatus($app_id,$data['currunt_report_status']);
                $data['last_modification_by'] = $user_id;
                $data['last_modification_at'] = date('Y-m-d H:i:s');
                $data['is_reminder_sent']     = 0;
                $data['last_modification']    = $status_data[0]['status_title'];
                $data['app_id']               = $app_id;
                $log_data['app_id']           = $app_id;
                $log_data['user_id']          = $user_id;
                $log_data['report_id']        = $data['migareference_report_id'];
                $log_data['log_source']       = "APP-END";
                $log_data['log_type']         = "Update Status";
                $log_data['log_detail']       = "Update Status to ".$status_data[0]['status_title'];
                $migareference->saveLog($log_data);
                $agent_user_mail              = $migareference->getAgentdata($user_id);
                $report_item['address']=$data['address'];
                $report_item['app_id']=$data['app_id'];
                $report_item['commission_fee']=$data['commission_fee'];
                $report_item['commission_type']=$data['commission_type'];
                $report_item['report_custom_type']=$data['report_custom_type'];
                $report_item['currunt_report_status']=$data['currunt_report_status'];
                $report_item['is_reminder_sent']=$data['is_reminder_sent'];
                $report_item['last_modification']=$data['last_modification'];
                $report_item['last_modification_at']=$data['last_modification_at'];
                $report_item['last_modification_by']=$data['last_modification_by'];
                $report_item['latitude']=$data['latitude'];
                $report_item['longitude']=$data['longitude'];
                $report_item['migareference_report_id']=$data['migareference_report_id'];
                $report_item['owner_hot']=$data['owner_hot'];
                $report_item['owner_name']=$data['owner_name'];
                $report_item['owner_surname']=$data['owner_surname'];
                $report_item['property_type']=$data['property_type'];
                $report_item['note']=$data['extra_10'];
                $report_item['reward_type']=$data['reward_type'];
                $report_item['sales_expectations']=$data['sales_expectations'];
                $report_item['extra_dynamic_fields']=serialize($data);
                $migareference->updatepropertyreport($report_item);
                $pre_report = $migareference->preReportsettigns($app_id);
                // On Update Report Type,Property Staus ->Save log,Send Notification
                // *99999 this user id use for System as a user
                $log_data['app_id']   = $app_id;
                $log_data['user_id']  = $user_id;
                $log_data['report_id']= $data['migareference_report_id'];
                if ($previous_item[0]['currunt_report_status']!=$data['currunt_report_status']) {
                  //Send Notification
                  $log_data['log_type']="Update Status";
                  $log_data['log_detail']="Update Status to ".$status_data[0]['status_title'];
                  $migareference->saveLog($log_data);
                  // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
                  $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$data['migareference_report_id'],$report_item['currunt_report_status'],$report_item['last_modification_by'],'APP-END','update');                            
                }
                if ($previous_item[0]['property_type']!=$data['property_type']) {
                  $type=($data['property_type']==1) ? "Villa" : "Flat" ;
                  $log_data['log_type']   = "Update Report Type";
                  $log_data['log_detail'] = "Update Report Type to ".$type;
                  $migareference->saveLog($log_data);
                }
                // Save earnings if Property Sold
                if ($standard_type==3 && $previous_item[0]['reward_type']==1) {
                  $earning['app_id']           = $app_id;
                  $earning['refferral_user_id']= $previous_item[0]['user_id'];
                  $earning['sold_user_id']     = $user_id;
                  $earning['report_id']        = $data['migareference_report_id'];
                  $earning['earn_amount']      =  $com_fee_report;
                  $earning['platform']         = "APP End";
                  $migareference->saveEarning($earning);
                }elseif($standard_type==3 && $previous_item[0]['reward_type']==2) {
                  $earning['app_id']           = $app_id;
                  $earning['user_id']          = $previous_item[0]['user_id'];
                  $earning['amount']           = $com_fee_report;
                  $earning['entry_type']       = 'C';
                  $earning['trsansection_by']  = $user_id;
                  $earning['prize_id']         = 0;
                  $earning['report_id']        = $previous_item[0]['migareference_report_id'];
                  $earning['trsansection_description'] ="Report #".$previous_item[0]['report_no'];
                  $migareference->saveLedger($earning);
                }
            }
          $payload = [
              'success' => true,
              'message' => __("Successfully Report update."),
              'admin_push_response' => $admin_push_response,
              'agent_push_response' => $agent_push_response,
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage(),
          'admin_push_response' => $admin_push_response,
          'agent_push_response' => $agent_push_response,
      ];
    }
    $this->_sendJson($payload);
  }
  public function savelogAction(){
    try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $data          = array();
          $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
          $data['app_id']=$app_id;
          $migareference->saveLog($data);
          $payload = [
            'success' => true,
            'message' => __("Successfully Log saved."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage()
      ];
    }
    $this->_sendJson($payload);
  }
  public function addcoommunicationlogAction(){
    try {
          $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $log_item=[
              'app_id'       => $app_id,
              'phonebook_id' => $data['phonebook_id'],
              'log_type'     => "Manual",
              'note'         => $data['notes_content'],
              'user_id'      => $data['user_id'],
              'created_at'   => date('Y-m-d H:i:s')
          ];
          $migareference->saveCommunicationLog($log_item);
          $payload = [
            'success' => true,
            'message' => __("Successfully Log saved."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage()
      ];
    }
    $this->_sendJson($payload);
  }
  public function deletecommunicationlogAction(){
    try {
          $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $migareference->deleteCommunicationLog($data['log_id']);
          $payload = [
            'success' => true,
            'message' => __("Successfully Log deleted."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage()
      ];
    }
    $this->_sendJson($payload);
  }
  public function deletereferrerAction(){
    try {
          $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $admin_data    = $migareference->is_admin($app_id,$data['user_id']);
          $agent_data    = $migareference->is_agent($app_id,$data['user_id']);
          if (!count($admin_data) && !count($agent_data)) {
            $migareference->deltereferrer($app_id,$data);
          }else{
            throw new Exception(__("This user is not anymore referrer."));
          }

          $payload = [
            'success' => true,
            'message' => __("Successfully Log deleted."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage()
      ];
    }
    $this->_sendJson($payload);
  }
  function randomPassword() {
    $alphabet = "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
  public function savenewuserAction(){
    try {
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      $application   = $this->getApplication();
      $app_id        = $application->getId();
      $migareference = new Migareference_Model_Migareference();
      $pre_settings  = $migareference->preReportsettigns($app_id);
      
      
      $agent_data=[];
      if (isset($data['customer_id'])) {
        $agent_data    = $migareference->is_agent($app_id,$data['customer_id']);        
      }
      if (!isset($data['firstname']) || empty($data['firstname'])) {
        $errors .= __('Please add user Name') . "<br/>";
      }
      if (!isset($data['lastname']) || empty($data['lastname'])) {
        $errors .= __('Please add user Surname') . "<br/>";
      }
      if (!isset($data['email']) || empty($data['email'])) {
        $errors .= __('Please add user Email') . "<br/>";
      }else{
        $customer=$migareference->getCustomer($app_id,$data['email']);
        if (count($customer)) {
          $errors .= __('This user already exist.') . "<br/>";
        }
      }
      if (!isset($data['password']) || empty($data['password'])) {
        $errors .= __('Please add first Password') . "<br/>";
      }
      if (!isset($data['mobile']) || empty($data['mobile'])) {
        $errors .= __('Please add user Mobile') . "<br/>";
      }
      // if($pre_settings[0]['enable_birthdate']==1 && $pre_settings[0]['mandatory_birthdate']==1 && (empty($data['birth_day']) || empty($data['birth_month']) || empty($data['birth_year']))){
        //           $errors .= __('Please add a valid Birthdate.') . "<br/>";
        //   }        
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
        // throw new Exception("Error Processing Request q", 1);
        
          $password = $data['password'];
          if(!empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])){
            $b_date     = $data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
            $birth_date = date('Y-m-d',strtotime($b_date));
            $birth_date = strtotime($birth_date);
          }else {
            $birth_date = strtotime('00-00-0000');
          }
          if (!isset($data['profession_id'])) {
            $data['profession_id']=0;
          }
          // create Customer
          $customer['app_id']         = $app_id;
          $customer['firstname']      = $data['firstname'];
          $customer['lastname']       = $data['lastname'];
          $customer['email']          = $data['email'];
          $customer['mobile']         = $data['mobile'];
          $customer['birthdate']      = $birth_date;
          $customer['password']       = sha1($password);
          $customer['privacy_policy'] = 1;          
          $user_id=$migareference->createUser($customer);
          // Save Invoice
                $inv_settings['app_id']                 = $app_id;
                $inv_settings['user_id']                = $user_id;
                $inv_settings['blockchain_password']    = $this->randomPassword();
                $inv_settings['invoice_name']           = $data['firstname'];
                $inv_settings['sponsor_id']             = (count($agent_data)>0) ? $agent_data[0]['user_id'] : 0 ;
                $inv_settings['invoice_surname']        = $data['lastname'];
                $inv_settings['invoice_mobile']         = $data['mobile'];
                $inv_settings['job_id']                 = $data['job_id'];
                // $inv_settings['profession_id']          = $data['profession_id'];
                $inv_settings['tax_id']                 = $this->randomTaxid();;
                $inv_settings['terms_accepted']         = 1;
                $inv_settings['special_terms_accepted'] = 1;
                $inv_settings['privacy_accepted']       = 1;
                // throw new Exception("Error Processing inside");
                $migareference->savePropertysettings($inv_settings); //This method also save phonebook entry if previously not exist
              // Send Welcome Email to referrer
                if ($pre_settings[0]['enable_welcome_email']==1
                    && !empty($pre_settings[0]['referrer_wellcome_email_title'])
                    && !empty($pre_settings[0]['referrer_wellcome_email_body']))
                  {
                  $notificationTags=$migareference->welcomeEmailTags();
                  $agent_user=$migareference->getSingleuser($app_id,0);
                  $notificationStrings = [
                    $customer['firstname']." ".$customer['lastname'],
                    $customer['email'],
                    $password,
                    $agent_user[0]['firstname']." ".$agent_user[0]['lastname']
                  ];
                  $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_title']);
                  $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_body']);
                  $email_data['type']        = 2;//type 2 for wellcome log
                  $migareference->sendMail($email_data,$app_id,$user_id);
                }
          $keys['app_id']=$app_id;
          $keys['user_id']=$user_id;
          $keys['key']=$password;
          $migareference->savekey($keys);
        }
          $payload = [
            'success' => true,
            'message' => __("Successfully User Created"),
            'user_id' => $user_id
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function savereminderAction(){
    try {
      $data          = array();
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      if (!isset($data['event_type']) || empty($data['event_type'])) {
        $errors .= __('Please select event type') . "<br/>";
      }
      if (!isset($data['event_day']) || empty($data['event_day'])) {
        $errors .= __('Please select event day') . "<br/>";
      }
      if (!isset($data['event_month']) || empty($data['event_month'])) {
        $errors .= __('Please select event month') . "<br/>";
      }
      if (!isset($data['event_year']) || empty($data['event_year'])) {
        $errors .= __('Please select event year') . "<br/>";
      }
      if (!isset($data['event_hour']) || empty($data['event_hour'])) {
        $errors .= __('Please select event hour') . "<br/>";
      }
      if (!isset($data['event_min']) || empty($data['event_min'])) {
        $errors .= __('Please select event minute') . "<br/>";
      }
      if (!isset($data['reminder_before_type']) || empty($data['reminder_before_type'])) {
        $errors .= __('Please select reminder time') . "<br/>";
      }
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
        $subtrct_min=0;
        switch ($data['reminder_before_type']) {
          case '01':
          $subtrct_min=0;
          break;
          case '02':
          $subtrct_min=15;
          break;
          case '03':
          $subtrct_min=30;
          break;
          case '04':
          $subtrct_min=45;
          break;
          case '05':
          $subtrct_min=60;
          break;
          case '06':
          $subtrct_min=120;
          break;
          case '07':
          $subtrct_min=360;
          break;
          case '08':
          $subtrct_min=1440;
          break;
        }
        $event_date_time    = mktime($data['event_hour'], $data['event_min'],0, $data['event_month'], $data['event_day'],$data['event_year']);
        $event_date         = date("Y-m-d H:i:s", $event_date_time);
        $reminder_date      = date("Y-m-d H:i:s", strtotime("-".$subtrct_min." minutes", strtotime($event_date)));
        $currunt_date       = date('Y-m-d H:i:s');
        if ($currunt_date>$reminder_date) {
          throw new Exception("You must select a valid datetime");
        }
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $data['app_id']= $app_id;
          $public_key    = $data['public_key'];
          $data['event_date_time']    = $event_date;
          $data['reminder_date_time'] = $reminder_date;
          unset($data['public_key']);
          if ($data['operation']=="create") {
            unset($data['operation']);
            $migareference->insert_reminder($data);
          }else {
            unset($data['operation']);
            $migareference->update_reminder($public_key,$data);
          }
        }
          $payload = [
            'success' => true,
            'message' => __("Successfully Reminder saved."),
            'data'=>$event_date." ".$reminder_date
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function savemanualconsentAction(){
    try {
      $data          = array();
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      if (!isset($data['consent_day']) || empty($data['consent_day'])) {
        $errors .= __('Please select consent day') . "<br/>";
      }
      if (!isset($data['consent_month']) || empty($data['consent_month'])) {
        $errors .= __('Please select consent month') . "<br/>";
      }
      if (!isset($data['consent_year']) || empty($data['consent_year'])) {
        $errors .= __('Please select consent year') . "<br/>";
      }
      if (!isset($data['consent_hour']) || empty($data['consent_hour'])) {
        $errors .= __('Please select consent hour') . "<br/>";
      }
      if (!isset($data['consent_min']) || empty($data['consent_min'])) {
        $errors .= __('Please select consent minuit') . "<br/>";
      }
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
          $migareference = new Migareference_Model_Migareference();

          $consent_date_time    = mktime($data['consent_hour'], $data['consent_min'],0, $data['consent_month'], $data['consent_day'],$data['consent_year']);
          $consent_date         = date("Y-m-d H:i:s", $consent_date_time);          
          $app_id               = $this->getApplication()->getId();

          $prospect['app_id']									= $app_id;          
          $prospect['gdpr_consent_ip']				= '';
          $prospect['gdpr_consent_timestamp']  = $consent_date;
          $prospect['gdpr_consent_source']		 = 'Manual Consent';
          
          $report_item = $migareference->get_report_by_key($data['report_id']);
                         $migareference->update_prospect($prospect,$report_item[0]['prospect_id'],0,0);//Also save log if their is change in Rating,Job,Notes                    

        }
          $payload = [
            'success' => true,
            'message' => __("Successfully data saved."),
            'data'=>$event_date." ".$reminder_date
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function savenoteAction(){
    try {
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      if (empty($data['notes_content'])) {
          $errors .= __('Please add Note content.') . "<br/>";
      }
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
          $app_id = $this->getApplication()->getId();
          $app_name = $this->getApplication()->getName();
          $data['app_id']= $app_id;
          if ($data['public_key']!=0) {$data['migarefrence_notes_id']= $data['public_key'];}
          (new Migareference_Model_Notes())->setData($data)->save();
          // Manage New Note Notification
          $note_template=(new Migareference_Model_Newnotnotification())->findAll(['app_id'=>$app_id])->toArray();
          if (COUNT($note_template)>0) {
            $migareference = new Migareference_Model_Migareference();
            $report_item=$migareference->get_report_by_key($data['report_id']);
            $customer = $migareference->getSingleuser($app_id,$data['user_id']);
            $base_url        = (new Core_Model_Default())->getBaseUrl();            
                  $tags_list=[
                    "@@report_no@@",
                    "@@report_date@@",
                    "@@owner_name@@",
                    "@@owner_surname@@",
                    "@@app_name@@",
                    "@@app_link@@",
                    "@@new_note@@",
                    "@@agent_name@@"
                  ];
                  $app_link = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                  $tag_values=[
                    $report_item[0]['report_no'],                    
                    $report_item[0]['report_created_at'],                    
                    $report_item[0]['owner_name'],                    
                    $report_item[0]['owner_surname'],                    
                    $app_name,
                    $app_link,
                    $data['notes_content'],
                    $customer[0]['firstname'].' '.$customer[0]['lastname']
                  ];
                  if ($note_template[0]['new_note_target_notification']==1 || $note_template[0]['new_note_target_notification']==2) {
                    $email_data['email_title']=str_replace($tags_list, $tag_values, $note_template[0]['new_note_email_title']);
                    $email_data['email_text']=str_replace($tags_list, $tag_values, $note_template[0]['new_note_email_text']);
                    $email_data['calling_method']='New_Report_Note';
                    $mail_retur = $migareference->sendMail($email_data,$app_id,$report_item[0]['user_id']);
                  }
                  if ($note_template[0]['new_note_target_notification']==1 || $note_template[0]['new_note_target_notification']==3) {
                    $push_data['open_feature'] = $note_template[0]['new_note_open_feature'];
                    $push_data['feature_id']   = $note_template[0]['new_note_feature_id'];
                    $push_data['custom_url']   = $note_template[0]['new_note_custom_url'];
                    $push_data['cover_image']  = $note_template[0]['new_note_cover_file'];
                    $push_data['app_id']       = $app_id;    
                    $push_data['calling_method']='New_Report_Note'; 
                    $push_data['push_title']   = str_replace($tags_list, $tag_values, $note_template[0]['new_note_push_title']);
                    $push_data['push_text']    = str_replace($tags_list, $tag_values, $note_template[0]['new_note_push_text']);
                    $push_return = $migareference->sendPush($push_data,$app_id,$report_item[0]['user_id']);
                  }
          }
          
        }
          $payload = [
            'success' => true,
            'message' => __("Successfully Note saved."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function addnewjobAction(){
    try {
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      if (empty($data['job_title'])) {
          $errors .= __('Please add Job Title.') . "<br/>";
      }
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $data['app_id']= $app_id;
          $migareference->insertjob($data);
        }
          $payload = [
            'success' => true,
            'message' => __("Successfully Note saved."),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function savenewphoneAction(){
    try {
      $data          = array();
      $data          = Siberian_Json::decode($this->getRequest()->getRawBody());
      if (empty($data['job_title'])) {
          $errors .= __('Please add a valid Job Title.') . "<br/>";
      }
      if (!empty($errors)) {
          throw new Exception($errors);
      } else {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $data['app_id']=$app_id;
          $migareference->insertjob($data);
        }
          $payload = [
            'success' => true,
            'message' => __('Job successfully saved.'),
            'data'=>$data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message'        => __($e->getMessage()),
          'data'=>$data
      ];
    }
    $this->_sendJson($payload);
  }
  public function redeemprizeAction(){
    try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $data          = array();
          $status        = 1;
          $raw_data['user_id']      = $this->getRequest()->getParam('user_id');
          $raw_data['prize_id']     = $this->getRequest()->getParam('prize_id');
          $prize_item               = $migareference->getSinglePrize($raw_data['prize_id']);
          $credit_balance           = $migareference->get_credit_balance($app_id,$raw_data['user_id']);
          $invoice_settings         = $migareference->getpropertysettings($app_id,$raw_data['user_id']);
          $data['app_id']           = $app_id;
          $data['user_id']          = $raw_data['user_id'];
          $data['amount']           = $prize_item[0]['credits_number'];
          $data['entry_type']       = 'D';
          $data['trsansection_by']  = $raw_data['user_id'];
          $data['trsansection_description']  = "Redeemed Prize";
          $data['prize_id']                  = $raw_data['prize_id'];
          $data_redeemed['app_id']           = $app_id;
          $data_redeemed['prize_id']         = $raw_data['prize_id'];
          $data_redeemed['redeemed_by']      = $raw_data['user_id'];
          $id=$migareference->saveRedeemed($data_redeemed);
          $data['redeem_id']         = $id;
          $migareference->saveLedger($data);
          $default              = new Core_Model_Default();
          $base_url             = $default->getBaseUrl();
          $app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
          $tags    = ['@@prize_title@@', '@@prize_credits@@', '@@user_credits@@','@@referral_name@@', '@@app_link@@', '@@app_name@@'];
          $strings = [$prize_item[0]['prize_name'], $prize_item[0]['credits_number'], $credit_balance[0]['credits'],$invoice_settings[0]['invoice_name']." ".$invoice_settings[0]['invoice_surname'],$app_link,$prize_item[0]['name']];
          $notification_data = $migareference->getprznotification($app_id,$status);
          $notification_data = $notification_data[0];

            if ($notification_data['prz_notification_to_user']==1 || $notification_data['prz_notification_to_user']==2) {
                  $email_data['email_title'] = str_replace($tags, $strings, $notification_data['ref_prz_email_title']);
                  $email_data['email_text']  = str_replace($tags,$strings,$notification_data['ref_prz_email_text']);
                  // Push data
                  $email_data['push_title']  = str_replace($tags,$strings,$notification_data['ref_prz_push_title']);
                  $email_data['push_text']   = str_replace($tags,$strings,$notification_data['ref_prz_push_text']);
                  $push_data['open_feature'] = $notification_data['ref_prz_open_feature'];
                  $push_data['feature_id']   = $notification_data['ref_prz_feature_id'];
                  $push_data['custom_url']   = $notification_data['ref_prz_custom_url'];
                  $push_data['cover_image']  = $notification_data['ref_prz_custom_file'];
                  $push_data['app_id']       = $app_id;
                  if ($notification_data['ref_prz_notification_type']==1 || $notification_data['ref_prz_notification_type']==2) {
                    $migareference->sendMail($email_data,$app_id,$raw_data['user_id']);
                  }
                  if ($notification_data['ref_prz_notification_type']==1 || $notification_data['ref_prz_notification_type']==3) {
                    $migareference->sendPush($push_data,$app_id,$raw_data['user_id']);
                  }
              }
            if ($notification_data['prz_notification_to_user']==1 || $notification_data['prz_notification_to_user']==3) {
                  $admin_user_data          = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agt_tags    = ['@@prize_title@@', '@@prize_credits@@', '@@user_credits@@','@@referral_name@@','@@agent_name@@','@@admin_name@@', '@@app_link@@', '@@app_name@@'];
                  foreach ($admin_user_data as $key => $value) {
                    $agent_user=$migareference->getSingleuser($app_id,$value['customer_id']);
                    $agt_strings = [$prize_item[0]['prize_name'], $prize_item[0]['credits_number'], $credit_balance[0]['credits'],$invoice_settings[0]['invoice_name']." ".$invoice_settings[0]['invoice_surname'],$agent_user[0]['firstname']." ".$agent_user[0]['lastname'],'',$app_link,$prize_item[0]['name']];
                    $email_data['email_title']= str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_email_title']);
                    $email_data['email_text'] = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_email_text']);
                    // Push data
                    $email_data['push_title']  = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_push_title']);
                    $email_data['push_text']   = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_push_text']);
                    $push_data['open_feature'] = $notification_data['agt_prz_open_feature'];
                    $push_data['feature_id']   = $notification_data['agt_prz_feature_id'];
                    $push_data['custom_url']   = $notification_data['agt_prz_custom_url'];
                    $push_data['cover_image']  = $notification_data['agt_prz_custom_file'];
                    $push_data['app_id']       = $app_id;
                    if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==2) {
                      $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                    }
                    if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==3) {
                      $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                    }
                  }
              }
            if ($notification_data['prz_notification_to_user']==1 || $notification_data['prz_notification_to_user']==3) {
                  $admin_user_data          = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agt_tags    = ['@@prize_title@@', '@@prize_credits@@', '@@user_credits@@','@@referral_name@@','@@agent_name@@','@@admin_name@@', '@@app_link@@', '@@app_name@@'];
                  foreach ($admin_user_data as $key => $value) {
                    $agent_user=$migareference->getSingleuser($app_id,$value['customer_id']);
                    $agt_strings = [$prize_item[0]['prize_name'], $prize_item[0]['credits_number'], $credit_balance[0]['credits'],$invoice_settings[0]['invoice_name']." ".$invoice_settings[0]['invoice_surname'],$agent_user[0]['firstname']." ".$agent_user[0]['lastname'],'',$app_link,$prize_item[0]['name']];
                    $email_data['email_title']= str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_email_title']);
                    $email_data['email_text'] = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_email_text']);
                    $email_data['bcc_to_email'] = $notification_data['agt_prz_email_bcc'];
                    // Push data
                    $email_data['push_title']  = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_push_title']);
                    $email_data['push_text']   = str_replace($agt_tags,$agt_strings,$notification_data['agt_prz_push_text']);
                    $push_data['open_feature'] = $notification_data['agt_prz_open_feature'];
                    $push_data['feature_id']   = $notification_data['agt_prz_feature_id'];
                    $push_data['custom_url']   = $notification_data['agt_prz_custom_url'];
                    $push_data['cover_image']  = $notification_data['agt_prz_custom_file'];
                    $push_data['app_id']       = $app_id;
                    if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==2) {
                      $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                    }
                    if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==3) {
                      $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                    }
                  }
              }
          $payload = [
            'success' => true,
            'message' => __("Successfully Redeemed."),
            'data'=>$notification_data
          ];
    } catch (Exception $e) {
      $payload = [
          'error' => true,
          'message' => $e->getMessage()
      ];
    }
    $this->_sendJson($payload);
  }
  public function getprizelistAction(){
        try {
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $prizes        = $migareference->getprizewithredeem($app_id,$user_id);
          $collection    = [];
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
            foreach ($prizes as $prize_item) {
              if ($prize_item['redeemed_once']==1 && $prize_item['redeemed_by']>0) {
                $a=true;
              }else {
                $prize_item['image_path']=$base_url."/images/application/".$app_id."/features/migareference/".$prize_item['prize_icon'];
                $collection[] = $prize_item;
              }
            }
            $payload = [
                "success" => true,
                "prizes"  => $collection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  private function _ReportNotesList($app_id,$report_id) {
    $all_notes = (new Migareference_Model_Notes())->findAll(
      ['app_id'=> $app_id, 'report_id' => $report_id], 
      ['order' => 'created_at DESC']
    )->toArray();
    // Build Notes Collection
    $notescollection = [];
    foreach ($all_notes as $key => $value) {
      $notescollection[]=[
        'date'=>date('d-m-Y',strtotime($value['created_at'])),
        'note'=>$value['notes_content'],
        'is_read'=>$value['is_read'],
        'public_key'=>$value['migarefrence_notes_id']
      ];
    }
    return $notescollection;
  }
  public function getnoteslistAction(){
        try {
          $app_id   = $this->getApplication()->getId();          
          $report_id  = $this->getRequest()->getParam('report_id');
          $notescollection=$this->_ReportNotesList($app_id,$report_id);
            $payload = [
                "success" => true,
                "notes"  => $notescollection,                
                "ios_tabs"           => "margin-top:30px !important;",
                "ios_back_detail"    => "margin-top:45px !important;",
                "ios_back"           => "margin-top:190px !important;",
                "ios_bottom"         => "height:200px !important;",
                "android_tabs"       => "margin-top:0px !important;",
                "android_back_detail"=> "margin-top:100px !important;",
                "android_back"       => "margin-top:100px !important;",
                "android_bottom"     => "height:100px !important;",
                "other_tabs"         => "margin-top:0px !important;",
                "other_back_detail"  => "margin-top:90px !important;",
                "other_back"         => "margin-top:90px !important;",
                "other_bottom"       => "height:100px !important;",
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function shownoteslistAction(){
        try {
          $app_id   = $this->getApplication()->getId();          
          $report_id  = $this->getRequest()->getParam('report_id');
          $notescollection=$this->_ReportNotesList($app_id,$report_id);          
          foreach ($notescollection as $note) {   
            if ($note['is_read']==0) {                            
              (new Migareference_Model_Notes())->setData(['migarefrence_notes_id' => $note['public_key'],'is_read' => 1,'read_at'=>date('Y-m-d H:i:s')])->save();
            }           
          }
            $payload = [
                "success" => true,
                "notes"  => $notescollection                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }

  public function getphonebookdetailAction(){
      try {
              $migareference   = new Migareference_Model_Migareference();
              $default         = new Core_Model_Default();
              $base_url        = $default->getBaseUrl();
              $app_id          = $this->getApplication()->getId();
              $phonebookid     = $this->getRequest()->getParam('phobebook_id');
              $agent_data      = $migareference->get_customer_agents($app_id);
              $partner_agents = $migareference->get_partner_agents($app_id);
              $all_jobs        = $migareference->getJobs($app_id);
              $all_professions= $migareference->getProfessions($app_id);
              $comunilog       = $migareference->getCommunicatioLog($app_id,$phonebookid);
              $pre_report      = $migareference->preReportsettigns($app_id);
              $countries       = $migareference->geoProvinceCountries($app_id);//Countries which have provinces 
              $StaticIons      = $migareference->getStaticIons();
              $logitem         = $migareference->getLatCommunication($phonebookid);

              if ($phonebookid>=1000000) {                
                $phonebookitem = $migareference->getProspectItem($app_id,$phonebookid);
                // Add some defaults for compatible
                $phonebookitem[0]['type']=2;
                $phonebookitem[0]['is_blacklist']='1';
                $phonebookitem[0]['migarefrence_phonebook_id']=$phonebookid;
              }else {
                $phonebookitem = $migareference->getSinglePhonebook($phonebookid);
              }

              $address_province_list = [];
              $invoiceDataItem       = [];
              $phonelog              = [];
              $gdpr_consent          = '';
              $is_terms_accepted     = false;
              $app_icon_path         = $base_url."/app/local/modules/Migareference/resources/appicons/";
              // Define Communication log List
              foreach ($comunilog as $key => $value) {
                $phonelog[]=[
                              'logdate' => date('d-m-Y H:i',strtotime($value['created_at'])),
                              'id'      => $value['user_id'],
                              'note'    => $value['note'],
                              'type'    => $value['log_type'],
                              'migareference_communication_logs_id'=> $value['migareference_communication_logs_id']
                            ];
              }                
                // Count Last Contact days
                $lastcontactdate = (!empty($logitem[0]['created_at'])) ? date('d-m-Y',strtotime($logitem[0]['created_at'])) : date('d-m-Y',strtotime($phonebookitem[0]['phone_creat_date'])) ;
                $now                            = time();
                $itemDate                       = strtotime($lastcontactdate);
                $datediff                       = $now-$itemDate;
                $datediff                       = round($datediff / (60 * 60 * 24));
                $phonebookitem[0]['lastcontact']= "-".($datediff);
                
              if ($phonebookitem[0]['type']==1) {
                  if ($phonebookitem[0]['invoice_id']!=0) {
                    $invoiceDataItem= $migareference->getInvoiceItem($phonebookitem[0]['invoice_id']);
                    $customer       = $migareference->getSingleuser($app_id,$invoiceDataItem[0]['user_id']);
                    $is_terms_accepted = (count($invoiceDataItem) && $invoiceDataItem[0]['terms_accepted']==0) ? false : true ;
                  }
                  // $phonebookitem[0]['sponsor_id']=$invoiceDataItem[0]['sponsor_id'];
                  // getReferrerAgents
                    $get_referrer_agents=$migareference->getReferrerAgents($app_id,$invoiceDataItem[0]['user_id']);
                    $agent_count=COUNT($get_referrer_agents);
                    $phonebookitem[0]['sponsor_id']=0;
                    $phonebookitem[0]['partner_sponsor_id']=0;
                    if($agent_count==1){
                      $phonebookitem[0]['sponsor_id']=$get_referrer_agents[0]['agent_id'];
                      $phonebookitem[0]['partner_sponsor_id']=0;
                    }else if($agent_count==2){
                      $phonebookitem[0]['sponsor_id']=$get_referrer_agents[0]['agent_id'];
                      $phonebookitem[0]['partner_sponsor_id']=$get_referrer_agents[1]['agent_id'];
                    }
                  $engagement_bar_level=$phonebookitem[0]['engagement_level']*10;
                  $phonebookitem[0]['engagement_bar_level']=$engagement_bar_level."%";
                  //Build birth date
                  if (!empty($customer[0]['birthdate']) && $customer[0]['birthdate']!=0 && $customer[0]['birthdate']!=-3600) {
                    $birht_date = date('d-m-Y', $customer[0]['birthdate']);
                    $timestamp  = strtotime($birht_date);
                    $phonebookitem[0]['birth_day']=date("d", $timestamp);
                    $phonebookitem[0]['birth_month']=date("m", $timestamp);
                    $phonebookitem[0]['birth_year']=date("Y", $timestamp);
                      if ($phonebookitem[0]['birth_day']=='01' && $phonebookitem[0]['birth_month']=='01' && $phonebookitem[0]['birth_year']=='1970') {
                        $phonebookitem[0]['birth_day']="00";
                        $phonebookitem[0]['birth_month']="00";
                        $phonebookitem[0]['birth_year']="0000";    
                      }
                  }else {
                    $phonebookitem[0]['birth_day']="00";
                    $phonebookitem[0]['birth_month']="00";
                    $phonebookitem[0]['birth_year']="0000";
                  }
                  // Referrer Consent Description
                  $gdpr_consent="<div class='col-sm-2'>";
                  if ($phonebookitem[0]['privacy_policy']==1) {
                    $gdpr_consent.="<img style='height:40px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
                    $gdpr_consent.="</div>";
                    $gdpr_consent.="<div class='col-sm-10'>";
                    $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['customer_consent_date']."-".__("App User")."</p>";                    
                  }else {                    
                    $gdpr_consent.="<img style='height:40px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
                    $gdpr_consent.="</div>";
                    $gdpr_consent.="<div class='col-sm-10'>";
                    $gdpr_consent.="<p>".__("No GDPR")."</p>";
                  }
                  $gdpr_consent.="</div>";
              }else {
                if ($phonebookitem[0]['owner_dob']!=NULL) {
                  $timestamp  = strtotime($phonebookitem[0]['owner_dob']);
                  $phonebookitem[0]['birth_day']  = date("d", $timestamp);
                  $phonebookitem[0]['birth_month']= date("m", $timestamp);
                  $phonebookitem[0]['birth_year'] = date("Y", $timestamp);
                }else {
                  $phonebookitem[0]['birth_day']  = 0;
                  $phonebookitem[0]['birth_month']= 0;
                  $phonebookitem[0]['birth_year'] = 0;
                }
                // Prospect Consent Description
                if ($phonebookitem[0]['gdpr_consent_timestamp']!=NULL) {
                  $gdpr_consent="<div class='col-sm-2'>";
                  $gdpr_consent.="<img style='height:40px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
                  $gdpr_consent.="</div>";
                  $gdpr_consent.="<div class='col-sm-10'>";
                  $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['gdpr_consent_timestamp']."-".$phonebookitem[0]['gdpr_consent_source']."-".$phonebookitem[0]['gdpr_consent_ip']."</p>";
                  $gdpr_consent.="</div>";
                }else {
                  $gdpr_consent="<div class='col-sm-2'>";
                  $gdpr_consent.="<img style='height:40px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
                  $gdpr_consent.="</div>";
                  $gdpr_consent.="<div class='col-sm-10'>";
                  $gdpr_consent.="<p>".__("No GDPR")."</p>";
                  $gdpr_consent.="</div>";
                }
              }              
              //Define default jobs 
              $jobs_collection[]=[
                                'job_title'      =>  __("Non classifiable"),
                                'job_title_copy' =>  __("Non classifiable"),
                                'job_id'         => 0
                              ];
              $jobs_collection[]=[
                                'job_title'      =>  __("Add New Job"),
                                'job_title_copy' =>  __("Add New Job"),
                                'job_id'         => -1
                              ];
              // Update job list from DB
              foreach ($all_jobs as $key => $value) {
                  $jobs_collection[]=[
                                    'job_title'      => $value['job_title'],
                                    'job_title_copy' => $value['job_title'],
                                    'job_id'         => $value['migareference_jobs_id']
                                  ];
                }
              //Define default Sector
              // Update Sector list from DB
              foreach ($all_professions as $key => $value) {
                  $professions_collection[]=[
                                    'profession_title'      => $value['profession_title'],
                                    'profession_title_copy' => $value['profession_title'],
                                    'profession_id'         => $value['migareference_professions_id']
                                  ];
                }
                $professions_collection[]=[
                  'profession_title'      =>  __("N/A"),
                  'profession_title_copy' =>  __("N/A"),
                  'profession_id'         => 0
                ];
              // Define Agent Collection
              $agent_collection= [];
              foreach ($agent_data as $key => $value) {
                  $agent_collection[] = array(
                    'id'=>$value['customer_id'],
                    'name'=>$value['firstname']." ".$value['lastname']
                  );
                }
                if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
                $agent_collection[] = array(
                  'id'   => 0,
                  'name' => __("I don’t know")
                );
              }
              $partner_agent_collection= [];
              foreach ($partner_agents as $key => $value) {
                  $partner_agent_collection[] = array(
                    'id'=>$value['customer_id'],
                    'name'=>$value['firstname']." ".$value['lastname']
                  );
                }
                if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
                $partner_agent_collection[] = array(
                  'id'   => 0,
                  'name' => __("I don’t know")
                );
              }
                $consent_collection=($pre_report[0]['consent_collection']==1) ? true : false ;
                // Main and Sub Address settings
                $default_country_id=0;
                foreach ($countries as $key => $value) {                   
                  $countries_list[]=[
                    'country'=>$value['country'],
                    'country_id'=>$value['migareference_geo_countries_id']
                  ];
                }
                $countries_list =  array_map("unserialize", array_unique(array_map("serialize", $countries_list)));
                sort($countries_list);
                  if (count($countries_list)) {
                  $default_country_id=$countries_list[0]['country_id'];
                }
                  if ($phonebookitem[0]['address_country_id']!=0 && count($countries_list)>1) {
                  $default_country_id=$phonebookitem[0]['address_country_id'];
                }
                $country_provinces        = $migareference->getGeoCountrieProvinces($app_id,$default_country_id);
                foreach ($country_provinces as $key => $value) {
                  $address_province_list[]=[
                    'province'=>$value['province'],
                    'province_id'=>$value['migareference_geo_provinces_id']
                  ];
                }
                $address_province_list =  array_map("unserialize", array_unique(array_map("serialize", $address_province_list)));
                sort($address_province_list);
                $phonebookitem[0]['note']=$phonebookitem[0]['phone_note'];
              $payload = [
                  "success"            => true,
                  "phonebookitem"      => $phonebookitem[0],
                  "cutomer"            => $customer,
                  "jobscollection"     => $jobs_collection,
                  "professionscollection" => $professions_collection,
                  "agentcollection"    => $agent_collection,
                  "partner_agent_collection"    => $partner_agent_collection,
                  "phonelog"           => $phonelog,
                  "invoiceDataItem"    => $invoiceDataItem,
                  "gdpr_consent"       => $gdpr_consent,                  
                  "birht_date"         => $timestamp,
                  "icon_list"          => $StaticIons,
                  "is_terms_accepted"  => $is_terms_accepted,
                  'default_country_id' => $default_country_id,
                  'countries_list'     => $countries_list,
                  'countries_count'    => count($countries_list),
                  'address_province_list'=> $address_province_list,
                  "consent_collection" => $consent_collection,
                  "pre_report"         => $pre_report
              ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function loadjobslistAction(){
      try {
              $migareference = new Migareference_Model_Migareference();
              $Utilities = new Migareference_Model_Utilities();
              
              $app_id        = $this->getApplication()->getId();
              $all_jobs      = $migareference->getJobs($app_id);
              $all_professions= $migareference->getProfessions($app_id);
              $pre_report    = $migareference->preReportsettigns($app_id);
              $type_one_agents = $Utilities->getTypeOneAgents($app_id);
              $type_two_agents = $Utilities->getTypeTwoAgents($app_id);
              $all_agents = $Utilities->getAllAgents($app_id);
              $jobs_collection = [];
              $jobs_collection[]=[
                'job_title'=> __("Non classifiable"),
                'job_title_copy'=> __("Non classifiable"),
                'job_id'=>0
              ];
              $jobs_collection[]=[
                'job_title'=> __("Add New Job"),
                'job_title_copy'=> __("Add New Job"),
                'job_id'=>-1
              ];
              foreach ($all_jobs as $key => $value) {
                  $jobs_collection[]=[
                                    'job_title'=>$value['job_title'],
                                    'job_title_copy'=>$value['job_title'],
                                    'job_id'=>$value['migareference_jobs_id']
                                ];
                }
              $professions_collection = [];
             
              foreach ($all_professions as $key => $value) {
                  $professions_collection[]=[
                                    'profession_title'=>$value['profession_title'],
                                    'profession_title_copy'=>$value['profession_title'],
                                    'profession_id'=>$value['migareference_professions_id']
                                ];
                }
                $professions_collection[]=[
                  'profession_title'=> __("N/A"),
                  'profession_title_copy'=> __("N/A"),
                  'profession_id'=>0
                ];

                // All Agents
                $all_agent_collection=[];
                foreach ($all_agents as $key => $value) {
                  $all_agent_collection[] = array(
                    'id'=>$value['agent_id'],
                    'name'=>$value['agent_name'],
                    'email'=>$value['agent_email'],
                  );
                }
                // Type one or customer Agents
                $type_one_agent_collection=[];
                foreach ($type_one_agents as $key => $value) {
                  $type_one_agent_collection[] = array(
                    'id'=>$value['agent_id'],
                    'name'=>$value['agent_name'],
                    'email'=>$value['agent_email'],
                  );
                }
                // Type Two or Partner Agents
                $type_two_agent_collection=[];
                foreach ($type_two_agents as $key => $value) {
                  $type_two_agent_collection[] = array(
                    'id'=>$value['agent_id'],
                    'name'=>$value['agent_name'],
                    'email'=>$value['agent_email'],
                  );
                }

                // Main and Sub Address settings
              $countries = $migareference->geoProvinceCountries($app_id);//Countries which have provinces 
              $address_province_list = [];
              $default_country_id=0;
              foreach ($countries as $key => $value) {                   
                $countries_list[]=[
                  'country'=>$value['country'],
                  'country_id'=>$value['migareference_geo_countries_id']
                ];
              }
              $countries_list =  array_map("unserialize", array_unique(array_map("serialize", $countries_list)));
              sort($countries_list);
                if (count($countries_list)) {
                $default_country_id=$countries_list[0]['country_id'];
              }              
              $country_provinces        = $migareference->getGeoCountrieProvinces($app_id,$default_country_id);
              foreach ($country_provinces as $key => $value) {
                $address_province_list[]=[
                  'province'=>$value['province'],
                  'province_id'=>$value['migareference_geo_provinces_id']
                ];
              }
              $address_province_list =  array_map("unserialize", array_unique(array_map("serialize", $address_province_list)));
              sort($address_province_list);
            // END Address Settings
              $payload = [
                  "success"              => true,
                  "jobscollection"       => $jobs_collection,
                  "professionscollection"=> $professions_collection,
                  "pre_report"           => $pre_report,
                  "agentcollection"      => $type_one_agent_collection,
                  "partner_agent_collection" => $type_two_agent_collection,
                  'default_country_id'   => $default_country_id,
                  'countries_list'       => $countries_list,
                  'all_agent_collection' => $all_agent_collection,
                  'countries_count'      => count($countries_list),
                  'address_province_list'=> $address_province_list,
              ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getphonebooksAction(){
        try {
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $rating        = $this->getRequest()->getParam('rating');
          $prospectrating= $this->getRequest()->getParam('prospectrating');
          $user_id       = $this->getRequest()->getParam('user_id');
          $contact_users = $migareference->getContactsUsers($app_id);
          $StaticIons    = $migareference->getStaticIons();
          if (isset($user_id)) {
            $agent_data    = $migareference->is_agent($app_id,$user_id);
            if (count($agent_data) && $agent_data[0]['full_phonebook']==1) {
              $all_ref_contacts = $migareference->getAgentReferrerPhonebook($app_id,$user_id);
              $prospect_jobs    = $migareference->getAgentProspectPhonebook($app_id,$user_id);
            }else{
                // $all_ref_contacts = $migareference->getProspectJobs($app_id,1);
                $all_ref_contacts = $migareference->get_opt_referral_users($app_id,'');
                $prospect_jobs    = $migareference->getAllProspect($app_id);                
            }
          }

          $all_jobs                    = $migareference->getJobs($app_id);
          $referrerPhonebookCollection = [];
          $prospectPhonebookCollection = [];
          $contactPhonebookCollection  = [];
          $jobs_collection             = [];
          // Contact user collection
          foreach ($contact_users as $key => $value) {
              $contactPhonebookCollection[]=[
                'name'      =>$value['lastname']." ".$value['firstname'],
                'phone'     =>$value['mobile'],
                'rating'    =>5,
                'job_title' =>'',
                'created_at'=>$value['created_at'],
                'customer_id' =>$value['customer_id']
              ];
          }
          // Referrer usersList
          $now               = time();
          foreach ($all_ref_contacts as $key => $value) {
            if ($value['rating']>=$rating) {                
                // $report_stats    = $migareference->phonebookReportStats($app_id,$value['invoice_id']);
                $lastcontactdate=$value['last_contact_at'];
                if (empty($value['last_contact_at'])) {
                  $logitem           = $migareference->getLatCommunication($value['migarefrence_phonebook_id']);
                  $lastcontactdate   = (!empty($logitem[0]['created_at'])) ? $logitem[0]['created_at'] : $value['phone_creat_date'] ;                 
                }      
                $sponsor_one=0;          
                $sponsor_two=0;          
                if ($value['sponsor_count']>0) {
                  $agents_list=$migareference->getReferrerAgents($app_id,$value['user_id']);              
                  // $agent=$agents_list[0]['lastname']." ".$agents_list[0]['firstname'];
                  $sponsor_one=$agents_list[0]['agent_id'];                           
                  if (COUNT($agents_list)>1) {
                    // $agent.=' & '.$agents_list[1]['lastname']." ".$agents_list[1]['firstname'];
                    $sponsor_two=$agents_list[1]['agent_id'];                           
                  }
                }
                $itemDate          = strtotime($lastcontactdate);
                $datediff          = $now-$itemDate;
                $datediff          = round($datediff / (60 * 60 * 24));
                $is_terms_accepted = ( $report_stats[0]['terms_accepted']==0) ? false : true ;
                $referrerPhonebookCollection[]=[
                  'name'             =>$value['firstname']." ".$value['lastname'],
                  'phone'            =>$value['mobile'],
                  'rating'           =>$value['rating'],
                  'admin_user_id'    =>$value['admin_user_id'],
                  'sponsor_id'       =>$value['sponsor_id'],
                  'sponsor_one'       =>$sponsor_one,
                  'sponsor_two'       =>$sponsor_two,
                  'is_terms_accepted'=>$is_terms_accepted,
                  'total_reports'    =>$value['total_reports'],
                  'active_reports'   =>$report_stats[0]['active_reports'],
                  'ratingfilter'     =>"rating".$value['rating'],
                  'job_title'        =>$value['job_title'],
                  'created_at'       =>$lastcontactdate,
                  'lastcontact'      =>"-".($datediff),
                  'id'               =>$value['migarefrence_phonebook_id']
                ];
            }
          }
          // Prospect List
            foreach ($prospect_jobs as $key => $value) {
              if ($value['rating']>=$prospectrating) {
                  $logitem         = $migareference->getLatCommunication($value['migarefrence_prospect_id']);
                  $lastcontactdate = (!empty($logitem[0]['created_at'])) ? date('d-m-Y',strtotime($logitem[0]['created_at'])) : date('d-m-Y',strtotime($value['phone_creat_date']));                  
                  $itemDate = strtotime($lastcontactdate);
                  $datediff = $now-$itemDate;
                  $datediff = round($datediff / (60 * 60 * 24));
                  $prospectPhonebookCollection[]=[
                        'name'          =>ucwords($value['name'])." ".ucwords($value['surname']),
                        'phone'         =>$value['mobile'],
                        'job_title'     =>$value['job_title'],
                        'admin_user_id' =>$value['admin_user_id'],
                        'sponsor_id'    =>$value['sponsor_id'],
                        'rating'        =>$value['rating'],
                        'ratingfilter'  =>"rating".$value['rating'],
                        'created_at'    =>$lastcontactdate,
                        'lastcontact'   =>"-".($datediff),
                        'id'            =>$value['migarefrence_prospect_id']
                    ];
                }
              }
              // Define default jobs
              $jobs_collection = [
                  [
                      'job_title' => __("Show All"),
                      'job_title_copy' => "",
                      'job_id' => 0
                  ],
                  [
                      'job_title' => __("ADD NEW JOB"),
                      'job_title_copy' => 1000,
                      'job_id' => 1000
                  ]
              ];
              // Add jobs from database
              foreach ($all_jobs as $value) {
                  $jobs_collection[] = [
                      'job_title' => $value['job_title'],
                      'job_title_copy' => $value['job_title'],
                      'job_id' => $value['migareference_jobs_id']
                  ];
              }
              // Define Engagement
              $engagment_list[]=['engagement_level'=>__("Show All")];
              for ($i=1; $i <= 10; $i++) {
                $engagment_list[]=['engagement_level'=>$i];
              }
              // Define Referrer Report LImits
              // $reports_filter_list[]=['filter_id'=>'','report_limit'=>__("Life Time Reports")];              
              $reports_filter_list[]=['filter_id'=>0,'report_limit'=>__("0-5 Reports")];              
              $reports_filter_list[]=['filter_id'=>5,'report_limit'=>__("5-10 Reports")];              
              $reports_filter_list[]=['filter_id'=>10,'report_limit'=>__("10-30 Reports")];              
              $reports_filter_list[]=['filter_id'=>30,'report_limit'=>__("30-50 Reports")];              
              $reports_filter_list[]=['filter_id'=>50,'report_limit'=>__("More Than 50 Reports")];              
              // Define Referrer Last Contact LImits
              // $ref_filter_lastcontact[]=['filter_id'=>'','last_contact'=>__("Days since last contact")];              
              $ref_filter_lastcontact[]=['filter_id'=>1,'last_contact'=>__("Less than 30 Days")];              
              $ref_filter_lastcontact[]=['filter_id'=>2,'last_contact'=>__("30-90 Days")];                            
              $ref_filter_lastcontact[]=['filter_id'=>3,'last_contact'=>__("More Than 90 Days")];     

              $ref_filter_date_range[]=['filter_id'=>1,'date_range'=>__("Past 7 Days")];              
              $ref_filter_date_range[]=['filter_id'=>2,'date_range'=>__("Past 30 Days")];                            
              $ref_filter_date_range[]=['filter_id'=>3,'date_range'=>__("“Past 3 Months")];              
              $ref_filter_date_range[]=['filter_id'=>4,'date_range'=>__("“Past 6 Months")];              
              $ref_filter_date_range[]=['filter_id'=>5,'date_range'=>__("“Past 12 Months")];     

              $ref_sort_by_filter[]=['filter_id'=>1,'sort_by'=>__("Sort by New to Old")];              
              $ref_sort_by_filter[]=['filter_id'=>2,'sort_by'=>__("Sort by Old to New")];              
              $ref_sort_by_filter[]=['filter_id'=>3,'sort_by'=>__("Sort by A to Z")];                            
              $ref_sort_by_filter[]=['filter_id'=>4,'sort_by'=>__("Sort by Z to A")];                            
              $ref_sort_by_filter[]=['filter_id'=>5,'sort_by'=>__("Sort by Rating High to Low")];                            
              $ref_sort_by_filter[]=['filter_id'=>6,'sort_by'=>__("Sort by Rating Low to High")];                                                 

            $payload = [
                "success" => true,
                "referrerPhonebook"  => $referrerPhonebookCollection,
                "prospectPhonebook"  => $prospectPhonebookCollection,
                "prospect_jobs"      => [],
                "contactPhonebook"   => $contactPhonebookCollection,
                "jobs_list"          => $jobs_collection,
                "engagment_list"     => $engagment_list,
                "reports_filter_list"=> $reports_filter_list,
                "ref_filter_lastcontact"=> $ref_filter_lastcontact,
                "ref_sort_by_filter"=> $ref_sort_by_filter,
                "ref_filter_date_range"=> $ref_filter_date_range,
                "icon_list"          => $StaticIons,
                "engagment_t"        => $all_ref_contacts,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getprospectphonebookAction(){
        try {
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $prospectrating= $this->getRequest()->getParam('rating');          
          $user_id       = $this->getRequest()->getParam('user_id');
          $contact_users = $migareference->getContactsUsers($app_id);          
          if (isset($user_id)) {
            $agent_data    = $migareference->is_agent($app_id,$user_id);
            if (count($agent_data)) {              
              $prospect_jobs    = $migareference->getAgentProspectPhonebook($app_id,$user_id);
            }else{                
                $prospect_jobs    = $migareference->getAllProspect($app_id);                
            }
          }          
          $prospectPhonebookCollection = [];
          $contactPhonebookCollection  = [];          
          // Contact user collection
          foreach ($contact_users as $key => $value) {
              $contactPhonebookCollection[]=[
                'name'      =>$value['lastname']." ".$value['firstname'],
                'phone'     =>$value['mobile'],
                'rating'    =>5,
                'job_title' =>'',
                'created_at'=>$value['created_at'],
                'customer_id' =>$value['customer_id']
              ];
          }
          // Referrer usersList
          $now               = time();          
          // Prospect List
            foreach ($prospect_jobs as $key => $value) {
              if ($value['rating']>=$prospectrating) {
                  $logitem         = $migareference->getLatCommunication($value['migarefrence_prospect_id']);
                  $lastcontactdate = (!empty($logitem[0]['created_at'])) ? date('d-m-Y',strtotime($logitem[0]['created_at'])) : date('d-m-Y',strtotime($value['phone_creat_date']));                  
                  $itemDate = strtotime($lastcontactdate);
                  $datediff = $now-$itemDate;
                  $datediff = round($datediff / (60 * 60 * 24));
                  $prospectPhonebookCollection[]=[
                        'name'          =>ucwords($value['name'])." ".ucwords($value['surname']),
                        'phone'         =>$value['mobile'],
                        'job_title'     =>$value['job_title'],
                        'admin_user_id' =>$value['admin_user_id'],
                        'sponsor_id'    =>$value['sponsor_id'],
                        'rating'        =>$value['rating'],
                        'ratingfilter'  =>"rating".$value['rating'],
                        'created_at'    =>$lastcontactdate,
                        'lastcontact'   =>"-".($datediff),
                        'id'            =>$value['migarefrence_prospect_id']
                    ];
                }
              }
              
            $payload = [
                "success" => true,
                "referrerPhonebook"  => $referrerPhonebookCollection,
                "prospectPhonebook"  => $prospectPhonebookCollection,
                "prospect_jobs"      => [],
                "contactPhonebook"   => $contactPhonebookCollection,
                "jobs_list"          => $jobs_collection,
                "engagment_list"     => $engagment_list,
                "reports_filter_list"=> $reports_filter_list,
                "ref_filter_lastcontact"=> $ref_filter_lastcontact,
                "icon_list"          => $StaticIons,
                "engagment_t"        => $all_ref_contacts,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getreferrerphonebookAction(){
        try {
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $rating        = $this->getRequest()->getParam('rating');          
          $user_id       = $this->getRequest()->getParam('user_id');          
          $currentPage   = $this->getRequest()->getParam('currentPage');          
          $recordsPerPage= $this->getRequest()->getParam('recordsPerPage');          
          $StaticIons    = $migareference->getStaticIons();
          $all_jobs      = $migareference->getJobs($app_id);
          $all_professions= $migareference->getProfessions($app_id);
          $referrer_provinces = $migareference->referrerAppProvinces($app_id);          

          if (isset($user_id)) {
            $agent_data    = $migareference->is_agent($app_id,$user_id);
            if (count($agent_data) && $agent_data[0]['full_phonebook']==0) {
              $all_ref_contacts = $migareference->getAgentReferrerPhonebook($app_id,$user_id);              
            }else{               
                $all_ref_contacts = $migareference->get_opt_referral_users($app_id,'');                                    
            }
          }
          
          $referrerPhonebookCollection = [];          
          $jobs_collection             = [];          
          // Referrer usersList
          $now               = time();
          $profiled="<img title='".__('User Profiled')."' src=".$icon_list['profiled']." alt='' width='35px'>";
          $not_profiled="<img title='".__('User Not Profiled')."' src=".$icon_list['not_profiled']." alt='' width='35px'>";
          foreach ($all_ref_contacts as $key => $value) {
            if ($value['rating']>=$rating) {     
              $date = new DateTime($value['phone_creat_date']);
              $isoDate = $date->format(DateTime::ATOM);                           
                $lastcontactdate=$value['last_contact_at'];
                if (empty($value['last_contact_at'])) {
                  $logitem           = $migareference->getLatCommunication($value['migarefrence_phonebook_id']);
                  $lastcontactdate   = (!empty($logitem[0]['created_at'])) ? $logitem[0]['created_at'] : $value['phone_creat_date'] ;                 
                }                
                $itemDate          = strtotime($lastcontactdate);
                $datediff          = $now-$itemDate;
                $datediff          = round($datediff / (60 * 60 * 24));
                $is_terms_accepted = ( $value['terms_accepted']==0) ? false : true ;
                $is_profiled = ( $value['job_id']>0 && $value['rating']>0) ?  true: false ;                  
                // Calculate days since creation
                $creationDate = strtotime($value['phone_creat_date']);
                $creationDateDiff = $now - $creationDate;
                $creationDaysAgo = round($creationDateDiff / (60 * 60 * 24));
                $referrerPhonebookCollection[]=[
                  'name'             =>$value['firstname']." ".$value['lastname'],
                  'phone'            =>$value['mobile'],
                  'rating'           =>$value['rating'],
                  'province'         =>$value['province'].'-'.$value['province_code'],
                  'admin_user_id'    =>$value['admin_user_id'],
                  'sponsor_id'       =>$value['sponsor_id'],
                  'is_terms_accepted'=>$is_terms_accepted,
                  'is_profiled'      =>$is_profiled,
                  'sponsor_one'      =>$value['sponsor_one_id'],
                  'sponsor_two'      =>$value['sponsor_two_id'],
                  'total_reports'    =>$value['total_reports'],
                  'active_reports'   =>$value['active_reports'],
                  'ratingfilter'     =>"rating".$value['rating'],
                  'job_title'        =>$value['job_title'],
                  'profession_title' =>$value['profession_title'],
                  'created_at'       =>$isoDate,
                  'days_since_creation'=>$creationDaysAgo,
                  'lastcontact'      =>"-".($datediff),
                  'id'               =>$value['migarefrence_phonebook_id']
                ];
            }
          }          
          // Define default jobs
          $jobs_collection = [
              [
                  'job_title' => __("Job"),
                  'job_title_copy' => "",
                  'job_id' => 0
              ]
          ];
              // Add jobs from database
              foreach ($all_jobs as $value) {
                  $jobs_collection[] = [
                      'job_title' => $value['job_title'],
                      'job_title_copy' => $value['job_title'],
                      'job_id' => $value['migareference_jobs_id']
                  ];
              }
          // Define default Sector
          $professions_collection = [
              [
                  'profession_title' => __("Sector"),
                  'profession_title_copy' => "",
                  'profession_id' => 0
              ]
          ];
           // Add jobs from database
           foreach ($all_professions as $value) {
            $professions_collection[] = [
                'profession_title' => $value['profession_title'],
                'profession_title_copy' => $value['profession_title'],
                'profession_id' => $value['migareference_professions_id']
            ];
        }
          $provinces_collection = [
              [
                  'province_title' => __("Province"),
                  'province_title_copy' => "",
                  'province_id' => 0
              ]
          ];
              // Add Sectors from database
              foreach ($referrer_provinces as $value) {
                  $provinces_collection[] = [
                      'province_title' => $value['province'].'-'.$value['province_code'],
                      'province_title_copy' => $value['province'].'-'.$value['province_code'],
                      'province_id' => $value['migareference_geo_provinces_id']
                  ];
              }
              // Define Engagement
              $engagment_list[]=['engagement_level'=>__("Show All")];
              for ($i=1; $i <= 10; $i++) {
                $engagment_list[]=['engagement_level'=>$i];
              }
              // Define Referrer Report LImits
              $reports_filter_list[]=['filter_id'=>0,'report_limit'=>__("0-5 Reports")];              
              $reports_filter_list[]=['filter_id'=>5,'report_limit'=>__("5-10 Reports")];              
              $reports_filter_list[]=['filter_id'=>10,'report_limit'=>__("10-30 Reports")];              
              $reports_filter_list[]=['filter_id'=>30,'report_limit'=>__("30-50 Reports")];              
              $reports_filter_list[]=['filter_id'=>50,'report_limit'=>__("More Than 50 Reports")];              
              // Define Referrer Last Contact LImits
              $ref_filter_lastcontact[]=['filter_id'=>1,'last_contact'=>__("Less than 30 Days")];              
              $ref_filter_lastcontact[]=['filter_id'=>2,'last_contact'=>__("30-90 Days")];                            
              $ref_filter_lastcontact[]=['filter_id'=>3,'last_contact'=>__("More Than 90 Days")];    
              
              $ref_filter_date_range[]=['filter_id'=>1,'date_range'=>__("Past 7 Days")];              
              $ref_filter_date_range[]=['filter_id'=>2,'date_range'=>__("Past 30 Days")];                            
              $ref_filter_date_range[]=['filter_id'=>3,'date_range'=>__("Past 3 Months")];              
              $ref_filter_date_range[]=['filter_id'=>4,'date_range'=>__("Past 6 Months")];              
              $ref_filter_date_range[]=['filter_id'=>5,'date_range'=>__("Past 12 Months")];   

              $ref_sort_by_filter[]=['filter_id'=>'-created_at','sort_by'=>__("New to Old")];              
              $ref_sort_by_filter[]=['filter_id'=>'created_at','sort_by'=>__("Old to New")];              
              $ref_sort_by_filter[]=['filter_id'=>'name','sort_by'=>__("A to Z")];                            
              $ref_sort_by_filter[]=['filter_id'=>'-name','sort_by'=>__("Z to A")];                            
              $ref_sort_by_filter[]=['filter_id'=>'-rating','sort_by'=>__("Rating High to Low")];                            
              $ref_sort_by_filter[]=['filter_id'=>'rating','sort_by'=>__("Rating Low to High")];                                                               

            $payload = [
                "success" => true,
                "referrerPhonebook"  => $referrerPhonebookCollection,                               
                "jobs_list"          => $jobs_collection,
                "profession_list"    => $professions_collection,
                "province_list"      => $provinces_collection,
                "grandtotalReferrers" => 774,
                "engagment_list"     => $engagment_list,
                "reports_filter_list"=> $reports_filter_list,
                "ref_filter_lastcontact"=> $ref_filter_lastcontact,
                "ref_filter_date_range"=> $ref_filter_date_range,
                "ref_sort_by_filter"=> $ref_sort_by_filter,
                "icon_list"          => $StaticIons,
                "engagment_t"        => $all_ref_contacts,                
                "offset"        => $offset,                
                "limist"        => $limit,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function editnoteAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $public_key    = $this->getRequest()->getParam('public_key');
          $note_item     = $migareference->editnote($public_key);
            $payload = [
                "success" => true,
                "note"  => $note_item[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function deletenoteAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $public_key    = $this->getRequest()->getParam('public_key');
                           $migareference->deletenote($public_key);
            $payload = [
                "success" => true,
                "message" => __("Successfully delete Note.")
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function deletereminderAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $public_key    = $this->getRequest()->getParam('public_key');
          $data['is_deleted']=1;
          $migareference->update_reminder($public_key,$data);
            $payload = [
                "success" => true,
                "message" => __("Successfully delete Reminder.")
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function updatereminderAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $reminder_id   = $this->getRequest()->getParam('public_key');
          $report_id     = $this->getRequest()->getParam('report_id');
          $user_id       = $this->getRequest()->getParam('user_id');
          $status        = $this->getRequest()->getParam('status');
          $changevalue   = $this->getRequest()->getParam('changevalue');
          $application   = $this->getApplication();
          $app_id        = $application->getId();


          $autom_log['app_id']       = $app_id;
          $autom_log['reminder_id']  = $reminder_id;
          $autom_log['report_id']    = $report_id;
          $autom_log['user_id']      = $user_id;
          $autom_log['receipent']    = "Manual"." ".$status;
          $autom_log['email_log_id'] = 0;
          $autom_log['push_log_id']  = 0;

          if ($status=="Done" || $status=="cancele") {
            $reminder['reminder_current_status']=$status;//set staus to done
            $migareference->update_reminder($reminder_id,$reminder);
            $migareference->saveRepoRemLog($autom_log);
          }elseif ($status=="Postpone") {
            // Resent Evenr Values
            $remItem            = $migareference->editreminder($reminder_id);
            $event_date=date('d-m-Y',strtotime($remItem[0]['event_date_time']));
              // Add days to date and display it
              $event_date=date('Y-m-d H:i:s', strtotime($remItem[0]['event_date_time']. ' + '.$changevalue.' days'));
              $remin_date=date('Y-m-d H:i:s', strtotime($remItem[0]['reminder_date_time']. ' + '.$changevalue.' days'));
              $reminder['event_day']=date('d',strtotime($event_date));
              $reminder['event_month']=date('m',strtotime($event_date));
              $reminder['event_year']=date('Y',strtotime($event_date));
              $reminder['reminder_current_status']="Postpone";
              $reminder['postpone_days']=$changevalue;
              $reminder['event_date_time']=$event_date;
              $reminder['reminder_date_time']=$remin_date;
              $migareference->update_reminder($reminder_id,$reminder);
              $migareference->saveRepoRemLog($autom_log);

          }elseif ($status=="Notes") {
            $reminder['reminder_content']=$changevalue;
            $migareference->update_reminder($reminder_id,$reminder);
          }
            $payload = [
                "success" => true,
                "message" => __("Successfully update Reminder."),
                "mse" => $event_date,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
                'mess' => $autom_log
            ];
        }
        $this->_sendJson($payload);
  }
  public function updateautoreminderAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $reminder_id   = $this->getRequest()->getParam('public_key');          
          $user_id       = $this->getRequest()->getParam('user_id');
          $phonebook_id  = $this->getRequest()->getParam('phobebook_id');
          $status        = $this->getRequest()->getParam('status');
          $changevalue   = $this->getRequest()->getParam('changed_value');          
          $app_id        = $this->getApplication()->getId();
          $remItem       = $migareference->autoReminderItem($reminder_id);
          $status=strtolower($status);
          $log_item=[
              'app_id'       => $app_id,
              'phonebook_id' => $phonebook_id,
              'reminder_id' => $reminder_id,
              'log_type'     => "Automation",              
              'user_id'      => $user_id,
              'created_at'   => date('Y-m-d H:i:s')
          ];          
          if ($status=="done" || $status=="cancele") {
            $reminder['current_reminder_status']=$status;//set staus to done
            $migareference->updateAutoReminder($reminder_id,$reminder);
            $log_item['note']="Reminder Status Change To:".$status;            
          }elseif ($status=="postpone") {              
              // Add days to date and display it
              $event_date=date('Y-m-d H:i:s', strtotime($remItem[0]['auto_event_date_time']. ' + '.$changevalue.' days'));              
              $reminder['current_reminder_status']="Postpone";
              $reminder['postpone_days']=$changevalue;
              $reminder['auto_event_date_time']=$event_date;              
              $migareference->updateAutoReminder($reminder_id,$reminder);
              $log_item['note']="Reminder Status Change To:".$status;
            }elseif ($status=="notes") {
              $reminder['reminder_content']=$changevalue;          
              $migareference->updateAutoReminder($reminder_id,$reminder);
              $log_item['note']="Reminder Note Change To".$changevalue;
            }
            $migareference->saveCommunicationLog($log_item);
            $payload = [
                "success" => true,
                "message" => __("Successfully update Reminder."),
                "mse" => $event_date,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
                'mess' => $remItem
            ];
        }
        $this->_sendJson($payload);
  }
  // Deprecated 3.2.xx
  public function getreminderlistAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $report_id     = $this->getRequest()->getParam('report_id');
          $all_reminders = $migareference->get_reminder($app_id,$report_id);
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $remindercollection[]=[
              'date'=>date('d-m-Y',strtotime($value['event_date_time'])),
              'reminder_content'=>$value['reminder_content'],
              'type'=>$value['reminder_type_text'],
              'public_key'=>$value['migarefrence_reminders_id']
            ];
          }
            $payload = [
                "success" => true,
                "remindercollection"  => $remindercollection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getfiltericonAction(){
        try {
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $report_id     = $this->getRequest()->getParam('report_id');
          $iconCollection = $migareference->getStaticIons();
            $payload = [
                "success" => true,
                "iconCollection"  => $iconCollection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getsinglereportreminderAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $report_id     = $this->getRequest()->getParam('report_id');
          $all_reminders = $migareference->getSingleReportReminder($app_id,$report_id);
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $remindercollection[]=[
              'date'            => date('d-m-Y',strtotime($value['event_date_time'])),
              'reminder_content'=> $value['reminder_type_text'],
              'type'            => $value['rep_rem_title'],
              'public_key'      => $value['migarefrence_reminders_id']
            ];
          }
            $payload = [
                "success" => true,
                "remindercollection"  => $remindercollection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function loadprovincesAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $country_id     = $this->getRequest()->getParam('country_id');
          $dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$country_id);
            $payload = [
                "success" => true,
                "proviceitems"  => $dataGeoConPro
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getprovinceAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $country_id     = $this->getRequest()->getParam('country_id');
          $country_provinces        = $migareference->getGeoCountrieProvinces($app_id,$country_id);
          foreach ($country_provinces as $key => $value) {
            $address_province_list[]=[
              'province'=>$value['province'],
              'province_id'=>$value['migareference_geo_provinces_id']
            ];
          }
          $address_province_list =  array_map("unserialize", array_unique(array_map("serialize", $address_province_list)));
          sort($address_province_list);
            $payload = [
                "success" => true,
                "address_province_list"  => $address_province_list
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  // Deprecated in 3.2.x
  public function getallremindersAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $all_reminders = $migareference->get_all_reminder($app_id);
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          // Build Notes Collection
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $remindercollection[]=[
              'event_img'=>$base_url."/app/local/modules/Migareference/resources/appicons/calender.png",
              'event_date'=>date('d-m-Y',strtotime($value['event_date_time'])),
              'event_time'=>date('h:i A',strtotime($value['event_date_time'])),
              'reminder_content'=>$value['reminder_content'],
              'type'=>$value['reminder_type_text'],
              'public_key'=>$value['migarefrence_reminders_id'],
              'status'=>$value['reminder_current_status']
            ];
          }
            $payload = [
                "success" => true,
                "remindercollection"  => $remindercollection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  // Deprecated Method in V 2.3.2
  public function getremindertypeAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $remindertypes = $migareference->getReportReminder($app_id);
          $payload = [
              "success" => true,
              "remindertypecollection"  => $remindertypes
          ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getreportremindersAction(){
        try {
          $customer_id   = $this->getRequest()->getParam('customer_id');
          $filter_key    = $this->getRequest()->getParam('filter_key');
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $all_reminders = $migareference->getReportReminders($app_id);
          $pre_settings  = $migareference->preReportsettigns($app_id);
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $agent_data    = $migareference->is_agent($app_id,$customer_id);
          $is_admin      = (count($agent_data)) ? false : true ;
          $agent_collection   = [];
          $owner_collection   = [];
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $reminder_id    = $value['migarefrence_reminders_id'];
            $find_in_log    = $migareference->findInRepoRemLog($reminder_id);
            $reminder_current_status= strtolower($value['reminder_current_status']);
            $status         = ($reminder_current_status=="done") ? 1 : 0 ;
            $allactivestatus= ($reminder_current_status!="cancele") ? 'allactivestatus' : '' ;
            $owner_name     = $value['owner_name']." ".$value['owner_surname'];
            $item=[
                'event_img'       => $base_url."/images/application/".$app_id."/features/migareference/".$value['rep_rem_icon_file'],
                'rep_rem_title'   => $value['rep_rem_title'],
                'event_date_time' => $value['event_date_time'],
                'owner_name'      => $owner_name,
                'owner_mobile'    => $value['owner_mobile'],
                'report_id'       => $value['migareference_report_id'],
                'user_id'         => $value['user_id'],
                'report_no'       => $value['report_no'],
                'reminder_id'     => $value['migarefrence_reminders_id'],
                'sponsor_id'      => $value['sponsor_id'],
                'admin_user_id'   => $value['admin_user_id'],
                'reminder_content'=> $value['reminder_content'],
                'postpone_days'   => $value['postpone_days'],
                'sponsor_name'    => '',
                'adminassiccation'=> 'adminreminder',
                'reminder_current_status' => $reminder_current_status,
                'allactivestatus' => $allactivestatus,
                'status'          => $status,
            ];
            if ($value['sponsor_id']!=0) {
              $sponsor_user        = $migareference->getSingleuser($app_id,$value['sponsor_id']);
              $agent_name=$sponsor_user[0]['firstname']." ".$sponsor_user[0]['lastname'];
              $item['sponsor_name']=$agent_name;
              $item['adminassiccation']='';
              $agent_collection[]=[
                          'agent_title'      => $agent_name,
                          'agent_title_copy' => $agent_name,
                          'sponsor_id'       => $value['sponsor_id']
                         ];
            }
            $filter_user=[
                          'owner_title'      => $owner_name,
                          'owner_title_copy' => $owner_name,
                          'job_id'           => $value['migareference_report_id']
                         ];
            if (count($agent_data) && $value['sponsor_id']==$customer_id) {
              $owner_collection[]  =$filter_user;
              $remindercollection[]=$item;
            } else{
              $owner_collection[]=$filter_user;
              $remindercollection[]=$item;
            }
          }
          $owner_collection =  array_map("unserialize", array_unique(array_map("serialize", $owner_collection)));
          sort($owner_collection);
          $agent_collection =  array_map("unserialize", array_unique(array_map("serialize", $agent_collection)));
          sort($agent_collection);
          $map_agent_collection[]=[
                              'agent_title'      =>  __("Only Admin Reminders"),
                              'agent_title_copy' =>  "adminreminder",
                              'sponsor_id'        => -1
                            ];
          $map_agent_collection[]=[
                              'agent_title'      =>  __("All Reminders"),
                              'agent_title_copy' =>  "",
                              'sponsor_id'        => 0
                            ];
            foreach ($agent_collection as $key => $value) {
              $map_agent_collection[]=$value;
            }
          $map_owner_collection[]=[
                              'owner_title'      =>  __("My Reminders"),
                              'owner_title_copy' =>  "",
                              'report_id'        => 0
                            ];
            foreach ($owner_collection as $key => $value) {
              $map_owner_collection[]=$value;
            }
            $payload = [
                "success" => true,
                "remindercollection"  => $remindercollection,
                "ownercollection"  => $map_owner_collection,
                "pre_settings"  => $pre_settings[0],
                "agentcollection"  => $map_agent_collection,
                "is_admin"  => $is_admin
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function loaddaybookAction(){
    try {          
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();
          $customer_id = $this->getRequest()->getParam('user_id');          
          $agent_data    = $migareference->is_agent($app_id, $customer_id);          
          //$receiver_id = 10000 for all amdins and $customer_id for particular agent
          $receiver_id   = (!count($agent_data)) ? 10000 : $customer_id ;
          $status        = 'pending';
          $daily_reminder_logs = $migareference->getDayBook($app_id,$receiver_id,$status);
          
          $day_book_collection = [];
          foreach ($daily_reminder_logs as $value) {
            $date = date('Y-m-d', strtotime($value['created_at'])); // extract just the date
        
            $day_book_collection[$date][] = [
                'migareference_reminder_daybook_id' => $value['migareference_reminder_daybook_id'],
                'receiver_id' => $value['receiver_id'],
                'data' => json_decode($value['data']),
                'status' => $value['status'],
                'type' => $value['type'],
                'description' => $value['description'],
                'title_info' => $value['title_info'],
                'created_at' => $value['created_at'],
            ];
          }
          $payload = [
              "success" => true,
              "raw"  => $daily_reminder_logs,                
              "day_book_collection"  => $day_book_collection,
              "receiver_id"  => $receiver_id,
          ];
        } catch (\Exception $e) {
          $payload = [
              'error' => true,
              'message' => $e->getMessage(),
          ];
        }
    $this->_sendJson($payload);
  }
  public function getreferrerremindersAction(){
        try {
        // Get parameters from the request
        $customer_id = $this->getRequest()->getParam('customer_id');          
        $app_id = $this->getApplication()->getId();
        $openai_config = (new Migareference_Model_OpenaiConfig())->findAll(['app_id'=> $app_id])->toArray();        
        $is_openai_enabled = (count($openai_config) && $openai_config[0]['is_api_enabled']==1) ? true : false ;
        // Initialize models and get base URL and feature icon path
        $migareference = new Migareference_Model_Migareference();          
        $base_url = (new Core_Model_Default())->getBaseUrl();
        $feature_icon_path = $base_url."/images/application/".$app_id."/features/migareference/";

        // Get reminders, agent data and settings
        // To optimize the all_reminders list we will load only that are not canceled and than load only last 30 canceled reminders
        $non_canceled_all_reminders = $migareference->getReferrerRemindersNonCanceled($app_id);
        $canceled_all_reminders = $migareference->getReferrerRemindersCanceled($app_id,$limit=30);
        $all_reminders = array_merge($non_canceled_all_reminders,$canceled_all_reminders);
        $agent_data = $migareference->is_agent($app_id, $customer_id);          
        $is_admin = (count($agent_data)) ? false : true ;
        $pre_settings = $migareference->preReportsettigns($app_id);

        // Initialize collections
        $referrer_collection = [];
        $remindercollection = [];
        $agent_collection = [];        
          // Process each reminder
          foreach ($all_reminders as $key => $value) {
            $referrer_name          = $value['invoice_surname']." ".$value['invoice_name'];
            $current_reminder_status= strtolower($value['current_reminder_status']);
            $status                 = ($current_reminder_status=="done") ? 1 : 0 ;
            $allactivestatus        = ($current_reminder_status!="cancele" && $current_reminder_status!='done' && $current_reminder_status!="postpone") ? 'allactivestatus' : '' ;          
            if (count($agent_data) && ($value['sponsor_one_id']==$customer_id || $value['sponsor_two_id']==$customer_id)) { //Build collection for Agent User
              $item=[
                'event_img'       => $feature_icon_path.$value['rep_rem_icon_file'],
                'rep_rem_title'   => $value['auto_rem_title'],
                'reminder_created_at'   => date('d-m-Y',strtotime($value['reminder_created_at'])),
                'event_date_time' => date('d-m-Y H:i',strtotime($value['automation_trigger_stamp'])),
                'referrer_name'   => $referrer_name,
                'referrer_mobile' => $value['mobile'],
                'reminder_id'     => $value['migareference_automation_log_id'],
                'allactivestatus' => $allactivestatus,
                'adminassiccation'=> 'adminreminder',
                'reminder_content'=> $value['reminder_content'],
                'phonebook_id'    => $value['migarefrence_phonebook_id'],
                'referrer_id'    => $value['referrer_id'],
                'postpone_days'   => $value['postpone_days'],
                'automation_trigger_report_id'       => $value['automation_trigger_report_id'],
                'report_no'       => $value['report_no'],
                'admin_user_id'      => $value['admin_user_id'],
                'status'          => $status,
                'sponsor_id'      => $value['sponsor_one_id'], //old Approach <3.5.70 :02-May-2024
                'sponsor_name'   => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'], //old Approach <3.5.70 :02-May-2024
                'sponsor_one_id'      => $value['sponsor_one_id'], 
                'sponsor_one_name'   => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'], 
                'sponsor_two_id'      => $value['sponsor_two_id'], 
                'sponsor_two_name'   => $value['sponsor_two_lastname']." ".$value['sponsor_two_firstname'], 
                'reminder_current_status' => $current_reminder_status,                
              ];
              if ($value['sponsor_one_id']!=0) {                                
                $item['adminassiccation']= '';
                $agent_collection[]=[
                  'agent_title'      => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'],
                  'agent_title_copy' => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'],
                  'sponsor_id'       => $value['sponsor_one_id']
                  ];
            }
            $referrer_collection[]=[
                'referrer_title'      => $referrer_name,
                'referrer_title_copy' => $referrer_name,
                'job_id'              => 0
                ];
              $remindercollection[]=$item;
            } else if(!count($agent_data)){ //Build coolection for Admin user
              $item=[
                  'event_img'       => $feature_icon_path.$value['rep_rem_icon_file'],
                  'rep_rem_title'   => $value['auto_rem_title'],
                  'reminder_created_at' => date('d-m-Y H:i',strtotime($value['reminder_created_at'])),
                  'event_date_time' => date('d-m-Y H:i',strtotime($value['automation_trigger_stamp'])),
                  'referrer_name'   => $referrer_name,
                  'referrer_mobile' => $value['mobile'],
                  'reminder_id'     => $value['migareference_automation_log_id'],
                  'referrer_id'     => $value['referrer_id'],
                  'allactivestatus' => $allactivestatus,
                  'adminassiccation'=> 'adminreminder',
                  'phonebook_id'    => $value['migarefrence_phonebook_id'],
                  'reminder_content'=> $value['reminder_content'],
                  'automation_trigger_report_id'       => $value['automation_trigger_report_id'],
                  'report_no'       => $value['report_no'],
                  'admin_user_id'   => $value['admin_user_id'],
                  'postpone_days'   => $value['postpone_days'],
                  'status'          => $status,
                  'sponsor_id'      => $value['sponsor_one_id'], //old Approach <3.5.70 :02-May-2024
                  'sponsor_name'   => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'], //old Approach <3.5.70 :02-May-2024
                  'sponsor_one_id'      => $value['sponsor_one_id'], 
                  'sponsor_one_name'   => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'], 
                  'sponsor_two_id'      => $value['sponsor_two_id'], 
                  'sponsor_two_name'   => $value['sponsor_two_lastname']." ".$value['sponsor_two_firstname'], 
                  'reminder_current_status' => $current_reminder_status,                
                ];
              if ($value['sponsor_one_id']!=0 && $pre_settings[0]['agent_can_manage_reminder_automation']==1) {                                
                  $item['adminassiccation']= '';
                  $agent_collection[]=[
                    'agent_title'      => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'],
                    'agent_title_copy' => $value['sponsor_one_lastname']." ".$value['sponsor_one_firstname'],
                    'sponsor_id'       => $value['sponsor_one_id']
                    ];
              }
              $referrer_collection[]=[
                'referrer_title'      => $referrer_name,
                'referrer_title_copy' => $referrer_name,
                'job_id'         => 0
              ];
              $remindercollection[]=$item;
          }
          }
          // Sort and Merge Referrer collection
          $map_referrer_collection[] = [
            'referrer_title'      => __("All Reminders"),
            'referrer_title_copy' => "",
            'report_id'           => 0,
          ];
          $referrer_collection = array_values($referrer_collection);
          $map_referrer_collection = array_merge($map_referrer_collection, $referrer_collection);
          // Sort and Merge Agent collection
          $map_agent_collection[] = [
            'agent_title'      => __("Only Admin Reminders"),
            'agent_title_copy' => "adminreminder",
            'sponsor_id'       => -1,
          ];
          
          if ($pre_settings[0]['agent_can_manage_reminder_automation'] == 1) {
              $map_agent_collection[] = [
                  'agent_title'      => __("All Reminders"),
                  'agent_title_copy' => "",
                  'sponsor_id'       => 0,
              ];
          }
          
          $agent_collection = array_values(array_map("unserialize", array_map("serialize", $agent_collection)));
          $map_agent_collection = array_merge($map_agent_collection, $agent_collection);
        
            $payload = [
                "success" => true,
                "remindercollection"  => $remindercollection,
                "referrercollection"  => $map_referrer_collection,
                "is_admin"            => $is_admin,                
                "is_call_script_enabled"=> $is_openai_enabled,                
                "agentcollection"    => $map_agent_collection,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getagentAction(){
        try {
          $user_id       = $this->getRequest()->getParam('user_id');          
          $app_id        = $this->getApplication()->getId();
          $migareference = new Migareference_Model_Migareference();          
          $df_opt        = explode("@",$user_id);          
          $agent_data    = $migareference->getSponsor($app_id,$df_opt[0]);          
          $ag_id="";
          if ($agent_data[0]['sponsor_id']!=0) {
            $ag_id=$agent_data[0]['sponsor_id']."@"."1";
          }
            $payload = [
                "success" => true,
                "agent_id"  => $ag_id,                
                "df_opt"  => $df_opt,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function usertypeAction(){
        try {
          $user_id       = $this->getRequest()->getParam('user_id');          
          $app_id        = $this->getApplication()->getId();

          $migareference = new Migareference_Model_Migareference();          

          $admin_data    = $migareference->is_admin($app_id,$user_id);
          $agent_data    = $migareference->is_agent($app_id,$user_id);
          $admin_agent   = $migareference->agmingAgentGroup($app_id,$user_id);

          $is_admin      = (COUNT($admin_data)) ? true : false ;
          $is_agent      = (COUNT($agent_data)) ? true : false ;
          $is_admin_agent_group = (COUNT($admin_agent)) ? true : false ;

            $payload = [
                "success" => true,                            
                "is_admin"=>$is_admin,
                "is_agent"=>$is_agent,
                "is_admin_agent_group"=>$is_admin_agent_group,
                "admin_agent"=>$admin_agent,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  private function _getCustomerId($throw = true)
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        $customerId = $session->getCustomerId();
        if ($throw && empty($customerId)) {
            throw new Exception(p__('xdelivery', 'Customer login required!'));
        }
        return $customerId;
    }
  public function loadagentgroupAction(){
        try {          
          $app_id        = $this->getApplication()->getId();
          // $user_id       = $this->getRequest()->getParam('user_id');
          $customerId = $this->_getCustomerId(false);
          $migareference = new Migareference_Model_Migareference();                    
          $agent_data    = $migareference->get_all_agents($app_id);
          $pre_report    = $migareference->preReportsettigns($app_id);
          $referral_usrs = $migareference->get_referral_users($app_id);
          $agent_user    = $migareference->getSingleuser($app_id,$customerId);  
          $referrer_collection=[];
          $agent_fullphonebook_collection[] = array('id'   => 0,'name' => __("Show All"));
          $agent_fullphonebook_collection[] = array('id'   => (int) $agent_user[0]['customer_id'],'name' => __("Only My Referrer"));
          $agent_group_collection[] = array('id'   => -2,'name' => __("Show All"));
          $agent_group_collection[] = array('id'   => -1,'name' => __("Admin Agents Group"));
          $is_allowed_full_phonebook=0;
          foreach ($agent_data as $key => $value) {
                  $agent_group_collection[] = array(
                    'id'=>$value['customer_id'],
                    'name'=>$value['lastname']." ".$value['firstname']
                  );
                  if ($value['customer_id']==$customerId) {
                    $is_allowed_full_phonebook=(int) $value['full_phonebook'];
                  }
          }
          // Last Default Item
          if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
            $agent_group_collection[] = array('id'   => 0,'name' => __("I don’t know"));
          }
          foreach ($referral_usrs as $key => $value) {
                  $referrer_collection[] = array(
                    'id'=>$value['user_id'],
                    'name'=>$value['invoice_surname']." ".$value['invoice_name']
                  );
          }
          $payload = [
                "success" => true,                
                "agent_group_collection" => $agent_group_collection,                
                "agent_fullphonebook_collection" => $agent_fullphonebook_collection,                
                "referrer_collection" => $referrer_collection,                
                "customerId" => $customerId,                
                "is_allowed_full_phonebook" => $is_allowed_full_phonebook,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getreportremindertypeAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $remindertypes = $migareference->getReportReminderType($app_id,1);//1 is Report 2 is automation

          $payload = [
              "success" => true,
              "remindertypecollection"  => $remindertypes
          ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function editreminderAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $public_key    = $this->getRequest()->getParam('public_key');
          $reminder_item = $migareference->editreminder($public_key);
            $payload = [
                "success" => true,
                "reminder"  => $reminder_item[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function getredeemprizelistAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $prizes        = $migareference->getredeemprizelist($app_id,$user_id);
          $collection    = [];
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
            foreach ($prizes as $prize_item) {
                $prize_item['image_path']=$base_url."/images/application/".$app_id."/features/migareference/".$prize_item['prize_icon'];
                $collection[] = $prize_item;
            }
            $payload = [
                "success" => true,
                "prizes"  => $collection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function loadprizeitemAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $prize_id      = $this->getRequest()->getParam('prize_id');
          $prizes        = $migareference->getSinglePrize($prize_id);
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $prizes[0]['image_path']=$base_url."/images/application/".$app_id."/features/migareference/".$prizes[0]['prize_icon'];
          // START:Patch for before # 3.3.22
          if (!isset($prizes[0]['prize_link1'])) {
            $prizes[0]['prize_link1']='';
          }
          if (!isset($prizes[0]['prize_link2'])) {
            $prizes[0]['prize_link2']='';
          }
          // END:Patch for before # 3.3.22
            $payload = [
                "success" => true,
                "prize_item"  => $prizes[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function prereportsettignsAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $pre_settings  = $migareference->preReportsettigns($app_id);
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $pre_settings[0]['base_url']=$base_url;
          $pre_settings[0]['invite_message']=trim($pre_settings[0]['invite_message']);
            $payload = [
                "success" => true,
                "pre_settings"  => $pre_settings[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function reportsettingsAction(){
        try {
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $type          = $this->getRequest()->getParam('report_type');//1 for Reffrel Report:2 for Agent or Admin Report
          $version       = ($this->getRequest()->getParam('version')!==null) ? $this->getRequest()->getParam('version') : 0 ;
          $app_id        = $this->getApplication()->getId();
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $siberian_usrs = [];
          $pre_settings  = $migareference->preReportsettigns($app_id);
          $app_content   = $migareference->get_app_content($app_id);
          $field_data    = $migareference->getreportfield($app_id);
          
          $admin_data    = $migareference->is_admin($app_id,$user_id);
          $agent_data    = $migareference->is_agent($app_id,$user_id);
          $invo_settings = $migareference->getpropertysettings($app_id,$user_id);
          $get_agents    = $migareference->get_customer_agents($app_id);
          $user_data     = $migareference->getAgentdata($user_id);
          $gdpr_settings = $migareference->get_gdpr_settings($app_id);
          $user_account_settings    = $migareference->useraccountSettings($app_id);   

          $user_account_settings    = json_decode($user_account_settings[0]['settings']);

          $pre_settings[0]['enable_birthdate']    = ($user_account_settings->extra_birthdate) ? 1 : 2 ;
          $pre_settings[0]['mandatory_birthdate'] = ($user_account_settings->extra_birthdate_required) ? 1 : 2 ;

          $default_model[0]="";
          $default_model[1]="";
          $is_agent      = (count($agent_data)>0) ? true : false ;
          $label_classes="item item-input item-stacked-label";
          $label_padding="padding:12px;";
          if ($version>2.14) {
            $label_classes="item item-input item-custom item-stacked-label";
            $label_padding="padding:16px;";
            $app_content[0]['page_text_color']=";width:100%;max-width:100%;padding:1px;";
          }          
          $pre_settings[0]['base_url'] = $base_url;
          $pre_settings[0]['gdpr_icon']=$base_url."/app/local/modules/Migareference/resources/appicons/gdpr.png";
          $pre_settings[0]['invite_message'] = trim($pre_settings[0]['invite_message']);
          $ref_user='<input  name="report_type" type="hidden" ng-model="migareferenceformchange.report_type">';
          $display = ($type==2 && (count($admin_data) || count($agent_data))) ? "" : "none" ;          
          if ($version>5.71 ) {  
            if ($type==2) { 
            $siberian_usrs = $migareference->get_siberianuser($app_id);        
            $ref_user .='<div>';
            $ref_user .= "<button type='button' style='width:18%;position:absolute;margin-left:80%;margin-top:40px;z-index:3;background-color:{$app_content[0]['add_ref_btn_bg_color']};color:{$app_content[0]['add_ref_btn_text_color']}' class='button-add' ng-click='addNewReferrer()'>+</button>";
            $ref_user .= "<label for='list-ref' style='" . $label_padding . "display:" . $display . "' class='" . $label_classes . "' >";
            $ref_user .= "<span class='input-label' style='color:" . $app_content[0]['page_text_color'] . " !important;' >";
            $ref_user .= __('Referrer User') . "*";
            $ref_user .= "</span><br>";          
            $ref_user .= '<button id="list-ref" type="button" ng-click="showusrlist()" style="border-radius:3px;width:80%;float:left;border:1px solid gray;background:none;height:38px;text-align:start;">{{selectedReferrerName}}<i class="icon ion-chevron-down" style="color: black; font-size: 10px;margin-right:5px;float:right;"></i> </button>';            
            $ref_user .= '</label>'; 
            $ref_user .='</div>';         
            
            $ref_user .= '<div ng-show="user_list" class="user-list" style="padding:16px;">';
            $ref_user .= "<input type='text' placeholder='".__("Search")."...."."' ng-model='searchTerm' style='width: 100%; padding: 8px; border-radius: 3px; font-size: medium; border: 1px solid gray;'>";
            
            foreach ($siberian_usrs as $key => $value) {
                $is_referral = ($value['migareference_invoice_settings_id'] == NULL) ? "*" : "";
                $referral_sign = ($value['migareference_invoice_settings_id'] == NULL) ? 1 : 2;
                if ($user_id != $value["customer_id"]) {
                    if ($is_agent) {
                        if ($value['agent_id'] == $user_id) {
                            $ref_user .= '<div ng-if="(\'' . $value["lastname"] . ' ' . $value['firstname'] . '\'.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1)" ng-click="setReferrerModel(\'' . $value["customer_id"] . "@" . $referral_sign . '\', \'' . $value["lastname"] . " " . $value['firstname'] . '\')" value="' . $value["customer_id"] . "@" . $referral_sign . '">' . $is_referral . " " . $value["lastname"] . " " . $value['firstname'] . '</div>';
                        }
                    } else {
                        $ref_user .= '<div ng-if="(\'' . $value["lastname"] . ' ' . $value['firstname'] . '\'.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1)" ng-click="setReferrerModel(\'' . $value["customer_id"] . "@" . $referral_sign . '\', \'' . $value["lastname"] . " " . $value['firstname'] . '\')" value="' . $value["customer_id"] . "@" . $referral_sign . '">' . $is_referral . " " . $value["lastname"] . " " . $value['firstname'] . '</div>';
                    }
                }
            }
            $ref_user .= '</div>';  
          }    
          }else{
            $siberian_usrs = $migareference->get_siberianuser($app_id);
            $ref_user.="<label style='".$label_padding."display:".$display."' class='".$label_classes."' >";
            $ref_user.="<span class='input-label' style='color:".$app_content[0]['page_text_color']." !important;' >";
            $ref_user.= __('Referrer User')."*";
            $ref_user.="</span><br>";
            $ref_user.='<form ng-if="is_visible_submit_report" name="reminderform" ng-submit="savePropertyreport()" ><select style="width:100%;padding:8px;border-radius:3px;font-size:medium" ng-change="addrefferaluser()"   ng-model="migareferenceformchange.refreral_user_id">';
                $ref_user.='<option style="font-weight:bold;" value=0>'.__("Add New User").'</option>';
                foreach ($siberian_usrs as $key => $value) {
                  $is_referral = ($value['migareference_invoice_settings_id']==NULL) ? "*" : "" ;
                  $referral_sign = ($value['migareference_invoice_settings_id']==NULL) ? 1 : 2 ;
                  if ($user_id!=$value["customer_id"]) {
                    if ($is_agent) {
                      if ($value['agent_id']==$user_id) {
                        $ref_user.='<option value='.$value["customer_id"]."@".$referral_sign.'>'.$is_referral." ".$value["lastname"]." ".$value['firstname'].'</option>';
                      }
                    }else {
                      $ref_user.='<option value='.$value["customer_id"]."@".$referral_sign.'>'.$is_referral." ".$value["lastname"]." ".$value['firstname'].'</option>';
                    }
                  }
                }
            $ref_user.='</select></label></form>';
          }
                
          
          $agent_user_data='<input  name="report_type" type="hidden" ng-model="migareferenceformchange.report_type">';
          $display = ($type==2 && (count($admin_data) )) ? "" : "none" ;
          $agent_list_label="<label style='".$label_padding."display:".$display."' class='".$label_classes."' >";
          $agent_list_label.="<span class='input-label' style='color:".$app_content[0]['page_text_color']." !important;' >";
          $agent_list_label.= __('Agent User')."*";
          $agent_list_label.="</span><br>";
          $agent_user_data.=$agent_list_label.'<form ng-if="is_visible_submit_report" name="reminderform" ng-submit="" >';

          $agent_list='<select style="width:100%;padding:8px;border-radius:3px;font-size:medium"   ng-model="migareferenceformchange.agent_user_id">';
          if ($pre_settings[0]['enable_mandatory_agent_selection']==2) {
            $agent_list.='<option value='."0@1".'>'.__("I don’t know").'</option>';
          }
          foreach ($get_agents as $key => $value) {
              $agent_list.='<option value='.$value["customer_id"]."@1".'>'.$value["lastname"]." ".$value['firstname'].'</option>';
          }
          $agent_list.='</select></label>';

          $agent_user_data.=$agent_list.'</form>';
          if ($app_content[0]['add_property_cover_file']!='') {
            // $pre_settings[0]['is_visible_invite_prospectus']=1;
            $cover_path=$base_url."/images/application/".$app_id."/features/migareference/".$app_content[0]['add_property_cover_file'];
            $cover_path="<img style='width:100%' alt='' src='".$cover_path."'>"; 
            if ($version<2) {  //To manage new version of desing for add report page in card              
              $pre_settings[0]['external_report_note']=$cover_path."<br>".$pre_settings[0]['external_report_note'];
            }else {              
              $pre_settings[0]['external_report_note']=$pre_settings[0]['external_report_note'];
            }           
          }
          if ($type==2 && $version!=0) {
            $pre_settings[0]['external_report_note']=$pre_settings[0]['external_report_note']."<br>".$agent_user_data;
          }elseif ($type==2 && $version==0) {
            $pre_settings[0]['is_visible_invite_prospectus']=2;
          }                    
          $static_fields[1]['name']="property_type";
          $static_fields[2]['name']="sales_expectations";
          $static_fields[3]['name']="address";
          $static_fields[4]['name']="owner_name";
          $static_fields[5]['name']="owner_surname";
          $static_fields[6]['name']="owner_mobile";
          $static_fields[7]['name']="note";
          $field=$ref_user.$agent_list_label.$agent_list;
          foreach ($field_data as $key => $value) {
            $disable="";
            $display=($value['is_visible']==1) ? "" : "none" ;
            $required = ($value['is_required']==1) ? "*" : "" ;
            if ($value['type']==1) {
                  $name=$static_fields[$value['field_type_count']]['name'];
                  $warrning = ($name=="owner_name" || $name=="owner_surname" || $name=="owner_mobile") ? "ng-click='showWarrning()'" : "" ;
                  $field.="<label style='".$label_padding."display:".$display."' class='".$label_classes."' ".$warrning." >";
                  $field.="<span class='input-label' style='color:".$app_content[0]['page_text_color']." !important;' >";
                  $field.=__($value['label'])." ".$required;
                  $field.="</span><br>";
                  $field.=$this->manageinputypeAction($app_id,$value['field_type'],$name,$value['field_option'],0,$disable,$value['options_type'],$value['default_option_value']);
            }else {
              $name="extra_".$value['field_type_count'];
              $field.="<label style='".$label_padding."display:".$display."' class='".$label_classes."'>";
              $field.="<span class='input-label' style='color:".$app_content[0]['page_text_color']." !important;' >";
              $field.=__($value['label'])." ".$required;
              $field.="</span><br>";
              $field.=$this->manageinputypeAction($app_id,$value['field_type'],$name,$value['field_option'],$value['field_type_count'],$disable,$value['options_type'],$value['default_option_value']);
              // Management of the default option values Countries and Province
              // Countries
              if ($value['field_type']==3 && $value['options_type']==1) {
                $df_opt=explode("@",$value['default_option_value']);
                $default_model[$value['field_type_count']]=$df_opt[0]."@".$value['options_type'];
              }
              // Province
              $provinceCollection = [];
              if ($value['field_type']==3 && $value['options_type']==2) {
                $df_opt             = explode("@",$value['default_option_value']);
                $dataGeoConPro      = $migareference->getGeoCountryProvicnes($app_id,$df_opt[0]);
                $provinceCollection = [];
                foreach ($dataGeoConPro as $keyyy => $valueee) {
                  $provinceCollection[$valueee['migareference_geo_provinces_id']]=[
                    "migareference_geo_provinces_id"=>$valueee['migareference_geo_provinces_id'],
                    "province"=>$valueee['province']
                  ];
                }
                $default_model[$value['field_type_count']]=$df_opt[1]."@".$value['options_type'];
              }
            }
          }
          // Replace refrrel_name tag for invite prspectus
          $pre_settings[0]['invite_message']= str_replace("@@referral_name@@",$user_data[0]['firstname']." ".$user_data[0]['lastname'],$gdpr_settings[0]['consent_info_popup_body']);
          $geoCountries              = $migareference->getGeoCountries($app_id);
          $countryCollection=[];
          foreach ($geoCountries as $key => $value) {
            $countryCollection[$value['migareference_geo_countries_id']]=[
              "id"=>$value['migareference_geo_countries_id'],
              "name"=>$value['country']
            ];
          }
          //11/24/2022

          $pre_settings[0]['report_page_popup_file'] =  $base_url."/images/application/".$app_id."/features/migareference/".$gdpr_settings[0]['report_page_popup_file'] ;
          $gdpr_settings[0]['invite_report_popup_file'] =  $base_url."/images/application/".$app_id."/features/migareference/".$gdpr_settings[0]['invite_report_popup_file'] ;
          $gdpr_settings[0]['invite_message'] = str_replace('@@referrer_name@@', '@@agent_name@@', $gdpr_settings[0]['invite_message']);
          $pre_settings[0]['consent_info_popup_title'] =  $gdpr_settings[0]['consent_info_popup_title'] ;
          $pre_settings[0]['consent_info_popup_body'] = $gdpr_settings[0]['consent_info_popup_body'] ;                    
          $pre_settings[0]['consent_collection'] = $gdpr_settings[0]['consent_info_active'] ;                              
          $gdpr_settings[0]['prospect_socail_share_copy_note'] = __("Copy and paste the text in your whatsapp, sms, email... and send it to your prospect") ;                              
          // 10/28/2022
          // Remove invite template for report type 2 or admin or agent reports
          if ($type==2) {
            $pre_settings[0]['is_visible_invite_prospectus']=2;//Force default settings to false
            $pre_settings[0]['is_visible_platform_report']=1;//Enable admin page always
          }
          // $pre_settings[0]['enable_birthdate']=0;//Enable admin page always

            $payload = [
                "success" => true,
                "pre_settings"  => $pre_settings[0],
                "gdpr_settings" => $gdpr_settings[0],
                "invo_settings" => $invo_settings[0],
                "form_builder"  => $field,
                "version"       => $version,
                "geoCountries"  => $countryCollection,
                "proviceitems"  => $provinceCollection,
                "default"       => $df_opt,
                "default_model" => $default_model,
                "is_agent" => $is_agent,
                "siberian_usrs" => $siberian_usrs,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$disable="",$option_type=0,$option_default="")
  {
    $migareference = new Migareference_Model_Migareference();
    $extra_input_template="";
    if ($type==1) {
      $extra_input_template.='<input '.$disable.' style="padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);;"  name="'.$ng_model.'" id="'.$ng_model.'" type="text" ng-model="migareferenceformchange.'.$ng_model.'"></label>';
    } else if($type==2) {
      $extra_input_template.='<input '.$disable.' style="padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);;"  name="'.$ng_model.'" id="'.$ng_model.'" type="number" ng-model="migareferenceformchange.'.$ng_model.'"  ></label>';
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
        $temp_options=explode('@',$options);
        $extra_input_template.="<select ".$disable." style='width:100%;padding:8px;border-radius:3px;font-size:medium;border: 1px solid rgb(169, 169, 169);'   data-ng-model='migareferenceformchange.".$ng_model."'>";
        foreach ($temp_options as $key => $value) {
          $option_value++;
          $extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
        }
        $extra_input_template.="</select></label>";
          break;
        case 1://Country List
          $geoCountries              = $migareference->getGeoCountries($app_id);
          $df_opt=explode("@",$option_default);
          $extra_input_template.="<br><select ng-change='loadprovinces(migareferenceformchange.".$ng_model.")' ng-options='item.name for item in countryitems track by item.id' ".$disable." style='width:100%;padding:8px;border-radius:3px;font-size:medium;border: 1px solid rgb(169, 169, 169);'   data-ng-model='migareferenceformchange.".$ng_model."'>";
          $extra_input_template.="</select></label>";
          break;
        case 2:
          $extra_input_template.="<br><select  ng-options='item.province for item in proviceitems track by item.migareference_geo_provinces_id' ".$disable." style='width:100%;padding:8px;border-radius:3px;font-size:medium;border: 1px solid rgb(169, 169, 169);'   data-ng-model='migareferenceformchange.".$ng_model."'>";
          $extra_input_template.="</select></label>";
          break;
        default:
          break;
      }
    }else if($type==4) {
      $latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
      $extra_input_template.="</span>";
      $extra_input_template.="<input ".$disable." style='padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);' id='searchPlaceFrom'";
      $extra_input_template.="ng-focus=disableTap("."searchPlaceFrom".") type='text'";
      $extra_input_template.="placeholder=".__("Location");
      $extra_input_template.=" ng-model='migareferenceformchange.".$ng_model."' sb-google-autocomplete location='origin' on-address-change='changeItinerary(".$address_counter.")'";
      $extra_input_template.="/>";
      $extra_input_template.="<input type='hidden' ng-model='migareferenceformchange.longitude".$latlong_name."' value=''>";
      $extra_input_template.="<input type='hidden' ng-model='migareferenceformchange.latitude".$latlong_name."' value=''> </label>";
    }else if($type==5) {
      if ($ng_model=="note") {        
        $extra_input_template.='<textarea '.$disable.' style="padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);;"  name="'.$ng_model.'" id="'."extra_10".'" ng-model="migareferenceformchange.'."extra_10".'"  rows="3" cols="80" ></textarea></label>';
      }else{
        $extra_input_template.='<textarea '.$disable.' style="padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);;"  name="'.$ng_model.'" id="'.$ng_model.'" ng-model="migareferenceformchange.'.$ng_model.'"  rows="3" cols="80" ></textarea></label>';
      }
    }else if($type==6) {
      $extra_input_template.='<div class="row" style="padding:0px">
        <div class="col" style="padding:0px">
              <select id="day" name="day"  ng-model="migareferenceformchange.birth_day" class="input-text" style="height:41px;border:1px solid gray"  >
                  <option ng:repeat="day in daysdateday" value="{{ day.id }}" >{{ day.name }}</option>
              </select>
        </div>
        <div class="col" style="padding-top:0px">
              <select id="month" name="month"  ng-model="migareferenceformchange.birth_month" class="input-text" style="height:41px;border:1px solid gray"  >
                  <option ng:repeat="month in months" value="{{ month.id }}" >{{ month.name }}</option>
              </select>
        </div>
        <div class="col" style="padding:0px;padding-right: 15px;">
              <select id="year" name="year"  ng-model="migareferenceformchange.birth_year" class="input-text" style="height:41px;border:1px solid gray"  >
                  <option ng:repeat="year in years" value="{{ year.id }}" >{{ year.name }}</option>
              </select>
        </div>
      </div>';
    }else if ($type==7) {
      $extra_input_template.='<input '.$disable.' style="padding:20px;font-size:medium;border-radius:3px;background:white;border: 1px solid rgb(169, 169, 169);;"  name="'.$ng_model.'" id="'.$ng_model.'" type="text" ng-model="migareferenceformchange.'.$ng_model.'"></label>';
    }
    return $extra_input_template;
  }
  public function loadredeemprizeitemAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $prize_id      = $this->getRequest()->getParam('prize_id');
          $user_id       = $this->getRequest()->getParam('user_id');
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $prizes        = $migareference->getSingleRedeemPrize($app_id,$user_id,$prize_id);
          $prizes[0]['image_path']=$base_url."/images/application/".$app_id."/features/migareference/".$prizes[0]['prize_icon'];
            if ($prizes[0]['redeemed_status']==0) {
              $prizes[0]['prize_manage_status'] = __('Pending'.$prizes['redeemed_status']);
            } elseif($prizes[0]['redeemed_status']==1) {
              $prizes[0]['prize_manage_status'] = __('Delivered');
            }elseif($prizes[0]['redeemed_status']==2) {
              $prizes[0]['prize_manage_status'] = __('Refused');
            }
            $payload = [
                "success" => true,
                "prize_item"  => $prizes[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function loadledgerAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $ledgerdata    = $migareference->get_leadger($app_id,$user_id);
          $balance       = 0;
          foreach ($ledgerdata as $ledger_item) {
              if ($ledger_item['entry_type']=='C') {
                $balance=$balance+$ledger_item['amount'];
                $credits="+".$ledger_item['amount'];
              }else {
                $balance=$balance-$ledger_item['amount'];
                $credits="-".$ledger_item['amount'];
              }
              $collection[] = [
                "entry_at"=>date('d-m-Y',strtotime($ledger_item['created_at'])),
                "description"=>$ledger_item['trsansection_description'],
                "credits"=>$credits,
                "balance"=>$balance
              ];
          }
            $payload = [
                "success" => true,
                "ledgerdata"  => $collection
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function savesharelogsAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $log_item=[
            'app_id'=>$app_id,
            'user_id'=>$user_id,
            'log_type'=>'share'            
          ];
          $migareference->saveSharelogs($log_item);        
            $payload = [
                "success" => true,
                "log_id"  => $log_item
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function totalcreditsAction(){
        try {
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $migareference = new Migareference_Model_Migareference();
          $user_id       = $this->getRequest()->getParam('user_id');
          $credit_balance= $migareference->get_credit_balance($app_id,$user_id);
            $payload = [
                "success" => true,
                "credit_balance"  => $credit_balance[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
    public function randomToken($length=0) {
    $alphabet = "abcdefghijklmn45o54pqrst6546uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUVWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
public function getexternallinksAction()
{                        
          $application   = $this->getApplication();
          $app_id        = $application->getId();
          $report_id     = $this->getRequest()->getParam('report_id');
          $migareference = new Migareference_Model_Migareference();
          $externalllins = new Migareference_Model_Externalreportlink();
          $utilities     = new Migareference_Model_Utilities();
          $default       = new Core_Model_Default();
          $base_url      = $default->getBaseUrl();
          $admins        = $externalllins->urladmins($app_id,$report_id); 
          $bitly_crede   = $migareference->getBitlycredentails($app_id);
          // Create Admin Links if not exist for any admin
          foreach ($admins as $key => $value) {
            $token=$this->randomToken(35);
            $long_url=$base_url."/migareference/crmreports?"."app_id=".$app_id."&token=".$token;
            $short_link = $utilities->shortLink($long_url);
            // if their is any error urrlshortAction will retrun long_url
            // instead to save long url in database we will save empty short url so later could be replaced
            if ($short_link==$long_url) {
              $short_link="";
            }
            $data['app_id']=$app_id;
            $data['user_id']=$value['customer_id'];
            $data['report_id']=$report_id;
            $data['long_url']=$long_url;
            $data['short_url']=$short_link;
            $data['token']=$token;
            $data['created_at']  = date('Y-m-d H:i:s');
            $externalllins->savedata($data);              
          }
          // Build collection fro admin link
          $report_links  = $externalllins->links($report_id);                               
           $link_collection[]=[                        
                      "name"=>__("Admin"),
                      "customer_id"=>0,                        
                      "share_url"=>"",                        
                      "url_id"=>0,
                      "report_id"=>0,
                      "style"=>"bold"
                    ]; 
          foreach ($report_links as $key => $value) {                 
                $report_url = (!empty($value['short_url'])) ? $value['short_url'] : $value['long_url'] ;              
                if (empty($value['short_url']) || 
                    (strpos($value['short_url'], 'bit.ly') === false && strpos($value['short_url'], 'mssl') === false)) {
                      $report_url = $utilities->shortLink($report_url);                      
                      $update['migareference_report_urls_id']=$value['migareference_report_urls_id'];      
                      $update['short_url']=$report_url;      
                      $externalllins->setData($update)->save(); 
                }
              $link_collection[]=[                        
                      "name"=>$value['firstname']." ".$value['lastname'],
                      "customer_id"=>$value['customer_id'],                        
                      "share_url"=>$report_url,
                      "url_id"=>$value['migareference_report_urls_id'],
                      "report_id"=>$report_id,
                      "style"=>""
                    ];              
          }
          $report_links  = $externalllins->links($report_id);                   
          $link_collection[]=[                        
                      "name"=>_("Agent"),
                      "customer_id"=>0,                        
                      "share_url"=>"",                        
                      "url_id"=>0,
                      "report_id"=>0,
                      "style"=>"bold"
                    ];
          foreach ($report_links as $key => $value) {  
            if ($value['is_agent']) {
              $link_collection[]=[
                      "name"=>$value['firstname']." ".$value['lastname'],
                      "customer_id"=>$value['customer_id'],                        
                      "share_url"=>($value['short_url']=="") ? $value['long_url'] : $value['short_url'],                        
                      "url_id"=>$value['migareference_report_urls_id'],
                      "report_id"=>$report_id,
                      "style"=>""
                    ];
            }                                        
          }
            $payload = [
                "user_list" => $link_collection,                  
                "report_links" => $report_links,                  
                "admins" => $admins,                  
            ];          
    $this->_sendJson($payload);
}
  public function getnotificationsAction(){
    $user_id          = $this->getRequest()->getParam('user_id');
    $migareference    = new Migareference_Model_Migareference();
    $application      = $this->getApplication();
    $app_id           = $this->getApplication()->getId();
    $youtube_key      = $application->getYoutubeKey();
    $last_visit       = $migareference->getLastvisit($app_id,$user_id);
    $admin_data       = $migareference->is_admin($app_id,$user_id);
    $agent_data       = $migareference->is_agent($app_id,$user_id);
    $pre_settings     = $migareference->preReportsettigns($app_id);
    $optin_setting    = (new Migareference_Model_Optinsetting())->find([
      'app_id' => $app_id,
    ]);
    $invoice_settings = $migareference->getpropertysettings($app_id,$user_id);
    $bitly_crede      = $migareference->getBitlycredentails($app_id);
    $total_earn       = $migareference->get_earnings($app_id,$user_id);
    $how_to_result    = $migareference->gethowto($app_id);
    $credit_balance   = $migareference->get_credit_balance($app_id,$user_id);
        $is_howto_data_missing=0;
        $is_apikey_missing=0;
        if (count($how_to_result)==0) {
          $is_howto_data_missing=1;
        }else {
          if (!empty($how_to_result[0]['video_link']) && $youtube_key==null) {
            $is_apikey_missing=1;
          }
        }
    if ($invoice_settings[0]['leave_status']==2) {
      $gcmdata=$migareference->checkGcm($invoice_settings[0]['user_id'],$invoice_settings[0]['app_id']);
      if (count($gcmdata)) {
        $data_token['token']=$gcmdata[0]['device_token'];
        $data_token['leave_status']=1;
        $data_token['leave_date']="";
      }else {
        $apnsdata=$migareference->checkApns($invoice_settings[0]['user_id'],$invoice_settings[0]['app_id']);
        $data_token['token']=$apnsdata[0]['registration_id'];
        $data_token['leave_status']=1;
        $data_token['leave_date']="";
      }
      $data_token['app_id']=$invoice_settings[0]['app_id'];
      $migareference->updatePropertysettings($data_token,$invoice_settings[0]['migareference_invoice_settings_id']);
    }
    $counter        = 0;
    $is_admin       = 0;
    $is_agent       = 0;
    $top_content    = "";

    $is_presettings = (count($pre_settings)) ? 1 : 0 ;
    if ($pre_settings[0]['reward_type']==1) {
       $earning         = ($total_earn[0]['total_earn']>0) ? $total_earn[0]['total_earn'] : 0 ;
       $fmt             = numfmt_create( $application->getLocale(), NumberFormatter::CURRENCY );
       $fmt_earning     = numfmt_format_currency($fmt, $earning, $application->getCurrency());
       $top_content     = __("Your Earnings:")." ".$earning.$pre_settings[0]['commission_lable'];
       $commission_title= __('Future Commission: Not Yet');
      if ($pre_settings[0]['commission_type']==1) {
        $commission_title= __('Your Commission is:')." ".$pre_settings[0]['percent_commission_amount']." %";
      }
      if ($pre_settings[0]['commission_type']==2) {
        $fmt           = numfmt_create( $application->getLocale(), NumberFormatter::CURRENCY );
        $fmt_commisson = numfmt_format_currency($fmt, $pre_settings[0]['fix_commission_amount'], $application->getCurrency());
        $commission_title    = __("Your Commission is").": ".$pre_settings[0]['fix_commission_amount'].$pre_settings[0]['commission_lable'];
      }
      if ($pre_settings[0]['commission_type']==3) {
        $commission_title= __('Your Commission for each Report')."<br>".$pre_settings[0]['price_range_text_from']." - ".$pre_settings[0]['price_range_text_to'];
      }
    }elseif ($pre_settings[0]['reward_type']==2) {
      $earning        = ($credit_balance[0]['credits']>0) ? $credit_balance[0]['credits'] : 0 ;
      $top_content    = __('Your Credits are:')." ".$earning;
      $commission_title= __('Future Credits: Not Yet');
      if ($pre_settings[0]['commission_type']==1) {
        $commission_title= __('Your Credits for each Report').": ".$pre_settings[0]['percent_commission_credits']." %";
      }
      if ($pre_settings[0]['commission_type']==2) {
        $commission_title= __('Your Credits for each Report').": ".$pre_settings[0]['fix_commission_credits'];
      }
      if ($pre_settings[0]['commission_type']==3) {
        $commission_title= __('Your Commission for each Report')."<br> ".$pre_settings[0]['price_range_text_from']." - ".$pre_settings[0]['price_range_text_to'];
      }
    }
    // What if user come at first time?
    if (!empty($last_visit)) {
      $lastvisit_date_time  = $last_visit[0]['created_at'];
      $notification_counter = $migareference->countNotifications($app_id,$lastvisit_date_time,$user_id);
      $counter              = $notification_counter[0]['total'];
    }
    if (count($admin_data)) {
      $is_admin=1;
      $top_content=__('WELCOME ADMIN');
    }
    if (count($agent_data)) {
      $is_agent=1;
      $is_admin=1;
    }
    // Work on
    if (count($pre_settings) && count($invoice_settings)) {
      if ($pre_settings[0]['payable_limit']>$earning && $invoice_settings[0]['vat_id']=="") {
        $is_need_vat_id=1;
      }
    }
    $enrolling_page_url   = '';
    $enroll_sharing_msg   = '';
    if ($optin_setting->getId()) {
      $enrolling_page_url = trim((string) $optin_setting->getEnrollingPageUrl());
      $enroll_sharing_msg = trim((string) $optin_setting->getEnrollSharingMessage());
      if ($enrolling_page_url && $enroll_sharing_msg) {
        $enroll_sharing_msg = str_replace(
          ['@@app_name@@', '@@enroll_url@@'],
          [$application->getName(), $enrolling_page_url],
          $enroll_sharing_msg
        );
      }
    }
    $payload  = [
        'success'           => true,
        'acurruncy'         => $curruncy,
        'total_notification'=> $counter,
        'is_admin'          => $is_admin,
        'is_agent'          => $is_agent,
        'is_presettings'    => $is_presettings,
        'presettings_warning'=> __('Admin must setup Terms and Privacy.'),
        'top_content'       => $top_content,
        'commission_title'  => $commission_title,
        'on_notification'   => ($counter>0) ? true : false,
        'pre_settings'      => $pre_settings,
        'invoice_settings'  => $invoice_settings,
        'reward_type'       => $pre_settings[0]['reward_type'],
        'agent_can_see'     => $pre_settings[0]['agent_can_see'],
        'agent_can_manage'  => $pre_settings[0]['agent_can_manage'],
        'optin_settings'    => [
          'enrolling_page_url'    => $enrolling_page_url,
          'enroll_sharing_message'=> $enroll_sharing_msg,
        ],
        'enroll_settings_warning' => __('Enroll URL settings are missing. Please complete the settings to share the message.'),
        'is_howto_data_missing'=> $is_howto_data_missing,
        'is_apikey_missing'    => $is_apikey_missing,
        'is_howto_data_missing_err'=> __('Could not find data.'),
        'is_apikey_missing_err'=> __('Could not find YouTube API Key.'),
        'agent_permission_err' => __('You are not allowed to see any report.')
    ];
    $this->_sendJson($payload);
  }
  public function transferreferrerAction() {
    try{
      if ($customer_id   = $this->getRequest()->getParam('user_id')) {
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $this->getApplication()->getId();
          $contact_user  = $migareference->getSingleuser($app_id,$customer_id);
          $errors="";
          if (!empty($errors)) {
            throw new Exception($errors);
          }else {
            //  Save Invoice
            $password  = $this->randomPassword(10);
            $taxID     = $this->randomTaxid();
            $inv_settings['app_id']=$app_id;
            $inv_settings['user_id']=$customer_id;
            $inv_settings['blockchain_password']=$password;
            $inv_settings['invoice_name']=$contact_user[0]['firstname'];
            $inv_settings['sponsor_id']=0;
            $inv_settings['invoice_surname']=$contact_user[0]['lastname'];
            $inv_settings['invoice_mobile']=$contact_user[0]['mobile'];
            $inv_settings['tax_id']=$taxID;
            $inv_settings['terms_accepted']=0;
            $inv_settings['special_terms_accepted']=0;
            $inv_settings['privacy_accepted']=0;
            $migareference->savePropertysettings($inv_settings);
          }
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader'  => 0,
              'message_button'  => 0,
              'message_timeout' => 2
          ];
      } else {
          $payload = [
              'error' => true,
              'message' => __('An error occurred while deleting the push. Please try again later.')
          ];
      }
    } catch (\Exception $e) {
        $payload = [
            'error' => true,
            'message' => __($e->getMessage())
        ];
    }
      $this->_sendJson($payload);
  }
  function parseyoutubetokenbyuriAction($url){
  	if (strncmp($url, 'user/', 5) === 0) { // 1.
  		return null;
  	}
  	if (preg_match('/^[a-zA-Z0-9\-\_]{11}$/', $url)) { // 2.
  		return $url;
  	}
  	if (preg_match('/(?:watch\?v=|v\/|embed\/|ytscreeningroom\?v=|\?v=|\?vi=|e\/|watch\?.*vi?=|\?feature=[a-z_]*&v=|vi\/)([a-zA-Z0-9\-\_]{11})/', $url, $regularMatch)) { // 3.
  		return $regularMatch[1];
  	}
  	if (preg_match('/([a-zA-Z0-9\-\_]{11})(?:\?[a-z]|\&[a-z])/', $url, $organicParametersMatch)) { // 4.
  		return $organicParametersMatch[1];
  	}
  	if (preg_match('/u\/1\/([a-zA-Z0-9\-\_]{11})(?:\?rel=0)?$/', $url)) { // 5.
  		return null; // 5. User channel without token.
  	}
  	if (preg_match('/(?:watch%3Fv%3D|watch\?v%3D)([a-zA-Z0-9\-\_]{11})[%&]/', $url, $urlEncoded)) { // 6.
  		return $urlEncoded[1];
  	}
  	// 7. Rules for special cases
  	if (preg_match('/watchv=([a-zA-Z0-9\-\_]{11})&list=/', $url, $special1)) {
  		return $special1[1];
  	}
  	return null;
  }

  
     
}

 
