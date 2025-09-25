<?php
class Migareference_Model_Db_Table_Reportnotification extends Core_Model_Db_Table
{
    public function reportNotiTagsList()
    {
        $notificationTags= [
                            "@@referral_name@@",
                            "@@report_owner@@",
                            "@@property_owner@@",
                            "@@report_owner_phone@@",
                            "@@property_owner_phone@@",
                            "@@report_no@@",
                            "@@commission@@",
                            "@@app_name@@",
                            "@@app_link@@",
                            "@@custom_field_1@@",
                            "@@custom_field_2@@",
                            "@@custom_field_3@@",
                            "@@custom_field_4@@",
                            "@@custom_field_5@@",
                            "@@custom_field_6@@",
                            "@@custom_field_7@@",
                            "@@custom_field_8@@",
                            "@@custom_field_9@@",
                            "@@custom_field_10@@"
                        ];
        return $notificationTags;
  }
    public function sendNotification($app_id=0,$report_id=0,$report_status_id=0,$modified_by=0,$call_source='', $report_operation=''){
        try {
          //Send Notification
        $migareference = new Migareference_Model_Db_Table_Migareference();
        $test_return['method_fired']=true;
        $test_return['method_pram']=$app_id."@".$report_id.'@'.$report_status_id."@".$modified_by."@".$call_source."@".$report_operation;

        if ($report_operation=='create') {
            $operation_note='Report Added';
        }elseif($report_operation=='update') {
            $operation_note='Report Updated';
        }
        $push_log_data['app_id']    = $app_id;
        $push_log_data['user_id']  = 99999;
        $push_log_data['log_type']="Push Notification sent";
        $push_log_data['log_detail']=$operation_note;
        $email_log_data['user_id']  = 99999;
        $email_log_data['app_id']    = $app_id;
        $email_log_data['log_type']="Email Notification sent";
        $email_log_data['log_detail']=$operation_note;

        $migareference->saveLog($push_log_data);
        $migareference->saveLog($email_log_data);  

        $eventtemplats=$migareference->getEventNotificationTemplats($app_id,$report_status_id);        
        // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH(Send SMS if Referral token not found))        
        if (!empty($eventtemplats) && $eventtemplats[0]['is_pause_sending']==0) { //Is notification are enabled
            $test_return['notification_enabled']=true;
            $report_item        = $migareference->getReportItem($app_id,$report_id);//@@report item
            $test_return['method_pram']=$report_item;
            $admin_customers    = $migareference->getAdminCustomers($app_id);
            $referral_customers = $migareference->getRefferalCustomers($app_id,$report_item[0]['user_id']);
            $sponsor_customers  = $migareference->getSponsorList($app_id,$report_item[0]['user_id']);                             
            $pre_report_settings= $migareference->preReportsettigns($app_id);            
            $agent_user_data    = $migareference->getReportSponsor($app_id,$report_id);//Agents user as per report type
            $field_data         = $migareference->getreportfield($app_id); 
            $base_url           = (new Core_Model_Default())->getBaseUrl();
            if ($call_source=='ADMIN-END') {
                $updated_by = $migareference->getAappadminagentdata($modified_by);
            }else {
                $updated_by = $migareference->getAgentdata($modified_by);
            }            
            $app_link           = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $tag_list= [
                "@@referral_name@@",
                "@@report_owner@@",
                "@@property_owner@@",
                "@@report_owner_phone@@",
                "@@property_owner_phone@@",
                "@@report_no@@",
                "@@commission@@",
                "@@app_name@@",
                "@@app_link@@",
                "@@agent_name@@",
                "@@custom_field_1@@",
                "@@custom_field_2@@",
                "@@custom_field_3@@",
                "@@custom_field_4@@",
                "@@custom_field_5@@",
                "@@custom_field_6@@",
                "@@custom_field_7@@",
                "@@custom_field_8@@",
                "@@custom_field_9@@",
                "@@custom_field_10@@"
            ];
            $tag_values= [
                $referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],
                $report_item[0]['owner_name'].' '.$report_item[0]['owner_surname'],
                $report_item[0]['owner_name'].' '.$report_item[0]['owner_surname'],
                $report_item[0]['owner_mobile'],
                $report_item[0]['owner_mobile'],
                $report_item[0]['report_no'],
                $report_item[0]['commission_fee'],
                $referral_customers[0]['name'],
                $app_link,
                $updated_by[0]['firstname'].' '.$updated_by[0]['lastname'],
            ];
            
            //Embed Custom Field values
            foreach ($field_data as $keyy => $valuee) {                                        
                $field_data_values = unserialize( $report_item[0]['extra_dynamic_fields']);                
                if($valuee['type']==2) {
                    if ($valuee['is_visible']==1) {
                        $name="extra_".$valuee['field_type_count'];                  
                        $field_value = (!empty($field_data_values[$name])) ? $field_data_values[$name] : "" ;
                        $longitude=$field_data_values[0]['longitude_'.$valuee['field_type_count']];
                        $latitude=$field_data_values[0]['latitude_'.$valuee['field_type_count']];
                        if ($valuee['options_type']==1) {
                            $country_id=$field_value;
                        }
                        $tag_value=$this->manageinputypevaluesignatureAction($app_id,$valuee['field_type'],$name,$valuee['field_option'],$valuee['field_type_count'],$field_value,$longitude,$latitude,$valuee['options_type'],$valuee['default_option_value'],$country_id);
                        $tag_values[] = (isset( $tag_value)) ?  $tag_value : '' ;
                    }else {
                        $tag_values[] = '';
                    }                  
                }
            }
                       
            //**EMAIL Notification**
            if ($eventtemplats[0]['email_delay_days']==0 && $eventtemplats[0]['email_delay_hours']==0) { //Email Notifications are eanbled               

                $test_return['email_enabled']=true;
                if ($eventtemplats[0]['is_email_agt']) { //Notfication to Admins and Agents

                    $test_return['is_email_agt']=true;
                    $email_data['email_title']=str_replace($tag_list, $tag_values, $eventtemplats[0]['agt_email_title']);
                    $email_data['email_text']=str_replace($tag_list, $tag_values, $eventtemplats[0]['agt_email_text']);

                    foreach ($admin_customers as $key => $value) {
                        $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                    }     

                    if (COUNT($agent_user_data)) { // Send Noti to Referrer Agent who have same agent type as report type (By default report and agents have type 1)
                        $migareference->sendMail($email_data,$app_id,$agent_user_data[0]['customer_id']); 
                    }     
                }                
                if ($eventtemplats[0]['is_email_ref']) { //Send to Refferral / User who add Report

                    // $test_return['is_email_ref']=$report_item[0]['user_id'];
                    $email_data['email_title']=str_replace($tag_list, $tag_values, $eventtemplats[0]['ref_email_title']);
                    $email_data['email_text']=str_replace($tag_list, $tag_values, $eventtemplats[0]['ref_email_text']);
                    $agent_id=0;
                    if (COUNT($sponsor_customers)) {
                        $agent_id=$sponsor_customers[0]['agent_id'];
                    }
                    // $invitation_link=$migareference->getRefInvitationLink($app_id,$report_item[0]['user_id'],$agent_id);                    
                    // $email_data['email_text'].="<br><br>".$invitation_link['email_formate']."<br>";
                    $test_return['is_email_ref_text']=$email_data['email_text'];
                    $test_return['is_email_ref_user']=$report_item[0]['user_id'];
                    $test_return['is_email_ref']=$migareference->sendMail($email_data,$app_id,$report_item[0]['user_id']);
                }
            }          
            // ***PUSH & SMS Notifications *** //
            if ($eventtemplats[0]['push_delay_days']==0 && $eventtemplats[0]['push_delay_hours']==0) {
                
                $test_return['push_enabled']=true;
                if ($eventtemplats[0]['is_push_agt']) {

                    $test_return['is_push_agt']=true;
                    $push_data['push_title']=str_replace($tag_list, $tag_values, $eventtemplats[0]['agt_push_title']);
                    $push_data['push_text']=str_replace($tag_list, $tag_values, $eventtemplats[0]['agt_push_text']);
                    $push_data['open_feature'] = $eventtemplats[0]['agt_open_feature'];
                    $push_data['feature_id'] = $eventtemplats[0]['agt_feature_id'];
                    $push_data['custom_url'] = $eventtemplats[0]['agt_custom_url'];
                    $push_data['cover_image'] = $eventtemplats[0]['agt_cover_image'];
                    $push_data['app_id'] = $app_id;

                    foreach ($admin_customers as $keyy => $valuee) {
                        $migareference->sendPush($push_data,$app_id,$valuee['customer_id']);
                    }
                    // Send Noti to Referrer Agent who have same agent type as report type (By default report and agents have type 1)
                    if (COUNT($agent_user_data)) {
                        $migareference->sendPush($push_data,$app_id,$agent_user_data[0]['customer_id']); 
                    }     
                }
                //Send to Refferral / User who add Report                           
                if ($eventtemplats[0]['is_push_ref']) {

                    $test_return['is_push_ref']=true;
                    $gcmData=$migareference->checkGcm($report_item[0]['user_id'],$app_id);
                    $apnsData=$migareference->checkApns($report_item[0]['user_id'],$app_id);

                    if (count($gcmData) || count($apnsData) || $pre_report_settings[0]['enable_twillio_notification']==2) {      

                        $test_return['ref_push_delivered']=true;
                        $push_data['push_title']=str_replace($tag_list, $tag_values, $eventtemplats[0]['ref_push_title']);
                        $push_data['push_text']=str_replace($tag_list, $tag_values, $eventtemplats[0]['ref_push_text']);
                        $push_data['open_feature'] = $eventtemplats[0]['ref_open_feature'];
                        $push_data['feature_id'] = $eventtemplats[0]['ref_feature_id'];
                        $push_data['custom_url'] = $eventtemplats[0]['ref_custom_url'];
                        $push_data['cover_image'] = $eventtemplats[0]['ref_cover_image'];
                        $push_data['app_id'] = $app_id; 

                        $migareference->sendPush($push_data,$app_id,$report_item[0]['user_id']);

                    }elseif ($pre_report_settings[0]['enable_twillio_notification']==1 && $eventtemplats[0]['is_sms_ref']) {  

                        $test_return['ref_sms_delivered']=true;
                        //Send to Refferral / User who add Report NOTE: Send sms only when Referer token is not found it mean user dont have installed app                                                                                                                                                                 
                        $sms_data['sms_text']=str_replace($tag_list, $tag_values, $eventtemplats[0]['ref_sms_text']);                         
                        $sms_retur = $migareference->sendSms($sms_data,$app_id,$report_item[0]['user_id']);                            
                    }
                }
            }
            //Schedule Cron Notification
            if($eventtemplats[0]['push_delay_days']>0 || $eventtemplats[0]['push_delay_hours']>0 || $eventtemplats[0]['email_delay_days']>0 || $eventtemplats[0]['email_delay_hours']>0){
                $push_hours=0;
                $push_hours  = ($eventtemplats[0]['push_delay_days']>0) ? $eventtemplats[0]['push_delay_days']*24 : 0 ;
                $push_hours  =  $push_hours+$eventtemplats[0]['push_delay_hours'];
                $email_hours = 0;
                $email_hours = ($eventtemplats[0]['email_delay_days']>0) ? $eventtemplats[0]['email_delay_days']*24 : 0 ;
                $email_hours = $email_hours+$eventtemplats[0]['email_delay_hours'];
                $cron_notification['app_id']=$app_id;
                $cron_notification['report_id']=$report_id;
                $cron_notification['notification_event_id']=$data['report_status'];
                $cron_notification['trigger_start_time']=date('Y-m-d H:i:s');
                $cron_notification['push_delay_hours']=$push_hours;
                $cron_notification['email_delay_hours']=$email_hours;
                $migareference->saveCronnotification($cron_notification);
              }
        }
        $payload = [
          'success' => true,          
          'test_return'=>$test_return
      ];
        } catch (\Throwable $th) {
            $payload = [
              'success' => false,
              'message' => __($e->getMessage()),
              'test_return'=>$test_return
          ];
        }
        return $payload;
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
}
