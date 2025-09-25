<?php
class Migareference_CrmreportsController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default = new Core_Model_Default();
		$errors = '';
		$success = '';
		$appid=0;
		$userid=0;//Who is attempting to update 
		$agentid=0;
		$reportby=0;
		$type=0;
    $default        = new Core_Model_Default();
    $base_url       = $default->getBaseUrl();
		$layout = new Siberian_Layout();
		$external   = new Migareference_Model_Externalreportlink();   
		$migareference        = new Migareference_Model_Migareference();
		$layout->setBaseRender('base', 'migareference/application/crmreport.phtml', 'core_view_default');
		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
			->setPlatform($default->getBaseUrl());
			if ($data = $this->getRequest()->getPost()) {
				$app_content  = $migareference->get_app_content($data['app_id']);
				$layout
					->getBaseRender()
					->setAppid($data['app_id'])
					->setErrors($errors)
					->setSuccess($success)
					->setPagetitle(__($app_content[0]['landing_page_title']))
					->setUserid($data['user_id']);
			}else {
				$token=$_GET['token'];
				$app_id=$_GET['app_id'];
				$urlItem = $external->matchToken($token);
				$app_content  = $migareference->get_app_content($app_id);
				$agent_id = ($urlItem[0]['is_agent']) ? $urlItem[0]['user_id'] : 0 ;        
        if (count($urlItem)) {          
          	$layout
					->getBaseRender()
					->setPagetitle(__($app_content[0]['landing_page_title']))
					->setAppid($urlItem[0]['app_id'])
					->setUserid($urlItem[0]['customer_id'])
					->setAgentid($agent_id)
					->setReportby($urlItem[0]['report_by'])
          ->setInvalidtoken(0)
					->setReportid($urlItem[0]['report_id']);
        } else {          
          	$layout
					->getBaseRender()
          ->setAppid($app_id)
					->setPagetitle(__($app_content[0]['landing_page_title']))
					->setInvalidtoken(1);					
        }
        
			}
		echo $layout->render();
        die;
	}
	 public function getsinglereportreminderAction(){                  
          $migareference = new Migareference_Model_Migareference();
          $report_id     = $this->getRequest()->getParam('report_id');
          $app_id        = $this->getRequest()->getParam('app_id');
          $all_reminders = $migareference->getSingleReportReminder($app_id,$report_id);
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $remindercollection[]=[
               date('d-m-Y',strtotime($value['event_date_time'])),              
               $value['rep_rem_title'],              
            ];
          }
            $payload = [
                "data" => $remindercollection,                
            ];        
        $this->_sendJson($payload);
  }
	 public function getnoteslistAction(){        		 
		 $report_id       = $this->getRequest()->getParam('report_id');
		 $app_id          = $this->getRequest()->getParam('app_id');
     $all_notes = (new Migareference_Model_Notes())->findAll(['app_id'=> $app_id,'report_id' => $report_id])->toArray();   
     $notescollection = [];     
      foreach ($all_notes as $key => $value) {
        $notescollection[]=[
          'date'=>date('d-m-Y',strtotime($value['created_at'])),
          'note'=>$value['notes_content'],              
        ];
      }
        $payload = [
            "data" => $notescollection,               
            "all_notes" => $all_notes                
        ];        
    $this->_sendJson($payload);
  }
	public function loadgeoproviceAction() {
      if ($datas = $this->getRequest()->getPost()) {
          try {
            $migareference = new Migareference_Model_Migareference();
            $dataGeoConPro = $migareference->getGeoCountryProvicnes($datas['app_id'],$datas['country_id']);
            $collection    = [];
            foreach ($dataGeoConPro as $key => $value) {
              $collection[]=[
                        'app_id'=>$value['app_id'],
                        'country_id'=>$value['country_id'],
                        'created_at'=>$value['created_at'],
                        'migareference_geo_provinces_id'=>$value['migareference_geo_provinces_id'],
                        'province'=>$value['province']
                      ];
            }
            $payload = [
                  "collection" => $collection
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      } else {
          $payload = [
              'error' => true,
              'message' => __('An error occurred during process. Please try again later.')
          ];
      }
      $this->_sendJson($payload);
  }
	public function updatereportAction(){
		try {
              $data           = $this->getRequest()->getPost();
              $migareference  = new Migareference_Model_Migareference();			
              $app_id         = $data['app_id'];
              $errors         = "";
              $default        = new Core_Model_Default();
              $base_url       = $default->getBaseUrl();
              $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
              $previous_item  = $migareference->getReport($data['app_id'],$data['migareference_report_id']);
              $pre_report_settings  = $migareference->preReportsettigns($app_id);
              $field_data     = unserialize( $previous_item[0]['extra_dynamic_field_settings']);
              if ($data['is_acquired']==1 && ($data['commission_type']==1 || $data['commission_type']==3) && $data['standard_type']!=4) {
                  if (empty($data['commission_fee']) && empty($data['commission_fee_report'])) {
                    $errors .= __('You must add commission fee.') . "<br/>";
                  }
              }
              if ($data['is_comment']==1 && empty($data['comment'])) {
                    $errors .= __('You must add Comment for Referral.') . "<br/>";
              }
              if ($data['new_order_id']<$data['order_id'] && $previous_item[0]['standard_type']==3 && ($data['reward_type']==2 || $data['reward_type']==1)) {
                    $errors .= __('You can only move to SUPERIOR status') . "<br/>";
              }elseif ($previous_item[0]['standard_type']==3) {
                $errors .= __('You can only move to SUPERIOR status') . "<br/>";
              }
              if (empty($data['invoice_name'])) {
                 $errors .= __('Invoice Name cannot be empty.') . "<br/>";
              }
              if (empty($data['invoice_surname'])) {
                 $errors .= __('Invoice Surname cannot be empty.') . "<br/>";
              }
              $earn_amount = ($data['commission_fee_report']>1) ? $data['commission_fee_report'] : $data['commission_fee'];
              if ($data['standard_type']==3 && $earn_amount<1) {
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
                if ($value['field_type']==6) {
                  $birth_date = date('Y-m-d',strtotime($data[$name]));
                  $property_report['owner_dob']=$birth_date;
                }
                if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$name])) {
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
                }
                if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='owner_mobile' && $pre_report_settings[0]['is_unique_mobile']==1) {
                  $days=$pre_report_settings[0]['grace_days'];
                  $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                  $mobile_duplication=$migareference->isMobileunique($app_id,$date,$data['owner_mobile']);
                  $mobile_blacklist=$migareference->isBlackList($app_id,$data['owner_mobile']);
        					if (strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])
                  || (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')){
        						$errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
        					}
                }
              }
              // End dynamic filed validation rules
              if (!empty($errors)) {
              throw new Exception($errors);
              }else{
                  $status_data   = $migareference->getStatus($app_id,$data['report_status']);
                  $property_report['last_modification_by'] = $data['modification_by'];
                  $earning['user_type']                    = 2;//1: for app cutomers 2: for app admins 3: for agent
                  $property_report['last_modification_at'] = date('Y-m-d H:i:s');
                  $property_report['is_reminder_sent']     = 0;
                  $property_report['last_modification']    = $status_data[0]['status_title'];
                  $property_report['property_type']        = $data['property_type'];
                  $property_report['currunt_report_status']= $data['report_status'];
                  $property_report['sales_expectations']   = $data['sales_expectations'];
                  $agent_user_mail                         = $migareference->getAappadminagentdata($property_report['last_modification_by']);
                  if ($data['is_acquired']==1 && $data['commission_type']==1) {
                    if (empty($data['commission_fee'])) {
                      $property_report['commission_fee']=$data['commission_fee_report'];
                    }else {
                      $property_report['commission_fee']=$data['commission_fee'];
                    }
                  }
                  if ($data['is_acquired']==1 && $data['commission_type']==3) {
                    if (empty($data['commission_fee'])) {
                      $property_report['commission_fee']=$data['commission_fee_report'];
                    }else {
                      $property_report['commission_fee']=$data['commission_fee'];
                    }
                  }
                  if ($data['is_comment']==1) {
                    $comment_tb['app_id']       = $app_id;
                    $comment_tb['report_id']    = $data['migareference_report_id'];
                    $comment_tb['status_id']    = $data['report_status'];
                    $comment_tb['comment']      = $data['comment'];
                    $commnent_id                = $migareference->saveComment($comment_tb);
                  }
                  $property_report['owner_name']    = $data['owner_name'];
                  $property_report['owner_surname'] = $data['owner_surname'];
                  $property_report['owner_mobile']  = $data['owner_mobile'];
                  $property_report['owner_hot']     = $data['owner_hot'];
                  $property_report['note']          = $data['note'];
                  $property_report['latitude']      = $data['latitude'];
                  $property_report['longitude']     = $data['longitude'];
                  $property_report['address']       = $data['address'];
                  $property_report['migareference_report_id']=$data['migareference_report_id'];
                  $property_report['extra_dynamic_fields']=serialize($data);
                  $save_data     = $migareference->updatepropertyreport($property_report);
                  // On Update Report Type,Property Staus ->Save log,Send Notification
                  $log_data['app_id']=$data['app_id'];
                  $log_data['user_id']=$property_report['last_modification_by'];
                  $log_data['user_type']=1;
                  $log_data['report_id']=$data['migareference_report_id'];
                  if ($previous_item[0]['currunt_report_status']!=$data['report_status']) {
                    // Save Staus Update Log
                    $log_data['log_type']="Update Status";
                    $log_data['log_detail']="Update Status to ".$status_data[0]['status_title'];
                    $migareference->saveLog($log_data);
                    // Save Notification send Log
                    $push_log_data['user_id']  = 99999;
                    $push_log_data['log_type']="Push Notification sent";
                    $push_log_data['log_detail']="Status change Notification";
                    $email_log_data['user_id']  = 99999;
                    $email_log_data['log_type']="Email Notification sent";
                    $email_log_data['log_detail']="Status change Notification";
                    $migareference->saveLog($push_log_data);
                    $migareference->saveLog($email_log_data);
                    // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
                    $eventtemplats=$migareference->getEventNotificationTemplats($app_id,$data['report_status']);
                    if (!empty($eventtemplats) && $eventtemplats[0]['is_pause_sending']==0) {
                        // Send Notification
                          // START EMAIL Notification
                          if ($eventtemplats[0]['email_delay_days']==0 && $eventtemplats[0]['email_delay_hours']==0) {
                            // Send Immidiately Notification
                              // Find users to send notification (All Admins+1 Referral Added Report)
                              $admin_customers   = $migareference->getAdminCustomers($app_id);
                              $referral_customers= $migareference->getRefferalCustomers($app_id,$previous_item[0]['user_id']);
                              $sponsor_customers   = $migareference->getSponsorList($app_id,$previous_item[0]['user_id']);
                              //Send to Agents / Admins
                                // Subject
                                  $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['agt_email_title']);
                                  $email_data['email_title']= str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_title']);
                                //Message
                                  $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['agt_email_text']);
                                  $email_data['email_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_name@@",$previous_item[0]['name'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_link@@",$app_link,$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@comment@@",$data['comment'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@commission@@",$earn_amount,$email_data['email_text']);
                                  if ($eventtemplats[0]['is_email_agt']) {
                                    foreach ($admin_customers as $key => $value) {
                                      $mail_retur = $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                                    }
                                    foreach ($sponsor_customers as $key => $value) {
                                      $mail_retur = $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                                    }
                                 }
                              //Send to Refferral / User who add Report
                                // Subject
                                  $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_email_title']);
                                  $email_data['email_title']= str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@report_no@@",$data['report_no'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_title']);
                                //Message
                                  $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_email_text']);
                                  $email_data['email_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@comment@@",$data['comment'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@commission@@",$earn_amount,$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_name@@",$previous_item[0]['name'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_link@@",$app_link,$email_data['email_text']);
                                  if ($eventtemplats[0]['is_email_ref']) {
                                    $mail_retur = $migareference->sendMail($email_data,$app_id,$previous_item[0]['user_id']);
                                  }
                          }
                          // START SMS Notification
                            $admin_customers    = $migareference->getAdminCustomers($app_id);
                            $referral_customers = $migareference->getRefferalCustomers($app_id,$previous_item[0]['user_id']);
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$previous_item[0]['user_id']);                            
                          // START PUSH Notification
                          if ($eventtemplats[0]['push_delay_days']==0 && $eventtemplats[0]['push_delay_hours']==0) {
                            // Send Immidiately Notification
                              // Find users to send notification (All Admins+1 Referral Added Report)
                              $push_agent_user_data    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                              $push_reffreal_user_data = $migareference->getRefferalCustomers($app_id,$previous_item[0]['user_id']);//Admin Users->Agents
                              //Send to Agents / Admins
                                // Subject
                                  $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner@@",$previous_item[0]['owner_name'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner@@",$previous_item[0]['owner_name'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner_phone@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner_phone@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_title']);
                                //Message
                                  $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_text']);
                                  $push_data['push_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@comment@@",$data['comment'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@commission@@",$data['commission_fee'],$push_data['push_text']);
                                  $push_data['open_feature'] = $eventtemplats[0]['agt_open_feature'];
                                  $push_data['feature_id']   = $eventtemplats[0]['agt_feature_id'];
                                  $push_data['custom_url']   = $eventtemplats[0]['agt_custom_url'];
                                  $push_data['cover_image']  = $eventtemplats[0]['agt_cover_image'];
                                  $push_data['app_id'] = $app_id;
                                  if ($eventtemplats[0]['is_push_agt']) {
                                    foreach ($push_agent_user_data as $key => $value) {
                                      $mail_retur = $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                                    }
                                  }                                                         
                                    //Send to Refferral / User who add Report                                
                                   if ($eventtemplats[0]['is_push_ref']) {
                                      $gcmData=$migareference->checkGcm($previous_item[0]['user_id'],$app_id);
                                      $apnsData=$migareference->checkApns($previous_item[0]['user_id'],$app_id);
                                      if (count($gcmData) || count($apnsData) || $pre_report_settings[0]['enable_twillio_notification']==2) {                                    
                                        // Subject
                                        $push_data['push_title'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_title']);
                                        $push_data['push_title'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                        $push_data['push_title'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                        $push_data['push_title'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_title']);
                                        $push_data['push_title'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_title']);
                                        $push_data['push_title'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_title']);
                                        $push_data['push_title'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_title']);
                                      //Message
                                        $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_text']);
                                        $push_data['push_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@comment@@",$data['comment'],$push_data['push_text']);
                                        $push_data['push_text'] = str_replace("@@commission@@",$data['commission_fee'],$push_data['push_text']);
                                        $push_data['open_feature'] = $eventtemplats[0]['ref_open_feature'];
                                        $push_data['feature_id']   = $eventtemplats[0]['ref_feature_id'];
                                        $push_data['custom_url']   = $eventtemplats[0]['ref_custom_url'];
                                        $push_data['cover_image']  = $eventtemplats[0]['ref_cover_image'];
                                        $push_data['app_id']       = $app_id;
                                        $migareference->sendPush($push_data,$app_id,$previous_item[0]['user_id']);                                          
                                      }elseif ($pre_report_settings[0]['enable_twillio_notification']==1 && $eventtemplats[0]['is_sms_ref']) {                                    
                                        //Send to Refferral / User who add Report NOTE: Send sms only when Referer token is not found it mean used dont have installed app                                                           
                                        $sms_data['sms_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@report_owner@@",$data['owner_name']." ".$data['owner_surname'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@property_owner@@",$data['owner_name']." ".$data['owner_surname'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@report_owner_phone@@",$data['owner_mobile'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@property_owner_phone@@",$data['owner_mobile'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@report_no@@",$data['report_no'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@commission@@",$data['commission_fee'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@app_name@@",$referral_customers[0]['name'],$sms_data['sms_text']);
                                        $sms_data['sms_text'] = str_replace("@@app_link@@",$app_link,$sms_data['sms_text']);
                                        $sms_retur = $migareference->sendSms($sms_data,$app_id,$previous_item[0]['user_id']);                                    
                                      }                                  
                                  } 
                          }
                          if($eventtemplats[0]['push_delay_days']>0 || $eventtemplats[0]['push_delay_hours']>0 || $eventtemplats[0]['email_delay_days']>0 || $eventtemplats[0]['email_delay_hours']>0){
                            $push_hours=0;
                            $push_hours  = ($eventtemplats[0]['push_delay_days']>0) ? $eventtemplats[0]['push_delay_days']*24 : 0 ;
                            $push_hours  =  $push_hours+$eventtemplats[0]['push_delay_hours'];
                            $email_hours = 0;
                            $email_hours = ($eventtemplats[0]['email_delay_days']>0) ? $eventtemplats[0]['email_delay_days']*24 : 0 ;
                            $email_hours = $email_hours+$eventtemplats[0]['email_delay_hours'];
                            $cron_notification['app_id']=$app_id;
                            $cron_notification['report_id']=$previous_item[0]['migareference_report_id'];
                            $cron_notification['notification_event_id']=$data['report_status'];
                            $cron_notification['trigger_start_time']=date('Y-m-d H:i:s');
                            $cron_notification['push_delay_hours']=$push_hours;
                            $cron_notification['email_delay_hours']=$email_hours;
                            $migareference->saveCronnotification($cron_notification);
                          }
                    }
                  }
                  // Update
                  $pro_settings['invoice_name']=$data['invoice_name'];
                  $pro_settings['invoice_surname']=$data['invoice_surname'];
                  $save_data = $migareference->updatePropertysettings($pro_settings,$data['migareference_invoice_settings_id']);
                  // Save earnings if Property Sold
                  if ($data['standard_type']==3  && $data['reward_type']==1) {
                    $earning['app_id']=$data['app_id'];
                    $earning['value_id']=$data['value_id'];
                    $earning['refferral_user_id']=$data['referral_user_id'];
                    $earning['sold_user_id']=$_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $earning['report_id']=$data['migareference_report_id'];
                    $earning['earn_amount'] = ($data['commission_fee_report']>1) ? $data['commission_fee_report'] : $data['commission_fee'] ; ;
                    $earning['platform']="Owner End";
                    if ($data['commission_fee_report']<1 && $data['commission_fee']<1) {
                          throw new Exception(__("First you must Mandate Acquired to make it Payable."));
                    }
                    $migareference->saveEarning($earning);                    
                  }elseif($data['standard_type']==3 && $data['reward_type']==2) {
                    $earning['app_id']           = $data['app_id'];
                    $earning['user_id']          = $data['referral_user_id'];
                    $earning['amount']           = ($data['commission_fee_report']>1) ? $data['commission_fee_report'] : $data['commission_fee'];
                    $earning['entry_type']       = 'C';
                    $earning['trsansection_by']  = $_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $earning['prize_id']         = 0;
                    $earning['report_id']        = $data['migareference_report_id'];
                    $earning['trsansection_description'] ="Report #".$previous_item[0]['report_no'];
                    $migareference->saveLedger($earning);
                  }
              }
              $payload = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0,
                  'messa'  => $eventtemplats
              ];									
			} catch (\Exception $e) {
					$payload = [
							'success' => false,
							'message' => __($e->getMessage())							
					];
			}
			$this->_sendJson($payload);
		}
	public function reportformbuilderAction() {
    try{
	 $app_id = $this->getRequest()->getParam('app_id');
	 $report_id = $this->getRequest()->getParam('report_id');		
   $migareference = new Migareference_Model_Migareference();
   $status        = $migareference->getReportStatus($app_id);
   $status        = $migareference->templateStatus($status,1);
   $edititem      = $migareference->getReportItem($app_id,$report_id);
   $destPath      = Core_Model_Directory::getBasePathTo();
   $platform_url  = explode("/",$destPath);//index 4 have platform url
   $edititem[0]['block_chain_icon']     = "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/certificate.png";
   $edititem[0]['created_at']           = date('Y-m-d', strtotime($edititem[0]['created_at']));
   $edititem[0]['last_modification_at'] = date('Y-m-d', strtotime($edititem[0]['last_modification_at']));
   $edititem[0]['status_icon_db_file']  = $edititem[0]['status_icon'];
   $field_data_values                   = unserialize( $edititem[0]['extra_dynamic_fields']);
   $field_data                          = unserialize( $edititem[0]['extra_dynamic_field_settings']);
   $field_data                          = $migareference->getreportfield($app_id);
   // START:Dynamic Filed Logic
   $static_header_start='<div class="form-group"><div class="col-sm-4"><label for="">';
   $static_header_closer=' *</label></div><div class="col-sm-12">';
   $static_footer='</div></div>';
           $report_status=$static_header_start;
           $report_status.= __('Report Status');
           $report_status.=$static_header_closer;
           $report_status.='<select id="report_status" onChange="chnagestatus(this)" class="input--style-1" name="report_status" >';
                 foreach ($status as $key => $value):
                   $selected = ($value['migareference_report_status_id']==$edititem[0]['currunt_report_status']) ? "selected" : "" ;
                   $report_status.='<option '.$selected.' value='.$value["migareference_report_status_id"].'>'.$value["status_title"].'</option>';
                 endforeach;
               $report_status.='</select>';
           $report_status.=$static_footer;
     if ($edititem[0]['commission_type']==1 || $edititem[0]['commission_type']==3) {
       $commission_fee_action="";
       if ($edititem[0]['commission_fee']>0) {
         $com_value=$edititem[0]['commission_fee'];
         $commission_fee_action='disabled';
       }else if ($edititem[0]['is_acquired']==1) {
         $com_value=$edititem[0]['commission_fee'];
       }else {
         $com_value=$edititem[0]['commission_fee'];
         $commission_fee_action='disabled';
       }
     }else {
       $com_value=$edititem[0]['commission_fee'];
       $commission_fee_action='disabled';
     }
      $report_credits=$static_header_start;
      $report_credits.= ($edititem[0]['reward_type']==1) ? __("Commission Fee") : __("Credits") ;
      $report_credits.=$static_header_closer;
      $report_credits.='<input type="text" '.$commission_fee_action.' name="commission_fee" id="commission_fee" value='.$com_value.' class="input--style-1" >';
      $report_credits.=$static_footer;
      $display = ($edititem[0]['is_comment']==1 || $edititem[0]['status_title']=="Declinato/Non Venduto" ) ? "" : "none" ;
      $declined_comment='<div style=display:'.$display.' class="form-group" id="comment-container-text"><div class="col-sm-4"><label for="">';
      $declined_comment.= __("Comment");
      $declined_comment.=$static_header_closer;
      $declined_comment.='<textarea  class="pin input--style-1" id="comment_text" name="comment" rows="4" cols="80">'.$edititem[0]['comment'].'</textarea>';
      $declined_comment.='<input id="is_comment_flag" type="hidden" name="is_comment" value='.$edititem[0]['is_comment'].'>';
      $declined_comment.=$static_footer;
      $ref_name=$static_header_start;
      $ref_name.= __("Referral Name");
      $ref_name.=$static_header_closer;
      $ref_name.='<input type="text"  name="invoice_name" id="invoice_name" value='.$edititem[0]['invoice_name'].' class="input--style-1" >';
      $ref_name.=$static_footer;
      $ref_surname=$static_header_start;
      $ref_surname.= __("Referral Sur Name");
      $ref_surname.=$static_header_closer;
      $ref_surname.='<input type="text"  name="invoice_surname" id="invoice_surname" value='.$edititem[0]['invoice_surname'].' class="input--style-1" >';
      $ref_surname.=$static_footer;
      $ref_mobile=$static_header_start;
      $ref_mobile.= __("Referral Mobile");
      $ref_mobile.=$static_header_closer;
      $ref_mobile.='<input type="text"  name="invoice_mobile" id="invoice_mobile" value='.$edititem[0]['invoice_mobile'].' class="input--style-1" >';
      $ref_mobile.=$static_footer;
      $created_at=$static_header_start;
      $created_at.= __("Created at");
      $created_at.=$static_header_closer;
      $created_at.='<input type="text" disabled name="created_at" id="created_at" value='.$edititem[0]['report_created_at'].' class="input--style-1" >';
      $created_at.=$static_footer;
      $last_modification=$static_header_start;
      $last_modification.= __("Last Modification at");
      $last_modification.=$static_header_closer;
      $last_modification.='<input type="text" disabled name="last_modification_at" id="last_modification_at" value='.$edititem[0]['last_modification_at'].' class="input--style-1" >';
      $last_modification.=$static_footer;
     // Dynamic Fields
     $static_fields[1]['name']="property_type";
     $static_fields[2]['name']="sales_expectations";
     $static_fields[3]['name']="address";
     $static_fields[4]['name']="owner_name";
     $static_fields[5]['name']="owner_surname";
     $static_fields[6]['name']="owner_mobile";
     $static_fields[7]['name']="note";
     $field=$report_status;
     $field.=$declined_comment;
     $field.=$report_credits;
     $country_id=0;
     foreach ($field_data as $key => $value) {
       $display=($value['is_visible']==1) ? "" : "none" ;
       $required = ($value['is_required']==1) ? "*" : "" ;
       if ($value['type']==1) {
             $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
             $field.='<label for='.$static_fields[$value['field_type_count']]['name'].'>'.__($value['label']).' '.$required.' </label>';
             $field.='</div><div class="col-sm-12" >';
             $name=$static_fields[$value['field_type_count']]['name'];
             $field_value = (!empty($edititem[0][$name])) ? $edititem[0][$name] : "" ;
             $longitude=$edititem[0]['longitude'];
             $latitude=$edititem[0]['latitude'];
             $field.=$this->manageinputypevalueAction($app_id,$value['field_type'],$name,$value['field_option'],0,$field_value,$longitude,$latitude,$value['option_type'],$value['default_option_value'],0);
       }else {
         $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
         $name="extra_".$value['field_type_count'];
         $field.='<label for='.$name.'>'.__($value['label']).' '.$required.'</label>';
         $field.='</div><div class="col-sm-12" >';
         $field_value = (!empty($field_data_values[$name])) ? $field_data_values[$name] : "" ;
         $longitude=$field_data_values[0]['longitude_'.$value['field_type_count']];
         $latitude=$field_data_values[0]['latitude_'.$value['field_type_count']];
         if ($value['options_type']==1) {
           $country_id=$field_value;
         }
         $field.=$this->manageinputypevalueAction($app_id,$value['field_type'],$name,$value['field_option'],$value['field_type_count'],$field_value,$longitude,$latitude,$value['options_type'],$value['default_option_value'],$country_id);
       }
     }
     $field.=$ref_name;
     $field.=$ref_surname;
     $field.=$ref_mobile;
    //  $field.=$created_at;
     $field.=$last_modification;
   // END:Dynamic Filed Logic
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
              'message_timeout' => 2,
              'form_builder' => $field,
              'item' => $edititem,
              'other'=>$siberian_usrs,
              'osther'=>$app_id
          ];
    } catch (\Exception $e) {
        $payload = [
            'error' => true,
            'message' => __($e->getMessage())
        ];
    }
    $this->_sendJson($payload);
  }
   public function manageinputypevalueAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$field_value="",$longitude="",$latitude="",$option_type=0,$option_default="",$country_id=0)
  {
    $extra_input_template="";
    $migareference  = new Migareference_Model_Migareference();
    if ($type==1) {
      $extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input--style-1" placeholder="" /></div></div>';
    } else if($type==2) {
      $extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input--style-1" placeholder="" /></div></div>';
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
        $temp_options=explode('@',$options);
        $extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1" name="'.$ng_model.'">';
        foreach ($temp_options as $key => $value) {
          $option_value++;
          $selected = ($option_value==$field_value) ? "selected" : "" ;
          $extra_input_template.="<option ".$selected." value='".$option_value."@".$option_type."@".$option_default."'>".__($value)."</option>";
        }
        $extra_input_template.="</select></div></div>";
          break;
        case 1://Country List
          $geoCountries              = $migareference->getGeoCountries($app_id);
          // $df_opt=explode("@",$option_default);
          $extra_input_template.='<select onChange=loadProvicnes(0,1,0) id="'.$ng_model.'"  class="input--style-1 country_default" name="'.$ng_model.'">';
          foreach ($geoCountries as $key => $value) {
            if ($field_value!=$value['migareference_geo_countries_id']) {
              $extra_input_template.="<option value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
            }else {
              $extra_input_template.="<option selected value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
            }
          }
          $extra_input_template.="</select></div></div>";
          break;
        case 2:
        // $df_opt=explode("@",$option_default);
        $dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$country_id);
        $extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1 province_default" name="'.$ng_model.'">';
        foreach ($dataGeoConPro as $key => $value) {
          if ($field_value!=$value['migareference_geo_provinces_id']) {
            $extra_input_template.="<option value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
          }else {
            $extra_input_template.="<option selected value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
          }
        }
        $extra_input_template.="</select></div></div>";
          break;
        default:
          // $extra_input_template.="<option value=''></option>";
          break;
      }
    }else if($type==4) {
      $latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
      $extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="'.$field_value.'" class="input--style-1" placeholder="Google Location" />';
      $extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value='".$longitude."'>";
      $extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value='".$latitude."'> </div></div>";
    }else if($type==5) {
      $extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input--style-1">'.$field_value.'</textarea></div></div>';
    }else if($type==6) {
      $extra_input_template.='<input type="date"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input--style-1" placeholder="" /></div></div>';
    }
    return $extra_input_template;
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
  public function randomPassword() {
	$alphabet = "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 10; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
// public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$option_type=0,$option_default="")
// {
// 	$extra_input_template="";
// 	$migareference  = new Migareference_Model_Migareference();
// 	if ($type==1) {
// 		$extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
// 	} else if($type==2) {
// 		$extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
// 	}else if($type==3) {
// 		$option_value=0;
// 		switch ($option_type) {
// 			case 0:
// 				$temp_options=explode('@',$options);
// 				$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1" name="'.$ng_model.'">';
// 				foreach ($temp_options as $key => $value) {
// 					$option_value++;
// 					$extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
// 				}
// 				$extra_input_template.="</select></div></div>";
// 				break;
// 			case 1://Country List
// 				$geoCountries              = $migareference->getGeoCountries($app_id);
// 				$df_opt=explode("@",$option_default);
// 				$extra_input_template.='<select onChange=loadProvicnes(0,1) id="'.$ng_model.'"  class="input--style-1 country_default" name="'.$ng_model.'">';
// 				foreach ($geoCountries as $key => $value) {
// 					if ($df_opt[0]!=$value['migareference_geo_countries_id']) {
// 						$extra_input_template.="<option value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
// 					}else {
// 						$extra_input_template.="<option selected value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
// 					}
// 				}
// 				$extra_input_template.="</select></div></div>";
// 				break;
// 			case 2:
// 			$df_opt=explode("@",$option_default);
// 			$dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$option_default[0]);
// 			$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1 province_default" name="'.$ng_model.'">';
// 			foreach ($dataGeoConPro as $key => $value) {
// 				if ($df_opt[1]!=$value['migareference_geo_provinces_id']) {
// 					$extra_input_template.="<option value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
// 				}else {
// 					$extra_input_template.="<option selected value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
// 				}
// 			}
// 			$extra_input_template.="</select></div></div>";
// 				break;
// 			default:
// 				$extra_input_template.="<option value=''></option>";
// 				break;
// 		}
//
// 	}else if($type==4) {
// 		$latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
// 		$extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="" class="input--style-1" placeholder="Google Location" />';
// 		$extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value=''>";
// 		$extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value=''> </div></div>";
// 	}else if($type==5) {
// 		$extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input--style-1"></textarea></div></div>';
// 	}
// 	return $extra_input_template;
// }
	public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$option_type=0,$option_default=""){
		$extra_input_template="";
		$migareference  = new Migareference_Model_Migareference();
		if ($type==1) {
			$extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
		} else if($type==2) {
			$extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
		}else if($type==3) {
			$option_value=0;
			switch ($option_type) {
			case 0:
			$temp_options=explode('@',$options);
			$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1" name="'.$ng_model.'">';
			foreach ($temp_options as $key => $value) {
				$option_value++;
				$extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
			}
			$extra_input_template.="</select></div></div>";
				break;
			case 1://Country List
				$geoCountries              = $migareference->getGeoCountries($app_id);
				$df_opt=explode("@",$option_default);
				$extra_input_template.='<select onChange=loadProvicnes(0,1) id="'.$ng_model.'"  class="input--style-1 country_default"  name="'.$ng_model.'">';
				foreach ($geoCountries as $key => $value) {
					if ($df_opt[0]!=$value['migareference_geo_countries_id']) {
						$extra_input_template.="<option value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
					}else {
						$extra_input_template.="<option selected value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
					}
				}
				$extra_input_template.="</select></div></div>";
				break;
			case 2:
			$df_opt=explode("@",$option_default);
			$dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$df_opt[0]);
			$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1 default_province"  name="'.$ng_model.'">';
			foreach ($dataGeoConPro as $key => $value) {
				if ($df_opt[1]!=$value['migareference_geo_provinces_id']) {
					$extra_input_template.="<option value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
				}else {
					$extra_input_template.="<option selected value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
				}
			}
			$extra_input_template.="</select></div></div>";
				break;
			default:
				$extra_input_template.="<option value=''></option>";
				break;
		}
		}else if($type==4) {
			$latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
			$extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="" class="input--style-1" placeholder="Google Location" />';
			$extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value=''>";
			$extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value=''> </div></div>";
		}else if($type==5) {
			$extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input--style-1"></textarea></div></div>';
		}
		return $extra_input_template;
	}
}
