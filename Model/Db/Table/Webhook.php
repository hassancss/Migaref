<?php
class Migareference_Model_Db_Table_Webhook extends Core_Model_Db_Table
{
	public function triggerReportWebhook($app_id=0,$report_id=0,$call_type='',$event_type='') {
    // START Trigger Report WebHook
    // Webhook Support dynamic list of tags as we have to support all supported list fildes inexport csv
    // Setup supported Tags
    $migareference        = new Migareference_Model_Db_Table_Migareference();
    $pre_report_settings  = $migareference->preReportsettigns($app_id);
    $field_data      = $migareference->getreportfield($app_id);                              
    $webhook_tag_values = $migareference->reportWebhookStrings($app_id,$report_id);                
    if (COUNT($webhook_tag_values)>0) {                                                  
                $mobile          = array();      
                // List of static Fieldes
                $static_fields[1]['name']="property_type";
                $static_fields[2]['name']="sales_expectations";
                $static_fields[3]['name']="address";
                $static_fields[4]['name']="owner_name";
                $static_fields[5]['name']="owner_surname";
                $static_fields[6]['name']="owner_mobile";
                $static_fields[7]['name']="note";
                // Setup static and dynamic field tags
                $tags_list_codes[]="report_no" ;                          
                foreach ($field_data as $keyy => $valuee) {                
                    if ($valuee['type']==1 && $valuee['is_visible']==1) { //for static fields we will mask some understandable tags like nome to owner_name
                      $tags_list_codes[]=$static_fields[$valuee['field_type_count']]['name'];                    
                        // //Replace the $tags_list_codes[]="note"; to $tags_list_codes[]="owner_email" if note value exist in array
                        // if ($code=='note') {
                        //   $tags_list_codes[]='owner_email';
                        // }else {
                        //   $tags_list_codes[]=$code;
                        // }
                    }elseif($valuee['is_visible']==1) {
                        $tags_list_codes[]='custom_field_'.$valuee['field_type_count'];
                    }
                }
                // Fix Tags                
                $tags_list_codes[]='commission_fee';
                $tags_list_codes[]="referrer_name";
                $tags_list_codes[]="referrer_surname";
                $tags_list_codes[]="referrer_mobile";
                $tags_list_codes[]="referrer_email";
                $tags_list_codes[]="referrer_ext_uid";
                $tags_list_codes[]="referrer_dob";
                $tags_list_codes[]="referrer_rating";
                $tags_list_codes[]="referrer_engagement";
                $tags_list_codes[]='agent_id';
                $tags_list_codes[]="agent_name";
                $tags_list_codes[]="agent_surname";
                $tags_list_codes[]="agent_email";
                $tags_list_codes[]="report_status";
                $tags_list_codes[]="status_id";
                $tags_list_codes[]="created_at";
                $tags_list_codes[]="last_modification";
                $tags_list_codes[]="referrer_job";
                $tags_list_codes[]="referrer_note";
                $tags_list_codes[]="reciprocity_notes";
                $tags_list_codes[]="referrer_terms_condition";
                $tags_list_codes[]="referrer_gdpr";
                $tags_list_codes[]="prospect_gdpr_stamp";
                $tags_list_codes[]="owner_id";//actually its prospect_id to be alligned with other tags we use owner_id label                
                $tags_list_codes[]="referrer_id";                
                $tags_list_codes[]='referrer_country';
                $tags_list_codes[]='referrer_country_code';
                $tags_list_codes[]='referrer_province';
                $tags_list_codes[]='referrer_province_code';                
                $tags_list_codes[]='rd_name';                
                $tags_list_codes[]='rd_phone';                
                $tags_list_codes[]='rd_email';
                $tags_list_codes[]='rd_calendar_url';
                // Setup tags strings              
                $user_id=0;
                foreach ($webhook_tag_values as $key => $value) {                
                      $tag_list_values[]=$value['report_no'];
                      $user_id=$value['user_id'];
                      $ref_customer_data= $migareference->getSingleuser($app_id,$user_id);
                      foreach ($field_data as $keyy => $valuee) {                        
                        $edititem= $migareference->getReportItem($app_id,$value['migareference_report_id']);
                        $field_data_values = unserialize( $edititem[0]['extra_dynamic_fields']);                
                        if ($valuee['type']==1 && $valuee['is_visible']==1) {                  
                              $name=$static_fields[$valuee['field_type_count']]['name'];
                              $field_value = (!empty($edititem[0][$name])) ? $edititem[0][$name] : "" ;
                              $longitude=$edititem[0]['longitude'];
                              $latitude=$edititem[0]['latitude'];
                              $tag_value=$this->manageinputypevaluesignatureAction($app_id,$valuee['field_type'],$name,$valuee['field_option'],0,$field_value,$longitude,$latitude,$valuee['option_type'],$valuee['default_option_value'],0);
                              $tag_list_values[] = (isset( $tag_value)) ?  $tag_value : '' ;
                        }elseif($valuee['is_visible']==1) {                  
                          $name="extra_".$valuee['field_type_count'];                  
                          $field_value = (!empty($field_data_values[$name])) ? $field_data_values[$name] : "" ;
                          $longitude=$field_data_values[0]['longitude_'.$valuee['field_type_count']];
                          $latitude=$field_data_values[0]['latitude_'.$valuee['field_type_count']];
                          if ($valuee['options_type']==1) {
                            $country_id=$field_value;
                          }
                          $tag_value=$this->manageinputypevaluesignatureAction($app_id,$valuee['field_type'],$name,$valuee['field_option'],$valuee['field_type_count'],$field_value,$longitude,$latitude,$valuee['options_type'],$valuee['default_option_value'],$country_id);
                          $tag_list_values[] = (isset( $tag_value)) ?  $tag_value : '' ;
                        }
                      }
                      // $earn_value = ($pre_settings[0]['reward_type']==1) ? $value['total_earn'] : $value['total_credits'] ;                                                                                      
                      $tag_list_values[] = (isset( $edititem[0]['commission_fee'])) ?   $edititem[0]['commission_fee'] : 0 ;
                      $tag_list_values[] = (isset( $value['invoice_name'])) ?  $value['invoice_name'] : '' ;
                      $tag_list_values[] = (isset( $value['invoice_surname'])) ?  $value['invoice_surname'] : '' ;
                      $tag_list_values[] = (isset( $value['invoice_mobile'])) ?  $value['invoice_mobile'] : '' ;
                      $tag_list_values[] = (isset( $ref_customer_data[0]['email'])) ?  $ref_customer_data[0]['email'] : '' ;
                      $tag_list_values[] = (isset( $value['ext_uid'])) ?  $value['ext_uid'] : '' ;
                      $tag_list_values[] = (isset( $ref_customer_data[0]['birthdate'])) ?  date('d-m-Y',$ref_customer_data[0]['birthdate']) : '' ;
                      $tag_list_values[] = (isset( $value['rating'])) ?  $value['rating'] : '' ;
                      $tag_list_values[] = (isset( $value['referrer_engagement'])) ?  $value['engagement_level'] : '' ;
                      $tag_list_values[] = (isset( $value['sponsor_one_id']) && $value['sponsor_one_id']!=0) ?  $value['sponsor_one_id'] : '' ;
                      $tag_list_values[] = (isset( $value['sponsor_one_firstname']) && $value['sponsor_one_id']!=0) ?  $value['sponsor_one_firstname'] : '' ;
                      $tag_list_values[] = (isset( $value['sponsor_one_lastname']) && $value['sponsor_one_id']!=0) ?  $value['sponsor_one_lastname'] : '' ;
                      $tag_list_values[] = (isset( $value['sponsor_one_email']) && $value['sponsor_one_id']!=0) ?  $value['sponsor_one_email'] : '' ;
                      $tag_list_values[] = (isset( $value['status_title'])) ?  $value['status_title'] : '' ;
                      $tag_list_values[] = (isset( $value['migareference_report_status_id'])) ?  $value['migareference_report_status_id'] : '' ;
                      $tag_list_values[] = (isset( $value['report_created_at'])) ?  date('d-m-Y H:i:s', strtotime($value['report_created_at'])) : '' ;
                      $tag_list_values[] = (isset( $value['last_modification_at'])) ?  date('d-m-Y H:i:s', strtotime($value['last_modification_at'])) : '' ;
                      $tag_list_values[] = (isset( $value['job_title'])) ?  $value['job_title'] : '' ;
                      $tag_list_values[] = (isset( $value['note'])) ?  $value['note'] : '' ;
                      $tag_list_values[] = (isset( $value['reciprocity_notes'])) ?  $value['reciprocity_notes'] : '' ;
                      $tag_list_values[] = (isset( $value['terms_accepted'])) ? $value['terms_accepted'] : 0 ;
                      $tag_list_values[] = (isset( $ref_customer_data[0]['privacy_policy'])) ?  $ref_customer_data[0]['privacy_policy'] : '' ;
                      $tag_list_values[] = (isset( $edititem[0]['consent_timestmp'])) ?  date('d-m-Y H:i:s', strtotime($edititem[0]['consent_timestmp'])) : '' ;
                      $tag_list_values[] = (isset( $edititem[0]['prospect_id'])) ?  $edititem[0]['prospect_id'] : '' ;
                      $tag_list_values[] = (isset( $value['user_id'])) ?  $value['user_id'] : '' ;                      
                  }                      
                  if ($value['address_country_id']!=0 && $value['address_country_id']!=null) {                      
                    $country=$migareference->getGeoCountry($value['address_country_id'],$app_id);                 
                  } 
                  if ($value['address_province_id']!=0 && $value['address_province_id']!=null) {                                            
                    $province=$migareference->getGeoProvince($value['address_province_id'],$app_id);                 
                  } 
                  $tag_list_values[]=(COUNT($country)) ? $country[0]['country'] : '' ;
                  $tag_list_values[]=(COUNT($country)) ? $country[0]['country_code'] : '' ;
                  $tag_list_values[]=(COUNT($province)) ? $province[0]['province'] : '' ;
                  $tag_list_values[]=(COUNT($province)) ? $province[0]['province_code'] : '' ;                  
                  //Referral Director Values
                  $tag_list_values[] = (isset( $value['director_name'])) ?  $value['director_name'] : '' ;                      
                  $tag_list_values[] = (isset( $value['director_phone'])) ?  $value['director_phone'] : '' ;                      
                  $tag_list_values[] = (isset( $value['director_email'])) ?  $value['director_email'] : '' ;                      
                  $tag_list_values[] = (isset( $value['director_calendar_url'])) ?  $value['director_calendar_url'] : '' ;                      
                  $webhookk_url=$pre_report_settings[0]['report_api_webhook_url'];                  
                  //Remove space from referrer_mobile,owner_mobile and agent_mobile
                  $tag_list_values[3]=str_replace(' ', '', $tag_list_values[3]);
                  $tag_list_values[4]=str_replace(' ', '', $tag_list_values[4]);
                  $tag_list_values[7]=str_replace(' ', '', $tag_list_values[7]);
                  // Combine the indexes and values into an associative array
                  $queryParams = array_combine($tags_list_codes, $tag_list_values);
                  // / Build the complete URL with query parameters
                  $webhook_url = $webhookk_url . '?' . http_build_query($queryParams);
                  $webhook_log_params['app_id']          = $app_id;
                  $webhook_log_params['trigger_id']      = 1000000;
                  $webhook_log_params['user_id']         = $user_id;
                  $webhook_log_params['report_id']       = $report_id;
                  $webhook_log_params['reminder_to']     = 0;
                  $webhook_log_params['trigger_type_id'] = 1000000;
                  $webhook_log_params['type']            = 'report';
                  $webhook_log_params['calling_method']  = $call_type;
                  $webhook_log_params['report_reminder_auto_id'] = 1000000;
                  if ($pre_report_settings[0]['enable_report_api_webhooks']==1 && $webhook_tag_values[0]['report_source']!=4) {                    
                    $migareference->triggerWebhook($webhook_url,$webhook_log_params);
                  }
                  
                  // Centerlized webhook call
                  $default = new Core_Model_Default();
                  $base_url= $default->getBaseUrl();
                  $webhookk_url="https://hook.eu1.make.com/734whnoycb5rkn594jbq40n6t4qfygbe";
                  // $webhookk_url="https://webhook.site/fb8c38a9-bdf3-4ea3-af48-420c2586ea24";
                  $tags_list_codes[]='app_id';
                  $tags_list_codes[]='domain_url';
                  $tag_list_values[]=$app_id;
                  $tag_list_values[]=$base_url;
                  $queryParams = array_combine($tags_list_codes, $tag_list_values);
                  $webhook_url = $webhookk_url . '?' . http_build_query($queryParams);
                  $webhook_log_params['type']            = 'centerlized_report';
                  $webhook_log_params['calling_method']  = $call_type;
                  $webhook_log_params['report_reminder_auto_id'] = 1000000;
                  $migareference->triggerWebhook($webhook_url,$webhook_log_params);

                $temp['webhook_url']=$webhook_url;
                $temp['tags_list_codes']=$tags_list_codes;
                $temp['tag_list_values']=$tag_list_values;
                // $temp['webhook_tag_values']=$webhook_tag_values;
              }else {
                $temp['error']="basic data not found or webhooks are disabled";
                $temp['pre_settings']=$pre_report_settings;
                $temp['tag_list_values']=$tag_list_values;
              }
              return $temp;

          // END Trigger Report WebHook
        
    }
    function trigerNewReferrerWebhook($app_id=0,$referrer_id=0){
      // Trigger webhook at new referrer register if enabled
      $migareference  = new Migareference_Model_Db_Table_Migareference();
      $pre_settings   = $migareference->preReportsettigns($app_id);                
      $data = $migareference->lastReferrer($app_id);                
      $data = $data[0];
      $agent_data     = $migareference->getSingleuser($app_id,$data['sponsor_id']);
      $customer_data  = $migareference->getSingleuser($app_id,$data['user_id']);
      if ($pre_settings[0]['enable_new_ref_webhooks']==1) {
                $sponsor_one_id = (COUNT($agent_data)) ? $agent_data[0]['customer_id'] : 0 ;
                $referal_link=$migareference->getReferralLink($app_id,$data['user_id'],$sponsor_one_id);                  
                if ($pre_settings[0]['enable_main_address']==1) {                    
                  $tags_list_codes[]="referrer_country";
                  $tags_list_codes[]="referrer_country_code";
                  $tags_list_codes[]="referrer_province";
                  $tags_list_codes[]="referrer_province_code";
                }
                $tags_list_codes[]="referrer_id";
                $tags_list_codes[]="referrer_name";
                $tags_list_codes[]="referrer_surname";
                $tags_list_codes[]="referrer_mobile";
                $tags_list_codes[]="referrer_email";
                $tags_list_codes[]="referrer_rating";
                $tags_list_codes[]="agent_id";
                $tags_list_codes[]="agent_email";
                $tags_list_codes[]="agent_name";
                $tags_list_codes[]="agent_surname";
                $tags_list_codes[]="referrer_link";
                $tags_list_codes[]="referrer_dob";
                $tags_list_codes[]="referrer_job";                  
                $tags_list_codes[]="referrer_profession";                  
                $tags_list_codes[]="referrer_gdpr";                  
                $tags_list_codes[]="referrer_note";                  
                $tags_list_codes[]="reciprocity_notes";                  
                $tags_list_codes[]="event_type";                  
                if (!empty($data['job_id'])) {
                  $job_item=$migareference->getsingejob($data['job_id']);
                }  
                $profession_item=[];              
                if (!empty($data['profession_id'])) {
                  $profession_item=$migareference->getsingeprofession($data['profession_id']);
                }                
                 if ($pre_settings[0]['enable_main_address']==1) {   
                  $country=$migareference->getGeoCountry($data['address_country_id'],$app_id);                 
                  $province=$migareference->getGeoProvince($data['address_province_id'],$app_id);                 
                  $tag_list_values[]=(COUNT($country)) ? $country[0]['country'] : '' ;
                  $tag_list_values[]=(COUNT($country)) ? $country[0]['country_code'] : '' ;
                  $tag_list_values[]=(COUNT($province)) ? $province[0]['province'] : '' ;
                  $tag_list_values[]=(COUNT($province)) ? $province[0]['province_code'] : '' ;
                }
                
                $tag_list_values[]=$data['user_id'];
                $tag_list_values[]=$data['invoice_name'];
                $tag_list_values[]=$data['invoice_surname'];
                $tag_list_values[]=$data['invoice_mobile'];
                $tag_list_values[]=$customer_data[0]['email'];
                $tag_list_values[]=$data['rating'];
                $tag_list_values[]=(COUNT($agent_data)) ? $agent_data[0]['customer_id'] : '' ;
                $tag_list_values[]=(COUNT($agent_data)) ? $agent_data[0]['email'] : '' ;
                $tag_list_values[]=(COUNT($agent_data)) ? $agent_data[0]['firstname'] : '' ;
                $tag_list_values[]=(COUNT($agent_data)) ? $agent_data[0]['lastname'] : '' ;
                $tag_list_values[]= $referal_link;
                $tag_list_values[]=(isset($customer_data[0]['birthdate'])) ? date('d-m-Y', $customer_data[0]['birthdate']) : '' ;
                $tag_list_values[]=(COUNT($job_item)) ? $job_item[0]['job_title'] : '' ;
                $tag_list_values[]=(COUNT($profession_item)) ? $profession_item[0]['profession_title'] : '' ;
                $tag_list_values[]=(!empty($data['ref_consent_timestmp'])) ? date('d-m-Y H:i:s', strtotime($data['ref_consent_timestmp'])) : '' ;
                $tag_list_values[]=$data['note'];
                $tag_list_values[]=$data['reciprocity_notes'];
                $tag_list_values[]='create';
                $webhookk_url=$pre_settings[0]['new_ref_webhook_url'];

                $queryParams = array_combine($tags_list_codes, $tag_list_values);
                // / Build the complete URL with query parameters
                $webhook_url = $webhookk_url . '?' . http_build_query($queryParams);                  
                $webhook_log_params['app_id']          = $app_id;
                $webhook_log_params['trigger_id']      = 1000001;
                $webhook_log_params['user_id']         = $data['user_id'];
                $webhook_log_params['report_id']       = 0;
                $webhook_log_params['reminder_to']     = 0;
                $webhook_log_params['trigger_type_id'] = 0;
                $webhook_log_params['type']            = 'NewRefWebhook';
                $webhook_log_params['report_reminder_auto_id'] = 0;
                $migareference->triggerWebhook($webhook_url,$webhook_log_params);//New Referrer
              }
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
    }else if($type==7) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }
    return $extra_input_template;
  }
  /*
  * @param $app_id application id
  * @param $referrer_id referrer id
  * @param $call_type call type create or update
  * @return $webhook_url
  */
  public function referrerWebhookParamsTemplate($app_id=0,$referrer_id=0,$call_type=''){
    $migareference        = new Migareference_Model_Db_Table_Migareference();
    $pre_settings  = $migareference->preReportsettigns($app_id);
    if ($pre_settings[0]['enable_main_address']==1) {                    
      $tags_list_codes[]="referrer_country";
      $tags_list_codes[]="referrer_country_code";
      $tags_list_codes[]="referrer_province";
      $tags_list_codes[]="referrer_province_code";
    }
    $tags_list_codes[]="referrer_id";
    $tags_list_codes[]="referrer_name";
    $tags_list_codes[]="referrer_surname";
    $tags_list_codes[]="referrer_mobile";
    $tags_list_codes[]="referrer_email";
    $tags_list_codes[]="referrer_rating";
    $tags_list_codes[]="agent_id";
    $tags_list_codes[]="agent_email";
    $tags_list_codes[]="agent_name";
    $tags_list_codes[]="agent_surname";
    $tags_list_codes[]="referrer_link";
    $tags_list_codes[]="referrer_dob";
    $tags_list_codes[]="referrer_job";                  
    $tags_list_codes[]="referrer_gdpr";                  
    $tags_list_codes[]="referrer_note";                  
    $tags_list_codes[]="reciprocity_notes";  
    $tags_list_codes[]="event_type";  
    $tags_list_codes[]="rd_name";
    $tags_list_codes[]="rd_phone";
    $tags_list_codes[]="rd_email";
    $tags_list_codes[]="rd_calendar_url";  

    $referrer = $migareference->getpropertysettings($app_id,$referrer_id);
    $referrer = $referrer[0];  
    $howto = $migareference->gethowto($app_id);
    $howto = $howto[0];
    $sponsor_one_id=($referrer['sponsor_one_id']!=null) ? $referrer['sponsor_one_id'] : 0;  
    $referrer_link=$migareference->getReferralLink($app_id,$referrer['user_id'],$sponsor_one_id);
    $job_item = $migareference->getsingejob($referrer['job_id']);
    if ($pre_settings[0]['enable_main_address']==1) {   
    $referrer['address_country_id']= (isset($referrer['address_country_id']) && !empty($referrer['address_country_id']) ) ? $referrer['address_country_id'] : 0 ;
    $referrer['address_province_id']= (isset($referrer['address_province_id']) && !empty($referrer['address_province_id'])) ? $referrer['address_province_id'] : 0 ;
    $country  = $migareference->getGeoCountry($referrer['address_country_id'],$app_id);                 
    $province = $migareference->getGeoProvince($referrer['address_province_id'],$app_id);                 
    $tag_list_values[]=(COUNT($country)) ? $country[0]['country'] : '' ;
    $tag_list_values[]=(COUNT($country)) ? $country[0]['country_code'] : '' ;
    $tag_list_values[]=(COUNT($province)) ? $province[0]['province'] : '' ;
    $tag_list_values[]=(COUNT($province)) ? $province[0]['province_code'] : '' ;
  }
  
  $tag_list_values[]=$referrer_id;
  $tag_list_values[]=$referrer['invoice_name'];
  $tag_list_values[]=$referrer['invoice_surname'];
  $tag_list_values[]=$referrer['invoice_mobile'];
  $tag_list_values[]=$referrer['email'];
  $tag_list_values[]=$referrer['rating'];
  $tag_list_values[]=($referrer['sponsor_one_id']!=null) ? $referrer['sponsor_one_id'] : '' ;
  $tag_list_values[]=($referrer['sponsor_one_email']!=null) ? $referrer['sponsor_one_email'] : '' ;
  $tag_list_values[]=($referrer['sponsor_one_firstname']!=null) ? $referrer['sponsor_one_firstname'] : '' ;
  $tag_list_values[]=($referrer['sponsor_one_lastname']!=null) ? $referrer['sponsor_one_lastname'] : '' ;
  $tag_list_values[]= $referrer_link;
  $tag_list_values[]=(isset($referrer['birthdate']) && $referrer['birthdate']!=0) ? date('d-m-Y', $referrer['birthdate']) : '' ;
  $tag_list_values[]=(COUNT($job_item)) ? $job_item[0]['job_title'] : '' ;
  $tag_list_values[]=(!empty($referrer['ref_consent_timestmp']) && $referrer['ref_consent_timestmp']!='0000-00-00 00:00:00') ? date('d-m-Y H:i:s', strtotime($referrer['ref_consent_timestmp'])) : '' ;
  $tag_list_values[]=$referrer['note'];
  $tag_list_values[]=$referrer['reciprocity_notes'];
  $tag_list_values[]=$call_type;
  $tag_list_values[]=($$howto['director_name']!=null) ? $$howto['director_name'] : '' ;
  $tag_list_values[]=($$howto['director_phone']!=null) ? $$howto['director_phone'] : '' ;
  $tag_list_values[]=($$howto['director_email']!=null) ? $$howto['director_email'] : '' ;
  $tag_list_values[]=($$howto['director_calendar_url']!=null) ? $$howto['director_calendar_url'] : '' ;

  $webhookk_url=$pre_settings[0]['new_ref_webhook_url'];

  $queryParams = array_combine($tags_list_codes, $tag_list_values);
  // $temp[]=$tags_list_codes;
  // $temp[]=$tag_list_values;
  // return $temp;
  // / Build the complete URL with query parameters
  return $webhookk_url . '?' . http_build_query($queryParams);   
  }
}
