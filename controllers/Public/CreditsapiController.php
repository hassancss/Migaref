<?php
/**
 * Class Migareference_Public_CreditsapiController
 * API Response Codes
  *     // ErrCode=
  *     // “0” = Error, Token mismatch
  *     // “1” = Report added
  *     // “2” = Error, wrong data format
  *     // “3” = Error, Phone owner already present*
  *     // “4” = Error, unknow
 */
class Migareference_Public_CreditsapiController extends Migareference_Controller_Default {
    /*
    @params
    tokne*
    (referrer_email || referrer_mobile)*
    credit_type*
    credit_amount*
    credit_description*
    */
  public function customcreditsAction(){
    $app_id=0;
    try{ 
      $data                 = $this->getRequest()->getPost();      
      // Validate TOKEN
      if (empty($data['token']) || strlen($data['token'])!=35) {
        throw new Exception("Token Mismatchd");
      }
      $api_obj            = new Migareference_Model_Reportapi();
      $migareference        = new Migareference_Model_Migareference();
      $default              = new Core_Model_Default();
      $base_url             = $default->getBaseUrl();
      $pre_report_settings=$api_obj->validateCreditApiToken($data['token']);
      $referrerCustomer=[];
       // Notification Allowed Tags
       $tags_list=[
        "@@referrer_name@@",
        "@@referrer_surname@@",
        "@@credit@@",
        "@@credit_type@@",
        "@@credit_description@@",
        "@@credit_balance@@",
        "@@app_link@@"
      ];
      if (!count($pre_report_settings)) {
        throw new Exception("Token Mismatched");
      }
      if ($pre_report_settings[0]['enable_credits_api']==2) {
        throw new Exception("This API is not enabled.");
      }
      $app_id=$pre_report_settings[0]['app_id'];
      // We match Referrer with Email or Mobile
      if (empty($data['referrer_email']) || empty($data['referrer_mobile'])) {
        throw new Exception("Referrer Email or Mobile is required.");
      }
      // Validate Refrrer data via email
      if (!empty($data['referrer_email'])) {
        if (!filter_var($data['referrer_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid Email.");
        }else {
            $referrerCustomer=$migareference->getCustomer($app_id,$data['referrer_email']);
            if (empty($data['referrer_mobile']) && !COUNT($referrerCustomer)) {
                throw new Exception("Referrer not found.");
            }
        }
      }
        // Validate referrer data via mobile
      if (!empty($data['referrer_mobile']) && empty($referrerCustomer)) {
        if(strlen($data['referrer_mobile']) < 10 || strlen($data['referrer_mobile']) > 14 || empty($data['referrer_mobile']) || preg_match('@[a-z]@', $data['referrer_mobile']) || (substr($data['referrer_mobile'], 0, 1)!='+' && substr($data['referrer_mobile'], 0, 2)!='00')) {          
            throw new Exception("Invalid Referrer mobile.");        
        }else {
            $referrerCustomer=$migareference->getCustomerMobile($app_id,$data['referrer_mobile']);
            if (!COUNT($referrerCustomer)) {
                throw new Exception("Referrer not found.");
            }
            if (COUNT($referrerCustomer)>1) {
                throw new Exception("Multiple Referrers found.");
            }
        }
      }
     // Validate CREDITS required data
        if (!preg_match('/^[01]$/', $data['credit_type'])) {
            throw new Exception("Credit type is missing or invalid.");
        }
        if (empty($data['credit_amount']) || !preg_match('/^[1-9][0-9]*$/', $data['credit_amount'])) {
            throw new Exception("Credit amount is missing or invalid.");
        }
        if (empty($data['credit_description']) || !preg_match('/^[a-zA-Z\s]+$/', $data['credit_description'])) {
            throw new Exception("Credit description is missing or invalid.");
        }
        // API Admin validation
        $api_admin   = $api_obj->getCreditApiAdmin($app_id);
        if (!count($api_admin)) {
          throw new Exception("Invalid Admin Access");
        }
        // Save data
        $referrer_id=$referrerCustomer[0]['customer_id'];
        $earning['app_id']           = $app_id;
        $earning['user_id']          = $referrer_id;
        $earning['amount']           = $data['credit_amount'];
        $earning['transection_source']= __("Api_Custom");
        $earning['entry_type']       = ($data['credit_type']==1) ? 'C' : 'D';
        $earning['trsansection_by'] = $api_admin[0]['user_id'];
        $earning['user_type']        = 4;//1: for app cutomers 2: for app admins 3: for agent,4 for api admin
        $earning['prize_id']         = 0;
        $earning['trsansection_description']  = $data['credit_description'];
        $migareference->saveLedger($earning);
          // Send Notifications
          $getCreditsApiNotification = $migareference->getCreditsApiNotification($app_id);
          if ($getCreditsApiNotification[0]['ref_credits_api_enable_notification']==1) {
            $referrer_user=$migareference->getSingleuser($app_id, $referrer_id);
            $credit_balance   = $migareference->get_credit_balance($app_id,$referrer_id);
            $tags_list=[
              "@@referrer_name@@",
              "@@referrer_surname@@",
              "@@credit@@",
              "@@credit_type@@",
              "@@credit_description@@",
              "@@credit_balance@@",
              "@@app_link@@"
            ];
            $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            $tag_values=[
              $referrer_user[0]['firstname'],
              $referrer_user[0]['lastname'],
              $data['credit_amount'],
              ($data['credit_type']==1) ? 'Credit' : 'Debit',
              $data['credit_description'],
              ($credit_balance[0]['credits']>0) ? $credit_balance[0]['credits'] : 0, 
              $app_link
            ];
            if ($getCreditsApiNotification[0]['ref_credits_api_notification_type']==1 || $getCreditsApiNotification[0]['ref_credits_api_notification_type']==2) {
              $email_data['email_title']=str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_email_title']);
              $email_data['email_text']=str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_email_text']);
              $email_data['calling_method']='Credits_Api_Custom';
              $mail_retur = $migareference->sendMail($email_data,$app_id,$referrer_id);
            }
            if ($getCreditsApiNotification[0]['ref_credits_api_notification_type']==1 || $getCreditsApiNotification[0]['ref_credits_api_notification_type']==3) {
              $push_data['open_feature'] = $getCreditsApiNotification[0]['ref_credits_api_open_feature'];
              $push_data['feature_id']   = $getCreditsApiNotification[0]['ref_credits_api_feature_id'];
              $push_data['custom_url']   = $getCreditsApiNotification[0]['ref_credits_api_custom_url'];
              $push_data['cover_image']  = $getCreditsApiNotification[0]['ref_credits_api_cover_file'];
              $push_data['app_id']       = $app_id;    
              $push_data['calling_method']='Credits_Api_Custom'; 
              $push_data['push_title']   = str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_push_title']);
              $push_data['push_text']    = str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_push_text']);
            //   $push_return = $migareference->sendPush($push_data,$app_id,$referrer_id);
            }
        }
          $payload = [
              'response'      => true,
              'description'  => __('Successfully credits saved.'),         
          ];
    } catch (\Exception $e) {
        $payload = [
            'response'      => false,
            'description'  => __($e->getMessage())                    
        ];
    }

    if ($app_id>0) {
       // Check for shared internet/ISP IP
     if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
          $request_ip= $_SERVER['HTTP_CLIENT_IP'];
      }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {  // Check for IP from proxy
          $request_ip= $_SERVER['HTTP_X_FORWARDED_FOR'];
      }elseif (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) { // Check for IP from remote address
          $request_ip= $_SERVER['REMOTE_ADDR'];
      }else { //  a default value if no valid IP is found
          $request_ip= 'UNKNOWN';
      }
      // $api_log
      $api_log['app_id']=$app_id;
      $api_log['request_ip']=$request_ip;
      $api_log['response']=$payload['response'];
      $api_log['description']=$payload['description'];
      $api_obj->saveCreditsApiLog($api_log);
    }
    $this->_sendJson($payload);
  }
}
