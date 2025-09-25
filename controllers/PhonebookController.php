<?php
/**
 * Class Migareference_PhonebookController
 */
class Migareference_PhonebookController extends Application_Controller_Default{

    public function viewAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function getprovincelistAction() {
        try {
          $migareference = new Migareference_Model_Migareference();
          $country_id    = $this->getRequest()->getParam('param1');          
          $app_id        = $this->getApplication()->getId();          
          $dataGeoConPro = $migareference->getGeoCountryProvicnes($app_id,$country_id);            
          
          $extra_input_template="<option value=''></option>";                
            foreach ($dataGeoConPro as $key => $value) {
                $selected = ($field_value!=$value['migareference_geo_provinces_id']) ? '' : 'selected' ;                
                 $extra_input_template.="<option ".$selected." value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";                
            }            
            $payload = [
                "data" => $extra_input_template                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function referrerlistAction() {
    if ($data = $this->getRequest()->getQuery()) {
        try {
          $migareference     = new Migareference_Model_Migareference();            
          $app_id=$data['app_id'];
          $query_join='';
          if($data['rpovince_key']>0){
            $query_join.=' AND mis.address_province_id='.$data['rpovince_key'];
          }
          if($data['agent_key']>0){
            $query_join.=' AND (refag_one.agent_id='.$data['agent_key'].' OR refag_two.agent_id='.$data['agent_key'].')';
          }else if($data['agent_key']==0){
            $query_join.=' AND (refag_one.agent_id IS NULL AND refag_two.agent_id IS NULL)';
          }
          $prospect_jobs     = $migareference->get_opt_referral_users($app_id,$query_join);
          $referrer_collection=[];
          foreach ($prospect_jobs as $key => $value) { 
            // Check if both sponsor one and sponsor two IDs are not NULL
            if ($value['sponsor_one_id'] != NULL && $value['sponsor_two_id'] != NULL) {
              $agent = $value['sponsor_one_lastname'] . " " . $value['sponsor_one_firstname'] . " & " . $value['sponsor_two_lastname'] . " " . $value['sponsor_two_firstname'];
              $sponsor_one=$value['sponsor_one_id']; 
              $sponsor_two=$value['sponsor_two_id']; 
            } 
            // Check if only sponsor one ID is not NULL
            elseif ($value['sponsor_one_id'] != NULL) {
              $sponsor_one=$value['sponsor_one_id']; 
              $agent = $value['sponsor_one_lastname'] . " " . $value['sponsor_one_firstname'];
            } 
            // Check if only sponsor two ID is not NULL
            elseif ($value['sponsor_two_id'] != NULL) {
              $sponsor_two=$value['sponsor_two_id']; 
              $agent = $value['sponsor_two_lastname'] . " " . $value['sponsor_two_firstname'];
            } 
            // If both sponsor one and sponsor two IDs are NULL
            else {
              $sponsor_one=0;             
              $sponsor_two=0;  
              $agent = __("Not Found");
            }  
            $multi_selection='<input type="checkbox" onchange="referrerSelection(this.value)" name="referrerCheckbox[]" value="' . $value['migareference_invoice_settings_id'] . '">';
            $referrer_collection[]=[
                                    $value['user_id'],
                                    $multi_selection,                                    
                                    $value['lastname']." ".$value['firstname'],
                                    $value['email'],
                                    $agent
                                ];     
                }              
            $payload = [
                "data" => $referrer_collection                
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
  public function getphonebookitemAction(){
   $param           = $this->getRequest()->getParam('param1');
   $referrer_id     = $this->getRequest()->getParam('referrer_id');
   $type            = $this->getRequest()->getParam('type');
   $app_id          = $this->getApplication()->getId();
   $migareference   = new Migareference_Model_Migareference();
   $pre_settings  = $migareference->preReportsettigns($app_id);
   $phonebookitem   = $migareference->getSinglePhonebook($param);
   $gdpr_consent    = '';
   $default         = new Core_Model_Default();
   $base_url        = $default->getBaseUrl();
   $app_icon_path   = $base_url."/app/local/modules/Migareference/resources/appicons/";
   $phonebookitem[0]['enable_sub_address']=$pre_settings[0]['enable_sub_address'];
   if ($type==1) {
      // getReferrerAgents
      $get_referrer_agents=$migareference->getReferrerAgents($app_id,$referrer_id);
      $agent_count=COUNT($get_referrer_agents);
      $phonebookitem[0]['customer_sponsor_id']=0;
      $phonebookitem[0]['partner_sponsor_id']=0;
      if($agent_count==1){
        $phonebookitem[0]['customer_sponsor_id']=$get_referrer_agents[0]['agent_id'];
        $phonebookitem[0]['partner_sponsor_id']=0;
      }else if($agent_count==2){
        $phonebookitem[0]['customer_sponsor_id']=$get_referrer_agents[0]['agent_id'];
        $phonebookitem[0]['partner_sponsor_id']=$get_referrer_agents[1]['agent_id'];
      }
     $phonebookitem[0]['birth_date']    = (!empty($phonebookitem[0]['birthdate'])) ? date("Y-m-d", $phonebookitem[0]['birthdate']) : '' ;     
     $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
     // Referrer Consent Description
     if ($phonebookitem[0]['privacy_policy']==1) {
       $gdpr_consent="<div class='col-sm-2'>";
       $gdpr_consent.="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
       $gdpr_consent.="</div>";
       $gdpr_consent.="<div class='col-sm-10'>";
       $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['customer_consent_date']."-".__("APP User")."</p>";
       $gdpr_consent.="</div>";
       $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
     }else {
       $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
       $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
     }
   }else {
     // Prospect Consent Description
     if ($phonebookitem[0]['consent_timestmp']!=NULL) {
       $gdpr_consent="<div class='col-sm-2'>";
       $gdpr_consent.="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
       $gdpr_consent.="</div>";
       $gdpr_consent.="<div class='col-sm-10'>";
       $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['consent_timestmp']."-".$phonebookitem[0]['consent_source']."-".$phonebookitem[0]['consent_ip']."</p>";
       $gdpr_consent.="</div>";
       $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
     }else {
       $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
       $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
     }
   }
   $logitem=$migareference->getLatCommunication($phonebookitem[0]['migarefrence_phonebook_id']);
    $lastcontactdate = (!empty($logitem[0]['created_at'])) ? date('d-m-Y',strtotime($logitem[0]['created_at'])) : date('d-m-Y',strtotime($phonebookitem[0]['phone_creat_date'])) ;
    $now = time();
    $itemDate = strtotime($lastcontactdate);
    $datediff =  $now-$itemDate;
    $datediff=round($datediff / (60 * 60 * 24));
    $phonebookitem[0]['lastcontact']="-".($datediff);
   header('Content-type:application/json');
   $responsedata = json_encode($phonebookitem);
   print_r($responsedata);
   exit;
  }
  public function getprospectitemAction(){
    $migareference   = new Migareference_Model_Migareference();
    $default         = new Core_Model_Default();
    $base_url        = $default->getBaseUrl();
    $app_id          = $this->getApplication()->getId();

    $prospect_id     = $this->getRequest()->getParam('prospect_id');   
    $prospectitem    = $migareference->getProspectItem($app_id,$prospect_id);

    $gdpr_consent    = '';
    $app_icon_path   = $base_url."/app/local/modules/Migareference/resources/appicons/";
    // Consent Container
      $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
      if ($prospectitem[0]['gdpr_consent_timestamp']!=NULL) {
       $gdpr_consent="<div class='col-sm-2'>";
       $gdpr_consent.="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
       $gdpr_consent.="</div>";
       $gdpr_consent.="<div class='col-sm-10'>";
       $gdpr_consent.="<p>".__("Date Stamp")." ".$prospectitem[0]['gdpr_consent_timestamp']."-".$prospectitem[0]['gdpr_consent_source']."-".$prospectitem[0]['gdpr_consent_ip']."</p>";
       $gdpr_consent.="</div>";       
     }
    $prospectitem[0]['gdpr_consent'] = $gdpr_consent;
  //  if ($type==1) {
  //    $phonebookitem[0]['birth_date']    = (!empty($phonebookitem[0]['birthdate'])) ? date("Y-m-d", $phonebookitem[0]['birthdate']) : '' ;
  //    $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
  //    // Referrer Consent Description
  //    if ($phonebookitem[0]['privacy_policy']==1) {
  //      $gdpr_consent="<div class='col-sm-2'>";
  //      $gdpr_consent.="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
  //      $gdpr_consent.="</div>";
  //      $gdpr_consent.="<div class='col-sm-10'>";
  //      $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['customer_consent_date']."-".__("APP User")."</p>";
  //      $gdpr_consent.="</div>";
  //      $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
  //    }else {
  //      $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
  //      $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
  //    }
  //  }else {
  //    // Prospect Consent Description
  //    if ($phonebookitem[0]['consent_timestmp']!=NULL) {
  //      $gdpr_consent="<div class='col-sm-2'>";
  //      $gdpr_consent.="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
  //      $gdpr_consent.="</div>";
  //      $gdpr_consent.="<div class='col-sm-10'>";
  //      $gdpr_consent.="<p>".__("Date Stamp")." ".$phonebookitem[0]['consent_timestmp']."-".$phonebookitem[0]['consent_source']."-".$phonebookitem[0]['consent_ip']."</p>";
  //      $gdpr_consent.="</div>";
  //      $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
  //    }else {
  //      $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'no_gdpr.png'."'>";
  //      $phonebookitem[0]['gdpr_consent']  = $gdpr_consent;
  //    }
  //  }
    $logitem=$migareference->getLatCommunication($prospectitem[0]['migarefrence_prospect_id']);
    $lastcontactdate = (!empty($logitem[0]['created_at'])) ? date('d-m-Y',strtotime($logitem[0]['created_at'])) : date('d-m-Y',strtotime($prospectitem[0]['phone_creat_date'])) ;
    $now = time();
    $itemDate = strtotime($lastcontactdate);
    $datediff =  $now-$itemDate;
    $datediff=round($datediff / (60 * 60 * 24));
    $prospectitem[0]['lastcontact']="-".($datediff);
    header('Content-type:application/json');
    $responsedata = json_encode($prospectitem);
    print_r($responsedata);
    exit;
  }
  public function updatephonebookAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {                  
                  $errors = "";
                  $app_id=$data['app_id'];
                  $migareference = new Migareference_Model_Migareference();
                  $pre_settings  = $migareference->preReportsettigns($app_id);
                  $default       = new Core_Model_Default();
                  $base_url      = $default->getBaseUrl();
                  $app_link      = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                  $birth_date    = 0;
                  if (empty($data['name'])){
                    $errors .= __('Please add a valid Name.') . "<br/>";
                  }
                  if (empty($data['surname'])){
                    $errors .= __('Please add a valid Surname.') . "<br/>";
                  }
                  if ($data['type']==1) {                    
                    if(empty($data['password']) && $data['operation']=='create'){
                      $errors .= __('Please add a valid Password.') . "<br/>";
                    }
                    if(empty($data['email'])){
                      $errors .= __('Please add a valid Email.').$data['email'] . "<br/>";
                    }else{
                      $customer=$migareference->getCustomer($app_id,$data['email']);
                      $user_id=$customer[0]['customer_id'];
                    }
                  }
                  if (strlen($data['mobile']) < 10
                  || strlen($data['mobile']) > 15
                  || empty($data['mobile'])
                  || preg_match('@[a-z]@', $data['mobile'])
                  || (
                      substr($data['mobile'], 0, 1)!='+'
                   && substr($data['mobile'], 0, 2)!='00')
                      ){
                    $errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
                  }
                  if (!empty($data['mobile'])) {
                      $data['mobile'] = str_replace(' ', '', $data['mobile']);
                      $phone_email_exist=$migareference->isPhoneEmailExist($data['app_id'],$data['email'],$data['mobile'],$data['type']);
                      if ($data['operation']=='update' && count($phone_email_exist) && $phone_email_exist[0]['migarefrence_phonebook_id']!=$data['migarefrence_phonebook_id']) {
                        $errors .= __('Mobile already exist.') .$phone_email_exist[0]['migarefrence_phonebook_id']. "<br/>";
                      }
                    }
                  if (!empty($errors)) {
                      throw new Exception($errors);
                  } else {
                    $password = $data['password'];
                    if(!empty($data['birth_date'])){                      
                      $birth_date = date('Y-m-d',strtotime($data['birth_date']));
                      $birth_date = strtotime($birth_date);
                    }
                    // // Manage Agent keys while agent type is geolocation
                    // $data['partner_sponsor_id']=0;
                    // if ($pre_settings[0]['sponsor_type']==2) {                          
                    //   $agent_provonces=$migareference->agentMultiGeoProvince($data['app_id'],$data['address_province']);                      
                    //   $agent_count=COUNT($agent_provonces);
                    //   $data['sponsor_id']=0;                                                             
                    //   if($agent_count==1){
                    //     $data['sponsor_id']=$agent_provonces[0]['user_id'];                        
                    //   }else if($agent_count==2){
                    //     $data['sponsor_id']=$agent_provonces[0]['user_id'];
                    //     $data['partner_sponsor_id']=$agent_provonces[1]['user_id'];
                    //   }
                    // }     
                    
                    /******Create Operation *******/
                    if ($data['operation']=='create') {
                      if (!count($phone_email_exist)) {
                        // Create New Customer if already not exist
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
                          $inv_settings['app_id']=$app_id;
                          $inv_settings['user_id']=$user_id;
                          $inv_settings['blockchain_password']=$this->randomPassword(10);
                          $inv_settings['first_password']=$data['password'];
                          $inv_settings['invoice_name']=$data['name'];
                          $inv_settings['profession_id']=$data['profession_id'];
                          $inv_settings['job_id']=$data['job_id'];
                          $inv_settings['sponsor_id']=$data['customer_sponsor_id'];
                          $inv_settings['note']=$data['note'];
                          $inv_settings['reciprocity_notes']=$data['reciprocity_notes'];
                          $inv_settings['partner_sponsor_id']=$data['partner_sponsor_id'];
                          $inv_settings['invoice_surname']=$data['surname'];
                          $inv_settings['invoice_mobile']=$data['mobile'];
                          $inv_settings['tax_id']=$this->randomTaxid();
                          $inv_settings['terms_accepted']=1;
                          $inv_settings['special_terms_accepted']=1;
                          $inv_settings['privacy_accepted']=1;
                          $inv_settings['address_country_id']=$data['address_country'];
                          $inv_settings['address_province_id']=$data['address_province'];
                          $inv_settings['address_city']=$data['address_city'];
                          $inv_settings['address_zipcode']=$data['address_zipcode'];
                          $inv_settings['address_street']=$data['address_street'];  
                          //This method also save phonebook entry if previously not exist                                                                                                                               
                          $inv_data=$migareference->savePropertysettings($inv_settings);
                      
                          // Send Welcome Email to referrer
                        if ($pre_settings[0]['enable_welcome_email']==1
                            && !empty($pre_settings[0]['referrer_wellcome_email_title'])
                            && !empty($pre_settings[0]['referrer_wellcome_email_body']))
                          {
                          $notificationTags=$migareference->welcomeEmailTags();
                          if (isset($data['customer_sponsor_id']) && !empty($data['customer_sponsor_id']) ) {
                            $agent_user=$migareference->getSingleuser($app_id,$data['customer_sponsor_id']);
                          }
                          $notificationStrings = [
                            $customer['firstname']." ".$customer['lastname'],
                            $customer['email'],
                            $data['password'],
                            $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                            $app_link
                          ];
                          $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_title']);
                          $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_body']);
                          $email_data['type']        = 2;//type 2 for wellcome log
                          $migareference->sendMail($email_data,$app_id,$user_id);
                        }                        
                        // Send Welcome PUSH to referrer
                        $welcome_push = (new Migareference_Model_Welcomenotificationtemplate())->findAll(['app_id' => $app_id])->toArray(); 
                        if ($welcome_push[0]['welcome_push_enable']==1 && !empty($welcome_push[0]['welcome_push_title']) && !empty($welcome_push[0]['welcome_push_text']))
                          {
                          $notificationTags=$migareference->welcomeEmailTags();
                          if (isset($data['customer_sponsor_id']) && !empty($data['customer_sponsor_id']) ) {
                            $agent_user=$migareference->getSingleuser($app_id,$data['customer_sponsor_id']);
                          }
                          $notificationStrings = [
                            $customer['firstname']." ".$customer['lastname'],
                            $customer['push'],
                            $data['password'],
                            $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                            $app_link
                          ];
                          $push_data['push_title'] = str_replace($notificationTags, $notificationStrings,$welcome_push[0]['welcome_push_title']);
                          $push_data['push_text']  = str_replace($notificationTags, $notificationStrings,$welcome_push[0]['welcome_push_text']);                          
                          $push_data['open_feature'] = $notification_data['welcome_push_open_feature'];
                          $push_data['feature_id']   = $notification_data['welcome_push_feature_id'];
                          $push_data['custom_url']   = $notification_data['welcome_push_custom_url'];
                          $push_data['cover_image']  = $notification_data['welcome_push_custom_file'];
                          $push_data['app_id']       = $app_id;
                          $migareference->sendPush($push_data,$app_id,$user_id);
                        }                        
                      }else {                         
                          $phonebook["name"]=$data['name'];
                          $phonebook["surname"]=$data['surname'];
                          $phonebook["email"]=$data['email'];
                          $phonebook["note"]=$data['note'];
                          $phonebook["reciprocity_notes"]=$data['reciprocity_notes'];
                          $phonebook['rating']=$data['rating'];
                          $phonebook['job_id']=$data['job_id'];
                          $phonebook['mobile']=$data['mobile'];
                          $change_by=$_SESSION['front']['object_id'];
                          $migareference->update_phonebook($phonebook,$phone_email_exist[0]['migarefrence_phonebook_id'],$change_by,2);//Also save log if their is change in Rating,Job,Notes
                      }
                    }else { //Update Referrer
                      $referrer_previous_data=$migareference->getpropertysettings($app_id,$data['user_id']);                        
                      $invoice_item=$migareference->getReferrerByKey($data['phonebook_invoice_id']);                        
                      if ($data['type']==1) {                        
                        $invoice_data['invoice_name']=$data['name'];
                        $invoice_data['invoice_surname']=$data['surname'];
                        $invoice_data['invoice_mobile']=$data['mobile'];
                        $invoice_data['address_country_id']=$data['address_country'];
                        $invoice_data['address_street']=$data['address_street'];
                        $invoice_data['address_zipcode']=$data['address_zipcode'];
                        $invoice_data['address_city']=$data['address_city'];
                        $invoice_data['address_province_id']=$data['address_province'];
                        $migareference->updatePropertysettings($invoice_data,$data['phonebook_invoice_id']);
                        $customerdata['birthdate']= $birth_date;                        
                        $migareference->updateCustomerdob($data['user_id'],$customerdata);
                      }      
                      // Manage Agents        
                      $migareference->deleteSponsor($data['user_id']);                          
                      $referrer_agent['app_id']=$app_id;
                      $referrer_agent['referrer_id']=$data['user_id'];
                      $referrer_agent['created_at']=date('Y-m-d H:i:s');                      
                     if ($pre_settings[0]['enable_multi_agent_selection']==1) {                                                                           
                          $referrer_agent['agent_id']= (isset($data['customer_sponsor_id'])) ? $data['customer_sponsor_id'] : 0 ;
                          if ($referrer_agent['agent_id']!=0) {
                            $migareference->addSponsor($referrer_agent);
                          }                
                          $referrer_agent['agent_id']= (isset($data['partner_sponsor_id'])) ? $data['partner_sponsor_id'] : 0 ;
                          if ($referrer_agent['agent_id']!=0) {
                            $migareference->addSponsor($referrer_agent);        
                          }                                                     
                      }else{                        
                        $referrer_agent['agent_id']= (isset($data['customer_sponsor_id'])) ? $data['customer_sponsor_id'] : 0 ;
                          if ($referrer_agent['agent_id']!=0) {
                            $migareference->addSponsor($referrer_agent);
                          } 
                      }           
                             
                      $phonebook["name"]=$data['name'];
                      $phonebook["surname"]=$data['surname'];
                      $phonebook["email"]=$data['email'];
                      $phonebook["note"]=$data['note'];
                      $phonebook["reciprocity_notes"]=$data['reciprocity_notes'];
                      $phonebook['rating']=$data['rating'];
                      $phonebook['job_id']=$data['job_id'];
                      $phonebook['profession_id']=$data['profession_id'];
                      $phonebook['mobile']=$data['mobile'];
                      $change_by=$_SESSION['front']['object_id'];
                      $migareference->update_phonebook($phonebook,$data['migarefrence_phonebook_id'],$change_by,2);//Also save log if their is change in Rating,Job,Notes
                      // Trigger webhook if their is change in Referrer
                      $referrer_new_data=$migareference->getpropertysettings($app_id,$data['user_id']);                        
                      //This will detect particular changes and trigger webhook if changes found                                                                                 
                      $changes_detect=(new Migareference_Model_Utilities())->detectReferrerChanges($referrer_previous_data,$referrer_new_data,$app_id);                                           
                      
                    }
                  }
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                
                'inv_data'  => $inv_data,                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'changes_detect' => $changes_detect,
                'referrer_new_data' => $referrer_new_data,
                'referrer_matching' => $referrer_matching,
                'referrer_previous_data' => $referrer_previous_data                
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function updateprospectAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
                  $migareference = new Migareference_Model_Migareference();
                  $default       = new Core_Model_Default();
                  $base_url      = $default->getBaseUrl();

                  $errors        = "";
                  $app_id        = $data['app_id'];

                  if (empty($data['name'])){
                    $errors .= __('Please add a valid Name.') . "<br/>";
                  }
                  if (empty($data['surname'])){
                    $errors .= __('Please add a valid Surname.') . "<br/>";
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
                  }else{
                      $data['mobile'] = str_replace(' ', '', $data['mobile']);
                      $prospectitem    = $migareference->isProspectExist($app_id,$data['mobile']);                      
                      if (count($prospectitem) && $prospectitem[0]['migarefrence_prospect_id']!=$data['migarefrence_prospect_id']) {
                        $errors .= __('Prospect already exist.') .$phone_email_exist[0]['migarefrence_prospect_id']. "<br/>";
                      }
                    }
                  if (!empty($errors)) {
                      throw new Exception($errors);
                  } else {                                                                              
                      $prospect["name"]   = $data['name'];
                      $prospect["surname"]= $data['surname'];
                      $prospect['mobile'] = $data['mobile'];
                      $prospect["email"]  = $data['email'];
                      $prospect["note"]   = $data['note'];
                      $prospect['rating'] = $data['rating'];
                      $prospect['job_id'] = $data['job_id'];
                      $prospect['updated_at'] = date('Y-m-d H:i:s');
                      $change_by=$_SESSION['front']['object_id'];
                      $response=$migareference->update_prospect($prospect,$data['migarefrence_prospect_id'],$change_by,2);//Also save log if their is change in Rating,Job,Notes                    
                      $report_prospect["owner_name"]   = $data['name'];
                      $report_prospect["owner_surname"]= $data['surname'];
                      $report_prospect['owner_mobile'] = $data['mobile'];
                      $report_prospect['updated_at']   = date('Y-m-d H:i:s');
                      $migareference->updateReportProspect($report_prospect,$data['migarefrence_prospect_id']);
                  }
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'response'  => $response,
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'ddd' => $customer
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function refreshaimatchingAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id        = $this->getApplication()->getId();          
            $phonebook_id  = $this->getRequest()->getParam('phonebook_id');          
            $calling_method= 'refreshaimatching_mab';

            $response=$phonebook->referrerMatching($app_id,$phonebook_id,$calling_method);
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'response'  => $response,
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'response'  => $response,
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function getaimatchingdataAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $referrer_id  = $this->getRequest()->getParam('referrer_id');                      

            $available_matching = $phonebook->availableMatching($app_id,$referrer_id);
            $matched_matching   = $phonebook->matchedMatching($app_id,$referrer_id);
            $discard_matching   = $phonebook->discardMatching($app_id,$referrer_id);
            $last_matching_call = $phonebook->lastMatchingCall($app_id,$referrer_id);//last matching success call

            // Available Matchin
            // onclick="editProspectJob('.$value['migarefrence_phonebook_id'].',1,'.$value['terms_accepted'].','.$value['user_id'].','.$is_profiled_bol.')
            // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
            $available_matching_data=__("No data found");
            if (count($available_matching)) {
              $available_matching_data='<table class="table table-striped table-bordered table-hover">';
              $available_matching_data.="<tr>";
              $available_matching_data.="<th>".__("UID")."</th>";
              $available_matching_data.="<th>".__("Name")."</th>";
              $available_matching_data.="<th>".__("Email")."</th>";
              $available_matching_data.="<th>".__("Matching Description")."</th>";
              $available_matching_data.="<th>".__("Action")."</th>";
              $available_matching_data.="</tr>";
            }
            foreach ($available_matching as $key => $value) {
              $available_matching_data.="<tr>";
              $available_matching_data.="<td>".$value['customer_id']."</td>";
              $available_matching_data.="<td><u> <p  title='Phonebook' style='color:blue;cursor:pointer;' onclick='customerPhonebook(".$value['migarefrence_phonebook_id'].",1,".$value['terms_accepted'].",".$value['customer_id'].",1)' href='' >".$value['firstname']." ".$value['lastname']."</p></u></td>";
              $available_matching_data.="<td>".$value['email']."</td>";
              $available_matching_data.="<td>".$value['matching_description']."</td>";
              $available_matching_data.="<td><button class='btn btn-danger' onclick='discardCustomer(".$value['migareference_matching_network_id'].")'>".__("Discard")."</button>";
              $available_matching_data.="<button class='btn btn-primary' onclick='matchCustomer(".$value['migareference_matching_network_id'].")'>".__("Match")."</button></td>";
              $available_matching_data.="</tr>";
            }
            if (count($available_matching)) {
              $available_matching_data.='</table>';
            }

            // Matched Matching
            // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
            $matched_matching_data=__("No data found");
            if (count($matched_matching)) {
              $matched_matching_data='<table class="table table-striped table-bordered table-hover">';
              $matched_matching_data.="<tr>";
              $matched_matching_data.="<th>".__("UID")."</th>";
              $matched_matching_data.="<th>".__("Name")."</th>";
              $matched_matching_data.="<th>".__("Email")."</th>";
              $matched_matching_data.="<th>".__("Matching Description")."</th>";
              $matched_matching_data.="<th>".__("Action")."</th>";
              $matched_matching_data.="</tr>";
            }
            foreach ($matched_matching as $key => $value) {
              $matched_matching_data.="<tr>";
              $matched_matching_data.="<td>".$value['customer_id']."</td>";               
              $matched_matching_data.="<td><u> <p  title='Phonebook' style='color:blue;cursor:pointer;' onclick='customerPhonebook(".$value['migarefrence_phonebook_id'].",1,".$value['terms_accepted'].",".$value['customer_id'].",1)'' href='' >".$value['firstname']." ".$value['lastname']."</p></u></td>";
              $matched_matching_data.="<td>".$value['email']."</td>";
              $matched_matching_data.="<td>".$value['matching_description']."</td>";
              $matched_matching_data.="<td><button class='btn btn-danger' onclick='discardCustomer(".$value['migareference_matching_network_id'].")'>".__("Discard")."</button>
              <button class='btn btn-primary' onclick='unMatchCustomer(".$value['migareference_matching_network_id'].")'>".__("UnMatch")."</button></td>";
              $matched_matching_data.="</tr>";
            }
            if (count($matched_matching)) {
              $matched_matching_data.='</table>';
            }
            // Discard Referrers
            // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
            $discard_matching_data=__("No data found");
            if (count($discard_matching)) {
              $discard_matching_data='<table class="table table-striped table-bordered table-hover">';
              $discard_matching_data.="<tr>";
              $discard_matching_data.="<th>".__("UID")."</th>";
              $discard_matching_data.="<th>".__("Name")."</th>";
              $discard_matching_data.="<th>".__("Email")."</th>";
              $discard_matching_data.="<th>".__("Discarded At")."</th>";
              $discard_matching_data.="<th>".__("Action")."</th>";
              $discard_matching_data.="</tr>";
            }
            foreach ($discard_matching as $key => $value) {
              $discard_matching_data.="<tr>";
              $discard_matching_data.="<td>".$value['customer_id']."</td>";               
              $discard_matching_data.="<td><u> <p  title='Phonebook' style='color:blue;cursor:pointer;' onclick='customerPhonebook(".$value['migarefrence_phonebook_id'].",1,".$value['terms_accepted'].",".$value['customer_id'].",1)' href='' >".$value['firstname']." ".$value['lastname']."</p></u></td>";
              $discard_matching_data.="<td>".$value['email']."</td>";
              $discard_matching_data.="<td>".date('d-m-Y H:i:s',strtotime($value['matching_updated_at']))."</td>";
              $discard_matching_data.="<td>
              <button class='btn btn-danger' onclick='removeCustomer(".$value['migareference_matching_network_id'].")'>".__("Remove")."</button></td>";
              $discard_matching_data.="</tr>";
              // <button class='btn btn-primary' onclick='unMatchCustomer(".$value['migareference_matching_network_id'].")'>".__("Available")."</button>
            }
            if (count($discard_matching)) {
              $discard_matching_data.='</table>';
            }

            // Last Matching Call
            // we have to return lastmatching call date and time
            $last_matching_call_data="0000-00-00 00:00:00";
            if (count($last_matching_call)) {
              $last_matching_call_data=$last_matching_call[0]['created_at'];
            }
            $token_used=$last_matching_call[0]['token_used'];
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                
                'available_matching_data'  => $available_matching_data,
                'matched_data'=>$matched_matching_data,
                'discard_data'=>$discard_matching_data,
                'token_used'=>$token_used,
                'last_matching_call'=>$last_matching_call_data
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function matchcustomerAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->matchCustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function discardcustomerAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->discardCustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully discard.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function unmatchcustomerAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->unmatchcustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function removecustomerAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->removecustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
              ];
          }
          $this->_sendJson($html);
      }
  }
    function randomTaxid() {
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
  public function randomPassword($length=0) {
        $alphabet = "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
  ?>