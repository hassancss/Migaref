<?php
/**
 * Class Migareference_Public_CronController
 */
class Migareference_Public_OptinlandingController extends Migareference_Controller_Default {
   
  
  public function resolvesponsorbyemailAction()
  {
    try {
        $req = $this->getRequest();
        if (!$req->isPost()) {
            return $this->_sendJson(['success' => false, 'error' => 'Invalid method']);
        }

        $data   = $req->getPost();
        $app_id = isset($data['app_id']) ? (int)$data['app_id'] : 0;
        $email  = isset($data['email']) ? trim($data['email']) : '';

        if (!$app_id || $email === '') {
            return $this->_sendJson(['success' => false, 'error' => 'Missing app_id or email']);
        }
 

      $miga   = new Migareference_Model_Migareference();
      $rows = $miga->findAgentByEmail($app_id, $email);

      return $this->_sendJson([
          'success' => true,
          'source'  => 'agent',
          'count'   => is_array($rows) ? count($rows) : 0,
          'data'    => $rows,
      ]);
        
    } catch (Exception $e) {
        return $this->_sendJson(['success' => false, 'error' => 'Server error: '.$e->getMessage()]);
    }
  }

  public function loadsettingsAction() {
	  $migareference          = new Migareference_Model_Migareference();
    $default              = new Core_Model_Default();
    $base_url             = $default->getBaseUrl();
    $data                 = $this->getRequest()->getPost();
    $app_id               = $data['app_id'];
      
    $pre_settings   = $migareference->preReportsettigns($app_id);    
    $agents         = $migareference->get_customer_agents($app_id);
    if (is_array($agents) && !empty($agents)) {
      usort($agents, function ($first, $second) {
        $firstLastname  = isset($first['lastname']) ? mb_strtolower($first['lastname']) : '';
        $secondLastname = isset($second['lastname']) ? mb_strtolower($second['lastname']) : '';
        $lastNameSort   = strcmp($firstLastname, $secondLastname);
        if ($lastNameSort !== 0) {
          return $lastNameSort;
        }

        $firstFirstname  = isset($first['firstname']) ? mb_strtolower($first['firstname']) : '';
        $secondFirstname = isset($second['firstname']) ? mb_strtolower($second['firstname']) : '';

        return strcmp($firstFirstname, $secondFirstname);
      });
    }   
    $agentProvinces = $migareference->getGeoCountrieProvinces($app_id,0);
    $all_jobs       = $migareference->getJobs($app_id);
    $all_professions= $migareference->getProfessions($app_id);

    // Load captcha images
    $OptinCaptcha   = new Migareference_Model_Optin_Captcha();
    // $captcha_images will hold list of 10 images we have to use randomly any of single index from 0-9
    $captcha_images = $OptinCaptcha->findAll(['app_id' => $app_id])->toArray();
    if (!COUNT($captcha_images)) {
      //Create Captcha Images DB
      $captcha_images = $OptinCaptcha->createCaptchaImages($app_id);
    }
    if ($pre_settings[0]['sponsor_type']==1) {
      $sponsor_display="block";
      $province_display="block";
    } else {
      $sponsor_display="none";
      $province_display="block";
    }    
    $job_option="<option value='-1'>".__("Scegli")."</option>";
    $job_option.="<option value='0'>".__("Non Classificabile")."</option>";
    foreach ($all_jobs as $key => $value) {
      $job_option.="<option value='".$value['migareference_jobs_id']."' >".$value['job_title']."</option>";
    }
    $profession_option="";
    $profession_option.="<option value='-1'>".__("Scegli")."</option>";    
    foreach ($all_professions as $key => $value) {
      $profession_option.="<option value='".$value['migareference_professions_id']."' >".$value['profession_title']."</option>";
    }
    $profession_option.="<option value='0'>".__("N/A")."</option>";    
    if ($pre_settings[0]['enable_mandatory_agent_selection']==2) {
    $agent_option="<option value='0'>".__("I dont know")."</option>";
    }else{
      $agent_option="<option value='-1'>".__("Scegli")."</option>";
    }
    foreach ($agents as $key => $value) {
      $lastname  = isset($value['lastname']) ? trim($value['lastname']) : '';
      $firstname = isset($value['firstname']) ? trim($value['firstname']) : '';
      $agent_name = trim($lastname . ' ' . $firstname);
      $agent_option.="<option value='".$value['user_id']."' >".htmlspecialchars($agent_name, ENT_QUOTES, 'UTF-8')."</option>";
    }

    foreach ($agentProvinces as $prov_key => $prove_value) {
        $agent_province_list[]=[
            'id'=>$prove_value['user_id'],
            'province'=>$prove_value['province'],
            'province_id'=>$prove_value['migareference_geo_provinces_id']
        ];        
    }
    $agent_province_list =  array_map("unserialize", array_unique(array_map("serialize", $agent_province_list)));
    sort($agent_province_list); 
    $province_list="<option value='-1'>".__("Scegli")."</option>";
    foreach ($agent_province_list as $key => $value) {
      $province_list.="<option value='".$value['province_id']."' >".$value['province']."</option>";
    }

    // Random index of captcha image
    $random_index = rand(0, 9);
    $capcha=$captcha_images[$random_index];
    $path=$base_url."/app/local/modules/Migareference/resources/appicons/optincaptcha/";    
    $image="<img src='".$path.$capcha['image_name']."' />";

     $payload = [
            'province_list'    => $province_list,            
            'job_list'         => $job_option,            
            'profession_list'  => $profession_option,            
            'agent_option'     => $agent_option,            
            'sponsor_display'  => $sponsor_display,            
            'province_display' => $province_display,            
            'c_img'          => $image,            
            'track_id'         => $capcha['image_uid']            
        ];
      $this->_sendJson($payload);
    }

    // V3 Created at 09-27-2024
    public function subscribev3Action(){
      try {
        $data          = $this->getRequest()->getPost();      
        $app_id        = $data['app_id'];
        $migareference = new Migareference_Model_Migareference();
        $optinform     = new Migareference_Model_Optinform();
        $pre_settings  = $migareference->preReportsettigns($app_id);
        $optin_settings=$optinform->getOptinSettings($app_id);   
        //added by imran start
        $optin_setting_data = [];
        $optin_setting = (new Migareference_Model_Optinsetting())->find(['app_id' => $app_id]);
        if ($optin_setting->getId()) {
          $optin_setting_data = unserialize($optin_setting->getoptinSetting());
        }
        //added by imran end
        if (!isset($data['firstname']) || empty($data['firstname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['firstname'])) {
          $errors['firstname']= __('Please add valid First Name') ;
        }
        if (!isset($data['lastname']) || empty($data['lastname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['lastname'])) {
          $errors['lastname']= __('Please add valid Surname') ;
        }
        if (!isset($data['email']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
          $errors['email']= __('Please add valid Email') ;
        }            
        if (!isset($data['mobile'])  || empty($data['mobile']) || preg_match('@[a-z]@', $data['mobile']) || strlen($data['mobile']) < 10 || strlen($data['mobile']) > 14){
          $errors['mobile']= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning');
        }
        if (isset($data['job_id']) && $data['job_id'] == -1) {
          $errors['job_id']= __("Please select a job") ;
        }
        if (isset($data['profession_id']) && $data['profession_id'] == -1) {
            $errors['profession_id']= __("Please select a profession") ;
        }
        if (isset($data['sponsor_id']) && $data['sponsor_id'] == -1) {
            $errors['sponsor_id']= __("Please select an agent") ;
        }
        if (isset($data['province_id']) && $data['province_id'] == -1) {
            $errors['province_id']= __("Please select a province") ;
        }
        if (isset($data['province_id']) && $data['province_id'] == -1) {
            $errors['province_id']= __("Please select a province") ;
        }
        if (!isset($data['privacy'])) {
            $errors['privacy']= __("Please accept privacy policy") ;
        }

        // START: Custom Captcha validation
        if (empty($data['track_id'])) {
          $errors['exception']= __('Something went wrong. Please try again later.') ;
        }else {          
          $OptinCaptcha   = new Migareference_Model_Optin_Captcha();          
          $track_id=$data['track_id'];
          $captcha_image = $OptinCaptcha->findAll(['image_uid' => $track_id])->toArray();
          // Verify if the track id is not edited by bot
          if (COUNT($captcha_image)) {
            // Verify if the SUM 
            if ($captcha_image[0]['sum']!=$data['c_img_fi']) {              
              $errors['exception']= __('Something went wrong. Please try again later.') ;
            }
          } else {
            $errors['exception']= __('Something went wrong. Please try again later.') ;
          }
        }

        // END: Custom Captcha validation

        //Custom Settings fields
        if ($optin_setting_data) {//Apply custom settings validation rules
          if ($optin_setting_data['required']['birthdate']==1 && (empty($data['birthdate']))){
            $errors['birthdate']= __('Please add a valid Birthdate.') ;
          }  
          if ($optin_setting_data['required']['gdpr']==1 && !isset($data['consent'])) {
            $errors['consent']= __("Please accept consent") ;
          } 
        }else { //Apply default validation rules
          if ($pre_settings[0]['enable_birthdate']==1 && $pre_settings[0]['mandatory_birthdate']==1 && (empty($data['birthdate']))){
            $errors['birthdate']= __('Please add a valid Birthdate.') ;
          }   
          if (!isset($data['consent'])) {
            $errors['consent']= __("Please accept consent") ;
          } 
        }
        //Custom Settings fields End

        if (isset($data['g-recaptcha-response'])) {
          $url = 'https://www.google.com/recaptcha/api/siteverify';
          $datasss = array(
            'secret' => '6LdMEUApAAAAAPmVhCTuh-k6gvyu_SRXNw6sCO4_',
            'response' => $data['g-recaptcha-response']
          );
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datasss));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($ch);
          echo curl_error($ch); // Check for cURL errors
          curl_close($ch);
          $result = json_decode($response, true);
          if ($result['success']) {
            // Validation successful          
          } else {
            // Validation failed
            $errors['exception']= __('Something went wrong. Please try again later. Security') ;
          }
        }	 
        if (!empty($errors)) {               
          echo json_encode(['success' => false, 'errors' => $errors,'optin_setting_data'=>$optin_setting_data['required']]);
          exit;
        } else {                
            $password   = (new Migareference_Model_Utilities())->randomPassword();    
            //commneted by imran      
            /* $birth_date = date('Y-m-d',strtotime($data['birthdate']));
            $birth_date = strtotime($birth_date);   */   
            $ipAddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($ipList as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        $ipAddress = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            }
            // Setup sponsor and province id
            // For geo location use sponsor id          
            $data['sponsor_id'] = (isset($data['sponsor_id']) && $pre_settings[0]['sponsor_type']==1) ? $data['sponsor_id'] : $data['sponsord_by'] ;                    
            if ($data['sponsor_id']==null) {$data['sponsor_id']=0;}                    
            // create Customer
            $customer['app_id']         = $app_id;
            $customer['firstname']      = $data['firstname'];
            $customer['lastname']       = $data['lastname'];
            $customer['email']          = $data['email'];
            $customer['mobile']         = $data['mobile'];
            $customer['birthdate']      = isset($data['birthdate']) && !empty($data['birthdate']) ? strtotime(date('Y-m-d',strtotime($data['birthdate']))) : 0; //updated by imran
            $customer['password']       = sha1($password);
            $customer['privacy_policy'] = 1;
            $user_id=$migareference->createUser($customer);             
            // Save Invoice
            if (isset($data['province_id']) && !empty($data['province_id'])) {
              $province_data=$migareference->getGeoProvince($data['province_id'],$app_id);
              if (COUNT($province_data)) {
                $inv_settings['address_country_id']=$province_data[0]['country_id'];
              }
            }          
            
            $inv_settings['app_id']                 = $app_id;
            $inv_settings['user_id']                = $user_id;
            $inv_settings['blockchain_password']    = (new Migareference_Model_Utilities())->randomPassword();
            $inv_settings['invoice_name']           = $data['firstname'];
            $inv_settings['sponsor_id']             = $data['sponsor_id'];
            $inv_settings['partner_sponsor_id']     = 0;
            $inv_settings['address_province_id']    = isset($data['province_id']) ? $data['province_id'] : 0;
            $inv_settings['invoice_surname']        = $data['lastname'];
            $inv_settings['invoice_mobile']         = $data['mobile'];
            $inv_settings['job_id']                 = isset($data['job_id']) ? $data['job_id'] : 0;;
            $inv_settings['profession_id']          = isset($data['profession_id']) ? $data['profession_id'] : 0;;
            $inv_settings['tax_id']                 = (new Migareference_Model_Utilities())->randomTaxid();
            $inv_settings['referrer_source']        = 3;//3 for optin form
            $inv_settings['referrer_ip']            = $ipAddress;//only used to track ip of spam users
            $inv_settings['terms_accepted']         = 0;
            $inv_settings['special_terms_accepted'] = 0;
            $inv_settings['privacy_accepted']       = 0;
            
            $migareference->savePropertysettings($inv_settings); //This method also save phonebook entry if previously not exist          
            // Send Welcome Email to referrer
            if ($pre_settings[0]['enable_optin_welcome_email']==1
                && !empty($pre_settings[0]['referrer_optin_wellcome_email_title'])
                && !empty($pre_settings[0]['referrer_optin_wellcome_email_body']))
              {
                $notificationTags=$migareference->welcomeEmailTags();
                $agent_user=$migareference->getSingleuser($app_id,isset($data['province_id']) ? $data['province_id'] : 0);
                $default        = new Core_Model_Default();
                $base_url       = $default->getBaseUrl();
                $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                $notificationStrings = [
                $customer['firstname']." ".$customer['lastname'],
                $customer['email'],
                $password,
                $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                $app_link
              ];
              $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_title']);
              $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_body']);
              $email_data['reply_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_reply_to'];
              $email_data['bcc_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_bcc_to'];
              $email_data['type']        = 3;//type 3 for optin wellcome log            
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
            $keys['app_id']=$app_id;
            $keys['user_id']=$user_id;
            $keys['key']=$password;
            $migareference->savekey($keys);
            if ($optin_settings[0]['redirect_url']) {                          
                echo json_encode(['success' => true, 'success_title'=>__("Success"), 'is_redirect' => true, 'redirect_url'=>$optin_settings[0]['redirect_url']]);              
            }else {
                $success_message = __("Thank for your submission");
                if ($optin_settings[0]['confirmation_message']) {
                  $success_message=$optin_settings[0]['confirmation_message'];
                }              
                echo json_encode(['success' => true, 'success_title'=>__("Success"), 'is_redirect' => false, 'success_message'=>$success_message]);                            
            }
          }                                 
      } catch (Exception $e) {
        $errors['exception']= __('Something went wrong try again later. Exception'.$e->getMessage()) ;
        echo json_encode(['success' => false, 'errors' => $errors]);      
      }
      exit;
  }
    public function subscribev2Action(){
      try {
        $data          = $this->getRequest()->getPost();      
        $app_id        = $data['app_id'];
        $migareference = new Migareference_Model_Migareference();
        $optinform     = new Migareference_Model_Optinform();
        $pre_settings  = $migareference->preReportsettigns($app_id);
        $optin_settings=$optinform->getOptinSettings($app_id);   
        //added by imran start
        $optin_setting_data = [];
        $optin_setting = (new Migareference_Model_Optinsetting())->find(['app_id' => $app_id]);
        if ($optin_setting->getId()) {
          $optin_setting_data = unserialize($optin_setting->getoptinSetting());
        }
        //added by imran end
        if (!isset($data['firstname']) || empty($data['firstname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['firstname'])) {
          $errors['firstname']= __('Please add valid First Name') ;
        }
        if (!isset($data['lastname']) || empty($data['lastname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['lastname'])) {
          $errors['lastname']= __('Please add valid Surname') ;
        }
        if (!isset($data['email']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
          $errors['email']= __('Please add valid Email') ;
        }            
        if (!isset($data['mobile'])  || empty($data['mobile']) || preg_match('@[a-z]@', $data['mobile']) || strlen($data['mobile']) < 10 || strlen($data['mobile']) > 14){
          $errors['mobile']= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning');
        }
        if (isset($data['track_id']) && !empty($data['track_id'])) {
          $errors['exception']= __('Something went wrong. Please try again later.') ;
        }
        if (isset($data['job_id']) && $data['job_id'] == -1) {
          $errors['job_id']= __("Please select a job") ;
        }
        if (isset($data['profession_id']) && $data['profession_id'] == -1) {
            $errors['profession_id']= __("Please select a profession") ;
        }
        if (isset($data['sponsor_id']) && $data['sponsor_id'] == -1) {
            $errors['sponsor_id']= __("Please select an agent") ;
        }
        if (isset($data['province_id']) && $data['province_id'] == -1) {
            $errors['province_id']= __("Please select a province") ;
        }
        if (isset($data['province_id']) && $data['province_id'] == -1) {
            $errors['province_id']= __("Please select a province") ;
        }
        if (!isset($data['privacy'])) {
            $errors['privacy']= __("Please accept privacy policy") ;
        }

        //Custom Settings fields
        if ($optin_setting_data) {//Apply custom settings validation rules
          if ($optin_setting_data['required']['birthdate']==1 && (empty($data['birthdate']))){
            $errors['birthdate']= __('Please add a valid Birthdate.') ;
          }  
          if ($optin_setting_data['required']['gdpr']==1 && !isset($data['consent'])) {
            $errors['consent']= __("Please accept consent") ;
          } 
        }else { //Apply default validation rules
          if ($pre_settings[0]['enable_birthdate']==1 && $pre_settings[0]['mandatory_birthdate']==1 && (empty($data['birthdate']))){
            $errors['birthdate']= __('Please add a valid Birthdate.') ;
          }   
          if (!isset($data['consent'])) {
            $errors['consent']= __("Please accept consent") ;
          } 
        }
        //Custom Settings fields End

        if (isset($data['g-recaptcha-response'])) {
          $url = 'https://www.google.com/recaptcha/api/siteverify';
          $datasss = array(
            'secret' => '6LdMEUApAAAAAPmVhCTuh-k6gvyu_SRXNw6sCO4_',
            'response' => $data['g-recaptcha-response']
          );
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datasss));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($ch);
          echo curl_error($ch); // Check for cURL errors
          curl_close($ch);
          $result = json_decode($response, true);
          if ($result['success']) {
            // Validation successful          
          } else {
            // Validation failed
            $errors['exception']= __('Something went wrong. Please try again later. Security') ;
          }
        }	 
        if (!empty($errors)) {               
          echo json_encode(['success' => false, 'errors' => $errors,'optin_setting_data'=>$optin_setting_data['required']]);
          exit;
        } else {                
            $password   = (new Migareference_Model_Utilities())->randomPassword();    
            //commneted by imran      
            /* $birth_date = date('Y-m-d',strtotime($data['birthdate']));
            $birth_date = strtotime($birth_date);   */   
            $ipAddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($ipList as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        $ipAddress = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            }
            // Setup sponsor and province id
            // For geo location use sponsor id          
            $data['sponsor_id'] = (isset($data['sponsor_id']) && $pre_settings[0]['sponsor_type']==1) ? $data['sponsor_id'] : $data['sponsord_by'] ;                    
            if ($data['sponsor_id']==null) {$data['sponsor_id']=0;}                    
            // create Customer
            $customer['app_id']         = $app_id;
            $customer['firstname']      = $data['firstname'];
            $customer['lastname']       = $data['lastname'];
            $customer['email']          = $data['email'];
            $customer['mobile']         = $data['mobile'];
            $customer['birthdate']      = isset($data['birthdate']) && !empty($data['birthdate']) ? strtotime(date('Y-m-d',strtotime($data['birthdate']))) : 0; //updated by imran
            $customer['password']       = sha1($password);
            $customer['privacy_policy'] = 1;
            $user_id=$migareference->createUser($customer);             
            // Save Invoice
            if (isset($data['province_id']) && !empty($data['province_id'])) {
              $province_data=$migareference->getGeoProvince($data['province_id'],$app_id);
              if (COUNT($province_data)) {
                $inv_settings['address_country_id']=$province_data[0]['country_id'];
              }
            }          
            
            $inv_settings['app_id']                 = $app_id;
            $inv_settings['user_id']                = $user_id;
            $inv_settings['blockchain_password']    = (new Migareference_Model_Utilities())->randomPassword();
            $inv_settings['invoice_name']           = $data['firstname'];
            $inv_settings['sponsor_id']             = $data['sponsor_id'];
            $inv_settings['partner_sponsor_id']     = 0;
            $inv_settings['address_province_id']    = isset($data['province_id']) ? $data['province_id'] : 0;
            $inv_settings['invoice_surname']        = $data['lastname'];
            $inv_settings['invoice_mobile']         = $data['mobile'];
            $inv_settings['job_id']                 = isset($data['job_id']) ? $data['job_id'] : 0;;
            $inv_settings['profession_id']          = isset($data['profession_id']) ? $data['profession_id'] : 0;;
            $inv_settings['tax_id']                 = (new Migareference_Model_Utilities())->randomTaxid();
            $inv_settings['referrer_source']        = 3;//3 for optin form
            $inv_settings['referrer_ip']            = $ipAddress;//only used to track ip of spam users
            $inv_settings['terms_accepted']         = 0;
            $inv_settings['special_terms_accepted'] = 0;
            $inv_settings['privacy_accepted']       = 0;
            
            $migareference->savePropertysettings($inv_settings); //This method also save phonebook entry if previously not exist          
            // Send Welcome Email to referrer
            if ($pre_settings[0]['enable_optin_welcome_email']==1
                && !empty($pre_settings[0]['referrer_optin_wellcome_email_title'])
                && !empty($pre_settings[0]['referrer_optin_wellcome_email_body']))
              {
                $notificationTags=$migareference->welcomeEmailTags();
                $agent_user=$migareference->getSingleuser($app_id,isset($data['province_id']) ? $data['province_id'] : 0);
                $default        = new Core_Model_Default();
                $base_url       = $default->getBaseUrl();
                $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                $notificationStrings = [
                $customer['firstname']." ".$customer['lastname'],
                $customer['email'],
                $password,
                $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                $app_link
              ];
              $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_title']);
              $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_body']);
              $email_data['reply_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_reply_to'];
              $email_data['bcc_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_bcc_to'];
              $email_data['type']        = 3;//type 3 for optin wellcome log            
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
            $keys['app_id']=$app_id;
            $keys['user_id']=$user_id;
            $keys['key']=$password;
            $migareference->savekey($keys);
            if ($optin_settings[0]['redirect_url']) {                          
                echo json_encode(['success' => true, 'success_title'=>__("Success"), 'is_redirect' => true, 'redirect_url'=>$optin_settings[0]['redirect_url']]);              
            }else {
                $success_message = __("Thank for your submission");
                if ($optin_settings[0]['confirmation_message']) {
                  $success_message=$optin_settings[0]['confirmation_message'];
                }              
                echo json_encode(['success' => true, 'success_title'=>__("Success"), 'is_redirect' => false, 'success_message'=>$success_message]);                            
            }
          }                                 
      } catch (Exception $e) {
        $errors['exception']= __('Something went wrong try again later. Exception'.$e->getMessage()) ;
        echo json_encode(['success' => false, 'errors' => $errors]);      
      }
      exit;
  }
  public function subscribeAction(){
    try {
      $data          = $this->getRequest()->getPost();      
      $app_id        = $data['app_id'];
      if ($app_id==221) {
        throw new Exception("Error Processing Request", 1);
        
      }
      $migareference = new Migareference_Model_Migareference();
      $optinform     = new Migareference_Model_Optinform();
      $pre_settings  = $migareference->preReportsettigns($app_id);
      $optin_settings=$optinform->getOptinSettings($app_id);   
	  //added by imran start
	  	$optin_setting_data = [];
	  	$optin_setting = (new Migareference_Model_Optinsetting())->find([
			'app_id' => $app_id
		]);
		if ($optin_setting->getId()) {
			$optin_setting_data = unserialize($optin_setting->getoptinSetting());
		}
	  //added by imran end
      if (!isset($data['firstname']) || empty($data['firstname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['firstname'])) {
        $errors .= __('Please add valid First Name') ;
      }
      if (!isset($data['lastname']) || empty($data['lastname']) || !preg_match("/^[a-zA-Z-' ]*$/", $data['lastname'])) {
        $errors .= __('Please add valid Surname') ;
      }
      if (!isset($data['email']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors .= __('Please add valid Email') ;
      }      
      if (!isset($data['mobile']) || empty($data['mobile'])) {
        $errors .= __('Please add valid Mobile') ;
      }
      if (!isset($data['mobile'])  || empty($data['mobile']) || preg_match('@[a-z]@', $data['mobile'])){
              $errors .= __('Invalid Phone') ;
      }
      if (isset($data['track_id']) && !empty($data['track_id'])) {
        $errors .= __('Something went wrong. Please try again later.') ;
      }
      if (isset($data['job_id']) && $data['job_id'] == -1) {
        $errors .= __("Please select a job") ;
      }
      if (isset($data['profession_id']) && $data['profession_id'] == -1) {
          $errors .= __("Please select a profession") ;
      }
      if (isset($data['sponsor_id']) && $data['sponsor_id'] == -1) {
          $errors .= __("Please select an agent") ;
      }
      if (isset($data['province_id']) && $data['province_id'] == -1) {
          $errors .= __("Please select a province") ;
      }
      if (isset($data['g-recaptcha-response'])) {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $datasss = array(
          'secret' => '6LdMEUApAAAAAPmVhCTuh-k6gvyu_SRXNw6sCO4_',
          'response' => $data['g-recaptcha-response']
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datasss));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        echo curl_error($ch); // Check for cURL errors
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result['success']) {
          // Validation successful
          $temp="Success! Your first name is: " . $_POST['firstname'];
        } else {
          // Validation failed
          $errors .= __('Something went wrong try again later') ;
        }
      }
	  //updated by imran start
    	if (!$optin_setting_data && $pre_settings[0]['enable_birthdate']==1 && $pre_settings[0]['mandatory_birthdate']==1 && (empty($data['birthdate']))){
        $errors .= __('Please add a valid Birthdate') ;
      }      
	  //updated by imran end
      if (!empty($errors)) {               
          // throw new Exception($errors);
          $alert = "<script>alert('".$errors."')</script>";
            echo $alert;
            echo "<script>window.close();</script>";
      } else {       
            
                 
          $password   = (new Migareference_Model_Utilities())->randomPassword();    
		  //commneted by imran      
          /* $birth_date = date('Y-m-d',strtotime($data['birthdate']));
          $birth_date = strtotime($birth_date);   */   
          $ipAddress = '';
    
          if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
              $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
          } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
              $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
              foreach ($ipList as $ip) {
                  if (filter_var($ip, FILTER_VALIDATE_IP)) {
                      $ipAddress = $ip;
                      break;
                  }
              }
          } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
              $ipAddress = $_SERVER['REMOTE_ADDR'];
          }
          // Setup sponsor and province id
          // For geo location use sponsor id          
          $data['sponsor_id'] = (isset($data['sponsor_id']) && $pre_settings[0]['sponsor_type']==1) ? $data['sponsor_id'] : $data['sponsord_by'] ;                    
          if ($data['sponsor_id']==null) {
            $data['sponsor_id']=0;
          }           
          // create Customer
          $customer['app_id']         = $app_id;
          $customer['firstname']      = $data['firstname'];
          $customer['lastname']       = $data['lastname'];
          $customer['email']          = $data['email'];
          $customer['mobile']         = $data['mobile'];
          $customer['birthdate']      = isset($data['birthdate']) ? strtotime(date('Y-m-d',strtotime($data['birthdate']))) : NULL; //updated by imran
          $customer['password']       = sha1($password);
          $customer['privacy_policy'] = 1;
          $user_id=$migareference->createUser($customer);    
                          
          // Save Invoice
          if (isset($data['province_id']) && !empty($data['province_id'])) {
            $province_data=$migareference->getGeoProvince($data['province_id'],$app_id);
            if (COUNT($province_data)) {
              $inv_settings['address_country_id']=$province_data[0]['country_id'];
            }
          }          
          //$job_id = (!empty($data['job_id'])) ? $data['job_id'] : 0 ;
          $inv_settings['app_id']                 = $app_id;
          $inv_settings['user_id']                = $user_id;
          $inv_settings['blockchain_password']    = (new Migareference_Model_Utilities())->randomPassword();
          $inv_settings['invoice_name']           = $data['firstname'];
          $inv_settings['sponsor_id']             = $data['sponsor_id'];
          $inv_settings['partner_sponsor_id']     = 0;
          $inv_settings['address_province_id']    = isset($data['province_id']) ? $data['province_id'] : 0;
          $inv_settings['invoice_surname']        = $data['lastname'];
          $inv_settings['invoice_mobile']         = $data['mobile'];
          $inv_settings['job_id']                 = isset($data['job_id']) ? $data['job_id'] : 0;;
          $inv_settings['profession_id']          = isset($data['profession_id']) ? $data['profession_id'] : 0;;
          $inv_settings['tax_id']                 = (new Migareference_Model_Utilities())->randomTaxid();;
          $inv_settings['referrer_source']        = 3;//3 for optin form
          $inv_settings['referrer_ip']            = $ipAddress;//only used to track ip of spam users
          $inv_settings['terms_accepted']         = 0;
          $inv_settings['special_terms_accepted'] = 0;
          $inv_settings['privacy_accepted']       = 0;
          
          $migareference->savePropertysettings($inv_settings); //This method also save phonebook entry if previously not exist             
          // Send Welcome Email to referrer
          if ($pre_settings[0]['enable_optin_welcome_email']==1
              && !empty($pre_settings[0]['referrer_optin_wellcome_email_title'])
              && !empty($pre_settings[0]['referrer_optin_wellcome_email_body']))
            {
              $notificationTags=$migareference->welcomeEmailTags();
              $agent_user=$migareference->getSingleuser($app_id,isset($data['province_id']) ? $data['province_id'] : 0);
              $default        = new Core_Model_Default();
              $base_url       = $default->getBaseUrl();
              $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
              $notificationStrings = [
              $customer['firstname']." ".$customer['lastname'],
              $customer['email'],
              $password,
              $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
              $app_link
            ];
            $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_title']);
            $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_optin_wellcome_email_body']);
            $email_data['reply_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_reply_to'];
            $email_data['bcc_to_email']  = $pre_settings[0]['referrer_optin_wellcome_email_bcc_to'];
            $email_data['type']        = 3;//type 3 for optin wellcome log            
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
          $keys['app_id']=$app_id;
          $keys['user_id']=$user_id;
          $keys['key']=$password;
          $migareference->savekey($keys);
          if ($optin_settings[0]['redirect_url']) {               
              $alert = "<script>window.location.replace('".$optin_settings[0]['redirect_url']."')</script>";
              echo $alert;
          }else {
              $alert = "<script>alert('".__("Thank for your submission")."');</script>";
              if ($optin_settings[0]['confirmation_message']) {
                $alert = "<script>alert('".$optin_settings[0]['confirmation_message']."');</script>";
              }
              echo $alert;
              echo "<script>window.close();</script>";
          }
        }           
                 
          $payload = [
            'success' => true,
            'message' => __("Successfully User Created"),            
            'data' => $inv_settings,            
            'inv_settings' => $password,            
          ];
    } catch (Exception $e) {
      $alert = "<script>alert('"."Error : " . $e->getMessage()."')</script>";
      echo $alert;
      echo "<script>window.close();</script>";
    }
        exit;
  }
}