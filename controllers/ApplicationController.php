<?php
/**
 * Class Migareference_ApplicationController
 */
class Migareference_ApplicationController extends Application_Controller_Default{
  public function viewAction(){
      $application = $this->getApplication();
      $this->loadPartials();
  }
    public function exportstatusAction()
    {
        try {
            if ($app_id = $this->getRequest()->getParam('app_id')) {
                $migareference = new Migareference_Model_Migareference();
                $referral_usrs = $migareference->get_referral_users($app_id);
                $responce = $migareference->exportStatus($app_id);
                $payload =[
                    'success'        => true,
                    'data'           => $responce,
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout'=> 0
                ];
            }
        } catch (Exception $e) {
            $payload = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_loader' => 1,
                'message_button' => 1
            ];
        }
        $this->_sendJson($payload);
    }
  public function importstatusAction()
  {
      try {
            $message="";
            $app_id        = $this->getApplication()->getId();
            $migareference = new Migareference_Model_Migareference();
            $custome_status_reports=$migareference->get_custom_status_reports($app_id);
          if ($request = $this->getRequest()->getPost() && count($custome_status_reports)==0) {
            $request = $this->getRequest()->getPost();
                $value_id      = $request['value_id'];                
              if($_FILES["file"]["name"]) {
                  $filename = $_FILES["file"]["name"];
                  $source   = $_FILES["file"]["tmp_name"];
                  $type     = $_FILES["file"]["type"];
                  $name     = explode(".", $filename);
                  $okay     = false;
                  $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                  foreach($accepted_types as $mime_type) {
                      if($mime_type == $type) {
                          $okay = true;
                      }
                  }
                  if(!$okay) {
                      $continue = strtolower($name[1]) == 'zip' ? true : false;
                      if (!$continue) {
                          $message = __("The file you are trying to upload is not a .zip file. Please try again.");
                      }
                  }
                  $baseUrl = Core_Model_Directory::getBasePathTo("");
                  if (!file_exists($baseUrl."/var/tmp")) {
                      mkdir($baseUrl."/var/tmp");
                  }
                  if (!file_exists($baseUrl."/var/tmp/migareferenceimport")) {
                      mkdir($baseUrl."/var/tmp/migareferenceimport");
                  }
                  $target_path = $baseUrl."/var/tmp/migareferenceimport/".$filename;  // change this to the correct site path
                  if(move_uploaded_file($source, $target_path)) {
                      $zip = new ZipArchive();
                      $x = $zip->open($target_path);
                      if ($x === true) {
                          $zip->extractTo($baseUrl."/var/tmp/migareferenceimport/"); // change this to the correct site path
                          $zip->close();
                          unlink($target_path);
                      }
                      $json_data = $migareference->importstatus($app_id,$value_id);
                  } else {
                      $message = __("There was a problem with the upload. Please try again.");
                  }
              }
          }else {
              $message = __("You must chnage all reports to standard status before import settings.");
          }
          $success=true;
          if ($message!="") {
            $success=false;
          }
            $payload =[
                'success'        => $success,
                "data"           => $json_data,
                "test"           => $data,
                'message'        => $message,
                'new_path'       => $baseUrl."/var/tmp/migareferenceimport/".$name[0],
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout'=> 0
            ];
      } catch (Exception $e) {
          $payload = [
              'success'        => false,
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_loader' => 1,
              'message_button' => 1,
              'request' => $request,
              "json_data"   => $json_data,
          ];
      }
      $this->_sendJson($payload);
  }
public function getmanageprizeAction() {
    if ($data = $this->getRequest()->getQuery()) {
        try {
          $migareference = new Migareference_Model_Migareference();
          $redeemprizes  = $migareference->getmanageredeemprize($data['app_id']);
          $count         = 1;
          foreach ($redeemprizes as $key => $value) {
            if ($value['redeemed_status']==0) {
              $redeem_status = __('Pending');
            } elseif($value['redeemed_status']==1) {
              $redeem_status = __('Delivered');
            }elseif($value['redeemed_status']==2) {
              $redeem_status = __('Refused');
            }
            $action='<select class="input-flat" id="change_redeemstatus" onchange="chnageRedeemstatus(this)">';
              $action.='<option  value=""></option>';
              $action.='<option  value="'.$value['migarefrence_ledger_id'].'@1@'.$value['customer_id']."@".$value['prize_id']."@".$value['credits_number']."@".$value['ledger_id']."@".$value['prize_name'].'">'.__("Delivered").'</option>';
              $action.='<option  value="'.$value['migarefrence_ledger_id'].'@2@'.$value['customer_id']."@".$value['prize_id']."@".$value['credits_number']."@".$value['ledger_id']."@".$value['prize_name'].'">'.__("Refused").'</option>';
            $action.='</select>';
              $report_collection[]=[
                        $count++,
                        date('Y-m-d',strtotime($value['redeemed_at'])),
                        $value['firstname'],
                        $value['lastname'],
                        $value['email'],
                        $value['prize_name'],
                        $redeem_status,
                        $action
                      ];
          }
            $payload = [
                "data" => $report_collection
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
  public function savepushtemplateAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $value_id = $datas['value_id'];
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($datas['migareference_title'])) {
                  $errors .= __('Title cannot be empty.') . "<br/>";
              }
              if (empty($datas['migareference_message'])) {
                  $errors .= __('Message cannot be empty.') . "<br/>";
              }
              if (isset($datas['migareference_is_feature']) && !($datas['migareference_feature_id']) && empty($datas['migareference_custom_url'])) {
                  $errors .= __('Custom URL cannot be empty.') . "<br/>";
              } elseif (isset($datas['migareference_is_feature']) && !($datas['migareference_feature_id']) && !filter_var($datas['migareference_custom_url'], FILTER_VALIDATE_URL)) {
                  $errors .= __('Custom URL not valid.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $app_id       = $this->getApplication()->getId();
                  $migareference = new Migareference_Model_Migareference();
                  $migareference->savePush($app_id, $datas);
              }
              $html = [
                'success'         => true,
                'message'         => __('Push successfully   saved data.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savewebhookreporterrortemplateAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $value_id = $datas['value_id'];
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($datas['email_title'])) {
                  $errors .= __('Title cannot be empty.') . "<br/>";
              }
              if (empty($datas['email_message'])) {
                  $errors .= __('Message cannot be empty.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {                                  
                  $model = new Migareference_Model_Webhookerrortemplate();
                  $model->setData($datas)->save();
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savewebhookremindererrortemplateAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $value_id = $datas['value_id'];
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($datas['email_title'])) {
                  $errors .= __('Title cannot be empty.') . "<br/>";
              }
              if (empty($datas['email_message'])) {
                  $errors .= __('Message cannot be empty.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {                                  
                  $model = new Migareference_Model_Webhookerrortemplate();
                  $model->setData($datas)->save();
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  } 
  public function savecreditsapinotificationsAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $app_id = $datas['app_id'];
              if (!$app_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
                if ($datas['ref_credits_api_notification_type']==1 || $datas['ref_credits_api_notification_type']==2) {
                    if (empty($datas['ref_credits_api_email_title'])) {
                        $errors .= __('Referral Email title cannot be empty.') . "<br/>";
                    }
                    if (empty($datas['ref_credits_api_email_text'])) {
                        $errors .= __('Referral Email message cannot be empty.') . "<br/>";
                    }
                }
                if ($datas['ref_credits_api_notification_type']==1 || $datas['ref_credits_api_notification_type']==3) {
                  if (empty($datas['ref_credits_api_push_title'])) {
                      $errors .= __('Referral PUSH title cannot be empty.') . "<br/>";
                  }
                  if (empty($datas['ref_credits_api_push_title'])) {
                      $errors .= __('Referral PUSH message cannot be empty.') . "<br/>";
                  }
                  if ($datas['ref_credits_api_open_feature']==1 && $datas['ref_credits_api_feature_id']==0 && empty($datas['ref_credits_api_custom_url'])) {
                      $errors .= __('Referral CUSTOM ULR cannot be empty.') . "<br/>";
                  }
                }
            
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  $operation=$datas['operation'];
                  unset($datas['operation']);
                  if ($operation=='create') {
                    $migareference->saveCreditsApiNotification($datas);
                  }else {
                    $migareference->updateCreditsApiNotification($datas);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Notification saved successfully.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savereportreminderformAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $errors = "";
              if (empty($datas['rep_rem_title'])) {
                  $errors .= __('Reminder Title cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_icon_file']) && empty($datas['c_rep_rem_icon_file'])) {
                  $errors .= __('Reminder ICON cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_email_title'])) {
                  $errors .= __('Email subject cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_email_text'])) {
                  $errors .= __('Email Message cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_push_title'])) {
                  $errors .= __('PUSH title cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_push_text'])) {
                  $errors .= __('PUSH message cannot be empty.') . "<br/>";
              }
              if (empty($datas['rep_rem_custom_file']) && empty($datas['rep_rem_c_migareference_cover_file'])) {
                  $errors .= __('Cover image cannot be empty.') . "<br/>";
              }
              if (isset($datas['rep_rem_open_feature']) && !($datas['rep_rem_feature_id']) && empty($datas['rep_rem_custom_url'])) {
                  $errors .= __('Custom URL cannot be empty.') . "<br/>";
              } elseif (isset($datas['rep_rem_open_feature']) && !($datas['rep_rem_feature_id']) && !filter_var($datas['rep_rem_custom_url'], FILTER_VALIDATE_URL)) {
                  $errors .= __('Custom URL not valid.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  if ($datas['operation']=="create") {
                    unset($datas['operation']);
                    unset($datas['rep_rem_id']);
                    unset($datas['rep_rem_id']);
                    unset($datas['c_rep_rem_icon_file']);
                    $migareference->saveReportReminder($datas);
                  }else {
                    unset($datas['operation']);
                    $id=$datas['rep_rem_id'];
                    unset($datas['rep_rem_id']);
                    $migareference->updateReportReminder($id,$datas);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savenewnotenotificationAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {              
              $errors = "";              
              if (empty($datas['new_note_email_title'])) {
                  $errors .= __('Email subject cannot be empty.') . "<br/>";
              }
              if (empty($datas['new_note_email_text'])) {
                  $errors .= __('Email Message cannot be empty.') . "<br/>";
              }
              if (empty($datas['new_note_push_title'])) {
                  $errors .= __('PUSH title cannot be empty.') . "<br/>";
              }
              if (empty($datas['new_note_push_text'])) {
                  $errors .= __('PUSH message cannot be empty.') . "<br/>";
              }
              if (empty($datas['new_note_custom_file']) && empty($datas['new_note_c_migareference_cover_file'])) {
                  $errors .= __('Cover image cannot be empty.') . "<br/>";
              }
              if (isset($datas['new_note_open_feature']) && !($datas['new_note_feature_id']) && empty($datas['new_note_custom_url'])) {
                  $errors .= __('Custom URL cannot be empty.') . "<br/>";
              } elseif (isset($datas['new_note_open_feature']) && !($datas['new_note_feature_id']) && !filter_var($datas['new_note_custom_url'], FILTER_VALIDATE_URL)) {
                  $errors .= __('Custom URL not valid.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else { 
                $datas['app_id']=$this->getApplication()->getId();               
                $datas['created_at']=date('Y-m-d H:i:s');               
                if (!empty($datas['new_note_custom_file'])) {                  
                  (new Migareference_Model_Migareference())->uploadApplicationFile($datas['app_id'],$datas['new_note_custom_file'],0);;
                }else {
                  $datas['new_note_custom_file']=$datas['new_note_c_migareference_cover_file'];
                }
                (new Migareference_Model_Newnotnotification())->setData($datas)->save();                
              }              
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function getnewnotetemplateAction(){   
    $destPath      = Core_Model_Directory::getBasePathTo();
    $platform_url  = explode("/",$destPath);//index 4 have platform url
    $app_id   = $this->getApplication()->getId();
    $note_template=(new Migareference_Model_Newnotnotification())->findAll(['app_id'=>$app_id])->toArray();
    header('Content-type:application/json');
    $responsedata = json_encode($note_template[0]);
    print_r($responsedata);
    exit;
   }
  public function sendreportapitestcallAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $webhook = new Migareference_Model_Webhook();
              $pre_settings  = $migareference->preReportsettigns($datas['app_id']);
              $last_report  = $migareference->lastReport($datas['app_id']);
              $errors = "";
              if (!COUNT($pre_settings) && empty($pre_settings[0]['report_api_webhook_url'])) {
                  $errors .= __('Unable to find WEBHOOK URL make sure you have first save settings.') . "<br/>";
              }
              if (!COUNT($last_report)) {
                  $errors .= __('Make sure you have at least one report to make test call.') . "<br/>";
              }             
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {   
                $response=$webhook->triggerReportWebhook($datas['app_id'],$last_report[0]['migareference_report_id'],'testReport','update');
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully WEBHOOK called REPORT NO:'.$last_report[0]['report_no']),
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
                'response' => $response,
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function sendreferrerwebhooktestcallAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $webhook = new Migareference_Model_Webhook();
              $pre_settings  = $migareference->preReportsettigns($datas['app_id']);
              $last_referrer  = $migareference->lastReferrer($datas['app_id']);
              $log['app_id']=$datas['app_id'];
              $log['user_id']=1;
              // $test_referrer  = $migareference->triggerWebhook("https://webhook.site/74c64abc-0be6-4c3b-bd58-0a011f10a908",$log);
              // throw new Exception("Error Processing Request", 1);
              
              $errors = "";
              if (!COUNT($pre_settings) && empty($pre_settings[0]['new_ref_webhook_url'])) {
                  $errors .= __('Unable to find WEBHOOK URL make sure you have first save settings.') . "<br/>";
              }
              if (!COUNT($last_referrer)) {
                  $errors .= __('Make sure you have at least one REFERRER to make test call.') . "<br/>";
              }             
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {   
                $webhook->trigerNewReferrerWebhook($datas['app_id'],$last_referrer[0]['user_id']);
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully WEBHOOK called. REFERRER ID:'.$last_referrer[0]['user_id']),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'test_referrer' => $test_referrer
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function genrateaffiliatelinkAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $utilities = new Migareference_Model_Utilities();
              $errors = "";
              if (empty($datas['url'])) {
                  $errors .= __('URL cannot be empty.') . "<br/>";
              }             
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $app_id       = $this->getApplication()->getId();
                  $bitly_crede  = $migareference->getBitlycredentails($app_id);
                  $long_url=$datas['url']."?agval=".$datas['agent_id'];
                  $short_link   = $utilities->shortLink($long_url);                  
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'affiliate_url'  => $short_link
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function sendrecaptestmailAction(){
    try {  
      $app_id = $this->getRequest()->getParam('app_id');    
      $autoReminderResponse = Migareference_Model_Db_Table_Migareference::TestautomationTriggerscron($app_id);                         
      $html = [
        'success'         => true,
        'message'         => __('Email successfully sent.'),
        'autoReminderResponse'=> $autoReminderResponse,
        'message_timeout' => 0,
        'message_button'  => 0,
        'message_loader'  => 0
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
  public function sendrecaplivemailAction(){
    try {  
      $app_id = $this->getRequest()->getParam('app_id');    
      $autoReminderResponse = Migareference_Model_Db_Table_Migareference::automationTriggerscron('test');                         
      $html = [
        'success'         => true,
        'message'         => __('Email successfully sent.'),
        'autoReminderResponse'=> $autoReminderResponse,
        'message_timeout' => 0,
        'message_button'  => 0,
        'message_loader'  => 0
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
  public function saveautomationreminderformAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $app_id        = $this->getApplication()->getId();
              $pre_settings  = $migareference->preReportsettigns($app_id);                                            
              // $autoReminderResponse = Migareference_Model_Db_Table_Migareference::TestautomationTriggerscron($app_id);              
              $errors        = "";
              $trigger_status= "";
              if (empty($datas['auto_rem_trigger']) && $datas['operation']=='create') {
                  $errors .= __('Please select Trigger') . "<br/>";
              }
              if (empty($datas['auto_rem_title'])) {
                  $errors .= __('Title cannot be empty.') . "<br/>";
              }
              if ($datas['auto_rem_trigger']==4 || $datas['auto_rem_trigger_copy']==4) {  
                  if (empty($datas['auto_rem_report_trigger_status']) && $datas['operation']=='create') {
                    $errors .= __('Please select at least one status') . "<br/>";
                  }
              }
              if ($datas['auto_rem_trigger']==1 && empty($datas['auto_rem_reports'])) {
                  $errors .= __('Number of Reports cannot be empty.') . "<br/>";
              }elseif (empty($datas['auto_rem_days']) && ($datas['auto_rem_trigger']==2 || $datas['auto_rem_trigger']==4 || $datas['auto_rem_trigger']==6 || $datas['auto_rem_trigger']==7 || $datas['auto_rem_trigger']==8)) {
                  $errors .= __('Number of Days cannot be empty.') . "<br/>";
              }
              unset($datas['auto_rem_trigger_copy']);
              unset($datas['auto_rem_fix_rating_copy']);
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                $opreation=$datas['operation'];
                $id=$datas['auto_rem_id'];                                                
                unset($datas['operation']);
                unset($datas['auto_rem_id']);
                if ($opreation=="create") {                    
                  $migareference->saveReportReminderAuto($datas);
                }else {                                        
                    $migareference->updateReportReminderAuto($id,$datas);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,                
                'autoReminderResponse' =>$autoReminderResponse,                
                'found' =>$found,                
                'notfound' =>$notfound,                                               
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function saveprizeAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $value_id = $data['app_id'];
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($data['prize_name'])) {
                  $errors .= __('Prize Name cannot be empty.') . "<br/>";
              }
              if (empty($data['credits_number'])) {
                  $errors .= __('Prize Credits cannot be empty.') . "<br/>";
              }
              if (empty($data['prize_start_date'])) {
                  $errors .= __('Prize Start date cannot be empty.') . "<br/>";
              }
              if (empty($data['prize_expire_date'])) {
                  $errors .= __('Prize Expire date  cannot be empty.') . "<br/>";
              }
              if (empty($data['prize_description'])) {
                  $errors .= __('Prize description cannot be empty.') . "<br/>";
              }
              if ($data['prize_link1_enable']) {
                  if (empty($data['prize_link1'])) {
                    $errors .= __('Prize Link 1 URL cannot be empty') . "<br/>";
                  }
                  if (empty($data['prize_link1_btn_text'])) {
                    $errors .= __('Prize Link 1 Button Text cannot be empty') . "<br/>";
                  }
              }
              if ($data['prize_link2_enable']) {
                  if (empty($data['prize_link2'])) {
                    $errors .= __('Prize Link 2 URL cannot be empty') . "<br/>";
                  }
                  if (empty($data['prize_link2_btn_text'])) {
                    $errors .= __('Prize Link 2 Button Text cannot be empty') . "<br/>";
                  }
              }
              if (empty($data['prize_icon']) && $data['operation']=='create') {
                  $errors .= __('Prize Image cannot be empty.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  if ($data['operation']=="create") {
                    unset($data['operation']);
                    unset($data['migarefrence_prizes_id']);
                    $migareference->saveprize($data);
                  }else {
                    unset($data['operation']);
                    $migareference->updateprize($data);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Prze successfully saved data.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function uploadesingleaddressesAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $errors = "";
              if (empty($data['address'])) {
                  $errors .= __('Please add a valid address.') . "<br/>";
              }
              if (empty($data['longitude'])) {
                  $errors .= __('Please add a valid Longitude') . "<br/>";
              }
              if (empty($data['latitude'])) {
                  $errors .= __('Please add a valid Latitude') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  $migareference->insertaddresses($data);
              }
              $html = [
                'success'         => true,
                'message'         => __('Address successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function uploadesinglephonenumberAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $errors = "";
              if (empty($data['phone_number'])) {
                  $errors .= __('Please add a valid phone number.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  $migareference->insertphonenumber($data);
              }
              $html = [
                'success'         => true,
                'message'         => __('Phone Number successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function uploadesinglejobsAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $errors = "";
              if (empty($data['job_title'])) {
                  $errors .= __('Please add a valid Job Title.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                    $id=$data['job_id'];
                    unset($data['job_id']);
                  if ($data['operation']=="create") {
                    unset($data['operation']);
                    $migareference->insertjob($data);
                  }else {
                    unset($data['operation']);
                    $migareference->updatejob($id,$data);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Job successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function uploadesingleprofessionAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $errors = "";
              if (empty($data['profession_title'])) {
                  $errors .= __('Please add a valid Sector Title.') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                    $id=$data['profession_id'];
                    unset($data['profession_id']);
                  if ($data['operation']=="create") {
                    unset($data['operation']);
                    $migareference->insertProfession($data);
                  }else {
                    unset($data['operation']);
                    $migareference->updateProfession($id,$data);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Sector successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function loadpropertyaddressesAction(){
              $migareference   = new Migareference_Model_Migareference();
              $app_id          = $this->getApplication()->getId();
              $property_addres = $migareference->loadpropertyaddresses($app_id);
              $ajax_responce   = array();
              $fin_res         = array();
              $destPath        = Core_Model_Directory::getBasePathTo();
              $platform_url    = explode("/",$destPath);//index 4 have platform url
              $file            = fopen("app/local/modules/Migareference/resources/propertyaddresses/reportaddresses_".$app_id.".csv","w");
              foreach ($property_addres as $key => $value) {
                fputcsv($file, $value,";");
                $report_number="#".$value['report_no'];
                if ($value['count']>1) {
                  $report_number="";
                  $property_reports = $migareference->isAddressunique($app_id,$value);
                  foreach ($property_reports as $keyy => $valuee) {
                    $report_number.="#".$valuee['report_no']." ";
                  }
                }
                $ajax_responce[] = [
                    $value['address'],
                    $value['longitude'],
                    $value['latitude'],
                    $report_number
                ];
              }
              fclose($file);
              $fin_res = [
                  "data"=>$ajax_responce
              ];
        $this->_sendJson($fin_res);
  }
  public function loadexternaladdressAction(){
              $migareference   = new Migareference_Model_Migareference();
              $app_id          = $this->getApplication()->getId();
              $property_addres = $migareference->getaddresses($app_id);
              $ajax_responce   = array();
              $fin_res         = array();
              foreach ($property_addres as $key => $value) {
                $report_number="#".$value['report_no'];
                if ($value['count']>1) {
                  $report_number="";
                  $property_reports = $migareference->isAddressunique($app_id,$value);
                  foreach ($property_reports as $keyy => $valuee) {
                    $report_number.="#".$valuee['report_no']." ";
                  }
                }
                $ajax_responce[] = [
                    $value['address'],
                    $value['longitude'],
                    $value['latitude'],
                    $report_number,
                    "<input id=''  class='sb-form-checkbox color-blue height15'  type='checkbox' name='address_to_delete[".$value['migarefrence_property_addresses_id']."]' value=".$value['migarefrence_property_addresses_id'].">"
                ];
              }
              $fin_res = [
                  "data"=>$ajax_responce
              ];
        $this->_sendJson($fin_res);
  }
  public function updatereportfieldAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
            $errors = "";
            if (empty($data['label'])) {
                $errors .= __('Please add a valid Label.') . "<br/>";
            }
            if (empty($data['field_option']) && $data['field_type']==3 && $data['options_type']==0 ) {
                $errors .= __('Please add valid Options.') . "<br/>";
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            } else {
                $migareference = new Migareference_Model_Migareference();
                $extra_field['field_type']  = $data['field_type'];
                $extra_field['label']       = $data['label'];
                $extra_field['is_required'] = $data['is_required'];
                $extra_field['is_visible']  = $data['is_visible'];
                $extra_field['is_visible_status_report']  = $data['is_visible_status_report'];
                $extra_field['field_option']= $data['field_option'];
                $extra_field['options_type']= $data['options_type'];
                $extra_field['default_option_value']= $data['default_1']."@".$data['default_2'];
                $settings['migareference_pre_report_settings_id']=$data['migareference_pre_report_settings_id'];
                $settings['enable_unique_address']=$data['enable_unique_address'];
                $settings['block_address_report']=$data['block_address_report'];
                $settings['address_grace_days']=$data['address_grace_days'];
                // Prospect Mobile Settings
                $settings['is_unique_mobile']=$data['is_unique_mobile'];
                $settings['grace_days']=$data['grace_days'];
                $settings['mobile_grace_period_action']=$data['mobile_grace_period_action'];
                $settings['grace_period_warning_message']=$data['grace_period_warning_message'];
                $settings['mobile_grace_period_database']=$data['mobile_grace_period_database'];
                $settings['grace_period_extrnal_db_url']=$data['grace_period_extrnal_db_url'];
                $migareference->updateReportfieldbyKey($extra_field,$data['migareference_report_fields_id']);
                $migareference->updatePreReport($settings);
            }
              $html = [
                'success'         => true,
                'message'         => __('Successfully update field settings.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function saveremindertypeAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $value_id = $data['app_id'];
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($data['reminder_type_text'])) {
                  $errors .= __('Title cannot be empty') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  if ($data['operation']=="create") {
                    unset($data['operation']);
                    unset($data['migarefrence_reminder_type_id']);
                    $migareference->insert_reminder_type($data);
                  }else {
                    unset($data['operation']);
                    $migareference->update_reminder_type($data['migarefrence_reminder_type_id'],$data);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Reminder type successfully saved data.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function postponereminderAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $app_id = $data['app_id'];
              if (!$app_id) {
              }
              $migareference = new Migareference_Model_Migareference();
               $subtrct_min=0;
        switch ($data['reminder_before_type']) {
          case 1:
          $subtrct_min=0;
          break;
          case 2:
          $subtrct_min=15;
          break;
          case 3:
          $subtrct_min=30;
          break;
          case 4:
          $subtrct_min=45;
          break;
          case 5:
          $subtrct_min=60;
          break;
          case 6:
          $subtrct_min=120;
          break;
          case 7:
          $subtrct_min=360;
          break;
          case 8:
          $subtrct_min=1440;
          break;
        }
              $data['rep_rem_date'] = strtotime($data['rep_rem_date']);
              $data['rep_rem_time'] = strtotime($data['rep_rem_time']);
              $reminder['event_day']=date('d',$data['rep_rem_date']);
              $reminder['event_month']=date('m',$data['rep_rem_date']);
              $reminder['event_year']=date('Y',$data['rep_rem_date']);
              $reminder['event_hour']=date('H',$data['rep_rem_time']);
              $reminder['event_min']=date('i',$data['rep_rem_time']);
              $reminder['reminder_before_type']=$data['rep_rem_before'];
              $reminder['reminder_content']=$data['rep_rem_note'];
             $event_date_time    = mktime($reminder['event_hour'], $reminder['event_min'],0, $reminder['event_month'], $reminder['event_day'],$reminder['event_year']);
             $event_date         = date("Y-m-d H:i:s", $event_date_time);
             $reminder_date      = date("Y-m-d H:i:s", strtotime("-".$subtrct_min." minutes", strtotime($event_date)));
             $reminder['event_date_time']    = $event_date;
             $reminder['reminder_date_time'] = $reminder_date;
              $migareference->update_reminder($data['rep_rem_id'],$reminder);
              $html = [
                'success'         => true,
                'message'         => __('Data successfully saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
              ];
            } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message_ler'  => $reminder,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function saveagentprovinceAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
              $app_id = $data['app_id'];
              $customer_id = $data['customer_id'];
              if (!$app_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($data['geo_country'])) {
                  $errors .= __('You must select valid country') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  $migareference->deleteAgnetProvince($app_id,$customer_id,$data['geo_country']);
                  foreach ($data['province'] as $key => $value) {
                      $datas['app_id']=$app_id;
                      $datas['province_id']=$value;
                      $datas['country_id']=$data['geo_country'];
                      $datas['user_id']=$customer_id;
                    $migareference->saveAgnetProvince($datas);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'agent_data' => $agent_data,
                'admin_log_data' => $admin_log_data
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savegelocationAction(){
      if ($data = $this->getRequest()->getPost()) {
          try {
            
              $app_id = $data['app_id'];
              if (!$app_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($data['province']) && $data['geo_province']==0) {
                  $errors .= __('Please add Province Title') . "<br/>";
              }
              if (empty($data['province_code']) && $data['geo_province_code']==0) {
                  $errors .= __('Please add Province Code') . "<br/>";
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  $migareference = new Migareference_Model_Migareference();
                  $country_id=$data['geo_country'];
                  if ($data['operation']=="create") {                      
                      $province_data['app_id']    = $data['app_id'];
                      $province_data['country_id']    = $country_id;
                      $province_data['province']    = $data['province'];
                      $province_data['province_code']    = $data['province_code'];
                      $migareference->addProvince($province_data);
                  }else {
                    $province_data['app_id']      = $data['app_id'];
                    $province_data['country_id']  = $country_id;
                    $province_data['geo_province']    = $data['geo_province'];
                    $province_data['province']    = $data['province'];
                    $province_data['province_code']    = $data['province_code'];
                    $migareference->updateProvince($province_data);
                  }
              }
              $html = [
                'success'         => true,
                'message'         => __('Data successfully saved'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'province_data'  => $province_data,
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function savestatusAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $migareference  = new Migareference_Model_Migareference();
              $smstemplate    = new Migareference_Model_Smstemplate();
              $app_id         = $this->getApplication()->getId();
              $pre_report     = $migareference->preReportsettigns($app_id);
              $value_id       = $datas['value_id'];
              $is_email_ref   = 0;
              $is_email_agt   = 0;
              $is_sms_ref     = 0;
              $is_sms_agt     = 0;
              $reminder_is_email_ref=0;
              $reminder_is_email_agt=0;
              if (!$value_id) {
                  throw new Exception('An error occurred while saving. Please try again later.');
              }
              $errors = "";
              if (empty($datas['status_title'])) {
                $errors .= __('Status title cannot be empty.') . "<br/>";
              }
              if (empty($datas['c_migareference_status_icon_cover_file'])) {
                $errors .= __('Status Icon cannot be empty.') . "<br/>";
              }
              // Email Template Validations
              if ($datas['email_notification_to_user']==1 || $datas['email_notification_to_user']==2) {
                $is_email_ref=1;
                if (empty($datas['ref_email_title'])) {
                    $errors .= __('Email Subject for Referral cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['ref_email_title'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }
                if (empty($datas['ref_email_text'])) {
                    $errors .= __('Email Message body for Referral cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['ref_email_text'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }elseif ($datas['standard_index']==4) {
                  if (strpos($datas['ref_email_text'], '@@comment@@') !== false) {
                    $temp=true;
                  }else {
                    $errors .= __('Fallback status must need @@comment@@ tag.') . "<br/>";
                  }
                }
              }
              if ($datas['email_notification_to_user']==1 || $datas['email_notification_to_user']==3) {
                $is_email_agt=1;
                if (empty($datas['agt_email_title'])) {
                    $errors .= __('Email Subject for Agent cannot be empty.') . "<br/>";
                }
                if (empty($datas['agt_email_text'])) {
                    $errors .= __('Email Message body for Agent cannot be empty.') . "<br/>";
                }
              }
              //  // Twillio SMS Template Validations
              //  if ($pre_report[0]['enable_twillio_notification']==1) {
              //   if ($datas['sms_notification_to_user']==1 || $datas['sms_notification_to_user']==2) {
              //     $is_sms_ref=1;
              //     if (empty($datas['ref_sms_text'])) {
              //         $errors .= __('sms Message body for Referral cannot be empty.') . "<br/>";
              //     }
              //   }           
              // }
              // Push Templat Validations
              if ($datas['push_notification_to_user']==1 || $datas['push_notification_to_user']==2) {
                if (empty($datas['ref_push_title'])) {
                    $errors .= __('PUSH Title for Referral cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['ref_push_title'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }
                if (empty($datas['ref_push_text'])) {
                    $errors .= __('PUSH Message for Referral cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['ref_push_text'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }
                if (strpos($datas['ref_push_text'], '@@app_link@@') !== false || strpos($datas['ref_push_text'], '@@app_name@@') !== false || strpos($datas['ref_push_title'], '@@app_link@@') !== false || strpos($datas['ref_push_title'], '@@app_name@@') !== false) {
                  $errors .= __('Push Notification can not contain @@app_link@@,@@app_name@@ tags.') . "<br/>";
                }
              }
              if ($datas['push_notification_to_user']==1 || $datas['push_notification_to_user']==3) {
                if (empty($datas['agt_push_title'])) {
                    $errors .= __('PUSH Title for Agent cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['agt_push_title'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }
                if (empty($datas['agt_push_text'])) {
                    $errors .= __('PUSH Message for Agent cannot be empty.') . "<br/>";
                }elseif ($datas['standard_index']==1 && strpos($datas['agt_push_text'], '@@agent_name@@') !== false) {
                  $errors .= __('First status not allowed to add @@agent_name@@ tag.') . "<br/>";
                }
                if (isset($datas['agt_is_feature']) && !($datas['agt_feature_id']) && empty($datas['agt_custom_url'])) {
                    $errors .= __('PUSH Custom URL for Agent cannot be empty.') . "<br/>";
                } elseif (isset($datas['agt_is_feature']) && !($datas['agt_feature_id']) && !filter_var($datas['agt_custom_url'], FILTER_VALIDATE_URL)) {
                    $errors .= __('PUSH Custom URL for Agent not valid.') . "<br/>";
                }
                if (strpos($datas['agt_push_text'], '@@app_link@@') !== false || strpos($datas['agt_push_text'], '@@app_name@@') !== false || strpos($datas['agt_push_title'], '@@app_link@@') !== false || strpos($datas['agt_push_title'], '@@app_name@@') !== false) {
                  $errors .= __('Push Notification can not contain @@app_link@@,@@app_name@@ tags.') . "<br/>";
                }
              }
              // Reminder Validations (Deprecated)
              // if (isset($datas['is_auto_reminder'])) {
              //   // Grace Days
              //   if (empty($datas['reminder_grace']) || $datas['reminder_grace']<0) {
              //     $errors .= __('You must add Reminder Grace days.') . "<br/>";
              //   }
              //   //Reminder Email Templates Validations
              //   if ($datas['reminder_email_notification_to_user']==1 || $datas['reminder_email_notification_to_user']==2) {
              //     $reminder_is_email_ref=1;
              //     if (empty($datas['reminder_ref_email_title'])) {
              //       $errors .= __('Reminder Email Subject for Referral cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_ref_email_title'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }
              //     if (empty($datas['reminder_ref_email_text'])) {
              //       $errors .= __('Reminder Email Message body for Referral cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_ref_email_text'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==3) {
              //       if (strpos($datas['reminder_ref_email_text'], '@@comment@@') !== false) {
              //         $temp=true;
              //       }else {
              //         $errors .= __('Reminder Report closing status must need @@comment@@ tag.') . "<br/>";
              //       }
              //     }
              //   }
              //   if ($datas['reminder_email_notification_to_user']==1 || $datas['reminder_email_notification_to_user']==3) {
              //     $reminder_is_email_agt=1;
              //     if (empty($datas['reminder_agt_email_title'])) {
              //       $errors .= __('Reminder Email Subject for Agent cannot be empty.') . "<br/>";
              //     }
              //     if (empty($datas['reminder_agt_email_text'])) {
              //       $errors .= __('Reminder Email Message body for Agent cannot be empty.') . "<br/>";
              //     }
              //   }
              //   // Reminder PUSH Validations
              //   if ($datas['reminder_push_notification_to_user']==1 || $datas['reminder_push_notification_to_user']==2) {
              //     if (empty($datas['reminder_ref_push_title'])) {
              //         $errors .= __('Reminder PUSH Title for Referral cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_ref_push_title'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }
              //     if (empty($datas['reminder_ref_push_text'])) {
              //         $errors .= __('Reminder PUSH Message for Referral cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_ref_push_text'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==3) {
              //       if (strpos($datas['reminder_ref_email_text'], '@@comment@@') !== false) {
              //         $temp=true;
              //       }else {
              //         $errors .= __('Reminder Report closing status must need @@comment@@ tag.') . "<br/>";
              //       }
              //     }
              //     if (strpos($datas['reminder_ref_push_text'], '@@app_link@@') !== false || strpos($datas['reminder_ref_push_text'], '@@app_name@@') !== false || strpos($datas['reminder_ref_push_title'], '@@app_link@@') !== false || strpos($datas['reminder_ref_push_title'], '@@app_name@@') !== false) {
              //       $errors .= __('Push Notification can not contain @@app_link@@,@@app_name@@ tags.') . "<br/>";
              //     }
              //   }
              //   if ($datas['reminder_push_notification_to_user']==1 || $datas['reminder_push_notification_to_user']==3) {
              //     if (empty($datas['reminder_agt_push_title'])) {
              //         $errors .= __('Reminder PUSH Title for Agent cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_agt_push_title'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }
              //     if (empty($datas['reminder_agt_push_text'])) {
              //         $errors .= __('Reminder PUSH Message for Agent cannot be empty.') . "<br/>";
              //     }elseif ($datas['reminder_standard_index']==1 && strpos($datas['reminder_agt_push_text'], '@@agent_name@@') !== false) {
              //       $errors .= __('Reminder First status not allowed to add @@agent_name@@ tag.') . "<br/>";
              //     }
              //     if (isset($datas['reminder_agt_is_feature']) && !($datas['reminder_agt_feature_id']) && empty($datas['reminder_agt_custom_url'])) {
              //         $errors .= __('Reminder PUSH Custom URL for Agent cannot be empty.') . "<br/>";
              //     } elseif (isset($datas['reminder_agt_is_feature']) && !($datas['reminder_agt_feature_id']) && !filter_var($datas['reminder_agt_custom_url'], FILTER_VALIDATE_URL)) {
              //         $errors .= __('Reminder PUSH Custom URL for Agent not valid.') . "<br/>";
              //     }
              //     if (strpos($datas['reminder_agt_push_text'], '@@app_link@@') !== false || strpos($datas['reminder_agt_push_text'], '@@app_name@@') !== false || strpos($datas['reminder_agt_push_title'], '@@app_link@@') !== false || strpos($datas['reminder_agt_push_title'], '@@app_name@@') !== false) {
              //       $errors .= __('Push Notification can not contain @@app_link@@,@@app_name@@ tags.') . "<br/>";
              //     }
              //   }
              // }
              $status['is_comment']    = 0;
              // Declined Validations
              if (isset($datas['is_auto_declined'])) {
                if (empty($datas['declined_grace']) || $datas['declined_grace']<0) {
                  $errors .= __('You must add Declined grace days.') . "<br/>";
                }
                if (empty($datas['declined_to_status'])) {
                  $errors .= __('You must select Declined status.') . "<br/>";
                }
                if (empty($datas['auto_fallabck_comment'])) {
                  $errors .= __('You must add Fallback Comment.') . "<br/>";
                }else {
                  $status['is_comment'] = 1;
                  $status['auto_fallabck_comment'] = $datas['auto_fallabck_comment'];
                }
              }
              // Comment required Validations
              if (isset($datas['is_comment'])) {
                $status['is_comment']    = 1;
              }
              //Import Reports
              if ($datas['operation']=='update' && $datas['import_status']>0) {
                $import_order = $migareference->reportStatusByKey($datas['import_status'],0);//from
                $status_order = $migareference->reportStatusByKey($datas['migareference_report_status_id'],0);//where
                if ($status_order[0]['order_id']>$import_order[0]['order_id']) {
                  $reports=$migareference->statusreports($app_id,$datas['import_status']);
                  foreach ($reports as $key => $value) {
                    $value['currunt_report_status'] = $status_order[0]['migareference_report_status_id'];
                    $value['last_modification']     = $status_order[0]['status_title'];
                    $value['last_modification_at']  = date('Y-m-d H:i:s');
                    $value['is_reminder_sent']      = 0;
                    $value['last_modification_by']  = $_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $migareference->updatepropertyreport($value);
                    $log_data['app_id']=$value['app_id'];
                    $log_data['user_id']=$_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $log_data['report_id']=$value['migareference_report_id'];
                    $log_data['log_type']="Update Status";
                    $log_data['log_detail']="Import Status to ".$status_order[0]['status_title'];
                    // Save Staus Update Log
                    $migareference->saveLog($log_data);
                  }
                }else {
                  $errors .= __('The Order of Status should be higher than the status from where you import.') . "<br/>";
                }
              }
              // Pause Sending
              if (isset($datas['is_pause_sending'])) {
                $status['is_pause_sending']  = 1;
              }
              if (!empty($errors)) {
                  throw new Exception($errors);
              } else {
                  if ($datas['operation']=="create") {
                      // Save
                      if ($datas['status_type']==1) {
                          throw new Exception("Error Proklfjdsaklst", 1);
                          // Standard Staus
                          // 1 for new report
                          // 2 Make trigger to paid
                          // 3 for closing report
                          // 4 Declined/Fallback
                          // Save Status
                          $translation['app_id']      = $app_id;
                          $translation['text_field']  = $datas['status_title'];
                          $status['app_id']        = $app_id;
                          $status['status_title']  = $datas['status_title'];
                          $status['status_icon']   = $datas['c_migareference_status_icon_cover_file'];
                          $status['is_standard']   = 1;
                          $status['standard_type'] = $datas['standard_index'];
                          $status['order_id']      = 10000;
                          $max_order               = $migareference->getMaxorder($app_id);
                          if (count($max_order)>0) {
                            $status['order_id']    = $max_order[0]['order_id']+1;
                          }
                          if ($datas['standard_index']==2 || $datas['standard_index']==4) {
                            $status['is_acquired']    = 1;
                          }else {
                            $status['is_acquired']    = 0;
                          }
                          // Declined
                          $status['is_declined']      = 0;
                          if (isset($datas['is_auto_declined'])) {
                            $status['is_declined']          = 1;
                            $status['declined_grace_days']  = $datas['declined_grace'];
                            $status['declined_to']          = $datas['declined_to_status'];
                          }
                          // Reminder
                          $status['is_reminder']      = 0;
                          $datas['is_auto_reminder']=0;
                          if (isset($datas['is_auto_reminder'])) {
                            // Report Status
                            $status['is_reminder']         = 1;
                            $status['reminder_grace_days'] = $datas['reminder_grace'];
                            // Email Template
                            $email_template_data['reminder_is_email_ref']    = $reminder_is_email_ref;
                            $email_template_data['reminder_is_email_agt']    = $reminder_is_email_agt;
                            $email_template_data['reminder_ref_email_title'] = $datas['reminder_ref_email_title'];
                            $email_template_data['reminder_ref_email_text']  = $datas['reminder_ref_email_text'];
                            $email_template_data['reminder_agt_email_title'] = $datas['reminder_agt_email_title'];
                            $email_template_data['reminder_agt_email_text']  = $datas['reminder_agt_email_text'];
                          }
                          $status_id                              = $migareference->saveStatus($status);
                          // Email Template
                          $email_template_data['app_id']          = $app_id;
                          $email_template_data['value_id']        = $datas['value_id'];
                          $email_template_data['is_email_ref']    = $is_email_ref;
                          $email_template_data['is_email_agt']    = $is_email_agt;
                          $email_template_data['event_id']        = $status_id;
                          $email_template_data['ref_email_title'] = $datas['ref_email_title'];
                          $email_template_data['ref_email_text']  = $datas['ref_email_text'];
                          $email_template_data['agt_email_title'] = $datas['agt_email_title'];
                          $email_template_data['agt_email_text']  = $datas['agt_email_text'];
                          $email_template_id                      = $migareference->saveEmail($email_template_data);
                          // SMS template
                          $sms_template_data['app_id']          = $app_id;
                          $sms_template_data['value_id']        = $datas['value_id'];
                          $sms_template_data['is_sms_ref']      = $is_sms_ref;
                          $sms_template_data['is_sms_agt']      = $is_sms_agt;
                          $sms_template_data['event_id']        = $status_id;                          
                          $sms_template_data['ref_sms_text']    = $datas['ref_sms_text'];                          
                          $sms_template_data['agt_sms_text']    = $datas['agt_sms_text'];
                          $sms_template_id=$smstemplate->insertSmsTemplate($sms_template_data);                                                  
                          // PUSH Template
                          $datas['event_id']                      = $status_id;
                          $push_template_id                       = $migareference->savePush($app_id, $datas);
                          $noti_event_data['app_id']            = $app_id;
                          $noti_event_data['value_id']          = $datas['value_id'];
                          $noti_event_data['event_id']          = $status_id;
                          $noti_event_data['push_template_id']  = $push_template_id;
                          $noti_event_data['email_template_id'] = $email_template_id;
                          $noti_event_data['sms_template_id']   = $sms_template_id;
                          $noti_event_data['email_delay_days']  = $datas['email_delay_days'];
                          $noti_event_data['push_delay_days']   = $datas['push_delay_days'];
                          $noti_event_data['email_delay_hours'] = $datas['email_delay_hours'];
                          $noti_event_data['push_delay_hours']  = $datas['push_delay_hours'];
                          $push_template_id                     = $migareference->saveNotificationevent( $noti_event_data);
                        } else {
                          // Normarl Staus
                          $translation['app_id']      = $app_id;
                          $translation['text_field']  = $datas['status_title'];
                          $status['app_id']        = $app_id;
                          $status['status_title']  = $datas['status_title'];
                          $status['status_icon']   = $datas['c_migareference_status_icon_cover_file'];
                          $status['is_standard']   = 0;
                          $status['standard_type'] = 0;
                          $status['is_acquired']   = 0;
                          $status['order_id']      = 10000;
                          $max_order               = $migareference->getMaxorder($app_id);
                          if (count($max_order)>0) {
                            $status['order_id']    = $max_order[0]['order_id']+1;
                          }
                          if (isset($datas['is_acquired'])) {
                            $status['is_acquired']    = 1;
                          }
                          // Declined
                          $status['is_declined']      = 0;                          
                          if (isset($datas['is_auto_declined'])) {
                            $status['is_declined']          = 1;
                            $status['declined_grace_days']  = $datas['declined_grace'];
                            $status['declined_to']          = $datas['declined_to_status'];
                          }
                          // Reminder
                          $status['is_reminder']      = 0;
                          $datas['is_auto_reminder']=0;
                          if (isset($datas['is_auto_reminder'])) {
                            // Report Status
                            $status['is_reminder']         = 1;
                            $status['reminder_grace_days'] = $datas['reminder_grace'];
                            // Email Template
                            $email_template_data['reminder_is_email_ref']    = $reminder_is_email_ref;
                            $email_template_data['reminder_is_email_agt']    = $reminder_is_email_agt;
                            $email_template_data['reminder_ref_email_title'] = $datas['reminder_ref_email_title'];
                            $email_template_data['reminder_ref_email_text']  = $datas['reminder_ref_email_text'];
                            $email_template_data['reminder_agt_email_title'] = $datas['reminder_agt_email_title'];
                            $email_template_data['reminder_agt_email_text']  = $datas['reminder_agt_email_text'];
                          }                          
                          $status_id                              = $migareference->saveStatus($status);
                          $email_template_data['app_id']          = $app_id;
                          $email_template_data['value_id']        = $datas['value_id'];
                          $email_template_data['is_email_ref']    = $is_email_ref;
                          $email_template_data['is_email_agt']    = $is_email_agt;
                          $email_template_data['event_id']        = $status_id;
                          $email_template_data['ref_email_title'] = $datas['ref_email_title'];
                          $email_template_data['ref_email_text']  = $datas['ref_email_text'];
                          $email_template_data['agt_email_title'] = $datas['agt_email_title'];
                          $email_template_data['agt_email_text']  = $datas['agt_email_text'];
                          $email_template_id                      = $migareference->saveEmail($email_template_data);
                          $sms_template_data['app_id']          = $app_id;
                          $sms_template_data['value_id']        = $datas['value_id'];
                          $sms_template_data['is_sms_ref']      = $is_sms_ref;
                          $sms_template_data['is_sms_agt']      = $is_sms_agt;
                          $sms_template_data['event_id']        = $status_id;                          
                          $sms_template_data['ref_sms_text']    = $datas['ref_sms_text'];                          
                          $sms_template_data['agt_sms_text']    = $datas['agt_sms_text'];
                          $sms_template_id=$smstemplate->insertSmsTemplate($sms_template_data);
                          $datas['event_id']                      = $status_id;
                          $push_template_id                       = $migareference->savePush($app_id, $datas);
                          $noti_event_data['app_id']            = $app_id;
                          $noti_event_data['value_id']          = $datas['value_id'];
                          $noti_event_data['event_id']          = $status_id;
                          $noti_event_data['push_template_id']  = $push_template_id;
                          $noti_event_data['email_template_id'] = $email_template_id;
                          $noti_event_data['sms_template_id']   = $sms_template_id;
                          $noti_event_data['email_delay_days']  = $datas['email_delay_days'];
                          $noti_event_data['push_delay_days']   = $datas['push_delay_days'];
                          $noti_event_data['email_delay_hours'] = $datas['email_delay_hours'];
                          $noti_event_data['push_delay_hours']  = $datas['push_delay_hours'];
                          $push_template_id                     = $migareference->saveNotificationevent( $noti_event_data);
                        }
                    }else {
                      // Update
                      if (!isset($datas['is_pause_sending'])) {
                        $datas['is_pause_sending']=0;
                      }
                      if ($datas['status_type']==1) {
                        // Standard Status
                        // Reminder
                        $datas['is_auto_declined'] = (isset($datas['is_auto_declined'])) ? 1 : 0 ;
                        // $datas['IS_REMIND']=0;
                        if (isset($datas['is_auto_reminder'])) {
                          // Email Template
                          $email_template_data['reminder_is_email_ref']    = $reminder_is_email_ref;
                          $email_template_data['reminder_is_email_agt']    = $reminder_is_email_agt;
                          $email_template_data['reminder_ref_email_title'] = $datas['reminder_ref_email_title'];
                          $email_template_data['reminder_ref_email_text']  = $datas['reminder_ref_email_text'];
                          $email_template_data['reminder_agt_email_title'] = $datas['reminder_agt_email_title'];
                          $email_template_data['reminder_agt_email_text']  = $datas['reminder_agt_email_text'];
                        }
                        $email_template_data['is_email_ref']    = $is_email_ref;
                        $email_template_data['is_email_agt']    = $is_email_agt;
                        $email_template_data['event_id']        = $datas['migareference_notification_event_id'];
                        $email_template_data['ref_email_title'] = $datas['ref_email_title'];
                        $email_template_data['ref_email_text']  = $datas['ref_email_text'];
                        $email_template_data['agt_email_title'] = $datas['agt_email_title'];
                        $email_template_data['agt_email_text']  = $datas['agt_email_text'];
                        $email_template_data['migareference_email_template_id']  = $datas['migareference_email_template_id'];
                        $sms_template_data['app_id']          = $app_id;
                        $sms_template_data['value_id']        = $datas['value_id'];
                        $sms_template_data['event_id']        = $status_id;                          
                        $sms_template_data['is_sms_ref']      = $is_sms_ref;
                        $sms_template_data['is_sms_agt']      = $is_sms_agt;
                        $sms_template_data['ref_sms_text']    = $datas['ref_sms_text'];                          
                        $sms_template_data['agt_sms_text']    = $datas['agt_sms_text'];
                        $sms_template_data['created_at']      = date('Y-m-d H:i:s');
                        $smstemplate->setData($sms_template_data)->save();
                        $email_template_id   = $migareference->updateEmail( $email_template_data);
                        $update_push         = $migareference->updatePush($app_id, $datas);
                        $update_status       = $migareference->updateStatus($datas);
                        
                        
                          $noti_event_data['app_id']            = $app_id;
                          $noti_event_data['value_id']          = $datas['value_id'];
                          $noti_event_data['event_id']          = $datas['migareference_notification_event_id'];
                          $noti_event_data['email_delay_days']  = $datas['email_delay_days'];
                          $noti_event_data['push_delay_days']   = $datas['push_delay_days'];
                          $noti_event_data['email_delay_hours'] = $datas['email_delay_hours'];
                          $noti_event_data['push_delay_hours']  = $datas['push_delay_hours'];
                          // $noti_event_data['migareference_notification_event_id']  = $datas['migareference_notification_event_id'];
                          $push_template_id                     = $migareference->updateNotificationevent( $noti_event_data);
                      } else {
                        // Normarl Staus
                        $datas['is_auto_declined'] = (isset($datas['is_auto_declined'])) ? 1 : 0 ;
                        $datas['is_auto_reminder']=0;
                        // if (isset($datas['is_auto_reminder'])) {
                        //   // Email Template
                        //   $email_template_data['reminder_is_email_ref']    = $reminder_is_email_ref;
                        //   $email_template_data['reminder_is_email_agt']    = $reminder_is_email_agt;
                        //   $email_template_data['reminder_ref_email_title'] = $datas['reminder_ref_email_title'];
                        //   $email_template_data['reminder_ref_email_text']  = $datas['reminder_ref_email_text'];
                        //   $email_template_data['reminder_agt_email_title'] = $datas['reminder_agt_email_title'];
                        //   $email_template_data['reminder_agt_email_text']  = $datas['reminder_agt_email_text'];
                        // }else {
                        // }
                        $email_template_data['is_email_ref']    = $is_email_ref;
                        $email_template_data['is_email_agt']    = $is_email_agt;
                        $email_template_data['event_id']        = $datas['migareference_notification_event_id'];
                        $email_template_data['ref_email_title'] = $datas['ref_email_title'];
                        $email_template_data['ref_email_text']  = $datas['ref_email_text'];
                        $email_template_data['agt_email_title'] = $datas['agt_email_title'];
                        $email_template_data['agt_email_text']  = $datas['agt_email_text'];
                        $email_template_data['migareference_email_template_id']  = $datas['migareference_email_template_id'];
                        if (isset($datas['is_acquired'])) {
                          $datas['is_acquired']= 1;
                        }
                        $sms_template_data['app_id']          = $app_id;
                        $sms_template_data['value_id']        = $datas['value_id'];
                        $sms_template_data['event_id']        = $datas['migareference_notification_event_id'];                          
                        $sms_template_data['is_sms_ref']      = $is_sms_ref;
                        $sms_template_data['is_sms_agt']      = $is_sms_agt;
                        $sms_template_data['ref_sms_text']    = $datas['ref_sms_text'];                          
                        $sms_template_data['agt_sms_text']    = $datas['agt_sms_text'];
                        $sms_template_data['created_at']      = date('Y-m-d H:i:s');
                        $smstemplate->setData($sms_template_data)->save();
                        $email_template_id   = $migareference->updateEmail( $email_template_data);
                        $update_push         = $migareference->updatePush($app_id, $datas);
                        $update_status       = $migareference->updateStatus($datas);
                          $noti_event_data['app_id']            = $app_id;
                          $noti_event_data['value_id']          = $datas['value_id'];
                          $noti_event_data['event_id']          = $datas['migareference_notification_event_id'];
                          $noti_event_data['email_delay_days']  = $datas['email_delay_days'];
                          $noti_event_data['push_delay_days']   = $datas['push_delay_days'];
                          $noti_event_data['email_delay_hours'] = $datas['email_delay_hours'];
                          $noti_event_data['push_delay_hours']  = $datas['push_delay_hours'];
                          // $noti_event_data['migareference_notification_event_id']  = $datas['migareference_notification_event_id'];
                          $push_template_id                     = $migareference->updateNotificationevent( $noti_event_data);
                      }
                    }
              }
              $html = [
                'success'         => true,
                'message'         => __('Status Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'status'          => $status,                
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'status'         => $status,
                'datas'          => $datas,
                'mytestdata'          => $mytestdata,
                'sms_template_id'           => $sms_template_id
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function cropAction(){
      if ($datas = $this->getRequest()->getPost()) {
          try {
              $uploader = new Core_Model_Lib_Uploader();
              $file = $uploader->savecrop($datas);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'file'            => $file,
                  'message_button'  => 0,
                  'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
  }
  public function addhowtoAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
				      $errors = '';
              $migareference = new Migareference_Model_Migareference();
              if (!empty($datA['site_link'])) {
                if (!filter_var($data['site_link'], FILTER_VALIDATE_URL)) {
                  $errors .= __('Add a valid Site link.')."<br/>";
                }
              }
              if (!empty($errors)) {
                throw new Exception($errors);
              }else {
                if ($data['operation']=='create') {
                  unset($data['operation']);
                  $last_id=$migareference->addhowto($data);
                  $log_array['app_id']    = $data['app_id'];
                  $log_array['user_id']   = 0;
                  $log_array['log_type']  = "Save How To";
                  $log_array['log_detail']= "Save How To use Report Form";
                  $migareference->saveLog($log_array);
                }else {
                  unset($data['operation']);
                  $last_id=$migareference->updatehowto($data);
                  $log_array['app_id']    = $data['app_id'];
                  $log_array['user_id']   = 0;
                  $log_array['log_type']  = "Update How To";
                  $log_array['log_detail']= "Update How To use Report Form";
                  $migareference->saveLog($log_array);
                }
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0
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
  public function customcreditsAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference = new Migareference_Model_Migareference();
              if (empty($data['custom_credits']) ) {
                $errors .= __('Please add credits.')."<br/>";
              }
              if (empty($data['custom_credit_description']) ) {
                $errors .= __('Please add description')."<br/>";
              }
              if (!empty($errors)) {
                throw new Exception($errors);
              }else {
                $app_id      = $this->getApplication()->getId();
                $referrer_id = $data['user_id'];
                $earning['app_id']           = $app_id;
                $earning['user_id']          = $referrer_id;
                $earning['amount']           = $data['custom_credits'];
                $earning['transection_source']= __("Admin_Custom");
                $earning['entry_type']       = ($data['entry_type']==1) ? 'C' : 'D';
                $earning['trsansection_by']  = $_SESSION['front']['object_id'];
                $earning['user_type']        = 2;//1: for app cutomers 2: for app admins 3: for agent
                $earning['prize_id']         = 0;
                $earning['trsansection_description']  = $data['custom_credit_description'];
                $migareference->saveLedger($earning);
                // Send Notifications
                $getCreditsApiNotification = $migareference->getCreditsApiNotification($app_id);
                if ($getCreditsApiNotification[0]['ref_credits_api_enable_notification']==1) {
                  $referrer_user=$migareference->getSingleuser($app_id, $referrer_id);
                  $credit_balance   = $migareference->get_credit_balance($app_id,$referrer_id);
                  $default        = new Core_Model_Default();
                  $base_url       = $default->getBaseUrl();
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
                    $data['custom_credits'],
                    ($data['entry_type']==1) ? 'Credit' : 'Debit',
                    $data['custom_credit_description'],
                    ($credit_balance[0]['credits']>0) ? $credit_balance[0]['credits'] : 0, 
                    $app_link
                  ];
                  if ($getCreditsApiNotification[0]['ref_credits_api_notification_type']==1 || $getCreditsApiNotification[0]['ref_credits_api_notification_type']==2) {
                    $email_data['email_title']=str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_email_title']);
                    $email_data['email_text']=str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_email_text']);
                    // Email Meta Data
                    $email_data['calling_method']='Credits_Admin_Custom';                    
                    $mail_retur = $migareference->sendMail($email_data,$app_id,$referrer_id);
                  }
                  if ($getCreditsApiNotification[0]['ref_credits_api_notification_type']==1 || $getCreditsApiNotification[0]['ref_credits_api_notification_type']==3) {
                    $push_data['open_feature'] = $getCreditsApiNotification[0]['ref_credits_api_open_feature'];
                    $push_data['feature_id']   = $getCreditsApiNotification[0]['ref_credits_api_feature_id'];
                    $push_data['custom_url']   = $getCreditsApiNotification[0]['ref_credits_api_custom_url'];
                    $push_data['cover_image']  = $getCreditsApiNotification[0]['ref_credits_api_cover_file'];
                    $push_data['app_id']       = $app_id;    
                    $push_data['calling_method']='Credits_Admin_Custom'; 
                    $push_data['push_title']   = str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_push_title']);
                    $push_data['push_text']    = str_replace($tags_list, $tag_values, $getCreditsApiNotification[0]['ref_credits_api_push_text']);
                    $push_return = $migareference->sendPush($push_data,$app_id,$referrer_id);
                  }
              }
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'testresponse'   => $response
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function chrckmandateAction(){
          $migareference  = new Migareference_Model_Migareference();
          $pkid           = $this->getRequest()->getParam('pkid');
          $report_id      = $this->getRequest()->getParam('report_id');
          $app_id         = $this->getApplication()->getId();
          $checkmandate   = $migareference->reportStatusByKey($pkid,$report_id);
          $pre_report     = $migareference->preReportsettigns($app_id);
          if ($report_id>0) {
            $report         = $migareference->get_report_by_key($report_id);
          }else {
            $report[0]=[];
          }
          $payload  = [
              'checkmandate'=> $checkmandate[0],
              'pre_report'=>$pre_report,
              'report'=>$report[0]
          ];
      $this->_sendJson($payload);
  }
  public function getreportAction(){
          $migareference  = new Migareference_Model_Migareference();
          $report_id      = $this->getRequest()->getParam('report_id');
          $report         = $migareference->get_report_by_key($report_id);
          $payload  = [
              'report'=>$report[0]
          ];
      $this->_sendJson($payload);
  }
  public function updatereportAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $application    = $this->getApplication();
              $app_id         = $application->getId();
              $errors         = "";
              $default        = new Core_Model_Default();
              $base_url       = $default->getBaseUrl();
              $temp_data=[];
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
                //General validation rules for required fileds
                // Explisit validation rules for email to exlude from required fields
                if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && $value['field_type']!=7  && empty($data[$name])) {
                  $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
                }elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($static_fields[$value['field_type_count']])) {
                  $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
                }
                // Explisit validation rules for email
                 // Explicitly check for email
                if ($value['type']==2 && $value['is_visible']==1 && $value['field_type']==7 && !empty($data[$name]) && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                  $errors .= __('Email is not correct. Please add a valid email address') . "<br/>";
                }
                //Explisit validation rules for owner mobile
                if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='address' && $pre_report_settings[0]['enable_unique_address']==1) {
                    $address['address']  = $data['address'];
                    $address['longitude']= $data['longitude'];
                    $address['latitude'] = $data['latitude'];
                    $days=$pre_report_settings[0]['address_grace_days'];
                    $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
                    $internal_address_duplication=$migareference->isinternalAddressunique($app_id,$address,$date);
                    $external_address_duplication=$migareference->isexternalAddressunique($app_id,$address);
                }
                //Explisit validation for owner_mobile
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
                  $property_report['last_modification_by'] = $_SESSION['front']['object_id'];
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
                  $property_report['report_custom_type']= $data['report_custom_type'];
                  $property_report['owner_mobile']  = $data['owner_mobile'];
                  $property_report['owner_hot']     = $data['owner_hot'];
                  $property_report['note']          = $data['note'];
                  $property_report['latitude']      = $data['latitude'];
                  $property_report['longitude']     = $data['longitude'];
                  $property_report['address']       = $data['address'];
                  $property_report['migareference_report_id']=$data['migareference_report_id'];
                  $property_report['extra_dynamic_fields']=serialize($data);
                  $save_data     = $migareference->updatepropertyreport($property_report);
                  $agent_user_data  = $migareference->getReportSponsor($app_id,$data['migareference_report_id']);
                  // On Update Report Type,Property Staus ->Save log,Send Notification
                  $log_data['app_id']=$data['app_id'];
                  $log_data['user_id']=$property_report['last_modification_by'];
                  $log_data['user_type']=2;
                  $log_data['report_id']=$data['migareference_report_id'];
                  if ($previous_item[0]['currunt_report_status']!=$data['report_status']) {
                    // Save Staus Update Log
                    $log_data['log_source']="ADMIN-END";
                    $log_data['log_type']="Update Status";
                    $log_data['log_detail']="Update Status to ".$status_data[0]['status_title'];
                    $migareference->saveLog($log_data);              
                    // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
                    $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($data['app_id'],$data['migareference_report_id'],$data['report_status'],$property_report['last_modification_by'],'ADMIN-END','update');                                                                    
                    
                  }
                  // Update
                  $pro_settings['invoice_name']=$data['invoice_name'];
                  $pro_settings['invoice_surname']=$data['invoice_surname'];
                   $migareference->updatePropertysettings($pro_settings,$data['migareference_invoice_settings_id']);
                  // Save earnings if Property Sold
                  if ($data['standard_type']==3  && $data['reward_type']==1) { //Commision in Euro
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
                  }elseif($data['standard_type']==3 && $data['reward_type']==2) { //Credits commission
                    $earning['app_id']           = $data['app_id'];
                    $earning['user_id']          = $data['referral_user_id'];
                    $earning['amount']           = ($data['commission_fee_report']>1) ? $data['commission_fee_report'] : $data['commission_fee'];
                    $earning['entry_type']       = 'C';
                    $earning['trsansection_by']  = $_SESSION['front']['object_id'];
                    $earning['user_type']        = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $earning['prize_id']         = 0;
                    $earning['report_id']        = $data['migareference_report_id'];
                    $earning['trsansection_description'] ="Report #".$previous_item[0]['report_no'];
                    $migareference->saveLedger($earning);
                  }
              }
              // throw new Exception("Error Processing Request", 1);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0,
                  'notifcation_response' => $$notifcation_response,                    
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'temp_data' => $temp_data,
                    'notifcation_response' => $notifcation_response,                    
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function updatebulkreportAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference = new Migareference_Model_Migareference();
              $application    = $this->getApplication();
              $app_id         = $application->getId();
              $errors         = "";
              $is_error       = 0;
              $default        = new Core_Model_Default();
              $base_url       = $default->getBaseUrl();
              $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
              if ($data['is_comment_bulk']==1 && empty($data['comment_bulk'])) {
                    $errors .= __('You must add Comment for Referral.') . "<br/>";
              }
              if (!empty($errors)) {
                throw new Exception($errors);
              }
              $errors         = "";
              $errors        .= __('These reports cannot update.') . "<br/>";
              $acquire_error_text="";
              // Validations
              foreach ($data['report_to_update'] as $key => $value) {
                $satus_key                                = "report_status_bulk_".$value;
                $stauts_id                                = $data['bulk_status'];
                $report_id                                = $value;
                // Udpate Report table
                $status_data                              = $migareference->getStatus($app_id,$stauts_id);
                $property_report['last_modification_by']  = $_SESSION['front']['object_id'];
                $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                $agent_user_mail                          = $migareference->getAappadminagentdata($property_report['last_modification_by']);
                $previous_item                            = $migareference->getReport($data['app_id'],$report_id);
                $acquire_error=1;
                $bulk_index='bulk_acquire_'.$report_id;
                if ($data['is_acquired_bulk']==1 && isset($data[$bulk_index])) {
                  if ($data[$bulk_index]>0) {
                    $property_report['commission_fee'] =$data[$bulk_index];
                  }else {
                    $acquire_error=0;
                  }
                }
                if ($acquire_error==1) {
                }else {
                if ($acquire_error==0) {
                  $errors.=__("Commission Required for ").$previous_item[0]['report_no']."</br>";
                }
                // else {
                //   $errors .= __("Can not move to lower status").$previous_item[0]['report_no']."</br>";
                // }
                // ERROR a report cannot goes back to previous status
                $is_error++;
              }
            }
            if(!empty($is_error)) {
                throw new Exception($errors);
            }
              foreach ($data['report_to_update'] as $key => $value) {
                $satus_key                                = "report_status_bulk_".$value;
                $stauts_id                                 = $data['bulk_status'];
                $report_id                                = $value;
                // Udpate Report table
                $status_data                              = $migareference->getStatus($app_id,$stauts_id);
                $property_report['last_modification']     = $status_data[0]['status_title'];
                $property_report['last_modification_by']  = $_SESSION['front']['object_id'];
                $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                $property_report['last_modification_at']  = date('Y-m-d H:i:s');
                $property_report['is_reminder_sent']      = 0;
                $property_report['migareference_report_id']= $report_id;
                $property_report['currunt_report_status'] = $stauts_id;
                $agent_user_mail                          = $migareference->getAappadminagentdata($property_report['last_modification_by']);
                $previous_item                            = $migareference->getReport($data['app_id'],$report_id);
                $acquire_error=1;
                unset($property_report['commission_fee']);
                $bulk_index='bulk_acquire_'.$report_id;
                if ($data['is_acquired_bulk']==1 && isset($data[$bulk_index])) {
                  if ($data[$bulk_index]>0) {
                    $property_report['commission_fee'] =$data[$bulk_index];
                  }else {
                    $acquire_error=0;
                  }
                }
                if ($acquire_error==1) {
                  $save_data                = $migareference->updatepropertyreport($property_report);
                  // Save Log
                  // localize some veribales
                  $data['commission_fee']   = $previous_item[0]['commision_fee'];
                  $log_data['app_id']       = $app_id;
                  $log_data['user_id']      = $property_report['last_modification_by'];
                  $log_data['user_type']      = 2;
                  $log_data['report_id']    = $report_id;
                  $log_data['log_type']     = "Update Status";
                  $log_data['log_detail']   = "Update Status to ".$status_data[0]['status_title'];
                  $migareference->saveLog($log_data);
                  // On Update Report Type,Property Staus ->Save log,Send Notification
                  if ($previous_item[0]['currunt_report_status']!=$stauts_id) {
                    // Save Staus Update Log
                    // Save Notification send Log
                    $push_log_data['user_id']    = 99999;
                    $push_log_data['log_type']   = "Push Notification sent";
                    $push_log_data['log_detail'] = "Status change Notification";
                    $email_log_data['user_id']   = 99999;
                    $email_log_data['log_type']  = "Email Notification sent";
                    $email_log_data['log_detail']= "Status change Notification";
                    $migareference->saveLog($push_log_data);
                    $migareference->saveLog($email_log_data);
                    // // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
                    $eventtemplats=$migareference->getEventNotificationTemplats($app_id,$stauts_id);
                    $user_id=$property_report['last_modification_by'];
                    if (!empty($eventtemplats) && $eventtemplats[0]['is_pause_sending']==0) {
                    //     // Send Notification
                    //       // START EMAIL Notification
                          if ($eventtemplats[0]['email_delay_days']==0 && $eventtemplats[0]['email_delay_hours']==0) {
                            // Send Immidiately Notification
                              // Find users to send notification (All Admins+1 Referral Added Report)
                              $admin_customers   = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                              $agent_data              = $migareference->getAappadminagentdata($user_id);//Who update the report
                              $referral_customers= $migareference->getRefferalCustomers($app_id,$previous_item[0]['user_id']);//Admin Users->Agents
                              $sponsor_customers         = $migareference->getSponsorList($app_id,$previous_item[0]['user_id']);//Agents user
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
                                  $email_data['email_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@comment@@",$data['comment'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@commission@@",$previous_item[0]['commission_fee'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@agnet_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_name@@",$previous_item[0]['name'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_link@@",$app_link,$email_data['email_text']);
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
                                  $email_data['email_title']= str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_title']);
                                  $email_data['email_title']= str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_title']);
                                //Message
                                  $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@comment@@",$data['comment'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@commission@@",$previous_item[0]['commission_fee'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_name@@",$previous_item[0]['name'],$email_data['email_text']);
                                  $email_data['email_text'] = str_replace("@@app_link@@",$app_link,$email_data['email_text']);
                                  if ($eventtemplats[0]['is_email_ref']) {
                                    $mail_retur = $migareference->sendMail($email_data,$app_id,$previous_item[0]['user_id']);
                                  }
                          }
                          // START PUSH Notification
                          if ($eventtemplats[0]['push_delay_days']==0 && $eventtemplats[0]['push_delay_hours']==0) {
                            // Send Immidiately Notification
                              // Find users to send notification (All Admins+1 Referral Added Report)
                              $push_agent_user_data    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                              $agent_data              = $migareference->getAappadminagentdata($user_id);//Who update the report
                              $push_reffreal_user_data = $migareference->getRefferalCustomers($app_id,$previous_item[0]['user_id']);//Admin Users->Agents
                              //Send to Agents / Admins
                                // Subject
                                  $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner@@",$previous_item[0]['owner_name'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner@@",$previous_item[0]['owner_name'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner_phone@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner_phone@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@agnet_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_title']);
                                //Message
                                  $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@comment@@",$data['comment'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@commission@@",$previous_item[0]['commission_fee'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_text']);
                                  $push_data['open_feature'] = $eventtemplats[0]['agt_open_feature'];
                                  $push_data['feature_id'] = $eventtemplats[0]['agt_feature_id'];
                                  $push_data['custom_url'] = $eventtemplats[0]['agt_custom_url'];
                                  $push_data['cover_image'] = $eventtemplats[0]['agt_cover_image'];
                                  $push_data['app_id'] = $app_id;
                                  if ($eventtemplats[0]['is_push_agt']) {
                                    foreach ($push_agent_user_data as $key => $value) {
                                      $mail_retur = $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                                    }
                                  }
                              //Send to Refferral / User who add Report
                                // Subject
                                  $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_title']);
                                  $push_data['push_title']= str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_title']);
                                //Message
                                  $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner@@",$previous_item[0]['owner_name']." ".$previous_item[0]['owner_surname'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@property_owner_phone@@",$previous_item[0]['owner_mobile'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@report_no@@",$previous_item[0]['report_no'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@commission@@",$previous_item[0]['commission_fee'],$push_data['push_text']);
                                  $push_data['push_text'] = str_replace("@@agent_name@@",$agent_user_mail[0]['firstname']." ".$agent_user_mail[0]['lastname'],$push_data['push_text']);
                                  $push_data['open_feature'] = $eventtemplats[0]['ref_open_feature'];
                                  $push_data['feature_id']   = $eventtemplats[0]['ref_feature_id'];
                                  $push_data['custom_url']   = $eventtemplats[0]['ref_custom_url'];
                                  $push_data['cover_image']  = $eventtemplats[0]['ref_cover_image'];
                                  $push_data['app_id'] = $app_id;
                                  if ($eventtemplats[0]['is_push_ref']) {
                                   $mail_retur = $migareference->sendPush($push_data,$app_id,$previous_item[0]['user_id']);
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
                            $cron_notification['notification_event_id']=$stauts_id;
                            $cron_notification['trigger_start_time']=date('Y-m-d H:i:s');
                            $cron_notification['push_delay_hours']=$push_hours;
                            $cron_notification['email_delay_hours']=$email_hours;
                            $migareference->saveCronnotification($cron_notification);
                          }
                    }
                  }
                  // Save earnings if Property Sold
                  if ($data['standard_type_bulk']==3 && $previous_item[0]['reward_type']==1) {
                    $earning['app_id']=$data['app_id'];
                    $earning['value_id']=$data['value_id'];
                    $earning['refferral_user_id']=$previous_item[0]['user_id'];
                    $earning['sold_user_id']=$_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $earning['report_id']=$report_id;
                    $earning['earn_amount']=$previous_item[0]['commission_fee'];
                    $earning['platform']="Owner End";
                    $migareference->saveEarning($earning);
                  }elseif ($data['standard_type_bulk']==3 && $previous_item[0]['reward_type']==2) {
                    $earning['app_id']           = $data['app_id'];
                    $earning['user_id']          = $previous_item[0]['user_id'];
                    $earning['amount']           = $previous_item[0]['commission_fee'];
                    $earning['entry_type']       = 'C';
                    $earning['trsansection_by']  = $_SESSION['front']['object_id'];
                    $earning['user_type']             = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $earning['prize_id']         = 0;
                    $earning['report_id']        = $previous_item[0]['migareference_report_id'];
                    $earning['trsansection_description']  = "Report #".$previous_item[0]['report_no'];
                    $migareference->saveLedger($earning);
                  }
                } else {
                  if ($acquire_error==0) {
                    $errors.=__("Commission Required for ").$previous_item[0]['report_no']."</br>";
                  }
                  $is_error++;
                }
              }
              if(!empty($is_error)) {
                  throw new Exception($errors);
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'message_ader' => $save_data
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function saveprereportAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              if (empty($data['payable_limit'])) {
                  $errors .= __('Limit cannot be Empty.')."<br/>";
              }
              if ($data['commission_type']==2 && $data['reward_type']==1) {
                if (empty($data['fix_commission_amount'])) {
                    $errors .= __('For Fix Commission type you must add Fix amount.')."<br/>";
                }
              }              
              if ($data['commission_type']==2 && $data['reward_type']==2) {
                if (empty($data['fix_commission_credits'])) {
                    $errors .= __('For Fix Commission type you must add Fix Credit Points.')."<br/>";
                }
              }
              if ($data['reward_type']==2 && empty($data['credit_expire'])) {
                $errors .= __('You must add credit expire limit.')."<br/>";
              }
              if ($data['commission_type']==1  && $data['reward_type']==1) {
                if (empty($data['percent_commission_amount'])) {
                    $errors .= __('For Percent Commission type you must add Calculated Amount.')."<br/>";
                }elseif ($data['percent_commission_amount']>100 || $data['percent_commission_amount']<1) {
                    $errors .= __('Percent value should be in between 1 to 100')."<br/>";
                }
              }
              if ($data['commission_type']==3  && $data['reward_type']==1) {
                if (empty($data['percent_commission_amount'])) {
                    $errors .= __('For Percent Commission type you must add Calculated Amount.')."<br/>";
                }elseif ($data['percent_commission_amount']>100 || $data['percent_commission_amount']<1) {
                    $errors .= __('Percent value should be in between 1 to 100')."<br/>";
                }
                if (empty($data['price_range_text_from']) || empty($data['price_range_text_to'])) {
                    $errors .= __('You must add price Range.')."<br/>";
                }elseif ($data['price_range_text_from']<0 || $data['price_range_text_to']<0) {
                    $errors .= __('Range values must be a positive number.')."<br/>";
                }
              }
              if ($data['commission_type']==1  && $data['reward_type']==2) {
                if (empty($data['percent_commission_credits'])) {
                    $errors .= __('For Percent Commission type you must add Calculated Credits.')."<br/>";
                }elseif ($data['percent_commission_credits']>100 || $data['percent_commission_credits']<1) {
                    $errors .= __('Percent value should be in between 1 to 100')."<br/>";
                }
              }
              if ($data['commission_type']==3  && $data['reward_type']==2) {
                if (empty($data['percent_commission_credits'])) {
                    $errors .= __('For Percent Commission type you must add Calculated Credits.')."<br/>";
                }elseif ($data['percent_commission_credits']>100 || $data['percent_commission_credits']<1) {
                    $errors .= __('Percent value should be in between 1 to 100')."<br/>";
                }
              }
              if ($data['is_unique_mobile']==1) {
                if (empty($data['grace_days']) || $data['grace_days']==0) {
                    $errors .= __('You must add Grace Days to set Unique Mobile Number.')."<br/>";
                }
              }
              if ($data['is_declined']==1) {
                if (empty($data['declined_grace']) || $data['declined_grace']<1 || empty($data['declined_comment'])) {
                    $errors .= __('You must add Decline Period Days and comment to set up Declined Status.')."<br/>";
                }
              }
              if ($data['mandatory_extra_one']==1 && empty($data['extra_one_label']) ) {
                $errors .= __('You must add label for Extra 1')."<br/>";
              }
              if ($data['mandatory_extra_two']==1 && empty($data['extra_two_label']) ) {
                $errors .= __('You must add label for Extra 2')."<br/>";
              }
              if ($data['check_android_version']==1 && empty($data['android_store_version']) ) {
                $errors .= __('You must add Android Store Version')."<br/>";
              }
              if ($data['check_ios_version']==1 && empty($data['ios_store_version']) ) {
                $errors .= __('You must add IOS Store Version')."<br/>";
              }
              if (empty(trim($data['privacy']))) {
                  $errors .= __('Privacy cannot be Empty.')."<br/>";
              }
              if (empty(trim($data['terms']))) {
                  $errors .= __('Terms cannot be Empty.')."<br/>";
              }
			  //commented by imran
              /* if (empty(trim($data['confirm_report_privacy_label']))) {
                  $errors .= __('Privacy Label cannot be Empty.')."<br/>";
              } */
              if (empty(trim($data['authorized_call_back_label']))) {
                  $errors .= __('Authorized Label cannot be Empty.')."<br/>";
              }
              if (empty(trim($data['owner_hot_label']))) {
                  $errors .= __('Owner hot Label cannot be Empty.')."<br/>";
              }
              // Map addrsss settings as sponsor type
              // https://trello.com/c/w5kkKIM7/264-province-field-twice-on-profile
              if ($data['sponsor_type']==2) {
                  $data['enable_main_address']=1;
                  $data['mandatory_main_address']=1;
                  $data['enable_sub_address']=1;
                  $data['mandatory_sub_address']=1;
              }        
              if(!empty($errors)) {
                  throw new Exception($errors);
              }else {
                // Map Account DOB settings in Module
                $user_account_settings = $migareference->useraccountSettings($data['app_id']);
                $value_id              = $user_account_settings[0]['value_id'];
                $user_account_settings = json_decode($user_account_settings[0]['settings']);
                $user_account_settings->extra_birthdate= ($data['enable_birthdate']==1) ? true : false ;
                $user_account_settings->extra_birthdate_required= ($data['mandatory_birthdate']==1) ? true : false ;
                $account_data['settings']     = json_encode($user_account_settings);
                $migareference->updateuseraccountSettings($value_id,$account_data);
                unset($data['enable_birthdate']);
                unset($data['mandatory_birthdate']);
                if ($data['operation']=='create') {
                  unset($data['operation']);
                  $migareference->savePreReport($data);
                }else {
                  unset($data['operation']);
                  $migareference->updatePreReport($data);
                }
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'messaton'  => $account_data,
                  'short_link'  => $short_link,
                  'messat'  => $value_id
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'temp' => $short_link,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function upategdprsettingsAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              // GDPR Teplates Validation
              if ($data['consent_info_active']==1) {
                if (empty(trim($data['consent_info_popup_title']))) {
                    $errors .= __('Consent info Popup Title can not be empty')."<br/>";
                }
                if (empty(trim($data['consent_info_popup_body']))) {
                    $errors .= __('Consent info Popup Body can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_invit_msg_body']))) {
                    $errors .= __('Consent info Sharing Text can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_col_page_title']))) {
                    $errors .= __('Consent page Titel can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_col_page_header']))) {
                    $errors .= __('Consent page Header can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_col_page_body']))) {
                    $errors .= __('Consent page Body can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_thank_page_title']))) {
                    $errors .= __('Consent THANK YOU PAGE Title can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_thank_page_header']))) {
                    $errors .= __('Consent THANK YOU PAGE Header can not be empty')."<br/>";
                }                
                if (empty(trim($data['consent_thank_page_body']))) {
                    $errors .= __('Consent THANK YOU PAGE Body can not be empty')."<br/>";
                }                
              }
              // // Invite Popup Validations
              if ($data['invite_consent_warning_active']==2) {
                if (empty(trim($data['invite_consent_warning_title']))) {
                    $errors .= __('Consent info Popup Title can not be empty')."<br/>";
                }
                if (empty(trim($data['invite_consent_warning_body']))) {
                    $errors .= __('Consent info Popup Body can not be empty')."<br/>";
                }    
                if (empty($data['invite_message'])) {
                  $errors .= __('You must add sharing Text while allow to INVITE PROSPECTUS.')."<br/>";
                }            
                if (empty(trim($data['landing_page_title']))) {
                    $errors .= __('Landing page Title can not be empty')."<br/>";
                }    
                if (empty(trim($data['landing_page_form_title']))) {
                    $errors .= __('Landing page Form Title can not be empty')."<br/>";
                }    
                if (empty(trim($data['reportconfirm_page_title']))) {
                    $errors .= __('Report Confirm Page Titel can not be empty')."<br/>";
                }    
                if (empty(trim($data['reportconfirm_page_message']))) {
                    $errors .= __('Report Confirm Page Message can not be empty')."<br/>";
                }    
              }                            
              if(!empty($errors)) {
                  throw new Exception($errors);
              }               
              if ($data['operation']=="create") {
                unset($data['operation']);
                $migareference->saveGdprSetings($data);              
              }else {
                unset($data['operation']);
                $migareference->updateGdprSetings($data);              
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'bitly' =>$bitlyresult
                ];
              }
              $this->_sendJson($html);
            }
  }  
  public function gdprprivacyAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
			  if (empty(trim($data['confirm_report_privacy_label']))) {
				$errors .= __('Privacy label cannot be empty.')."<br/>";
			  }              
              if ($data['enable_privacy_global_settings'] == 1 && empty(trim($data['privacy_global_settings']))) {
				$errors .= __('GDPR Statement can not be empty')."<br/>";         
              } else if (in_array($data['enable_privacy_global_settings'], [2, 3])) {
				if (empty(trim($data['confirm_report_privacy_link']))) {
					$errors .= __('Privacy link cannot be empty.')."<br/>";
				} else if (filter_var(trim($data['confirm_report_privacy_link']), FILTER_VALIDATE_URL) === FALSE) {
					$errors .= __('Privacy link is not a valid URL')."<br/>";
				}
			  }

              unset($data['operation']);
              if(!empty($errors)) {
                  throw new Exception($errors);
              }               
              $migareference->updatePreReport($data);              
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'bitly' =>$bitlyresult
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function gdprspecialAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();                            
              unset($data['operation']);
              if(!empty($errors)) {
                  throw new Exception($errors);
              }               
              $migareference->updatePreReport($data);              
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'bitly' =>$bitlyresult
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function wellcomeemailAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
                if (empty(trim($data['referrer_wellcome_email_title']))) {
                    $errors .= __('Title can not be empty')."<br/>";
                }
                if (empty(trim($data['referrer_wellcome_email_body']))) {
                    $errors .= __('Body can not be empty')."<br/>";
                }
              if(!empty($errors)) {
                  throw new Exception($errors);
              }
              if ($data['operation']=='create') {
                unset($data['operation']);
                $migareference->savePreReport($data);
              }else {
                unset($data['operation']);
                $migareference->updatePreReport($data);
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
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
  public function optinwellcomeemailAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $WelcomeModal  = new Migareference_Model_Welcomenotificationtemplate();
              if (empty(trim($data['referrer_optin_wellcome_email_title']))) {
                  $errors .= __('Title can not be empty')."<br/>";
              }
              if (empty(trim($data['referrer_optin_wellcome_email_body']))) {
                  $errors .= __('Body can not be empty')."<br/>";
              }
              if (empty($data['welcome_push_title'])) {
                  $errors .= __('PUSH title cannot be empty.') . "<br/>";
              }
              if (empty($data['welcome_push_title'])) {
                  $errors .= __('PUSH message cannot be empty.') . "<br/>";
              }
              if ($data['welcome_push_open_feature']==1 && $data['welcome_push_feature_id']==0 && empty($data['welcome_push_custom_url'])) {
                  $errors .= __('CUSTOM ULR cannot be empty.') . "<br/>";
              }
              if(!empty($errors)) {
                  throw new Exception($errors);
              }              
              $pre_settings['migareference_pre_report_settings_id']=$data['migareference_pre_report_settings_id'];
              $pre_settings['app_id']=$data['app_id'];
              $pre_settings['enable_optin_welcome_email']=$data['enable_optin_welcome_email'];
              $pre_settings['referrer_optin_wellcome_email_reply_to']=$data['referrer_optin_wellcome_email_reply_to'];
              $pre_settings['referrer_optin_wellcome_email_bcc_to']=$data['referrer_optin_wellcome_email_bcc_to'];
              $pre_settings['referrer_optin_wellcome_email_title']=$data['referrer_optin_wellcome_email_title'];
              $pre_settings['referrer_optin_wellcome_email_body']=$data['referrer_optin_wellcome_email_body'];
              
              $welcome_push['app_id']=$data['app_id'];
              $welcome_push['migarefrence_welcome_notification_id']=$data['migarefrence_welcome_notification_id'];
              $welcome_push['welcome_push_enable']=$data['welcome_push_enable'];
              $welcome_push['welcome_push_title']=$data['welcome_push_title'];
              $welcome_push['welcome_push_text']=$data['welcome_push_text'];
              $welcome_push['welcome_push_open_feature']=$data['welcome_push_open_feature'];
              $welcome_push['welcome_push_feature_id']=$data['welcome_push_feature_id'];
              $welcome_push['welcome_push_custom_url']=$data['welcome_push_custom_url'];
              $welcome_push['welcome_push_custom_file']=$data['welcome_push_custom_file'];
              $welcome_push['remove_welcome_push_custom_cover_img_field']=$data['remove_welcome_push_custom_cover_img_field'];
              if ($data['operation']=='create') {
                $migareference->savePreReport($pre_settings);                
              }else {
                $migareference->updatePreReport($pre_settings);
              }
              if (!empty($data['welcome_push_custom_file'])) {                  
                (new Migareference_Model_Migareference())->uploadApplicationFile($data['app_id'],$data['welcome_push_custom_file'],0);;
              }else {
                $welcome_push['welcome_push_custom_file']=$data['welcome_push_c_migareference_cover_file'];
              }
              $WelcomeModal->setData($welcome_push)->save();
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
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
  public function savereportapiAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $data['report_api_token']=$this->randomPassword(35);
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function savecreditsapiAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $data['credits_api_token']=$this->randomPassword(35);
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function savetwilliocredentialsAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function savereportwebhookAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function savenewrefwebhookAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              // $webhook  = new Migareference_Model_Webhook();
              // $webhook_url=$webhook->referrerWebhookParamsTemplate(1,39,'create');
              // throw new Exception("Error Processing Request", 1);
              
              $data['enable_new_ref_webhooks_create'] = (isset($data['enable_new_ref_webhooks_create'])) ? 1 : 0 ;
              $data['enable_new_ref_webhooks_update'] = (isset($data['enable_new_ref_webhooks_update'])) ? 1 : 0 ;
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1,
                    'webhook_url' => $webhook_url
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function saveenablecreditsapiAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $migareference->updatePreReport($data);
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function saveoptinformAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $optinform  = new Migareference_Model_Optinform();
              $optin_settings=$optinform->getOptinSettings($data['app_id']);

              $enrolling_page_url = isset($data['enrolling_page_url']) ? trim($data['enrolling_page_url']) : '';
              $enroll_sharing_message = isset($data['enroll_sharing_message']) ? trim($data['enroll_sharing_message']) : '';

              if (empty($enrolling_page_url)) {
                throw new Exception(__('Enrolling Page URL is required.'));
              }

              if (!filter_var($enrolling_page_url, FILTER_VALIDATE_URL)) {
                throw new Exception(__('Please provide a valid Enrolling Page URL.'));
              }

              if (empty($enroll_sharing_message)) {
                throw new Exception(__('Enroll Sharing Message is required.'));
              }

              $optin_setting_model = new Migareference_Model_Optinsetting();
              $optin_setting = $optin_setting_model->find([
                'app_id' => $data['app_id'],
              ]);

              $optin_setting_payload = [
                'enrolling_page_url' => $enrolling_page_url,
                'enroll_sharing_message' => $enroll_sharing_message,
              ];

              if ($optin_setting->getId()) {
                $optin_setting_payload['migareference_optin_setting_id'] = $optin_setting->getId();
                $existingSerializedSettings = $optin_setting->getoptinSetting();
                if (!is_null($existingSerializedSettings)) {
                  $optin_setting_payload['optin_setting'] = $existingSerializedSettings;
                }
                $optin_setting->setData($optin_setting_payload)->save();
              } else {
                $optin_setting_payload['app_id'] = $data['app_id'];
                $optin_setting_model->setData($optin_setting_payload)->save();
              }

              unset($data['enrolling_page_url'], $data['enroll_sharing_message']);

              if (count($optin_settings)) {
                $data['migareference_optin_form_id']=$optin_settings[0]['migareference_optin_form_id'];
              }
              $optinform->setData($data)->save();
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function twilliotestformAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              $pre_report_settings = $migareference->preReportsettigns($data['app_id']);              
              if (empty($data['twillio_test_message']) || empty($data['test_phone'])) {
                throw new Exception(__("Please add valid Phone and Message body"));
              }
              if (!empty($pre_report_settings[0]['twillio_token']) && !empty($pre_report_settings[0]['twillio_sid'])) {
                $response=$migareference->sendTestSms($data['twillio_test_message'],$data['test_phone'],$data['app_id']);
                if ($response!="OK") {
                  throw new Exception($response);
                }
              }else {
                throw new Exception(__("Could not found TWILLIO API credentials")); 
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully send test SMS.'),
                  'message_timeout' => 0,
                  'message_button'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'count' => $count,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function saveappcontentAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              $migareference  = new Migareference_Model_Migareference();
              if (empty($data['how_it_works_label'])) {
                  $errors .= __('You must add label for HOW IT WORKS BUTTON.')."<br/>";
              }
              if (empty($data['add_property_box_label'])) {
                  $errors .= __('You must add label for Add Report BUTTON.')."<br/>";
              }
              if (empty($data['report_status_box_label'])) {
                  $errors .= __('You must add label for REPORT STATUS BUTTON.')."<br/>";
              }
              if (empty($data['prizes_box_label'])) {
                  $errors .= __('You must add label for PRIZE BUTTON.')."<br/>";
              }
              if (empty($data['reminders_box_label'])) {
                  $errors .= __('You must add label for REMINDER BUTTON.')."<br/>";
              }
              if (empty($data['phonebooks_box_label'])) {
                  $errors .= __('You must add label for PHONEBOOK BUTTON.')."<br/>";
              }
              if (empty($data['statistics_box_label'])) {
                  $errors .= __('You must add label for STATISTICS BUTTON.')."<br/>";
              }
              if (empty($data['settings_box_label'])) {
                  $errors .= __('You must add label for SETTINGS BUTTON.')."<br/>";
              }
              if (empty($data['referre_report_box_label'])) {
                  $errors .= __('You must add label for REFERRER BUTTON.')."<br/>";
              }              
              if(!empty($errors)) {
                  throw new Exception($errors);
              }else {
                $application       = $this->getApplication();
                $app_id            = $application->getId();
                $data['app_id']    = $app_id;
                $pre_settings['migareference_pre_report_settings_id']=$data['migareference_pre_report_settings_id'];
                $pre_settings['internal_report_note']=$data['internal_report_note'];
                $pre_settings['external_report_note']=$data['external_report_note'];
                unset($data['migareference_pre_report_settings_id']);
                unset($data['internal_report_note']);
                unset($data['external_report_note']);

                $app_c_two['app_id']=$data['app_id'];
                $app_c_two['report_type_pop_title']=$data['report_type_pop_title'];                
                $app_c_two['add_ref_btn_bg_color']=$data['add_ref_btn_bg_color'];                
                $app_c_two['add_ref_btn_text_color']=$data['add_ref_btn_text_color'];                
                $app_c_two['report_type_pop_text']=$data['report_type_pop_text'];
                $app_c_two['report_type_pop_btn_one_text']=$data['report_type_pop_btn_one_text'];
                $app_c_two['report_type_pop_btn_one_color']=$data['report_type_pop_btn_one_color'];
                $app_c_two['report_type_pop_btn_one_bg_color']=$data['report_type_pop_btn_one_bg_color'];                
                $app_c_two['report_type_pop_btn_one_icon_pos']=$data['report_type_pop_btn_one_icon_pos'];
                $app_c_two['report_type_pop_btn_two_text']=$data['report_type_pop_btn_two_text'];
                $app_c_two['report_type_pop_btn_two_color']=$data['report_type_pop_btn_two_color'];
                $app_c_two['report_type_pop_btn_two_bg_color']=$data['report_type_pop_btn_two_bg_color'];                
                $app_c_two['report_type_pop_btn_two_icon_pos']=$data['report_type_pop_btn_two_icon_pos'];
                $app_c_two['report_type_pop_btn_two_icon']=$data['report_type_pop_btn_two_icon'];
                $app_c_two['report_type_pop_btn_one_icon']=$data['report_type_pop_btn_one_icon'];
                $app_c_two['report_type_pop_cover']=$data['report_type_pop_cover'];
                // Assign values
                $app_c_two['qlf_box_label'] = $data['qlf_box_label'];
                $app_c_two['qlf_box_bg_color'] = $data['qlf_box_bg_color'];
                $app_c_two['qlf_box_text_color'] = $data['qlf_box_text_color'];
                $app_c_two['qlf_cover_file'] = $data['qlf_cover_file'];
                $app_c_two['qlf_level_one_cover'] = $data['qlf_level_one_cover'];
                $app_c_two['qlf_level_one_title'] = $data['qlf_level_one_title'];
                $app_c_two['qlf_level_one_subtitle'] = $data['qlf_level_one_subtitle'];
                $app_c_two['qlf_level_one_btn_one_cover'] = $data['qlf_level_one_btn_one_cover'];
                $app_c_two['qlf_level_one_btn_one_title'] = $data['qlf_level_one_btn_one_title'];
                $app_c_two['qlf_level_one_btn_one_subtitle'] = $data['qlf_level_one_btn_one_subtitle'];
                $app_c_two['qlf_level_one_btn_two_cover'] = $data['qlf_level_one_btn_two_cover'];
                $app_c_two['qlf_level_one_btn_two_title'] = $data['qlf_level_one_btn_two_title'];
                $app_c_two['qlf_level_one_btn_two_subtitle'] = $data['qlf_level_one_btn_two_subtitle'];
                $app_c_two['qlf_level_two_cover'] = $data['qlf_level_two_cover'];
                $app_c_two['qlf_level_two_title'] = $data['qlf_level_two_title'];
                $app_c_two['qlf_level_two_subtitle'] = $data['qlf_level_two_subtitle'];

                $app_c_two['enroll_url_box_label'] = $data['enroll_url_box_label'];
                $app_c_two['enroll_url_box_bg_color'] = $data['enroll_url_box_bg_color'];
                $app_c_two['enroll_url_box_text_color'] = $data['enroll_url_box_text_color'];
                $app_c_two['enroll_url_cover_file'] = $data['enroll_url_cover_file'];

                unset($data['add_ref_btn_bg_color']);                
                unset($data['add_ref_btn_text_color']);                
                unset($data['report_type_pop_title']);                
                unset($data['report_type_pop_text']);
                unset($data['report_type_pop_btn_one_text']);
                unset($data['report_type_pop_btn_one_color']);
                unset($data['report_type_pop_btn_one_bg_color']);                
                unset($data['report_type_pop_btn_one_icon_pos']);
                unset($data['report_type_pop_btn_two_text']);
                unset($data['report_type_pop_btn_two_color']);
                unset($data['report_type_pop_btn_two_bg_color']);                
                unset($data['report_type_pop_btn_two_icon_pos']);
                unset($data['qlf_box_label']);
                //unset field add 
                unset($data['qlf_box_bg_color']);
                unset($data['qlf_box_text_color']);
                
                 
                unset($data['qlf_level_one_title']);
                unset($data['qlf_level_one_subtitle']);
                
                unset($data['qlf_level_one_btn_one_title']);
                unset($data['qlf_level_one_btn_one_subtitle']);
               
                unset($data['qlf_level_one_btn_two_title']);
                unset($data['qlf_level_one_btn_two_subtitle']);
               
                unset($data['qlf_level_two_title']);
                unset($data['qlf_level_two_subtitle']);

                unset($data['enroll_url_box_label']);
              
                unset($data['enroll_url_box_bg_color']);
                unset($data['enroll_url_box_text_color']);
                $app_content_two = $migareference->get_app_content_two($app_id);
                if (!COUNT($app_content_two)) {
                  $migareference->addAppcontenttwo($app_c_two);
                }else {
                  $app_c_two['migarefrence_app_content_two_id']=$app_content_two[0]['migarefrence_app_content_two_id'];
                  if (empty($app_c_two['report_type_pop_btn_two_icon'])) {
                    unset($app_c_two['report_type_pop_btn_two_icon']);
                  }
                  if (empty($app_c_two['report_type_pop_btn_one_icon'])) {
                    unset($app_c_two['report_type_pop_btn_one_icon']);
                  }
                  if (empty($app_c_two['report_type_pop_cover'])) {
                    unset($app_c_two['report_type_pop_cover']);
                  }
                  if (empty($app_c_two['qlf_cover_file'])) {
                    unset($app_c_two['qlf_cover_file']);
                  }
                   if (empty($app_c_two['qlf_level_one_cover'])) {
                    unset($app_c_two['qlf_level_one_cover']);
                  }
                   if (empty($app_c_two['qlf_level_one_btn_one_cover'])) {
                    unset($app_c_two['qlf_level_one_btn_one_cover']);
                  }
                   if (empty($app_c_two['qlf_level_one_btn_two_cover'])) {
                    unset($app_c_two['qlf_level_one_btn_two_cover']);
                  }
                   if (empty($app_c_two['qlf_level_two_cover'])) {
                    unset($app_c_two['qlf_level_two_cover']);
                  }
                   if (empty($app_c_two['enroll_url_cover_file'])) {
                    unset($app_c_two['enroll_url_cover_file']);
                  }
                  $migareference->updateAppcontenttwo($app_c_two);  
                }
                $migareference->updateAppcontent($data);
                $migareference->updatePreReport($pre_settings);
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0,
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function responseaddressesfileAction(){
    if ($data = $this->getRequest()->getPost()) {
        try {
              if($data['response_type']==2) {
                  throw new Exception(__($data['response_message_body']));
              }
              $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved data.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0
              ];
            } catch (Exception $e) {
                $html = [
                    'error'          => true,
                    'message'        => __($e->getMessage()),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
              }
              $this->_sendJson($html);
            }
  }
  public function appadminsAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {
              $customer      = new Customer_Model_Customer();
              $migareference = new Migareference_Model_Migareference();
              $admin         = new Migareference_Model_Admin();
              $customers           = $customer->findAll(['app_id = ?' => $datas['app_id'],]);
              $admins              = $migareference->getAdmins($datas['app_id']);
              $agents              = $migareference->get_all_agents($datas['app_id']);
              if ($datas['province_id']!=0) {
                $agents              = $migareference->agentGeoProvince($datas['app_id'],$datas['province_id']);
              }              
              $contact_users       = $migareference->getContactsUsers($datas['app_id']);
              $social_sahre        = $migareference->get_socialshares($datas['app_id']);
              $pre_report_settings = $migareference->preReportsettigns($datas['app_id']);
              $data                = [];
              $admin_array         = [];
              $agent_array         = [];
              $contact_users_array = [];
              $index               = 1;
              $socialshares_array  = [];
              $allow_paid_toggle   = (count($pre_report_settings) && intval($pre_report_settings[0]['agent_can_manage']) === 1);
              foreach ($admins as $key => $value) {
                $admin_array[$index]=$value['user_id'];
                $index++;               
              }              
              $index=1;
              foreach ($agents as $key => $value) {
                $agent_array[$index]=$value['user_id'];
                $index++;
              }
              $index=1;
              foreach ($contact_users as $key => $value) {
                $contact_users_array[$index]=$value['customer_id'];
                $index++;
              }
              $index=1;
              foreach ($social_sahre as $key => $value) {
                $socialshares_array[$index]=$value['user_id'];
                $index++;
              }
              if(count($customers)) {
                  foreach($customers as $customer) {
                      $agent_type='';
                      // Build Action Buttons
                      $admin->find(['user_id' => $customer->getId(),'app_id'  => $datas['app_id']]);
                      $is_admin=0;
                      $is_agent=0;
                      $is_contact=0;
                      $admin_action = '<button title="'.__('Is Admin').' ?'.'" class="btn btn-danger" onclick="ownerStatus(1,'.$customer->getId().')">'."<i class='fa fa-user' rel=''></i>".'</button>';
                      if(array_search($customer->getId(),$admin_array)) {
                          $admin_action  = '<button title="'.__('Is Admin').' ?'.'"  class="btn btn-info" onclick="ownerStatus(2,'.$customer->getId().')">'."<i class='fa fa-user' rel=''></i>".'</button>';
                          $is_admin=1;
                      }
                      $display = ($pre_report_settings[0]['sponsor_type']==1) ? 'none' : '' ;
                      $affiliate_display = ($pre_report_settings[0]['sponsor_type']==2) ? 'none' : '' ;
                      $is_agent_mandatory=$pre_report_settings[0]['enable_mandatory_agent_selection'];
                      $is_multi_agent=$pre_report_settings[0]['enable_multi_agent_selection'];
                      $agetn_action = '<button title="'.__("Is Agent")." ?".'" class="btn btn-danger" onclick="agentStatus(1,'.$customer->getId().','.COUNT($agents).','.$is_agent_mandatory.','.$is_multi_agent.')">'."<i class='fa fa-users' rel=''></i>".'</button>';
                      $province_action = '<button title="'.__("Assign Provinces")." ?".'" style="display:'.$display.'" disabled class="btn btn-danger" onclick="openProvincesmodal(1,'.$customer->getId().','.'0'.')">'."<i class='fa fa-map-marker' rel=''></i>".'</button>';
                      $affiliate_action = '<button title="'.__("Get Affiliate Link")." ?".'" style="display:'.$affiliate_display.'" disabled class="btn btn-danger" onclick="openAffiliatemodal(1,'.$customer->getId().')">'."<i class='fa fa-handshake-o' rel=''></i>".'</button>';                      
                      if(array_search($customer->getId(),$agent_array)) {
                          $agent_province=$migareference->agentProvinces($datas['app_id'],$customer->getId());
                          $country_id = (COUNT($agent_province)) ? $agent_province[0]['country_id'] : 0 ;
                          $is_agent         = 1;
                          $agetn_action     = '<button title="'.__("Is Agent")." ?".'" class="btn btn-info" onclick="agentStatus(2,'.$customer->getId().','.COUNT($agents).','.$is_agent_mandatory.','.$is_multi_agent.')">'."<i class='fa fa-users' rel=''></i>".'</button>';
                          $province_action  = '<button title="'.__("Assign Provinces")." ?".'" style="display:'.$display.'" class="btn btn-info" onclick="openProvincesmodal(2,'.$customer->getId().','.$country_id.')">'."<i class='fa fa-map-marker' rel=''></i>".'</button>';
                          $affiliate_action = '<button title="'.__("Get Affiliate Link")." ?".'" style="display:'.$affiliate_display.'" class="btn btn-info" onclick="openAffiliatemodal(2,'.$customer->getId().')">'."<i class='fa fa-handshake-o' rel=''></i>".'</button>';
                          $agent_item      = $migareference->is_agent($datas['app_id'],$customer->getId());
                        }
                        $action='';
                        $agent_phonebook='';
                        if ($is_agent) {
                          $action.='<select  class="input-flat admin-dropdown">';
                          $action.='<option  value="">'.__("Assign Admin").'</option>';
                          foreach ($admins as $key => $value) {
                            $selected = ($agent_item[0]['admin_user_id']==$value['user_id']) ? 'selected' : '' ;
                            $action.='<option '.$selected.' value="'.$value['user_id'].'">'.$value['lastname']." ".$value['firstname'].'</option>';
                          }
                          $action.='<option value="0">'._('No Admin').'</option>';
                          $action.='</select>';

                          if ($agent_item[0]['agent_type']==1) {
                            $agent_type='<span class="badge" style="background-color:rgba(17, 193, 243, 1);color:white;">'.$pre_report_settings[0]['agent_type_label_one'].'</span>';
                          }else {
                            $agent_type='<span class="badge" style="background-color:rgba(255, 201, 0, 1);color:white;">'.$pre_report_settings[0]['agent_type_label_two'].'</span>';
                          }
                          $agent_phonebook = '<input id="" class="sb-form-checkbox color-blue full_phonebook height15" type="checkbox" name="full_phonebook" value="1"';
                          if ($agent_item[0]['full_phonebook'] == 1) {
                              $agent_phonebook .= ' checked'; // Checkbox should be checked
                          }
                          $agent_phonebook .= '>';
                          $paid_status_access = '<input class="sb-form-checkbox color-blue paid_status_access height15" type="checkbox" name="paid_status_access" value="1"';
                          if ((int)$agent_item[0]['paid_status_access'] === 1) {
                              $paid_status_access .= ' checked';
                          }
                          $paid_status_access .= '>';
                        }else {
                          $action.='<select disabled  class="input-flat admin-dropdown">';
                          $action.='<option  value="">'.__("Assign Admin").'</option>';
                          foreach ($admins as $key => $value) {
                            $action.='<option  value="'.$value['user_id'].'">'.$value['lastname']." ".$value['firstname'].'</option>';
                          }
                          $action.='<option value="0">'._('No Admin').'</option>';
                          $action.='</select>';
                          $paid_status_access = ($allow_paid_toggle) ? '<input class="sb-form-checkbox color-blue paid_status_access height15" type="checkbox" disabled>' : '';
                        }

                      $contact_action = '<button title="'.__('Is referrer').' ?'.'" class="btn btn-danger" onclick="ownerStatus(1,'.$customer->getId().')">'."<i class='fa fa-phone' rel=''></i>".'</button>';
                      if(array_search($customer->getId(),$contact_users_array)) {
                          $is_contact=1;
                          $contact_action  = '<button title="'.__('Is referrer').' ?'.'"  class="btn btn-info" onclick="ownerStatus(2,'.$customer->getId().')">'."<i class='fa fa-phone' rel=''></i>".'</button>';
                      }
                      $share_action = '<button title="'.__("Can Share")." ?".'" class="btn btn-danger" onclick="socailshareStatus(1,'.$customer->getId().')">'."<i class='fa fa-share' rel=''></i>".'</button>';
                      if(array_search($customer->getId(),$socialshares_array)) {
                          $share_action = '<button title="'.__("Can Share")." ?".'" class="btn btn-info" onclick="socailshareStatus(2,'.$customer->getId().')">'."<i class='fa fa-share' rel=''></i>".'</button>';
                      }
                      //Apply Filters
                      if ($datas['filter']!='default' && $datas['filter']=='admin' && !$is_admin) {
                        continue;
                      }elseif ($datas['filter']!='default' && ($datas['filter']=='agent' || $datas['filter']=='province') && !$is_agent) {
                        continue;
                      }elseif ($datas['filter']!='default' && $datas['filter']=='contact' && !$is_contact) {
                        continue;
                      }elseif ($datas['filter']!='default' && $datas['filter']=='referrer' && ($is_agent || $is_admin || $is_contact )) {
                        continue;
                      }       
                                    
                      if ($allow_paid_toggle) {
                        $data[] = [
                            $admin_action." ".$agetn_action." ".$province_action." ".$affiliate_action." ".$share_action,
                            $customer->getId(),
                            $customer->getLastname().' '.$customer->getFirstname(),
                            $customer->getEmail(),
                            $agent_type,
                            $paid_status_access,
                            $agent_phonebook,
                            $action
                        ];
                      } else {
                        $data[] = [
                            $admin_action." ".$agetn_action." ".$province_action." ".$affiliate_action." ".$share_action,
                            $customer->getId(),
                            $customer->getLastname().' '.$customer->getFirstname(),
                            $customer->getEmail(),
                            $agent_type,
                            $agent_phonebook,
                            $action
                        ];
                      }
                  }
              }
              $payload = [
                  "data" => $data
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
  public function loadcontactsphonebookAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {
              $migareference = new Migareference_Model_Migareference();
              $contact_users = $migareference->getContactsUsers($datas['app_id']);
              $data          = [];
              foreach($contact_users as $user) {
                $transform_action  = '<button title="'.__('Transfer To Referrer').' ?'.'"  class="btn btn-info" onclick="contactTransfer('.$user['customer_id'].')">'."<i class='fa fa-user' rel=''></i>".'</button>';
                  $data[] = [
                      $transform_action,
                      $user['customer_id'],
                      $user['firstname'],
                      $user['lastname'],
                      $user['mobile'],
                      $user['email'],
                      $user['created_at']
                  ];
              }
              $payload = [
                  "data" => $data
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
  public function automationeffectedusesAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {
              $trigger_status="";
              $migareference = new Migareference_Model_Migareference();
              if ($datas['operation']=='update') {
                $datas['auto_rem_trigger']=$datas['auto_rem_trigger_copy'];
                $datas['auto_rem_fix_rating']=$datas['auto_rem_fix_rating_copy'];
              }
              if ($datas['auto_rem_trigger']==4 || $datas['auto_rem_trigger_copy']==4) {
                  foreach ($datas['status_list'] as $key => $value) {
                    $trigger_status.=$value;
                    $trigger_status.="@";
                  }
              }
              $datas['auto_rem_report_trigger_status']=$trigger_status;
              $users            = $migareference->automationTriggersEffect($datas);
              $auto_rem_trigger = $datas['auto_rem_trigger'];
              $data             = [];
              $datas             = [];
              $id               = 1;
              foreach($users as $user) {
                  $data[] = [
                      $user['user_id'],
                      $user['invoice_name'],
                      $user['invoice_surname'],
                      $user['email'],
                  ];
              }
              $data = array_map("unserialize", array_unique(array_map("serialize", $data)));
              foreach($data as $user) {
                  $datas[] = [
                      $user[0],
                      $user[1],
                      $user[2],
                      $user[3],
                  ];
              }
              $payload = [
                  "data" => $datas
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
  public function appgdprusersAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {
              $customer      = new Customer_Model_Customer();
              $migareference = new Migareference_Model_Migareference();
              $admin         = new Migareference_Model_Admin();
              $customers     = $customer->findAll(['app_id = ?' => $datas['app_id'],]);
              $admins        = $migareference->getAdmins($datas['app_id']);
              $agents        = $migareference->get_customer_agents($datas['app_id']);
              $social_sahre  = $migareference->get_socialshares($datas['app_id']);
              $data          = [];
              $admin_array   = [];
              $agent_array   = [];
              $index         = 1;
              $socialshares_array=[];
              $file            = fopen("app/local/modules/Migareference/resources/propertyaddresses/gdprusers_".$datas['app_id'].".csv","w");
              foreach ($admins as $key => $value) {
                $admin_array[$index]=$value['user_id'];
                $index++;
              }
              $index=1;
              foreach ($agents as $key => $value) {
                $agent_array[$index]=$value['user_id'];
                $index++;
              }
              $index=1;
              foreach ($social_sahre as $key => $value) {
                $socialshares_array[$index]=$value['user_id'];
                $index++;
              }
              if(count($customers)) {
                  foreach($customers as $customer) {
                      $action = '';
                      $admin->find(['user_id' => $customer->getId(),'app_id'  => $datas['app_id']]);
                      if(array_search($customer->getId(),$admin_array)) {
                          $action = __("Admin");
                      }elseif(array_search($customer->getId(),$agent_array)) {
                          $action = __('Agent');
                      }else {
                        $action=__('Referrer');
                      }
                      $data[] = [
                          $customer->getId(),
                          $customer->getFirstname(),
                          $customer->getLastname(),
                          $customer->getEmail(),
                          $customer->getMobile(),
                          $action,
                          "<input class='sb-form-checkbox color-blue height15 customer_ids gdpr-item' onChange='gdprchange()' type='checkbox' name='gdpr_to_delete[]'  value=".$customer->getId().">"
                      ];
                    $csv_data[0]=$customer->getId();
                    $csv_data[1]=$customer->getFirstname();
                    $csv_data[2]=$customer->getLastname();
                    $csv_data[3]=$customer->getEmail();
                    $csv_data[4]=$customer->getMobile();
                    // Build CSV file for all users
                    fputcsv($file, $csv_data,";");
                  }
              }
              fclose($file);
              $payload = [
                  "data" => $data
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
  public function reportstatusAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
                $report_collection             = [];
                $filter_array                  = [];
                $filter_array['invoic_string'] = "";
                $filter_array['app_id']        = $data['app_id'];
                $app_id                        = $data['app_id'];
                $$filter_string="";
                if ($data['status_key']==0) { //Exlude Declined and Paid
                  $filter_string.=" AND  migareference_report_status.standard_type!=4 AND  migareference_report_status.standard_type!=3";
                }elseif ($data['status_key']!=0 && $data['status_key']!=-1) { //Single Filter
                  $filter_string.= " AND migareference_report.currunt_report_status=".$data['status_key'];
                }
                if ($data['referrer_key']) {
                  $filter_string.= " AND migareference_invoice_settings.user_id=".$data['referrer_key'];
                }                
                if ($data['agent_key']>-1) {
                  // $filter_string.= " AND migareference_invoice_settings.sponsor_id=".$data['agent_key'];
                  $filter_string.=" AND "."("."refag_one.agent_id=".$data['agent_key'];
                  $filter_string.=" OR refag_two.agent_id=".$data['agent_key'].')';
                }                
                if ($data['report_filter_reporttype']!=0) {                  
                  $filter_string.= " AND migareference_report.report_custom_type=".$data['report_filter_reporttype'];
                }                
                $filter_array['filter_string'] = $filter_string;
                $migareference = new Migareference_Model_Migareference();
                $utilities     = new Migareference_Model_Utilities();
                $all_reports   = $migareference->getReportList($filter_array);
                $pre_settings  = $migareference->preReportsettigns($data['app_id']);
                $bitly_crede   = $migareference->getBitlycredentails($app_id);
                $app_content   = $migareference->get_app_content($app_id);
                $default           = new Core_Model_Default();
                $base_url          = $default->getBaseUrl();
                $applicationBase   = $base_url."/images/application/".$app_id."/features/migareference/";
                $report_type_icon_one=$applicationBase.$app_content[0]['report_type_pop_btn_one_icon'];
                $report_type_icon_two=$applicationBase.$app_content[0]['report_type_pop_btn_two_icon'];
                $app_icon_path     = $base_url."/app/local/modules/Migareference/resources/appicons/";
                foreach ($all_reports as $key => $value) {
                  $last_modification_date=$value['last_modification_at'];
                  $now                = time();
                  $your_date          = strtotime($last_modification_date);
                  $datediff           = $now - $your_date;
                  $days               = round($datediff / (60 * 60 * 24));
                  $old_date_timestamp = strtotime($value['created_at']);
                  $new_date           = date('Y-m-d', $old_date_timestamp);
                  $old_date_timestamp = strtotime($value['last_modification_at']);
                  $modi_date          = date('Y-m-d', $old_date_timestamp);
                  $destPath           = Core_Model_Directory::getBasePathTo();
                  $platform_url       = explode("/",$destPath);//index 4 have platform url
                  $warrning_icon      = $app_icon_path."warrning.png";
                  $warrning_img       = "";
                  // Managed From 
                  $short_link='';                
                  $agent_list                 = $migareference->getSponsorList($app_id,$value['user_id']);
                  if (COUNT($agent_list)) {
                    $managed_by=$agent_list[0]['firstname']." ".$agent_list[0]['lastname'];
                    if (COUNT($agent_list)==2) {
                      $managed_by.=" & ".$agent_list[1]['firstname']." ".$agent_list[1]['lastname'];
                    }
                    // Built Link and save 
                    $externalllins = new Migareference_Model_Externalreportlink();                                                             
                    $is_link_exist=$externalllins->agentLink($value['migareference_report_id'],$value['sponsor_id']);              
                    if (!count($is_link_exist)) {
                        $default       = new Core_Model_Default();
                        $base_url      = $default->getBaseUrl();                    
                        $token=$this->randomToken(35);
                        $long_url=$base_url."/migareference/crmreports?"."app_id=".$app_id."&token=".$token;
                        $short_link = $utilities->shortLink($long_url);
                        // if their is any error urrlshortAction will retrun long_url
                        // instead to save long url in database we will save empty short url so later could be replaced
                        if ($short_link==$long_url) {
                          $short_link="";
                        }
                        $crm_entry['app_id']=$app_id;
                        $crm_entry['user_id']=$value['sponsor_id'];
                        $crm_entry['report_id']=$value['migareference_report_id'];
                        $crm_entry['long_url']=$long_url;
                        $crm_entry['short_url']=$short_link;
                        $crm_entry['is_agent']=1;
                        $crm_entry['token']=$token;
                        $crm_entry['created_at']  = date('Y-m-d H:i:s');
                        $externalllins->savedata($crm_entry);              
                    }else{
                      $short_link=$is_link_exist[0]['short_url'];
                    }
                  }else if($value['is_standard']==1 && $value['standard_type']==1){
                    $managed_by=__("Unknown");
                  }else {
                    $all_logs = $migareference->getReportlog($app_id,$value['migareference_report_id']);
                    $all_logs=end($all_logs);                    
                    if ($all_logs['user_type']==1) {
                      $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['cutomerfirstname']." ".$all_logs['cutomerlastname'];
                    }else {
                      $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['adminfirstname']." ".$all_logs['adminlastname'];
                    }                    
                  }                                    
                  if($value['current_reminder_status']!=""){
                    $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
                  }           
                  $disable='';
                  if ($value['report_no']==0) {
                    $disable="disabled";
                  }
                  // Build Response Array
                  $default              = new Core_Model_Default();
                  $base_url             = $default->getBaseUrl();
                  $gdpr_consent='';
                  if ($pre_settings[0]['consent_collection']==1) {
                    $gdpr_consent="<img onclick=collectConsentModal(".$value['migareference_report_id'].','.$value['user_id'].") style='height:44px;margin-top:-6px;' alt='' src='".$base_url."/app/local/modules/Migareference/resources/appicons/".'no_gdpr.png'."'>";
                    if ($value['gdpr_consent_timestamp']!=NULL) {
                      $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'gdpr.png'."'>";
                    } 
                  }
                  $report_type_icon='';
                  if ($pre_settings[0]['enable_report_type']==1) {
                    $report_type_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$report_type_icon_one."'>";
                    if ($value['report_custom_type']==2) {
                      $report_type_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$report_type_icon_two."'>";
                    }
                  }
                  //Report Source icon
                  // report_source 1: From APP end 2: From Labding Page 3 From backoffice or Owner end, 4 for API End
                  $report_source_icon='';
                  if ($value['report_source']==1) {
                    $report_source_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'report_app.png'."'>";
                  }elseif ($value['report_source']==2) {
                    $report_source_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'report_landing.png'."'>";
                  }elseif ($value['report_source']==3) {
                    $report_source_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'report_owner.png'."'>"; 
                  }elseif ($value['report_source']==4) {
                    $report_source_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$app_icon_path.'report_api.png'."'>"; 
                  }                  
                  $report_created_at=date('d-m-Y', strtotime($value['report_created_at']));
                  $report_created_att=date('Y-m-d', strtotime($value['report_created_at']));
                  $certificate_icon = $app_icon_path."certificate.png";
                  $collection_item=[
                                      "<div style='float:left;'><input ".$disable." style='margin-top:5px;' id='bulk_report_".$value['migareference_report_id']."' class='sb-form-checkbox color-blue height15 customer_ids bulk-report' onChange='bulkReport(".$value['migareference_report_id'].")' type='checkbox' name='report_to_update[]' value=".$value['migareference_report_id']."></div>"
                                      ."<div style='float:right;'><button ".$disable." style='float:left;' type='button' onclick='reportdetail(".$value['migareference_report_id'].")' class='button center-block btn bt_save btn color-blue'>"."<i class='fa fa-pencil' rel=''></i>"."</button></div>",
                                      '<div><button type="button" onclick="crmreportlinks(
                                          ' . htmlspecialchars($value['migareference_report_id'], ENT_QUOTES, 'UTF-8') . ',
                                          ' . htmlspecialchars($value['sponsor_id'], ENT_QUOTES, 'UTF-8') . ',
                                          \'' . htmlspecialchars($short_link, ENT_QUOTES, 'UTF-8') . '\'
                                        )" class="button center-block btn color-blue">
                                            <i class="fa fa-external-link"></i>
                                        </button>
                                    </div>',
                                       $report_source_icon.$gdpr_consent.$report_type_icon
                                       .$value['report_no'],
                                       __($value['status_title']),
                                      "<span style='display:none;'>".$report_created_att."</span>".$report_created_at,
                                      $value['invoice_name']." ".$value['invoice_surname'],
                                      $value['owner_name']." ".$value['owner_surname'],
                                      $modi_date,
                                      $managed_by,
                                      $value['is_notarized'] ? '<a href="/migareference/public_pdf/download-pdf/report_id/'.$value['migareference_report_id'].'" target="_blank"><img style="width: 32px; height: 32px; margin: 0 auto; display: block;" src="'.$certificate_icon.'" /></a>' : __("N/A")
                                    ];
                    $report_collection[]=$collection_item;
                }
            $payload = [
                "data" => $report_collection,
                "filter" => $filter_string,
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
  public function proreportstatusAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
                $report_collection             = [];
                $filter_array                  = [];
                $filter_array['invoic_string'] = "";
                $filter_array['app_id']        = $data['app_id'];
                $app_id                        = $data['app_id'];
                $$filter_string="";
                if ($data['status_key']==0) { //Exlude Declined and Paid
                  $filter_string.=" AND  migareference_report_status.standard_type!=4 AND  migareference_report_status.standard_type!=3";
                }elseif ($data['status_key']!=0 && $data['status_key']!=-1) { //Single Filter
                  $filter_string.= " AND migareference_report.currunt_report_status=".$data['status_key'];
                }
                if ($data['referrer_key']) {
                  $filter_string.= " AND migareference_invoice_settings.user_id=".$data['referrer_key'];
                }                
                if ($data['agent_key']>-1) {
                  // $filter_string.= " AND migareference_invoice_settings.sponsor_id=".$data['agent_key'];
                  // $filter_string.= " AND refag_one.agent_id=".$data['agent_key'];
                  $filter_string.=" AND "."("."refag_one.agent_id=".$data['agent_key'];
                  $filter_string.=" OR refag_two.agent_id=".$data['agent_key'].')';
                }                
                if ($data['report_filter_reporttype']!=0) {                  
                  $filter_string.= " AND migareference_report.report_custom_type=".$data['report_filter_reporttype'];
                }                
                $filter_array['filter_string'] = $filter_string;
                $migareference = new Migareference_Model_Migareference();
                $utilities = new Migareference_Model_Utilities();                
                $all_reports   = $migareference->getReportList($filter_array);
                $pre_settings  = $migareference->preReportsettigns($data['app_id']);
                $bitly_crede   = $migareference->getBitlycredentails($app_id);
                $app_content   = $migareference->get_app_content($app_id);
                $default           = new Core_Model_Default();
                $base_url          = $default->getBaseUrl();
                $status        = $migareference->getReportStatus($app_id);
                $report_status='';
                foreach ($status as $key => $value):
                  $report_status.='<option class="dropdown-item"'.$selected.' value='.$value["migareference_report_status_id"].'>'.$value["status_title"].'</option>';
                endforeach;
                $applicationBase   = $base_url."/images/application/".$app_id."/features/migareference/";
                $report_type_icon_one=$applicationBase.$app_content[0]['report_type_pop_btn_one_icon'];
                $report_type_icon_two=$applicationBase.$app_content[0]['report_type_pop_btn_two_icon'];
                foreach ($all_reports as $key => $value) {
                  $last_modification_date=$value['last_modification_at'];
                  $now                = time();
                  $your_date          = strtotime($last_modification_date);
                  $datediff           = $now - $your_date;
                  $days               = round($datediff / (60 * 60 * 24));
                  $old_date_timestamp = strtotime($value['created_at']);
                  $new_date           = date('Y-m-d', $old_date_timestamp);
                  $old_date_timestamp = strtotime($value['last_modification_at']);
                  $modi_date          = date('Y-m-d', $old_date_timestamp);
                  $destPath           = Core_Model_Directory::getBasePathTo();
                  $platform_url       = explode("/",$destPath);//index 4 have platform url
                  $warrning_icon      = "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/warrning.png";
                  $warrning_img       = "";
                  // Managed From 
                  $short_link='';                
                  $agent_list                 = $migareference->getSponsorList($app_id,$value['user_id']);
                  if (COUNT($agent_list)) {
                    $managed_by=$agent_list[0]['firstname']." ".$agent_list[0]['lastname'];
                    if (COUNT($agent_list)==2) {
                      $managed_by.=" & ".$agent_list[1]['firstname']." ".$agent_list[1]['lastname'];
                    }
                    // Built Link and save 
                    $externalllins = new Migareference_Model_Externalreportlink();                                                             
                    $is_link_exist=$externalllins->agentLink($value['migareference_report_id'],$value['sponsor_id']);              
                    if (!count($is_link_exist)) {
                        $default       = new Core_Model_Default();
                        $base_url      = $default->getBaseUrl();                    
                        $token=$this->randomToken(35);
                        $long_url=$base_url."/migareference/crmreports?"."app_id=".$app_id."&token=".$token;
                        $short_link = $utilities->shortLink($long_url);
                        // if their is any error urrlshortAction will retrun long_url
                        // instead to save long url in database we will save empty short url so later could be replaced
                        if ($short_link==$long_url) {
                          $short_link="";
                        }
                        $crm_entry['app_id']=$app_id;
                        $crm_entry['user_id']=$value['sponsor_id'];
                        $crm_entry['report_id']=$value['migareference_report_id'];
                        $crm_entry['long_url']=$long_url;
                        $crm_entry['short_url']=$short_link;
                        $crm_entry['is_agent']=1;
                        $crm_entry['token']=$token;
                        $crm_entry['created_at']  = date('Y-m-d H:i:s');
                        $externalllins->savedata($crm_entry);              
                    }else{
                      $short_link=$is_link_exist[0]['short_url'];
                    }
                  }else if($value['is_standard']==1 && $value['standard_type']==1){
                    $managed_by=__("Unknown");
                  }else {
                    $all_logs = $migareference->getReportlog($app_id,$value['migareference_report_id']);
                    $all_logs=end($all_logs);                    
                    if ($all_logs['user_type']==1) {
                      $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['cutomerfirstname']." ".$all_logs['cutomerlastname'];
                    }else {
                      $managed_by= ($all_logs['user_id']==99999) ? "System" : $all_logs['adminfirstname']." ".$all_logs['adminlastname'];
                    }                    
                  }                                    
                  if($value['current_reminder_status']!=""){
                    $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
                  }           
                  $disable='';
                  if ($value['report_no']==0) {
                    $disable="disabled";
                  }
                  // Build Response Array
                  $default              = new Core_Model_Default();
                  $base_url             = $default->getBaseUrl();
                  $gdpr_consent='';
                  if ($pre_settings[0]['consent_collection']==1) {
                    $gdpr_consent="<img onclick=collectConsentModal(".$value['migareference_report_id'].','.$value['user_id'].") style='height:44px;margin-top:-6px;' alt='' src='".$base_url."/app/local/modules/Migareference/resources/appicons/".'no_gdpr.png'."'>";
                    if ($value['gdpr_consent_timestamp']!=NULL) {
                      $gdpr_consent="<img style='height:44px;margin-top:-6px;' alt='' src='".$base_url."/app/local/modules/Migareference/resources/appicons/".'gdpr.png'."'>";
                    } 
                  }
                  $report_type_icon='';
                  if ($pre_settings[0]['enable_report_type']==1) {
                    $report_type_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$report_type_icon_one."'>";
                  if ($value['report_custom_type']==2) {
                    $report_type_icon="<img  style='height:44px;margin-top:-6px;' alt='' src='".$report_type_icon_two."'>";
                  }
                  }                  
                  $report_created_at=date('d-m-Y', strtotime($value['report_created_at']));
                  $report_created_att=date('Y-m-d', strtotime($value['report_created_at']));
                   $certificate_icon = "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/certificate.png";
                  $collection_item=[
                                     
                                       $gdpr_consent.$report_type_icon
                                       .$value['report_no'],
                                       $value['invoice_name']." ".$value['invoice_surname'],
                                       $value['owner_name']." ".$value['owner_surname'],
                                      "<span style='display:none;'>".$report_created_att."</span>".$report_created_at,
                                      $modi_date,
                                      $managed_by,
                                      "<div class='btn-group'>
                                      <button type='button' class='btn btn-dark btn-sm'>".__($value['status_title'])
                                      ."</button>
                                      <button type='button' class='btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split' id='dropdownMenuReference5' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false' data-reference='parent'>
                                      <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-chevron-down'><polyline points='6 9 12 15 18 9'></polyline></svg>
                                      </button>
                                      <div class='dropdown-menu' aria-labelledby='dropdownMenuReference5'>
                                        ".$report_status."
                                      </div>
                                  </div>",
                                      "<div style='float:left;'><input ".$disable." style='margin-top:5px;' id='bulk_report_".$value['migareference_report_id']."' class='sb-form-checkbox color-blue height15 customer_ids bulk-report' onChange='bulkReport(".$value['migareference_report_id'].")' type='checkbox' name='report_to_update[]' value=".$value['migareference_report_id']."></div>",
                                      "<div style='float:right;'><button ".$disable." style='float:left;' type='button' onclick='reportdetail(".$value['migareference_report_id'].")' class='button center-block btn bt_save btn color-blue'>"."<i class='fa fa-pencil' ></i>"."</button></div>"
                                    //   '<div><button type="button" onclick="crmreportlinks(
                                    //       ' . htmlspecialchars($value['migareference_report_id'], ENT_QUOTES, 'UTF-8') . ',
                                    //       ' . htmlspecialchars($value['sponsor_id'], ENT_QUOTES, 'UTF-8') . ',
                                    //       \'' . htmlspecialchars($short_link, ENT_QUOTES, 'UTF-8') . '\'
                                    //     )" class="button center-block btn color-blue">
                                    //         <i class="fa fa-external-link"></i>
                                    //     </button>
                                    // </div>'
                                      // $value['is_notarized'] ? '<a href="/migareference/public_pdf/download-pdf/report_id/'.$value['migareference_report_id'].'" target="_blank"><img style="width: 32px; height: 32px; margin: 0 auto; display: block;" src="'.$certificate_icon.'" /></a>' : __("N/A")
                                    ];
                    $report_collection[]=$collection_item;
                }
            $payload = [
                "data" => $report_collection,
                "filter" => $filter_string,
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
  public function refreportstatusAction() {
      if ($user_id = $this->getRequest()->getParam('user_id')) {
          try {
            $report_collection = [];
            $filter_array      = [];
            $report_rows       = "";
            $application       = $this->getApplication();
            $app_id            = $application->getId();
            $filter_array['filter_string'] = "";
            $filter_array['invoic_string'] = "";
            $filter_array['app_id']        = $app_id;
            $filter_array['user_id']       = $user_id;
            $migareference  = new Migareference_Model_Migareference();
            $all_reports    = $migareference->ref_get_all_reports($filter_array);
            $ref_user_log   = $migareference->getRefferalCustomers($app_id,$user_id);
            if (!count($all_reports)) {
              $report_rows="<tr><td colspan='5'>".__("No data found")."</td></tr>";
            }
            foreach ($all_reports as $key => $value) {
              $last_modification_date=$value['last_modification_at'];
              $now       = time(); // or your date as well
              $your_date = strtotime($last_modification_date);
              $datediff  = $now - $your_date;
              $days      = round($datediff / (60 * 60 * 24));
              $old_date_timestamp = strtotime($value['created_at']);
              $new_date = date('Y-m-d', $old_date_timestamp);
              $old_date_timestamp = strtotime($value['last_modification_at']);
              $modi_date = date('Y-m-d', $old_date_timestamp);
              $destPath     = Core_Model_Directory::getBasePathTo();
              $platform_url = explode("/",$destPath);//index 4 have platform url
              $warrning_icon= "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/warrning.png";
              $warrning_img="";
              // Manage Warring icon
              if ($value['reminder_grace_days']!=0 && $value['reminder_grace_days']<$days && $value['standard_type']!=3 && $value['standard_type']!=4) {
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }else if($days>30 && $value['standard_type']!=3 && $value['standard_type']!=4){
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }
                  $report_rows.="<tr>";
                  $report_rows.="<td>".$value['report_no']."</td>";
                  $report_rows.="<td>".$new_date."</td>";
                  $report_rows.="<td>".$modi_date."</td>";
                  $report_rows.="<td>".$value['owner_name']." ".$value['owner_surname']."</td>";
                  $report_rows.="<td>".__($value['status_title']).$warrning_img."</td>";
                  $report_rows.="</tr>";
            }
              $payload = [
                  "all_reports" => $all_reports,
                  "filter" => $filter_array,
                  'report_rows'=>$report_rows,
                  'log'=>$ref_user_log
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
  public function loadrefreportlogAction() {
      if ($user_id = $this->getRequest()->getParam('user_id')) {
          try {
            $report_collection = [];
            $filter_array      = [];
            $report_rows       = "";
            $application       = $this->getApplication();
            $app_id            = $application->getId();
            $filter_array['filter_string'] = "";
            $filter_array['invoic_string'] = "";
            $filter_array['app_id']        = $app_id;
            $filter_array['user_id']       = $user_id;
            $migareference = new Migareference_Model_Migareference();
            $all_reports    = $migareference->ref_get_all_reports($filter_array);
            $ref_user_log   = $migareference->getRefferalCustomers($app_id,$user_id);
            foreach ($all_reports as $key => $value) {
              $last_modification_date=$value['last_modification_at'];
              $now       = time(); // or your date as well
              $your_date = strtotime($last_modification_date);
              $datediff  = $now - $your_date;
              $days      = round($datediff / (60 * 60 * 24));
              $old_date_timestamp = strtotime($value['report_created_at']);
              $new_date = date('Y-m-d', $old_date_timestamp);
              $old_date_timestamp = strtotime($value['last_modification_at']);
              $modi_date = date('Y-m-d', $old_date_timestamp);
              $destPath     = Core_Model_Directory::getBasePathTo();
              $platform_url = explode("/",$destPath);//index 4 have platform url
              $warrning_icon= "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/warrning.png";
              $warrning_img="";
              // Manage Warring icon
              if ($value['reminder_grace_days']!=0 && $value['reminder_grace_days']<$days && $value['standard_type']!=3 && $value['standard_type']!=4) {
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }else if($days>30 && $value['standard_type']!=3 && $value['standard_type']!=4){
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }
                  $report_collection[]=[
                      $value['report_no'],
                      $new_date,
                      $modi_date,
                      $value['owner_name']." ".$value['owner_surname'],
                      __($value['status_title']).$warrning_img
                  ];
            }
              $payload = [
                  'data'=>$report_collection
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
  public function uploadeaddressesfileAction() {
          try {
            if ($request = $this->getRequest()->getPost()) {
              if($_FILES["file"]["name"]) {
                  $filename = $_FILES["file"]["name"];
                  $source   = $_FILES["file"]["tmp_name"];
                  $type     = $_FILES["file"]["type"];
                  $baseUrl = Core_Model_Directory::getBasePathTo("");
                  if (!file_exists($baseUrl."/var/tmp")) {
                      mkdir($baseUrl."/var/tmp");
                  }
                  if (!file_exists($baseUrl."/var/tmp/migarefrenceimport")) {
                      mkdir($baseUrl."/var/tmp/migarefrenceimport");
                  }
                  $target_path = $baseUrl."/var/tmp/migarefrenceimport/".$filename;  // change this to the correct site path
                  if(move_uploaded_file($source, $target_path)) {
                    // Open the file for reading
                    if (($h = fopen($target_path, "r")) !== FALSE)
                    {
                      // Convert each line into the local $data variable
                      $migareference = new Migareference_Model_Migareference();
                      $application  = $this->getApplication();
                      $app_id       = $application->getId();
                      $exteranl_addresses = $migareference->getaddresses($app_id);
                      if (count($exteranl_addresses)) {
                        $migareference->deleteaddresses($app_id);
                      }
                      $data_array=[];
                      while (($line = fgetcsv($h, 1000, ",")) !== FALSE)
                      {
                        if (count($line)==3) {
                          $data['app_id']=$app_id;
                          $data['address']=$line[0];
                          $data['longitude']=$line[1];
                          $data['latitude']=$line[2];
                          $migareference->insertaddresses($data);
                          $data_array[]=$data;
                        }else {
                          $error.=__("The file you are trying not match the format.");
                          break;
                        }
                        // Read the data from a single line
                      }
                      // Close the file
                      fclose($h);
                    }
                  }else {
                    $error.=__("Can not uploade file please try again.")."<br>";
                  }
                }else {
                  $error.=__("You must select a CSV file")."<br>";
                }
            }
                $payload = [
                    'success' => (!empty($error)) ? false : true ,
                    'message' =>(!empty($error)) ? $error :  __('Successfully Removed Reminder Type.') ,
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2,
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function uploadephonenumberfileAction() {
          try {
            if ($request = $this->getRequest()->getPost()) {
              if($_FILES["file"]["name"]) {
                  $filename = $_FILES["file"]["name"];
                  $source   = $_FILES["file"]["tmp_name"];
                  $type     = $_FILES["file"]["type"];
                  $baseUrl = Core_Model_Directory::getBasePathTo("");
                  if (!file_exists($baseUrl."/var/tmp")) {
                      mkdir($baseUrl."/var/tmp");
                  }
                  if (!file_exists($baseUrl."/var/tmp/migarefrenceimport")) {
                      mkdir($baseUrl."/var/tmp/migarefrenceimport");
                  }
                  $target_path = $baseUrl."/var/tmp/migarefrenceimport/".$filename;  // change this to the correct site path
                  if(move_uploaded_file($source, $target_path)) {
                    // Open the file for reading
                    if (($h = fopen($target_path, "r")) !== FALSE)
                    {
                      // Convert each line into the local $data variable
                      $migareference = new Migareference_Model_Migareference();
                      $application   = $this->getApplication();
                      $app_id        = $application->getId();
                      $not_validated=0;
                      $successfully_inserted=0;
                      $successfully_updated=0;
                      while (($line = fgetcsv($h, 1000, ",")) !== FALSE)
                      {
                        $errors='';
                        if (count($line)==6) { //Number of Columns in each row must be 6
                          // Validations
                          if (empty($line[0])){
                            $errors .= __('Please add a valid Name.') . "<br/>";
                          }
                          if (empty($line[1])){
                            $errors .= __('Please add a valid Surname.') . "<br/>";
                          }
                          if (strlen($line[2]) < 10 || strlen($line[2]) > 14 || empty($line[2]) || preg_match('@[a-z]@', $line[2])
                          || (substr($line[2], 0, 1)!='+' && substr($line[2], 0, 2)!='00')){
                            $errors .= __('Phone number is not correct. Please add a phone between 10-14 digits with 00 or + international country code at beginning') . "<br/>";
                          }
                          $temp_email="@gmail.com";
                          $phone_email_exist=$migareference->isPhoneEmailExist($app_id,$temp_email,$line[2],2);
                          if (empty($errors)) {
                            $data['app_id']=$app_id;
                            $data['rating']=1;
                            $data['type']=2;
                            $data['is_exclude']=$line[4];
                            $data['is_blacklist']=$line[5] ;
                            $data['job_id']=$line[3] ;
                            $data['name']=$line[0];
                            $data['surname']=$line[1];
                            $data['mobile']=$line[2];
                            if (!count($phone_email_exist)) {
                              $migareference->savePhoneBook($data);
                              $successfully_inserted++;
                            }else {
                              $change_by=$_SESSION['front']['object_id'];
                              $migareference->update_phonebook($data,$phone_email_exist[0]['migarefrence_phonebook_id'],$change_by,2);//Also save log if their is change in Rating,Job,Notes
                              $successfully_updated++;
                            }
                          }else {
                            $not_validated++;
                          }
                        }else {
                          $error.=__("The file you are trying not match the format.");
                          break;
                        }
                      }
                      fclose($h);// Close the file
                    }
                  }else {
                    $error.=__("Can not uploade file please try again.")."<br>";
                  }
                }else {
                  $error.=__("You must select a CSV file")."<br>";
                }
            }
            $error=__("Operation Completed")."<br>".__("Successfully Imported")." ".$successfully_inserted."<br>"."Successfully Updated"."<br>".$successfully_updated."<br>"."Not Validated"." ".$not_validated;
                $payload = [
                    'success' => (!empty($error)) ? false : true ,
                    'message' =>(!empty($error)) ? $error :  __('Successfully Removed Reminder Type.') ,
                    'message_loader'  => 0,
                    'message_button'  => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function uploadejobsfileAction() {
          try {
            if ($request = $this->getRequest()->getPost()) {
              if($_FILES["file"]["name"]) {
                  $filename = $_FILES["file"]["name"];
                  $source   = $_FILES["file"]["tmp_name"];
                  $type     = $_FILES["file"]["type"];
                  $baseUrl = Core_Model_Directory::getBasePathTo("");
                  if (!file_exists($baseUrl."/var/tmp")) {
                      mkdir($baseUrl."/var/tmp");
                  }
                  if (!file_exists($baseUrl."/var/tmp/migarefrenceimport")) {
                      mkdir($baseUrl."/var/tmp/migarefrenceimport");
                  }
                  $target_path = $baseUrl."/var/tmp/migarefrenceimport/".$filename;  // change this to the correct site path
                  if(move_uploaded_file($source, $target_path)) {
                    // Open the file for reading
                    if (($h = fopen($target_path, "r")) !== FALSE)
                    {
                      // Convert each line into the local $data variable
                      $migareference = new Migareference_Model_Migareference();
                      $application   = $this->getApplication();
                      $app_id        = $application->getId();
                      while (($line = fgetcsv($h, 1000, ",")) !== FALSE)
                      {
                        if (count($line)==1) {
                          $data['app_id']=$app_id;
                          $data['job_title']=$line[0];
                          $migareference->insertjob($data);
                        }else {
                          $error.=__("The file you are trying not match the format.");
                          break;
                        }
                      }
                      fclose($h);// Close the file
                    }
                  }else {
                    $error.=__("Can not uploade file please try again.")."<br>";
                  }
                }else {
                  $error.=__("You must select a CSV file")."<br>";
                }
            }
                $payload = [
                    'success' => (!empty($error)) ? false : true ,
                    'message' =>(!empty($error)) ? $error :  __('Successfully Removed Reminder Type.') ,
                    'message_loader'  => 0,
                    'message_button'  => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function uploadeprofessionfileAction() {
          try {
            if ($request = $this->getRequest()->getPost()) {
              if($_FILES["file"]["name"]) {
                  $filename = $_FILES["file"]["name"];
                  $source   = $_FILES["file"]["tmp_name"];
                  $type     = $_FILES["file"]["type"];
                  $baseUrl = Core_Model_Directory::getBasePathTo("");
                  if (!file_exists($baseUrl."/var/tmp")) {
                      mkdir($baseUrl."/var/tmp");
                  }
                  if (!file_exists($baseUrl."/var/tmp/migarefrenceimport")) {
                      mkdir($baseUrl."/var/tmp/migarefrenceimport");
                  }
                  $target_path = $baseUrl."/var/tmp/migarefrenceimport/".$filename;  // change this to the correct site path
                  if(move_uploaded_file($source, $target_path)) {
                    // Open the file for reading
                    if (($h = fopen($target_path, "r")) !== FALSE)
                    {
                      // Convert each line into the local $data variable
                      $migareference = new Migareference_Model_Migareference();
                      $application   = $this->getApplication();
                      $app_id        = $application->getId();
                      while (($line = fgetcsv($h, 1000, ",")) !== FALSE)
                      {
                        if (count($line)==1) {
                          $data['app_id']=$app_id;
                          $data['profession_title']=$line[0];
                          $migareference->insertProfession($data);
                        }else {
                          $error.=__("The file you are trying not match the format.");
                          break;
                        }
                      }
                      fclose($h);// Close the file
                    }
                  }else {
                    $error.=__("Can not uploade file please try again.")."<br>";
                  }
                }else {
                  $error.=__("You must select a CSV file")."<br>";
                }
            }
                $payload = [
                    'success' => (!empty($error)) ? false : true ,
                    'message' =>(!empty($error)) ? $error :  __('Successfully Removed Reminder Type.') ,
                    'message_loader'  => 0,
                    'message_button'  => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function deleteaddressAction() {
          try {
                $migareference = new Migareference_Model_Migareference();
                $data = $this->getRequest()->getParam('address_to_delete');
                foreach ($data as $key => $value) {
                  $migareference->deleteexternaladdress($value);
                }
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed Address.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function deletenoteAction() {
          try {                
                $note_id = $this->getRequest()->getParam('note_id');
                (new Migareference_Model_Notes())->find(['migarefrence_notes_id'=> $note_id])->delete();                               
                $payload = [
                    'success' => true,
                    'message' => __('Success.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function deletebyreportreminderAction() {
          try {                
            $migareference = new Migareference_Model_Migareference();
            $reminder_id    = $this->getRequest()->getParam('reminder_id');
            $data['is_deleted']=1;
            $migareference->update_reminder($reminder_id,$data);
                $payload = [
                    'success' => true,
                    'message' => __('Success.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function deletephonenumberAction() {
          try {
                $migareference = new Migareference_Model_Migareference();
                $data = $this->getRequest()->getParam('phone_number_to_delete');
                foreach ($data as $key => $value) {
                  $migareference->deleteexternalphonenumber($value);
                }
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Phone Number Removed.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2,
                    'message_timfeout' => $data
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }
      $this->_sendJson($payload);
  }
  public function deleterepotreminderAction() {
      if ($id = $this->getRequest()->getParam('id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migareference->deleteReminderType($id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed Reminder Type.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function deleteautorepotreminderAction() {
      if ($id = $this->getRequest()->getParam('id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migareference->deleteReportReminderAuto($id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed Reminder Type.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function deletecommunicationlogAction() {
      if ($id = $this->getRequest()->getParam('id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migareference->deleteCommunicationLog($id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed item.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function addcommunicationlogAction() {
      if ($phonebook_id = $this->getRequest()->getParam('phonebook_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $app_id        = $this->getApplication()->getId();
                $note          = $this->getRequest()->getParam('note');
                $log_item=[
                    'app_id'       => $app_id,
                    'phonebook_id' => $phonebook_id,
                    'log_type'     => "Manual",
                    'note'         => $note,
                    'user_id'      => $_SESSION['front']['object_id'],
                    'created_at'   => date('Y-m-d H:i:s')
                ];
                $migareference->saveCommunicationLog($log_item);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully item Saved.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function deletejobAction() {
      if ($id = $this->getRequest()->getParam('id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migareference->deletejob($id);
                $payload = [
                    'success' => true,
                    'message' => __('Job Successfully Removed.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function deleteprofessionAction() {
      if ($id = $this->getRequest()->getParam('id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migareference->deleteProfession($id);
                $payload = [
                    'success' => true,
                    'message' => __('Job Successfully Removed.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function deletegeoAction() {
      if ($country_id = $this->getRequest()->getParam('country_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $province_id=$this->getRequest()->getParam('province_id');
                $migareference->deleteGeo($country_id,$province_id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function updatesponsorAction() {
      if ($key = $this->getRequest()->getParam('sponsor_key')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $app_id = $this->getRequest()->getParam('app_id');
                $temp_keys = explode('@',$key);
                $user_id=$temp_keys[0];
                $sponsor_id=$temp_keys[1];
                $migareference->updateSponsoragent($app_id,$user_id,$sponsor_id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Sponsor update.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function customagentsAction() {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $application     = $this->getApplication();
                $app_id          = $application->getId();
                $user_id=$data['referrer_user'];
                $sponsor_id=$data['referrer_agent'];                
                $migareference->updateSponsoragent($app_id,$user_id,$sponsor_id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Sponsor update.'.$user_id." ".$sponsor_id),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function updateadminassignmentAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $application     = $this->getApplication();
                $app_id          = $application->getId();
                $admin_id=$this->getRequest()->getParam('admin_id');//admin user id
                $agent_id=$this->getRequest()->getParam('agent_id');                
                $data['admin_user_id']=$admin_id;               
                $migareference->updateAgent($data,$app_id,$agent_id);                
                $payload = [
                    'success' => true,
                    'message' => __('Successfully update.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function agentbulkassignmentAction() {      
          try {
                $migareference = new Migareference_Model_Migareference();                
                $data                 = $this->getRequest()->getPost();
                $pre_settings  = $migareference->preReportsettigns($data['app_id']);               
                if ($data['action_type']=='assign' || $data['action_type']=='remove') {
                  foreach ($data['referrerCheckbox'] as $key => $value) {                    
                    $migareference->deleteSponsor($value);                                                              
                    if ($data['action_type']=='assign') {                     
                      $referrer_agent['referrer_id']=$value;
                      $referrer_agent['app_id']=$data['app_id'];
                      $referrer_agent['created_at']=date('Y-m-d H:i:s');                    
                      if ($pre_settings[0]['enable_multi_agent_selection']==1) {                                                                           
                        $referrer_agent['agent_id']= (isset($data['customer_sponsor_id']) && $data['customer_sponsor_id']>0) ? $data['customer_sponsor_id'] : 0 ;
                        if ($referrer_agent['agent_id']!=0) {
                          $migareference->addSponsor($referrer_agent);
                        }                
                        $referrer_agent['agent_id']= (isset($data['partner_sponsor_id']) && $data['partner_sponsor_id']>0) ? $data['partner_sponsor_id'] : 0 ;
                        if ($referrer_agent['agent_id']!=0) {
                          $migareference->addSponsor($referrer_agent);        
                        }                                                     
                      }else{                        
                        $referrer_agent['agent_id']= (isset($data['customer_sponsor_id']) && $data['customer_sponsor_id']>0) ? $data['customer_sponsor_id'] : 0 ;
                          if ($referrer_agent['agent_id']!=0) {
                            $migareference->addSponsor($referrer_agent);
                          } 
                      }                    
                    }                  
                  } 
                }else {
                  throw new \Exception(__('Invalid action type.'));
                }
                
                $payload = [
                    'success' => true,
                    'message' => __('Successfully update.'),
                    'data' => $data,
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function updatephonebookaccessAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $application     = $this->getApplication();
                $app_id          = $application->getId();
                $full_phonebook=$this->getRequest()->getParam('full_phonebook');
                $agent_id=$this->getRequest()->getParam('agent_id');
                $data['full_phonebook']=$full_phonebook;
                $migareference->updateAgent($data,$app_id,$agent_id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully update.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function updatepaidstatusaccessAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $application     = $this->getApplication();
                $app_id          = $application->getId();
                $paid_status_access = $this->getRequest()->getParam('paid_status_access');
                $agent_id = $this->getRequest()->getParam('agent_id');
                $pre_settings = $migareference->preReportsettigns($app_id);
                if (!count($pre_settings) || intval($pre_settings[0]['agent_can_manage']) !== 1) {
                    throw new \Exception(__('Agent status management is disabled.'));
                }
                $data = [
                    'paid_status_access' => $paid_status_access
                ];
                $migareference->updateAgent($data, $app_id, $agent_id);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully update.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function updateredeemstatusAction() {
      if ($key = $this->getRequest()->getParam('key')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $app_id        = $this->getRequest()->getParam('app_id');
                $temp_keys     = explode('@',$key);
                $redeemed_id   = $temp_keys[0];
                $status        = $temp_keys[1];
                $referrer_id   = $temp_keys[2];
                $priz_id       = $temp_keys[3];
                $credits       = $temp_keys[4];
                $ledger_id     = $temp_keys[5];
                $prize_name    = $temp_keys[6];
                $earning['app_id']           = $app_id;
                $earning['user_id']          = $referrer_id;
                $earning['amount']           = $credits;
                $earning['entry_type']       = ($status==1) ? 'D' : 'C';
                $earning['trsansection_by']  = $_SESSION['front']['object_id'];
                $earning['user_type']        = 2;//1: for app cutomers 2: for app admins 3: for agent
                $earning['prize_id']         = $priz_id;
                $earning['self_id']          = $ledger_id;
                $earning['trsansection_description']  = ($status==1) ? 'Prize Delivered' : 'Prize Refused';
                $agent_id= $_SESSION['front']['object_id'];
                $entry_count=$migareference->get_prize_entry_count($ledger_id,$app_id,$priz_id,$referrer_id);
                $invoice_settings=$migareference->getpropertysettings($app_id,$referrer_id);
                $agent_user=$migareference->getSingleuser($app_id,$agent_id);
                $credit_balance= $migareference->get_credit_balance($app_id,$referrer_id);
                if ($status==1 && $entry_count[0]['credit']==$entry_count[0]['debit']) {
                  $migareference->saveLedger($earning);
                }
                $debit=$entry_count[0]['debit']-1;
                if ($status==2 && $entry_count[0]['credit']==$debit) {
                  $migareference->saveLedger($earning);
                }
                $default              = new Core_Model_Default();
                $base_url             = $default->getBaseUrl();
                $app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                $tags    = ['@@prize_title@@', '@@prize_credits@@', '@@user_credits@@','@@referral_name@@','@@agent_name@@', '@@app_link@@', '@@app_name@@'];
                $strings = [$prize_name, $credits, $credit_balance[0]['credits'],$invoice_settings[0]['invoice_name']." ".$invoice_settings[0]['invoice_surname'],$agent_user[0]['firstname']." ".$agent_user[0]['lastname'],$app_link,$entry_count[0]['name']];
                if ($status==1) {
                $set_status=3;
                }else {
                $set_status=2;
                }
                $notification_data = $migareference->getprznotification($app_id,$set_status);
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
                        $migareference->sendMail($email_data,$app_id,$referrer_id);
                      }
                      if ($notification_data['ref_prz_notification_type']==1 || $notification_data['ref_prz_notification_type']==3) {
                        $migareference->sendPush($push_data,$app_id,$referrer_id);
                      }
                  }
                if ($notification_data['prz_notification_to_user']==1 || $notification_data['prz_notification_to_user']==3) {
                      $admin_user_data          = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                      $email_data['email_title']= str_replace($tags,$strings,$notification_data['agt_prz_email_title']);
                      $email_data['email_text'] = str_replace($tags,$strings,$notification_data['agt_prz_email_text']);
                      // Push data
                      $push_data['push_title']  = str_replace($tags,$strings,$notification_data['agt_prz_push_title']);
                      $push_data['push_text']   = str_replace($tags,$strings,$notification_data['agt_prz_push_text']);
                      $push_data['open_feature'] = $notification_data['agt_prz_open_feature'];
                      $push_data['feature_id']   = $notification_data['agt_prz_feature_id'];
                      $push_data['custom_url']   = $notification_data['agt_prz_custom_url'];
                      $push_data['cover_image']  = $notification_data['agt_prz_custom_file'];
                      $push_data['app_id']       = $app_id;
                      foreach ($admin_user_data as $key => $value) {
                        if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==2) {
                          $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                        }
                        if ($notification_data['agt_prz_notification_type']==1 || $notification_data['agt_prz_notification_type']==3) {
                          $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                        }
                      }
                  }
                $migareference->updateredeemstatus($redeemed_id,$status);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully status update.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2,
                    'messageut' => $email_data['email_text']." ".$notification_data['agt_prz_email_text'],
                    'messagedut' => $notification_data
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'temp_keys' => $temp_keys,
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
  public function removestatusAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
                $data['app_id']=$this->getRequest()->getParam('app_id');
                $data['total_reports']=$this->getRequest()->getParam('total_reports');
                $data['is_standard']=$this->getRequest()->getParam('is_standard');
                $data['status_id']=$this->getRequest()->getParam('status_id');
                if ($data['total_reports']>0) {
                    $errors .= __('Status should not have any Connected Reports.')."<br/>";
                }
                if ($data['is_standard']>0) {
                    $errors .= __('Cannot Remove Standard Status.')."<br/>";
                }
                if(!empty($errors)) {
                    throw new Exception($errors);
                }
                $datas['status']=0;
                $migareference = new Migareference_Model_Migareference();
                $response=$migareference->updateStatusbyKey($datas,$data['status_id']);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed Status.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function resetreminderAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {       
                $data = $this->getRequest()->getPost();                       
                $migareference = new Migareference_Model_Migareference();
                foreach ($data['reminderCheckbox'] as $id) {                                  
                  $migareference->archiveReminder($id);
                }
                $reset_log['app_id']=$data['app_id'];
                $reset_log['admin_id']=$_SESSION['front']['object_id'];
                $reset_log['total_count']=count($data['reminderCheckbox']);;
                $reset_log['created_at']  = date('Y-m-d H:i:s');
                $migareference->reminderResetLog($reset_log);
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Reset Reminders.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  '$data' => $data,
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
  public function revertmigrationAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
                $migareference = new Migareference_Model_Migareference();
                $migration_log = $migareference->get_migration_log($app_id);
                $invoice_log_data=[];
                $report_log_data=[];
                $admin_log_data=[];
                $agent_log_data=[];
                if (count($migration_log)) {
                $invoice_log_data=unserialize($migration_log[0]['invoice_settings_log']);
                $report_log_data=unserialize($migration_log[0]['report_log']);
                $admin_log_data=unserialize($migration_log[0]['admins_log']);
                $agent_log_data=unserialize($migration_log[0]['agents_log']);
                }
                foreach ($report_log_data as $key => $value) {
                  $temp_invoice = explode('@',$value);
                  $tem_invoice  = explode('*',$temp_invoice[1]);
                  $migareference->deleteReport($tem_invoice[0]);
                  $migareference->deleteMigrationlog($app_id);
                }
                $payload = [
                    'success' => true,
                    'message' => __('Successfully Removed Status.'),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
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
  public function populateoptinformAction()
    {        
        try {
            // Pass the value_id to the layout
            $value_id = $this->getRequest()->getParam('option_value_id');            
            $resp = $this->getLayout()
            ->setBaseRender('emailform', 'migareference/application/includes/optinform.phtml', 'admin_view_default')
            ->setValueid($value_id); 
            $data = [
                'success' => true,
                'resp' => $resp,                
                'form' => $this->getLayout()->render(),
                'message_timeout' => 0,
                'message_button' => 0,
                'message_loader' => 0,
            ];
        } catch (Exception $e) {
            $data = [
                'error' => true,
                'message' => $e->getMessage(),
                'message_button' => 1,
                'message_loader' => 1,
            ];
        }
        $this->_sendJson($data);
    }
  public function populateaiintegrationviewAction()
    {        
        try {
            // Pass the value_id to the layout
            $value_id = $this->getRequest()->getParam('option_value_id');            
            $resp = $this->getLayout()
            ->setBaseRender('emailform', 'migareference/application/includes/ai_integrations.phtml', 'admin_view_default')
            ->setValueid($value_id); 
            $data = [
                'success' => true,
                'resp' => $resp,                
                'test' => "test",                
                'form' => $this->getLayout()->render(),
                'message_timeout' => 0,
                'message_button' => 0,
                'message_loader' => 0,
            ];
        } catch (Exception $e) {
            $data = [
                'error' => true,
                'message' => $e->getMessage(),
                'message_button' => 1,
                'message_loader' => 1,
            ];
        }
        $this->_sendJson($data);
    }
  public function populatehowitworksviewAction()
    {        
        try {
            // Pass the value_id to the layout
            $value_id = $this->getRequest()->getParam('option_value_id');            
            $resp = $this->getLayout()
            ->setBaseRender('emailform', 'migareference/application/includes/how_it_works.phtml', 'admin_view_default')
            ->setValueid($value_id); 
            $data = [
                'success' => true,                              
                'form' => $this->getLayout()->render(),
                'message_timeout' => 0,
                'message_button' => 0,
                'message_loader' => 0,
            ];
        } catch (Exception $e) {
            $data = [
                'error' => true,
                'message' => $e->getMessage(),
                'message_button' => 1,
                'message_loader' => 1,
            ];
        }
        $this->_sendJson($data);
    }
  public function populatereportsdataAction()
    {        
        try {
            $resp = $this->getLayout()->setBaseRender('emailform', 'migareference/application/reportsdata.phtml', 'admin_view_default');
            $data = [
                'success' => true,
                'resp' => $resp,
                'form' => $this->getLayout()->render(),
                'message_timeout' => 0,
                'message_button' => 0,
                'message_loader' => 0,
            ];
        } catch (Exception $e) {
            $data = [
                'error' => true,
                'message' => $e->getMessage(),
                'message_button' => 1,
                'message_loader' => 1,
            ];
        }
        $this->_sendJson($data);
    }
  public function resetdefaultstatusAction() {
    if ($app_id = $this->getRequest()->getParam('app_id')) {
        try {
              $value_id = $this->getRequest()->getParam('value_id');
              $migareference = new Migareference_Model_Migareference();
              $reports=$migareference->getCustomstatusreports($app_id);
              if (count($reports)) {
                throw new Exception("SORRY! WE CANNOT PROCEED, ONE OR MORE REPORTS ARE ASSIGNED TO CUSTOM STATUSES, PLEASE MOVE THEM ON ANY OF DEFAULT STATUS BEFORE PROCEED!");
              } else {
                $res=$migareference->defaultTriggers($app_id,$value_id,'update');
              }
              $payload = [
                  'success' => true,
                  'message' => __('Successfully Revert Settings.'),
                  'message_loader' => 0,
                  'message_button' => 0,
                  'message_timeout' => 2,
                  'other'=>$res
              ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => __($e->getMessage()),
                'other'=>$res
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
  public function revertpresetttingsAction() {
    if ($app_id = $this->getRequest()->getParam('app_id')) {
        try {
              $value_id = $this->getRequest()->getParam('value_id');
              $migareference = new Migareference_Model_Migareference();
              $migareference->defaultPreSettings($app_id,$value_id,'update');
              $payload = [
                  'success' => true,
                  'message' => __('Successfully Revert Settings.'),
                  'message_loader' => 0,
                  'message_button' => 0,
                  'message_timeout' => 2
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
  public function reportledgersAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $report_collection = [];
            $filter_array = [];
            $filter_array['filter_string']="";
            $filter_array['invoic_string']="";
            $filter_array['app_id']=$data['app_id'];
            $migareference = new Migareference_Model_Migareference();
            $all_reports  = $migareference->get_all_reports($filter_array);
            foreach ($all_reports as $key => $value) {
              $last_modification_date=$value['last_modification_at'];
              $now       = time(); // or your date as well
              $your_date = strtotime($last_modification_date);
              $datediff  = $now - $your_date;
              $days      = round($datediff / (60 * 60 * 24));
              $old_date_timestamp = strtotime($value['created_at']);
              $new_date = date('Y-m-d', $old_date_timestamp);
              $old_date_timestamp = strtotime($value['last_modification_at']);
              $modi_date = date('Y-m-d', $old_date_timestamp);
              $destPath     = Core_Model_Directory::getBasePathTo();
              $platform_url = explode("/",$destPath);//index 4 have platform url
              $warrning_icon= "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/warrning.png";
              $warrning_img="";
              if ($value['certificate_status']) {
                $certificte_status="<button onclick='reportdetail(".$value['migareference_report_id'].")' class='button center-block btn bt_save btn color-blue'>".__("Detail")."</button>";
              }else {
              $certificte_status ="<button  class='button center-block btn bt_save btn color-blue'>".__("Pending")."</button>";
              }
              // Manage Warring icon
              if ($value['reminder_grace_days']!=0 && $value['reminder_grace_days']<$days && $value['standard_type']!=3 && $value['standard_type']!=4) {
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }else if($days>30 && $value['standard_type']!=3 && $value['standard_type']!=4){
                $warrning_img = "<img src=".$warrning_icon." alt='' width='13px'>";
              }
                $report_collection[]=[
              $value['report_no'],
              $new_date,
              $value['invoice_name']." ".$value['invoice_surname'],
              $value['owner_name']." ".$value['owner_surname'],
              $certificte_status
            ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function updatelinkorderAction(){
    $build_index   = $this->getRequest()->getParam('build_index');
    $migareference  = new Migareference_Model_Migareference();
    $linkids       = explode("*",$build_index);
    foreach ($linkids as $key => $value) {
      $order            = $key+1;
      $data['order_id'] = $order;
      $migareference->updateStatusbyKey($data,$value);
    }
    header('Content-type:application/json');
    $responsedata = json_encode($linkids);
    print_r($responsedata);
    exit;
  }
  public function updatereportfieldbykeyAction(){
    $build_index   = $this->getRequest()->getParam('build_index');
    $migareference  = new Migareference_Model_Migareference();
    $linkids       = explode("*",$build_index);
    foreach ($linkids as $key => $value) {
      $order               = $key+1;
      $data['field_order'] = $order;
      $migareference->updateReportfieldbyKey($data,$value);
    }
    header('Content-type:application/json');
    $responsedata = json_encode($linkids);
    print_r($responsedata);
    exit;
  }
  public function importnotificationformdataAction(){
   $event_id        = $this->getRequest()->getParam('event_id');
   $application     = $this->getApplication();
   $app_id          = $application->getId();
   $migareference  = new Migareference_Model_Migareference();
   $edititem        = $migareference->getEventNotificationTemplats($app_id,$event_id);
   $destPath        = Core_Model_Directory::getBasePathTo();
   $platform_url    = explode("/",$destPath);//index 4 have platform url
   $edititem[0]['status_icon_db_file']=$edititem[0]['status_icon'];
   if ($edititem[0]['status_icon']!="") {
     $edititem[0]['status_icon']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$edititem[0]['status_icon'];
   }
   if ($edititem[0]['agt_cover_image']!="") {
     $edititem[0]['agt_cover_image']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$edititem[0]['agt_cover_image'];
   }
   if ($edititem[0]['ref_cover_image']!="") {
     $edititem[0]['ref_cover_image']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$edititem[0]['ref_cover_image'];
   }
   if ($edititem[0]['reminder_agt_cover_image']!="") {
     $edititem[0]['reminder_agt_cover_image']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$edititem[0]['reminder_agt_cover_image'];
   }
   if ($edititem[0]['reminder_ref_cover_image']!="") {
     $edititem[0]['reminder_ref_cover_image']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$edititem[0]['reminder_ref_cover_image'];
   }
   if ($edititem[0]['migareference_sms_template_id']==NULL) {
     if ($edititem[0]['agt_sms_text']=="") {
     $edititem[0]['agt_sms_text']=$edititem[0]['agt_push_text'];
    }
    if ($edititem[0]['ref_sms_text']=="" || $edititem[0]['ref_sms_text']==NULL) {
      $edititem[0]['ref_sms_text']=$edititem[0]['ref_push_text'];
    }
    $edititem[0]['sms_notification_to_user']=$edititem[0]['push_notification_to_user'];
   }
   $responsedata['item']=$edititem;
   header('Content-type:application/json');
   $responsedata = json_encode($responsedata);
   print_r($responsedata);
   exit;
  }
  public function importreportfieldformdataAction(){
   $key             = $this->getRequest()->getParam('key');
   $application     = $this->getApplication();
   $app_id          = $application->getId();
   $migareference   = new Migareference_Model_Migareference();
   $edititem        = $migareference->findReportField($key);
   $edititem[0]['default_option_value']  = explode("@",$edititem[0]['default_option_value']);//index 4 have platform url
  //  default warning text
  if ($edititem[0]['grace_period_warning_message']==null) {
    $edititem[0]['grace_period_warning_message']=__("Potentially Duplicate Report Detected");
  }
   header('Content-type:application/json');
   $responsedata = json_encode($edititem[0]);
   print_r($responsedata);
   exit;
  }
  public function getexternallinksAction()
  {   
          try {            
            $report_id     = $this->getRequest()->getParam('report_id'); 
            $app_id        = $this->getRequest()->getParam('app_id'); 
            $externalllins = new Migareference_Model_Externalreportlink();
            $migareference = new Migareference_Model_Migareference();
            $utilities = new Migareference_Model_Utilities();
            $admins        = $externalllins->urladmins($app_id,$report_id); 
            $bitly_crede   = $migareference->getBitlycredentails($app_id);
            $default       = new Core_Model_Default();
            $base_url      = $default->getBaseUrl();
            $temp=[];
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
            $report_links  = $externalllins->links($report_id);                   
            $link_collection = [];
            foreach ($report_links as $key => $value) {                            
              if (!$value['is_agent']) {                
                $report_url = (!empty($value['short_url'])) ? $value['short_url'] : $value['long_url'] ;              
                if (empty($value['short_url']) || 
                    (strpos($value['short_url'], 'bit.ly') === false && strpos($value['short_url'], 'mssl') === false)) {
                      $report_url = $utilities->shortLink($report_url);                      
                      $update['migareference_report_urls_id']=$value['migareference_report_urls_id'];      
                      $update['short_url']=$report_url;      
                      $externalllins->setData($update)->save(); 
                }

                $link_collection[]=[
                        NULL,
                        $value['firstname']." ".$value['lastname'],
                        $report_url,                        
                        '<button title="'.__('Reset Link').'" class="btn btn-secondary" onclick="resetExternalLink('.$value['migareference_report_urls_id'].','.$report_id.')">'."<i class='fa fa-refresh' rel=''></i>".'</button>'
                      ];
              }
            }
              $payload = [
                  "data" => $link_collection,                  
                  "temp" => $temp,                  
                  "tempp" => $temp,                  
                  "admins" => $admins,                  
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function getagentexternallinksAction()
  {   
          try {            
            $report_id     = $this->getRequest()->getParam('report_id'); 
            $app_id        = $this->getRequest()->getParam('app_id'); 
            $externalllins = new Migareference_Model_Externalreportlink();
            $migareference = new Migareference_Model_Migareference();            
            $report_links  = $externalllins->links($report_id);                   
            $link_collection = [];
            foreach ($report_links as $key => $value) {  
              if ($value['is_agent']) {
                $link_collection[]=[
                        NULL,
                        $value['firstname']." ".$value['lastname'],
                        $value['short_url'],                        
                        '<button title="'.__('Reset Link').'" class="btn btn-secondary" onclick="resetExternalLink('.$value['migareference_report_urls_id'].','.$report_id.')">'."<i class='fa fa-refresh' rel=''></i>".'</button>'
                      ];
              }                                        
            }
              $payload = [
                  "data" => $link_collection,                  
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function reportdetailAction(){
   $report_id     = $this->getRequest()->getParam('param1');
   $application   = $this->getApplication();
   $app_id        = $application->getId();
   $migareference = new Migareference_Model_Migareference();
   $status        = $migareference->getReportStatus($app_id);
   $status        = $migareference->templateStatus($status,1);
   $edititem      = $migareference->getReportItem($app_id,$report_id);
   $app_content   = $migareference->get_app_content($app_id);
   $pre_settings  = $migareference->preReportsettigns($app_id);
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
   $static_header_closer=' *</label></div><div class="col-sm-6">';
   $static_footer='</div></div>';
            $report_status=$static_header_start;
           $report_status.= __('');
           $report_status.=$static_header_closer."<strong>".__("Report NO")." # ".$edititem[0]['report_no']."</strong>"." | "."<strong>".__("Created at")." ".$edititem[0]['report_created_at']."</strong>";
           $report_status.=$static_footer;
           $report_status.=$static_header_start;
           $report_status.= __('Report Status');
           $report_status.=$static_header_closer;
           $report_status.='<select id="report_status" onChange="chnagestatus(this)" class="input-flat" name="report_status" >';
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
      $report_credits.='<input type="text" '.$commission_fee_action.' name="commission_fee" id="commission_fee" value='.$com_value.' class="input-flat" >';
      $report_credits.=$static_footer;
      $display = ($edititem[0]['is_comment']==1 || $edititem[0]['status_title']=="Declinato/Non Venduto" ) ? "" : "none" ;
      $declined_comment='<div style=display:'.$display.' class="form-group" id="comment-container-text"><div class="col-sm-4"><label for="">';
      $declined_comment.= __("Comment");
      $declined_comment.=$static_header_closer;
      $declined_comment.='<textarea  class="pin input-flat" id="comment_text" name="comment" rows="4" cols="80">'.$edititem[0]['comment'].'</textarea>';
      $declined_comment.='<input id="is_comment_flag" type="hidden" name="is_comment" value='.$edititem[0]['is_comment'].'>';
      $declined_comment.=$static_footer;
      $ref_name=$static_header_start;
      $ref_name.= __("Referral Name");
      $ref_name.=$static_header_closer;
      $ref_name.='<input type="text"  name="invoice_name" id="invoice_name" value='.$edititem[0]['invoice_name'].' class="input-flat" >';
      $ref_name.=$static_footer;
      $ref_surname=$static_header_start;
      $ref_surname.= __("Referral Sur Name");
      $ref_surname.=$static_header_closer;
      $ref_surname.='<input type="text"  name="invoice_surname" id="invoice_surname" value='.$edititem[0]['invoice_surname'].' class="input-flat" >';
      $ref_surname.=$static_footer;
      $ref_mobile=$static_header_start;
      $ref_mobile.= __("Referral Mobile");
      $ref_mobile.=$static_header_closer;
      $ref_mobile.='<input type="text"  name="invoice_mobile" id="invoice_mobile" value="'.$edititem[0]['invoice_mobile'].'" class="input-flat" >';
      $ref_mobile.=$static_footer;
      
      $last_modification=$static_header_start;
      $last_modification.= __("Last Modification at");
      $last_modification.=$static_header_closer;
      $last_modification.='<input type="text" disabled name="last_modification_at" id="last_modification_at" value='.$edititem[0]['last_modification_at'].' class="input-flat" >';
      $last_modification.=$static_footer;
      $custom_type = '<div class="form-group">';
      $custom_type .= '<div class="col-sm-4">';
      $custom_type .= '<label for="report_status">';
      $custom_type .= $app_content[0]['report_type_pop_title'] . " ";
      $custom_type .= ' *</label>';
      $custom_type .= '</div>';
      $custom_type .= '<div class="col-sm-6">';
      $custom_type .= '<select id="refreral_user_id" class="input-flat" name="report_custom_type"  >';
      $custom_type .= '<option value="1"';
      if ($edititem[0]['report_custom_type'] == 1) {
          $custom_type .= ' selected="selected"';
      }
      $custom_type .= '>' . $app_content[0]['report_type_pop_btn_one_text'] . '</option>';
      $custom_type .= '<option value="2"';
      if ($edititem[0]['report_custom_type'] == 2) {
          $custom_type .= ' selected="selected"';
      }
      $custom_type .= '>' . $app_content[0]['report_type_pop_btn_two_text'] . '</option>';
      $custom_type .= '</select>';
      $custom_type .= '</div>';
      $custom_type .= '</div>';

     // Dynamic Fields
     $static_fields[1]['name']="property_type";
     $static_fields[2]['name']="sales_expectations";
     $static_fields[3]['name']="address";
     $static_fields[4]['name']="owner_name";
     $static_fields[5]['name']="owner_surname";
     $static_fields[6]['name']="owner_mobile";
     $static_fields[7]['name']="note";
     $field=$report_status;
     if ($pre_settings[0]['enable_report_type']==1) {
      $field.=$custom_type; 
    }   
     $field.=$declined_comment;
     $field.=$report_credits;
     $country_id=0;
     foreach ($field_data as $key => $value) {
       $display=($value['is_visible']==1) ? "" : "none" ;
       $required = ($value['is_required']==1) ? "*" : "" ;
       if ($value['type']==1) {
             $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
             $field.='<label for='.$static_fields[$value['field_type_count']]['name'].'>'.__($value['label']).' '.$required.' </label>';
             $field.='</div><div class="col-sm-6" >';
             $name=$static_fields[$value['field_type_count']]['name'];
             $field_value = (!empty($edititem[0][$name])) ? $edititem[0][$name] : "" ;
             $longitude=$edititem[0]['longitude'];
             $latitude=$edititem[0]['latitude'];
             $field.=$this->manageinputypevalueAction($app_id,$value['field_type'],$name,$value['field_option'],0,$field_value,$longitude,$latitude,$value['option_type'],$value['default_option_value'],0);
       }else {
         $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
         $name="extra_".$value['field_type_count'];
         $field.='<label for='.$name.'>'.__($value['label']).' '.$required.'</label>';
         $field.='</div><div class="col-sm-6" >';
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
     $field.=$created_at;
     $field.=$last_modification;
   // END:Dynamic Filed Logic
   $responsedata['item']=$edititem;
   $responsedata['res']=$field_data;
   $responsedata['ress']=$field;
   $responsedata['resss']=$field_data_values;
   header('Content-type:application/json');
   $responsedata = json_encode($responsedata);
   print_r($responsedata);
   exit;
  }
  public function getrditprizeAction(){
   $prize_id      = $this->getRequest()->getParam('param1');
   $migareference = new Migareference_Model_Migareference();
   $prize         = $migareference->getSinglePrize($prize_id);
   $destPath      = Core_Model_Directory::getBasePathTo();
   $platform_url  = explode("/",$destPath);//index 4 have platform url
   $application   = $this->getApplication();
   $app_id        = $application->getId();
   $prize[0]['prize_icon']="https://".$platform_url[4]."/images/application/".$app_id."/features/migareference/".$prize[0]['prize_icon'];
   header('Content-type:application/json');
   $responsedata = json_encode($prize);
   print_r($responsedata);
   exit;
  }
  public function resetexternalurlAction(){
   $report_url_id = $this->getRequest()->getParam('report_url_id');
   $application   = $this->getApplication();
   $app_id        = $application->getId();
   $externalllins = new Migareference_Model_Externalreportlink();
   $migareference = new Migareference_Model_Migareference();
   $utilities = new Migareference_Model_Utilities();
   $bitly_crede   = $migareference->getBitlycredentails($app_id);
   $default       = new Core_Model_Default();
   $base_url      = $default->getBaseUrl();    
      $token=$this->randomPassword(35);      
      $long_url=$base_url."/migareference/crmreports?"."app_id=".$app_id."&token=".$token;
      $short_link = $utilities->shortLink($long_url);            
      // if their is any error urrlshortAction will retrun long_url
      // instead to save long url in database we will save empty short url so later could be replaced
      if ($short_link==$long_url) {
        $short_link="";
      }
      $data['migareference_report_urls_id']=$report_url_id;
      $data['long_url']=$long_url;
      $data['short_url']=$short_link;      
      $externalllins->setData($data)->save();                  
   $responsedata = json_encode($data);
   print_r($responsedata);
   exit;
  }
  public function getrditremindertypeAction(){
   $reminderTypeId  = $this->getRequest()->getParam('param1');
   $migareference   = new Migareference_Model_Migareference();
   $reminderType    = $migareference->getSingleReminderType($reminderTypeId);
   header('Content-type:application/json');
   $responsedata = json_encode($reminderType[0]);
   print_r($responsedata);
   exit;
  }
  public function getautoremindertypeAction(){
   $reminderAutoId  = $this->getRequest()->getParam('param1');
   $migareference   = new Migareference_Model_Migareference();
   $reminderAutos   = $migareference->getSingleReminderAuto($reminderAutoId);      
   header('Content-type:application/json');
   $responsedata = json_encode($reminderAutos[0]);
   print_r($responsedata);
   exit;
  } 
  public function getprospectrefitemAction(){
   $param           = $this->getRequest()->getParam('param1');
   $type            = $this->getRequest()->getParam('type');
   $migareference   = new Migareference_Model_Migareference();
   $phonebookitem   = $migareference->prospectRefPhnDetail($param);
   $gdpr_consent    = '';
   $default         = new Core_Model_Default();
   $base_url        = $default->getBaseUrl();
   $app_icon_path     = $base_url."/app/local/modules/Migareference/resources/appicons/";
   if ($type==1) {
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
  public function deletedusersAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {                 
              $app_id=$datas['app_id'];           
              $migareference   = new Migareference_Model_Migareference();
              $checkDeletedreferrer=$migareference->checkDeletedreferrer($app_id);   
              $referral_list       = $migareference->getReferrers($app_id);
              $all_agents     = $migareference->get_customer_agents($app_id);            
              $deleted_users=[];
              $referral_users='';
              $referral_users='<select id="" class="input-flat assign-customer" name="" onChange="assignToReferrer(this,\'Ref\')" >';
                  $referral_users.='<option  value="0">'.__('Select Referrer').'</option>';
                        foreach ($referral_list as $key => $valuee):
                          $referral_users.='<option value='.$valuee["user_id"].'>'.$valuee["invoice_surname"]." ".$valuee['invoice_name'].'</option>';
                        endforeach;
                      $referral_users.='</select>';

              $agent_users='';
              $agent_users='<select id="" class="input-flat assign-customer" name="" onChange="assignToReferrer(this,\'Agt\')" >';
                  $agent_users.='<option  value="0">'.__('Select Agent').'</option>';
                        foreach ($all_agents as $key => $valuee):
                          $agent_users.='<option value='.$valuee["user_id"].'>'.$valuee["lastname"]." ".$valuee['firstname'].'</option>';
                        endforeach;
                      $agent_users.='</select>';


              if (COUNT($checkDeletedreferrer)) {
                foreach ($checkDeletedreferrer as $key => $value) {                  
                  $reports=$migareference->getRefReports($app_id,$value['user_id']);
                  $report_list='';
                  foreach ($reports as $keyy => $valuee) {
                    $report_list.="#".$valuee['report_no']." ";
                  }
                  if ($report_list!='') {                    
                    $deleted_users[]=[
                      $value['user_id'],
                      $report_list,
                      $referral_users.'<br>'.$agent_users
                    ];
                  }
                }
              }
              $payload = [
                  "data" => $deleted_users
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
  public function assigntoreferrerAction() {
      if ($datas = $this->getRequest()->getQuery()) {
          try {                 
              $app_id=$datas['app_id'];           
              $from_user_id=$datas['from_user_id'];           
              $to_user_id=$datas['to_user_id'];           
              $migareference = new Migareference_Model_Migareference();              
                    $log_data['app_id']=$app_id;
                    $log_data['user_id']=$_SESSION['front']['object_id'];
                    $log_data['user_type'] = 2;//1: for app cutomers 2: for app admins 3: for agent
                    $log_data['log_type']="Deleted User: Reassign Reports";
                    $log_data['log_detail']="Reassing Reports From User ID ".$from_user_id." To User ID".$to_user_id;
                    $user_type = ($datas['type']=='Ref') ? 1 : 3 ;
                    // Get all corrosponding reports for 'From user'
                    $reports=$migareference->getRefReports($app_id,$from_user_id);                  
                    foreach ($reports as $keyy => $value) {                      
                      $log_data['report_id']=$value['migareference_report_id'];
                      // Save Staus Update Log
                      $value['user_id']=$to_user_id;
                      $value['user_type']=$user_type;
                      $migareference->updatepropertyreport($value);
                      $migareference->saveLog($log_data);
                    }
              $payload = [
                  'success' => true,
                  'message' => __("Successfully updated"),                  
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage()),
                  'data' => $app_id.'@'.$form_user_id
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
  public function getsingejobAction(){
   $jobId  = $this->getRequest()->getParam('param1');
   $migareference   = new Migareference_Model_Migareference();
   $job    = $migareference->getsingejob($jobId);
   header('Content-type:application/json');
   $responsedata = json_encode($job);
   print_r($responsedata);
   exit;
  }
  public function getsingeprofessionAction(){
   $professionId  = $this->getRequest()->getParam('param1');
   $migareference   = new Migareference_Model_Migareference();
   $profession    = $migareference->getsingeprofession($professionId);
   header('Content-type:application/json');
   $responsedata = json_encode($profession);
   print_r($responsedata);
   exit;
  }
  public function getextrnalurladminAction(){
   $report_id  = $this->getRequest()->getParam('report_id');
   $app_id     = $this->getApplication()->getId();
   $external   = new Migareference_Model_Externalreportlink();
   $admin_list = $external->urladmins($app_id,$report_id);
   header('Content-type:application/json');
   $responsedata = json_encode($admin_list);
   print_r($responsedata);
   exit;
  }
  public function socialsharestatusAction() {
    try{
      if ($customer_id = $this->getRequest()->getParam('customer_id')) {
          $migareference = new Migareference_Model_Migareference();
          $app_id=$this->getRequest()->getParam('app_id');
          if($this->getRequest()->getParam('status') == 2) {
          $migareference->deleteSocialshare($app_id,$customer_id);
          } else {
              $data['app_id'] = $this->getRequest()->getParam('app_id');
              $data['user_id'] = $this->getRequest()->getParam('customer_id');
              $migareference->saveSocialshare($data);
          }
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function prizestatusAction() {
    try{
      if ($id = $this->getRequest()->getParam('id')) {
          $migareference = new Migareference_Model_Migareference();
              $data['app_id'] = $this->getRequest()->getParam('app_id');
              $data['migarefrence_prizes_id'] = $this->getRequest()->getParam('id');
              $data['prize_status'] = $this->getRequest()->getParam('status');
              $migareference->prizestatus($data);
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function setblacklistAction() {
    try{
      if ($id = $this->getRequest()->getParam('id')) {
          $migareference = new Migareference_Model_Migareference();
          $data['is_blacklist']=$this->getRequest()->getParam('status');
          $change_by=$_SESSION['front']['object_id'];
          $migareference->update_phonebook($data,$id,$change_by,2);//Also save log if their is change in Rating,Job,Notes
          $payload = [
              'success' => true,
              'message' => __('Phone Number has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function setexcludelistAction() {
    try{
      if ($id = $this->getRequest()->getParam('id')) {
          $migareference = new Migareference_Model_Migareference();
          $data['is_exclude']=$this->getRequest()->getParam('status');
          $change_by=$_SESSION['front']['object_id'];
          $migareference->update_phonebook($data,$id,$change_by,2);//Also save log if their is change in Rating,Job,Notes
          $payload = [
              'success' => true,
              'message' => __('Phone Number has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function ownerstatusAction() {
    try{
      if ($customer_id = $this->getRequest()->getParam('customer_id')) {
          $app_id        = $this->getRequest()->getParam('app_id');
          $action_staus  = $this->getRequest()->getParam('status');
          $migareference = new Migareference_Model_Migareference();
          $admins        = $migareference->getAdmins($app_id);
          $haveReport    = $migareference->havereport($app_id,$customer_id);
          $is_agent      = $migareference->is_agent($app_id,$customer_id);
          $is_admin      = $migareference->is_admin($app_id,$customer_id);
          $errors="";
          if (count($admins)==1 && $action_staus==2) {
            $errors.=__('Cannot delete all admin settings.');
          }          
          if ($action_staus==1) {
            if ((COUNT($is_agent) || COUNT($is_admin))) {
              $errors.=__('A user can not be Admin and Agnet at same time.');
            }
          }
          
          if (!empty($errors)) {
            throw new Exception($errors);
          }else {
            if($action_staus==2) {
              $migareference->deleteAdmin($app_id,$customer_id);
            } else {
              $data['app_id'] = $app_id;
              $data['user_id'] = $customer_id;
              $migareference->saveAdmin($data);
            }
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
  public function contactransferAction() {
    try{
      if ($customer_id   = $this->getRequest()->getParam('customer_id')) {
          $app_id        = $this->getRequest()->getParam('app_id');
          $migareference = new Migareference_Model_Migareference();
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
            $inv_settings['terms_accepted']=1;
            $inv_settings['special_terms_accepted']=1;
            $inv_settings['privacy_accepted']=1;
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
  public function autoreminderstatusAction() {
    try{
      if ($id = $this->getRequest()->getParam('id')) {
          $datas['auto_rem_status']  = $this->getRequest()->getParam('status');
          $migareference = new Migareference_Model_Migareference();
          $migareference->updateReportReminderAuto($id,$datas);
          $payload = [
              'success' => true,
              'message' => __('Status has been updated successfully.'),
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
  public function agentstatusAction() {
    try{
      if ($customer_id   = $this->getRequest()->getParam('customer_id')) {
          $app_id        = $this->getRequest()->getParam('app_id');
          $action_staus  = $this->getRequest()->getParam('status');
          $agent_type    = $this->getRequest()->getParam('agent_type');
          $migareference = new Migareference_Model_Migareference();
          $is_admin      = $migareference->is_admin($app_id,$customer_id);          
          $errors="";
          if (count($is_admin) && $action_staus==1) {
            $errors.=__('A user can not be Agent and Admin at same time.');
          }
          if (!empty($errors)) {
            throw new Exception($errors);
          }else {
            if($action_staus == 2) {                            
              $migareference->deleteAgent($app_id,$customer_id);//Remove from _agent table
              $migareference->deleteAgentProvinces($app_id,$customer_id);//Unlink the provinces connected to this agent
              $migareference->deleteReferrerAgent($app_id,$customer_id);//Unlink the Referrers connected to this agent
              $agents              = $migareference->get_customer_agents($app_id);              
              $pre_report_settings = $migareference->preReportsettigns($app_id);
              if (!COUNT($agents) && $pre_report_settings[0]['enable_mandatory_agent_selection']==1) { //it mean we have no agent or delete all agents so we will check if agent selection is mandory we will make it optionla
                $pre_report_settings[0]['enable_mandatory_agent_selection']=2;
                $pre_report_settings=$pre_report_settings[0];
                $migareference->updatePreReport($pre_report_settings);  
              }
            } else {              
              $data['app_id']  = $app_id;
              $data['user_id'] = $customer_id;
              $data['agent_type'] = $agent_type;
              $migareference->saveAgent($data);              
            }
          }
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function remindertypestatusAction() {
    try{
      if ($id = $this->getRequest()->getParam('id')) {
          $migareference  = new Migareference_Model_Migareference();
          $data['status'] = $this->getRequest()->getParam('status');
                            $migareference->update_reminder_type($id,$data);
          $payload = [
              'success' => true,
              'message' => __('Successfully update reminder status.'),
              'message_loader' => 0,
              'message_button' => 0,
              'message_timeout' => 2,
              'other'=>$haveReport
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
  public function userstatusAction() {
    try{
      if ($setingsid = $this->getRequest()->getParam('setingsid')) {
          $migareference = new Migareference_Model_Migareference();
          $app_id       = $this->getRequest()->getParam('app_id');
          $status       = $this->getRequest()->getParam('status');
          $data['status']= ($status==1) ? 1 : 0;
          $migareference->updatePropertysettings($data,$setingsid);
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
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
  public function reportformbuilderAction() {
    try{
        $application   = $this->getApplication();
        $app_id        = $application->getId();
        $migareference = new Migareference_Model_Migareference();
        $pre_settings  = $migareference->preReportsettigns($app_id);
        $siberian_usrs = $migareference->get_siberianuser($app_id);
        $field_data    = $migareference->getreportfield($app_id);
        $app_content   = $migareference->get_app_content($app_id);
        $default       = new Core_Model_Default();
        $base_url      = $default->getBaseUrl();
        $pre_settings[0]['base_url']       = $base_url;
        $pre_settings[0]['invite_message'] = trim($pre_settings[0]['invite_message']);
        // Referrer User list
        $ref_user='<div class="form-group">';
            $ref_user.='<div class="col-sm-4">';
                $ref_user.='<label for="report_status">';
                  $ref_user.= __('Referrer User');
                $ref_user.=' *</label>';
            $ref_user.='</div>';
            $ref_user.='<div class="col-sm-6">';
                $ref_user.='<select id="refreral_user_id" class="input-flat" name="referral_user_id" onChange="newReportuser(this)" >';
                      foreach ($siberian_usrs as $key => $value):
                        $is_referral = ($value['migareference_invoice_settings_id']==NULL) ? "*" : "" ;
                        $referral_sign = ($value['migareference_invoice_settings_id']==NULL) ? 1 : 2 ;
                        $ref_user.='<option value='.$value["customer_id"]."@".$referral_sign.'>'.$is_referral." ".$value["lastname"]." ".$value['firstname']." - ".$value["email"].'</option>';
                      endforeach;
                    $ref_user.='</select>';
            $ref_user.='</div>';
          $ref_user.='</div>';
        $custom_type='<div class="form-group">';
            $custom_type.='<div class="col-sm-4">';
                $custom_type.='<label for="report_status">';
                  $custom_type.= $app_content[0]['report_type_pop_title']." ";
                $custom_type.=' *</label>';
            $custom_type.='</div>';
            $custom_type.='<div class="col-sm-6">';
                $custom_type.='<select id="refreral_user_id" class="input-flat" name="report_custom_type"  >';
                      $custom_type.='<option value="1">'.$app_content[0]['report_type_pop_btn_one_text'].'</option>';                      
                      $custom_type.='<option value="2">'.$app_content[0]['report_type_pop_btn_two_text'].'</option>';                      
                $custom_type.='</select>';
            $custom_type.='</div>';
          $custom_type.='</div>';
          $static_fields[1]['name']="property_type";
          $static_fields[2]['name']="sales_expectations";
          $static_fields[3]['name']="address";
          $static_fields[4]['name']="owner_name";
          $static_fields[5]['name']="owner_surname";
          $static_fields[6]['name']="owner_mobile";
          $static_fields[7]['name']="note";
          $field=$ref_user;
          if ($pre_settings[0]['enable_report_type']==1) {
            $field.=$custom_type; 
          }          
          foreach ($field_data as $key => $value) {
            $display=($value['is_visible']==1) ? "" : "none" ;
            $required = ($value['is_required']==1) ? "*" : "" ;
            if ($value['type']==1) {
                  $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
                  $field.='<label for='.$static_fields[$value['field_type_count']]['name'].'>'.__($value['label']).' '.$required.' </label>';
                  $field.='</div><div class="col-sm-6" >';
                  $field.=$this->manageinputypeAction($app_id,$value['field_type'],$static_fields[$value['field_type_count']]['name'],$value['field_option'],0,$value['options_type'],$value['default_option_value']);
            }else {
              $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
              $name="extra_".$value['field_type_count'];
              $field.='<label for='.$name.'>'.__($value['label']).' '.$required.'</label>';
              $field.='</div><div class="col-sm-6" >';
              $field.=$this->manageinputypeAction($app_id,$value['field_type'],$name,$value['field_option'],$value['field_type_count'],$value['options_type'],$value['default_option_value']);
            }
          }
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
              'message_timeout' => 2,
              'form_builder' => $field,
              'other'=>$siberian_usrs,
              'osther'=>$field_data
          ];
    } catch (\Exception $e) {
        $payload = [
            'error' => true,
            'message' => __($e->getMessage())
        ];
    }
      $this->_sendJson($payload);
  }
  public function savereportAction(){
    try{
      $data                 = $this->getRequest()->getPost();
      $app_id               = $data['app_id'];
      $errors               = "";
      $duplicate_warning    = "";
      $default              = new Core_Model_Default();
      $base_url             = $default->getBaseUrl();
      $app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
      $migareference        = new Migareference_Model_Migareference();
      $reportNotification   = new Migareference_Model_Reportnotification();
      $report_noti_tags_list= $reportNotification->reportNotiTagsList();
      $pre_report_settings  = $migareference->preReportsettigns($app_id);
      $field_data           = $migareference->getreportfield($app_id);      
      $temp_options         = explode('@',$data['referral_user_id']);
      $data['user_id']      = $temp_options[0];
      $taxID                = $this->randomTaxid();
      $static_fields[1]="property_type";
      $static_fields[2]="sales_expectations";
      $static_fields[3]="address";
      $static_fields[4]="owner_name";
      $static_fields[5]="owner_surname";
      $static_fields[6]="owner_mobile";
      $static_fields[7]="note";
      $address_error=false;
      // validation rules for dynamic report Fileds
      foreach ($field_data as $key => $value) {
        $name="extra_".$value['field_type_count'];
        if ($value['field_type']==6) {
          $birth_date = date('Y-m-d',strtotime($data[$name]));
          $report_entry['owner_dob']=$birth_date;
        }
        // Generic check for required fields
        //$value['field_type']==7 is email and excluded from this check 29-03-2025
        if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && $value['field_type']!=7  && empty($data[$name])) {
          $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
        }elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$static_fields[$value['field_type_count']]])) {
          $errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
        }
        // Explicitly check for email
        if ($value['type']==2 && $value['is_visible']==1 && $value['field_type']==7 && !empty($data[$name]) && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
          $errors .= __('Email is not correct. Please add a valid email address') . "<br/>";
        }
        // Explicitly check for address
        if ($value['type']==1 &&
            $value['is_visible']==1 &&
            $value['is_required']==1 &&
            !empty($static_fields[$value['field_type_count']]) &&
            $static_fields[$value['field_type_count']]=='address' &&
            $pre_report_settings[0]['enable_unique_address']==1)
          {
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
        // Explicitly check for owner mobile number
        if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='owner_mobile' && $pre_report_settings[0]['is_unique_mobile']==1) {
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
      if (!empty($errors)) {
      throw new Exception($errors);
    }else {
        $repo_data        = $migareference->get_last_report_no();
        $invoice_settings = $migareference->getpropertysettings($app_id,$data['user_id']);
        $password         = $this->randomPassword(10);
        $taxID            = $this->randomTaxid();
        $user_data        = $migareference->getAgentdata($data['user_id']);
        // if only siberian user save agrrement settings with default tax_id
          if ($temp_options[1]==1) {
            $inv_settings['app_id']=$app_id;
            $inv_settings['user_id']=$data['user_id'];
            $inv_settings['blockchain_password']=$password;
            $inv_settings['invoice_name']=$user_data[0]['firstname'];
            $inv_settings['invoice_surname']=$user_data[0]['lastname'];
            $inv_settings['invoice_mobile']=$user_data[0]['mobile'];
            $inv_settings['tax_id']=$taxID;
            $inv_settings['terms_accepted']=1;
            $inv_settings['special_terms_accepted']=1;
            $inv_settings['terms_artical_accepted']=1;
            $inv_settings['privacy_accepted']=1;
            $inv_settings['privacy_artical_accepted']=1;
            $invoice_response=$migareference->savePropertysettings($inv_settings);
          }
        // If owner not set Pre Settings default will be
        $data['commission_type']= 0;
        $data['commission_fee'] = 5000;
        $data['reward_type']    = $pre_report_settings[0]['reward_type'];
        $data['commission_type']= $pre_report_settings[0]['commission_type'];
        if (count($pre_report_settings) && $pre_report_settings[0]['reward_type']==1) {
            $data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_amount'] : 0 ;
        } else {
          $data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_credits'] : 0 ;
        }
          $data['app_id']=$app_id;
          $staus_data = $migareference->get_one_standard_status($app_id,1);//Standard index
          $data['currunt_report_status']=$staus_data[0]['migareference_report_status_id'];
          $data['last_modification']=$status_data[0]['status_title'];
          $data['last_modification_by']=$_SESSION['front']['object_id'];;
          $data['last_modification_at']=date('Y-m-d H:i:s');
          $data['report_no'] = (!count($repo_data)) ? 1000 : $repo_data[0]['report_no']+1;
          $report_entry['report_no']=$data['report_no'];
          $report_entry['app_id']=$app_id;
          $report_entry['user_id']=$data['user_id'];
          $report_entry['property_type']=$data['property_type'];
          $report_entry['sales_expectations']=$data['sales_expectations'];
          $report_entry['report_custom_type']=$data['report_custom_type'] ?? 1;
          $report_entry['commission_type']=$pre_report_settings[0]['commission_type'];
          $report_entry['reward_type']=$pre_report_settings[0]['reward_type'];
          $report_entry['commission_fee']=$data['commission_fee'];
          $report_entry['is_reminder_sent']=0;
          $report_entry['report_source']=3;//Report Source 3 for Owner end report source type
          $report_entry['address']=$data['address'];
          $report_entry['longitude']=$data['longitude'];
          $report_entry['latitude']=$data['latitude'];
          $report_entry['owner_name']=$data['owner_name'];
          $report_entry['owner_surname']=$data['owner_surname'];
          $report_entry['owner_mobile']=$data['owner_mobile'];
          $report_entry['owner_hot']=$data['owner_hot'];
          $report_entry['note']=$data['note'];
          $report_entry['currunt_report_status']=$staus_data[0]['migareference_report_status_id'];
          $report_entry['last_modification']=$status_data[0]['status_title'];
          $report_entry['last_modification_by']=$_SESSION['front']['object_id'];;
          $report_entry['last_modification_at']=date('Y-m-d H:i:s');
          $report_entry['extra_dynamic_fields']=serialize($data);
          $report_entry['extra_dynamic_field_settings']=serialize($field_data);
          $report_id = $migareference->savepropertyreport($report_entry);
          $agent_user_data  = $migareference->getReportSponsor($app_id,$report_id);
          if ($report_id>0) {
            // Add note for report
            if (!empty($data['report_note'])) {
              $note['app_id']        = $app_id;
              $note['user_id']       = 0;//The user or Admin who add report just for record no role anywhere;
              $note['report_id']     = $report_id;
              $note['notes_content'] = $data['report_note'];
              $migareference->insert_notes($note);
            }              
            // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
            $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_id,$report_entry['currunt_report_status'],$report_entry['last_modification_by'],'ADMIN-END','create');                            
          }
      }
          $payload = [
              'success' => true,
              'sms_retur' => $sms_retur,
              'message' => __('Successfully report saved.'),
              'address_error_message' => __('Warning! Address already used in another report. Please be aware that it is possible someone else already submitted the same report.'),
              'notifcation_response' => $notifcation_response,
              'message_loader' => 0,
              'message_button' => 0,
              'message_timeout' => 2
          ];
    } catch (\Exception $e) {
        $payload = [
            'error' => true,
            'message' => __($e->getMessage()),
            'notifcation_response' => $notifcation_response,
            'field_data' => $field_data,
        ];
    }
      $this->_sendJson($payload);
  }
  public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$option_type=0,$option_default="")
  {
    $extra_input_template="";
    $migareference  = new Migareference_Model_Migareference();
    if ($type==1) {
      $extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
    } else if($type==2) {
      $extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
          $temp_options=explode('@',$options);
          $extra_input_template.='<select id="'.$ng_model.'"  class="input-flat" name="'.$ng_model.'">';
          foreach ($temp_options as $key => $value) {
            $option_value++;
            $extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
          }
          $extra_input_template.="</select></div></div>";
          break;
        case 1://Country List
          $geoCountries              = $migareference->getGeoCountries($app_id);
          $df_opt=explode("@",$option_default);
          $extra_input_template.='<select onChange=loadProvicnes(0,1,0) id="'.$ng_model.'"  class="input-flat country_default" name="'.$ng_model.'">';
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
        $extra_input_template.='<select id="'.$ng_model.'"  class="input-flat province_default" name="'.$ng_model.'">';
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
      $extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="" class="input-flat" placeholder="Google Location" />';
      $extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value=''>";
      $extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value=''> </div></div>";
    }else if($type==5) {
      $extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input-flat"></textarea></div></div>';
    }else if($type==6) {
      $extra_input_template.='<input type="date"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
    }elseif ($type==7) {//email
      $extra_input_template.='<input type="email"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
    }
    return $extra_input_template;
  }
  public function manageinputypevalueAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$field_value="",$longitude="",$latitude="",$option_type=0,$option_default="",$country_id=0)
  {
    $extra_input_template="";
    $migareference  = new Migareference_Model_Migareference();
    if ($type==1) {
      $extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input-flat" placeholder="" /></div></div>';
    } else if($type==2) {
      $extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input-flat" placeholder="" /></div></div>';
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
        $temp_options=explode('@',$options);
        $extra_input_template.='<select id="'.$ng_model.'"  class="input-flat" name="'.$ng_model.'">';
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
          $extra_input_template.='<select onChange=loadProvicnes(0,1,0) id="'.$ng_model.'"  class="input-flat country_default" name="'.$ng_model.'">';
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
        $extra_input_template.='<select id="'.$ng_model.'"  class="input-flat province_default" name="'.$ng_model.'">';
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
      $extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="'.$field_value.'" class="input-flat" placeholder="Google Location" />';
      $extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value='".$longitude."'>";
      $extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value='".$latitude."'> </div></div>";
    }else if($type==5) {
      $extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input-flat">'.$field_value.'</textarea></div></div>';
    }else if($type==6) {
      $extra_input_template.='<input type="date"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input-flat" placeholder="" /></div></div>';
    }elseif ($type==7) {//email
      $extra_input_template.='<input type="email"  name="'.$ng_model.'" id="'.$ng_model.'" value="'.$field_value.'" class="input-flat" placeholder="" /></div></div>';
    }
    return $extra_input_template;
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
  
  public function creditledgerAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference   = new Migareference_Model_Migareference();
            $ledger_statment = $migareference->get_leadger_customer($data['app_id'],$data['referrer_id']);
            $balance         = 0;
            $report_collection=[];
            foreach ($ledger_statment as $key => $value) {
              $created_at = date('Y-m-d', strtotime($value['created_at']));
              if ($value['entry_type']=='C') {
                $balance=$balance+$value['amount'];
                $credits="+".$value['amount'];
              }else {
                $balance=$balance-$value['amount'];
                $credits="-".$value['amount'];
              }
                $report_collection[]=[
                          $created_at,
                          $value['email'],
                          $value['trsansection_description'],
                          $credits,
                          $balance
                        ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function loadgeoproviceAction() {
      if ($datas = $this->getRequest()->getPost()) {
          try {
            $migareference = new Migareference_Model_Migareference();
            $app_id        = $this->getApplication()->getId();            
            $dataGeoConPro = $migareference->getGeoCountryProvicnes($app_id,$datas['country_id']);            
            $is_agent      = $migareference->is_agent($app_id,$datas['customer_id']);
            $collection    = [];
            foreach ($dataGeoConPro as $key => $value) {
              $checkboxx="";              
              $allocated     = $migareference->getAllocatedProvinces($app_id,$datas['country_id'],$value['migareference_geo_provinces_id']);
              if (COUNT($allocated)==2) {
                if ($allocated[0]['user_id']==$datas['customer_id'] || $allocated[1]['user_id']==$datas['customer_id']) {
                  $checkboxx="checked";
                }                  
              }else if(COUNT($allocated)==1){
                  if ($allocated[0]['user_id']==$datas['customer_id']) {
                    $checkboxx="checked";
                  }else {
                    if ($allocated[0]['agent_type']==$is_agent[0]['agent_type']) {
                      $checkboxx="checked disabled";
                    }   
                  }             
              }              
              $collection[]=[
                        'app_id'=>$value['app_id'],
                        'country_id'=>$value['country_id'],
                        'created_at'=>$value['created_at'],
                        'migareference_geo_provinces_id'=>$value['migareference_geo_provinces_id'],
                        'province'=>$value['province'],
                        'province_code'=>$value['province_code'],
                        'checkbox'=>$checkboxx
                      ];
            }
            $payload = [
                  "data" => $collection,
                  "AGENTS" => $cu_provinces['province_id'],
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
  public function loadeprovincereferrersAction() {
      if ($datas = $this->getRequest()->getPost()) {
          try {
            $migareference = new Migareference_Model_Migareference();
            $app_id        = $this->getApplication()->getId();            
            $dataGeoConPro = $migareference->loadeProvinceReferrers($app_id,$datas['province_id'],$datas['agent_id']);                        
            $collection    = [];
            foreach ($dataGeoConPro as $key => $value) {              
              $collection[]=[
                        'app_id'=>$value['app_id'],
                        'user_id'=>$value['user_id'],
                        'name'=>$value['invoice_surname'].' '.$value['invoice_name'],                        
                      ];
            }
            $payload = [
                  "data" => $collection,                  
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
  public function getprizeAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference   = new Migareference_Model_Migareference();
            $referral_usrs  = $migareference->getprize($data['app_id']);
            foreach ($referral_usrs as $key => $value) {
              if ($value['prize_status']==1) {
                $disable = '<button class="btn btn-info" onclick="prizeStatus(2,'.$value['migarefrence_prizes_id'].')">'.__('NO').'</button>';
              }elseif($value['status']==0) {
                $disable = '<button class="btn btn-info" onclick="prizeStatus(1,'.$value['migarefrence_prizes_id'].')">'.__('Yes').'</button>';
              }
              $action = '<button class="btn btn-info" onclick="prizeEdit('.$value['migarefrence_prizes_id'].')">'.__('Edit').'</button>';
                $report_collection[]=[
                          $value['prize_name'],
                          $value['prize_start_date'],
                          $value['prize_expire_date'],
                          $value['credits_number'],
                          $disable,
                          $action
                        ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function referrerwebhooklogsAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
            $migareference   = new Migareference_Model_Migareference();
            $webhook_logs  = $migareference->getReferrerWebhookLog($app_id);
            $log_collection=[];
            foreach ($webhook_logs as $key => $value) {            
                $log_collection[]=[
                          $value['user_id'],
                          $value['lastname']." ".$value['firstname'],
                          $value['email'],
                          $value['response_type'],                           
                          $value['response_message'],                           
                          date('d-m-Y H:i:s',strtotime($value['log_created_at']))
                        ];
            }
              $payload = [
                  "data" => $log_collection
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'app_id' => $app_id,
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
  public function reportwebhooklogsAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
            $migareference   = new Migareference_Model_Migareference();
            $webhook_logs  = $migareference->getReportWebhookLog($app_id);
            $log_collection=[];
            foreach ($webhook_logs as $key => $value) {            
                $log_collection[]=[
                          $value['report_no'],                          
                          $value['response_type'],                           
                          $value['response_message'],                           
                          date('d-m-Y H:i:s',strtotime($value['log_created_at']))
                        ];
            }
              $payload = [
                  "data" => $log_collection
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'app_id' => $app_id,
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
  public function reminderwebhooklogsAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
            $migareference   = new Migareference_Model_Migareference();
            $webhook_logs  = $migareference->getReminderWebhookLog($app_id);
            $log_collection=[];
            foreach ($webhook_logs as $key => $value) {            
                $log_collection[]=[
                          $value['reminder_list_uid'],                          
                          $value['response_type'],                           
                          $value['response_message'],                           
                          date('d-m-Y H:i:s',strtotime($value['log_created_at']))
                        ];
            }
              $payload = [
                  "data" => $log_collection
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'app_id' => $app_id,
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
  public function creditsapilogAction() {
      if ($app_id = $this->getRequest()->getParam('app_id')) {
          try {
            $api_obj   = new Migareference_Model_Reportapi();
            $api_logs  = $api_obj->getCreditsApiLog($app_id);
            $log_collection=[];
          
            foreach ($api_logs as $key => $value) {      
              $response_type=__("Error");
              if ($value['response']==1) {
                $response_type=__("Success");
              }        
                $log_collection[]=[
                          $value['request_ip'],
                           $response_type,
                          $value['description'],
                          date('d-m-Y H:i:s',strtotime($value['created_at']))
                        ];
            }
              $payload = [
                  "data" => $log_collection
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'app_id' => $app_id,
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
  public function loadreportlogAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_logs          = $migareference->getReportlog($data['app_id'],$data['key']);
            $report_collection = [];
            foreach ($all_logs as $key => $value) {
                 $created_at = date('Y-m-d H:i:s', strtotime($value['created_at']));
                 if ($value['user_type']==1) {
                   $name= ($value['user_id']==99999) ? "System" : $value['cutomerfirstname']." ".$value['cutomerlastname'];
                 }else {
                   $name= ($value['user_id']==99999) ? "System" : $value['adminfirstname']." ".$value['adminlastname'];
                 }
                 $report_collection[]=[
                   $created_at,
                   $name,
                   $value['log_type'],
                   $value['log_source'],
                   $value['log_detail'],                   
                  ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function loadreportnotesAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {            
            $all_notes = (new Migareference_Model_Notes())->findAll(
              ['app_id'=> $data['app_id'], 'report_id' => $data['key']], 
              ['order' => 'created_at DESC']
            )->toArray();
            $notescollection = [];
            foreach ($all_notes as $key => $value) {
              $notescollection[]=[
                $value['migarefrence_notes_id'],
                date('d-m-Y',strtotime($value['created_at'])),
                $value['notes_content'],
                '<button class="btn btn-danger" onclick="deleteNote('.$value['migarefrence_notes_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>'
              ];
            }
              $payload = [
                  "data" => $notescollection
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
  public function loadreportremindersAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {                        
          $migareference = new Migareference_Model_Migareference();          
          $all_reminders = $migareference->getSingleReportReminder($data['app_id'],$data['key']);
            $remindercollection = [];
            foreach ($all_reminders as $key => $value) {
              $remindercollection[]=[
                $value['migarefrence_reminders_id'],
                date('d-m-Y',strtotime($value['event_date_time'])),
                $value['rep_rem_title'],
                '<button class="btn btn-danger" onclick="deleteByReportReminder('.$value['migarefrence_reminders_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>'
              ];
            }
              $payload = [
                  "data" => $remindercollection
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
  public function getpushlogAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_logs          = $migareference->getPushlog($data['app_id']);
            $report_collection = [];
            foreach ($all_logs as $key => $value) {
                 $report_collection[]=[
                              $value['push_message_id'],
                              $value['title'],
                              $value['text'],
                              $value['email'],
                              $value['status'],
                              $value['date']
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function getemaillogAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_logs          = $migareference->getEmaillog($data['app_id']);
            $report_collection = [];
            foreach ($all_logs as $key => $value) {
                 $report_collection[]=[
                               $value['migareference_email_log_id'],
                                $value['email_title'],
                                $value['email_text'],
                                $value['email'],
                                __("Delivered"),
                                date('Y-m-d', strtotime($value['created_at']))
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function getwilliologsAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $twillio_logs     = new Migareference_Model_Twilliolog();
            $all_logs          = $twillio_logs->getwilliologs($data['app_id']);//Last 7 days
            $logs_collection = [];
            foreach ($all_logs as $key => $value) {
                 $logs_collection[]=[
                              date('d-m-Y H:i', strtotime($value['created_at'])),
                               $value['firstname']." ".$value['lastname'],                                 
                               $value['sms_text'],                                
                               $value['api_response']                                
                            ];
              }
              $payload = [
                  "data" => $logs_collection
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
  public function loadjobsAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_logs          = $migareference->getJobs($data['app_id']);
            $report_collection = [];
            foreach ($all_logs as $key => $value) {
              $action1 = '<button class="btn btn-info" onclick="editJob('.$value['migareference_jobs_id'].')">'.__('Edit').'</button>';
              $action2 = '<button class="btn btn-danger" onclick="deleteJob('.$value['migareference_jobs_id'].')">'.__('Delete').'</button>';
              $report_collection[]=[
                                $value['migareference_jobs_id'],
                                __($value['job_title']),
                                $action1,
                                $action2
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function loadprofessionsAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_professions          = $migareference->getProfessions($data['app_id']);
            $report_collection = [];
            foreach ($all_professions as $key => $value) {
              $action1 = '<button class="btn btn-info" onclick="editProfession('.$value['migareference_professions_id'].')">'.__('Edit').'</button>';
              $action2 = '<button class="btn btn-danger" onclick="deleteProfession('.$value['migareference_professions_id'].')">'.__('Delete').'</button>';
              $report_collection[]=[
                                $value['migareference_professions_id'],
                                __($value['profession_title']),
                                $action1,
                                $action2
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function loadgeolocationsAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_geo_countries = $migareference->getGeoCountries($data['app_id']);
            if (!count($all_geo_countries)) {
              $migareference->defaultGeoCountrieProvinces($data['app_id']);
              $all_geo_countries = $migareference->getGeoCountrieProvinces($data['app_id'],$data['country_id']);
            }else {
              $all_geo_countries = $migareference->getGeoCountrieProvinces($data['app_id'],$data['country_id']);
            }
            $report_collection = [];
            foreach ($all_geo_countries as $key => $value) {
              $action1 = '<button class="btn btn-info" onclick="editGeoLocation(' . $value['migareference_geo_countries_id'] . ', ' . $value['migareference_geo_provinces_id'] . ', \'' . $value['province_code'] . '\')"><i class="fa fa-pencil" rel=""></i></button>';

              $action2 = '<button class="btn btn-danger" onclick="deleteGeo('.$value['migareference_geo_countries_id'].','.$value['migareference_geo_provinces_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>';
              $report_collection[]=[
                              $value['migareference_geo_provinces_id'],
                              __($value['province'])."-".__($value['province_code']),
                              $action1,
                              $action2
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function loadgeocountriesAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $all_geo_countries = $migareference->getGeoCountries($data['app_id']);                        
            $report_collection = [];
            foreach ($all_geo_countries as $key => $value) {            
              $report_collection[]=[
                              $value['migareference_geo_countries_id'],
                              __($value['country'])."-".__($value['country_code'])                              
                            ];
              }
              $payload = [
                  "data" => $report_collection
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
  public function referralusersAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference  = new Migareference_Model_Migareference();
            $pre_settings    = $migareference->preReportsettigns($data['app_id']);  
            $commission_lable=$pre_settings[0]['commission_lable'];
            if ($data['key']==-1) {
              $referral_usrs  = $migareference->get_referral_users($data['app_id']);
            }else {
              $referral_usrs  = $migareference->get_sponsor_agent($data['app_id'],$data['key']);
            }
            $all_agents     = $migareference->get_customer_agents($data['app_id']);            
            foreach ($referral_usrs as $key => $value) {
              $sponsors       = "";
              $sponsors.="<select class='input-flat' id='chnage_sponsor' onChange='chnageSponsor(this)'>";
              $sponsors.="<option value=".$value['user_id']."@0>".__("Not Found")."</option>";
              foreach ($all_agents as $keyy => $valuee) {
                $selected = ($value['migareference_app_agents_id']==$valuee['migareference_app_agents_id']) ? 'selected' : '' ;
                $sponsors.="<option  ".$selected." value=".$value['user_id']."@".$valuee['user_id'].">".$valuee['firstname']." ".$valuee['lastname']."</option>";
              }
              $sponsors.="</select>";
              $agent=__("Not Found");
              $migareference_app_agents_id=0;
              if ($value['migareference_app_agents_id']!=NULL) {
                $agent_data=$migareference->get_referral_agent($value['migareference_app_agents_id']);
                $migareference_app_agents_id=$agent_data[0]['customer_id'];
                $agent=$agent_data[0]['firstname']." ".$agent_data[0]['lastname'];
              }
              $created_at = date('Y-m-d', strtotime($value['created_at']));
              $total = ($value['total_earn']!=NULL) ? $value['total_earn'] : 0 ;
              if ($value['status']==1) {
                $action = '<button class="btn btn-info" onclick="userStatus(2,'.$value['migareference_invoice_settings_id'].')">'.__('Active').'</button>';
              }elseif($value['status']==0) {
                $action = '<button class="btn btn-info" onclick="userStatus(1,'.$value['migareference_invoice_settings_id'].')">'.__('Disabled').'</button>';
              }
              if ($value['leave_status']==1) {
                $push_status = '<p><i class="fa fa-check" style="font-size:40px;color:green;"></i></p>';
              }else {
                $push_status = '<p><i class="fa fa-times" style="font-size:40px;color:red;"></i></p>';
              }
              $detail = '<button class="btn" onclick="refDetail('.$migareference_app_agents_id.','.$value['user_id'].','."'".$agent."'".')">'.__('Detail').'</button>';
                $report_collection[]=[
                          $created_at,
                          $value['invoice_name']." ".$value['invoice_surname'],
                          $value['email'],
                          $value['mobile'],
                          $value['province'],
                          $detail,
                          $total.$commission_lable,
                          $value['credits'],
                          $agent,
                          $sponsors,
                          $action,
                          $push_status
                        ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function loadreferrerphonebookAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();            
            $app_id=$data['app_id'];
            $pre_settings    = $migareference->preReportsettigns($app_id);  
            $commission_lable=$pre_settings[0]['commission_lable'];
            // Build JOINS as per filter
            $query_join='';
            if($data['job_filter_key']>0){
              $query_join.=' AND ph.job_id='.$data['job_filter_key'];
            }
            if($data['profession_filter_key']>0){
              $query_join.=' AND ph.profession_id='.$data['profession_filter_key'];
            }
            if($data['ref_province']>0){
              $query_join.=' AND mis.address_province_id='.$data['ref_province'];
            }
            if($data['ref_profiled_filter']==1){ // Profiled
              $query_join.=' AND ph.job_id>0 AND ph.rating>0';
            }elseif ($data['ref_profiled_filter']==2) { // Not Profiled
              $query_join.=' AND (ph.job_id=0 OR ph.rating=0)';
            }
            if($data['range_filter_key'] > 0){
              // Get the current date
              $currentDate = new DateTime();
          
              // Calculate the start date based on the selected range
              switch($data['range_filter_key']) {
                  case 7: // Past 7 days
                      $startDate = $currentDate->modify('-7 days');
                      break;
                  case 30: // Past 30 days
                      $startDate = $currentDate->modify('-30 days');
                      break;
                  case 90: // Past 3 months
                      $startDate = $currentDate->modify('-3 months');
                      break;
                  case 180: // Past 6 months
                      $startDate = $currentDate->modify('-6 months');
                      break;
                  case 365: // Past 12 months
                      $startDate = $currentDate->modify('-12 months');
                      break;
                  default:
                      // Handle default case or error
                      $startDate = $currentDate;
              }
          
              // Format the start date to match database's date format
              $formattedStartDate = $startDate->format('Y-m-d');                     
              $query_join .= " AND DATE(mis.created_at) >= '" . $formattedStartDate . "'";
          }          
            $prospect_jobs     = $migareference->get_opt_referral_users($data['app_id'],$query_join);
            $icon_list         = $migareference->getStaticIons();
            $report_collection = [];
            // Filter Veriables
            $ref_reports_count = $data['ref_reports_count'];      
            switch ($ref_reports_count) {
              case -1:
                $min_report_count_filter=0;
                $max_report_count_filter=1000000;
                break;
              case 0:
                $min_report_count_filter=0;
                $max_report_count_filter=5;
                break;
              case 1:
                $min_report_count_filter=5;
                $max_report_count_filter=10;
                break;
              case 2:
                $min_report_count_filter=10;
                $max_report_count_filter=30;
                break;
              case 3:
                $min_report_count_filter=30;
                $max_report_count_filter=50;
                break;
              case 4:
                $min_report_count_filter=50;
                $max_report_count_filter=1000000;
                break;
            }
            $ref_last_contact  = $data['ref_last_contact'];
            switch ($ref_last_contact) {
              case -1:
                $min_ref_last_contact_filter=0;
                $max_ref_last_contact_filter=1000000;
                break;              
              case 1:
                $min_ref_last_contact_filter=0;
                $max_ref_last_contact_filter=30;
                break;
              case 2:
                $min_ref_last_contact_filter=30;
                $max_ref_last_contact_filter=90;
                break;              
              case 3:
                $min_ref_last_contact_filter=90;
                $max_ref_last_contact_filter=1000000;
                break;
            }
            $now = time();
            $term_warning="<img title='".__('T&C not accepted')."' src=".$icon_list['terms_warrning']." alt='' width='35px'>";
            $profiled="<img title='".__('User Profiled')."' src=".$icon_list['profiled']." alt='' width='35px'>";
            $not_profiled="<img title='".__('User Not Profiled')."' src=".$icon_list['not_profiled']." alt='' width='35px'>";
            foreach ($prospect_jobs as $key => $value) {
                $connected_prospect=$migareference->referrer_to_prospect($value['invoice_id']);
                $lastcontactdate=$value['last_contact_at'];
                if (empty($value['last_contact_at'])) {
                  $logitem           = $migareference->getLatCommunication($value['migarefrence_phonebook_id']);
                  $lastcontactdate   = (!empty($logitem[0]['created_at'])) ? $logitem[0]['created_at'] : $value['phone_creat_date'] ;                 
                }                
                $itemDate          = strtotime($lastcontactdate);
                $datediff          = $now-$itemDate;
                $datediff          = round($datediff / (60 * 60 * 24));
                if (
                  $value['total_reports']>=$min_report_count_filter && $value['total_reports']<=$max_report_count_filter
                  && $datediff>=$min_ref_last_contact_filter && $datediff<=$max_ref_last_contact_filter) {
                  $is_profiled = ( $value['job_id']>0 && $value['rating']>0) ?  $profiled: $not_profiled ;                  
                  $is_profiled_bol = ( $value['job_id']>0 && $value['rating']>0) ?  1: 0 ;                  
                  $edit_action = '<button class="btn btn-info" onclick="editProspectJob('.$value['migarefrence_phonebook_id'].',1,'.$value['terms_accepted'].','.$value['user_id'].','.$is_profiled_bol.')">'."<i class='fa fa-edit'></i>".'</button>';                                    
                  $ref_user='<select id="" class="input-flat" name="" onChange="showProspectEdit(this)" >';
                  $ref_user.='<option  value="0"></option>';
                      foreach ($connected_prospect as $key => $valuee):
                        $ref_user.='<option value='.$valuee["migarefrence_phonebook_id"].'>'.$valuee["lastname"]." ".$valuee['name']." ".$valuee["surname"].'</option>';
                      endforeach;
                  $ref_user.='</select>';
                                              
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

              $total = ($value['total_earn']!=NULL) ? $value['total_earn'] : 0 ;
              if ($value['status']==1) {
                $action = '<button class="btn btn-info" onclick="userStatus(2,'.$value['migareference_invoice_settings_id'].')">'.__('Active').'</button>';
              }elseif($value['status']==0) {
                $action = '<button class="btn btn-info" onclick="userStatus(1,'.$value['migareference_invoice_settings_id'].')">'.__('Disabled').'</button>';
              }                            
              $is_terms_accepted = ( $value['terms_accepted']==0) ?  $term_warning: '' ;                  
              
                    
                    $report_collection_item=[
                                      $edit_action,
                                      // $edit_action,
                                      $is_profiled.' '.$is_terms_accepted.' '.$value['firstname']." ".$value['lastname'],
                                      $value['mobile'],
                                      $value['email'],
                                      date('d-m-Y',strtotime($value['phone_creat_date'])),
                                      "-".($datediff)."d",
                                      $value['credits'],
                                      $total.$commission_lable,
                                      $value['total_reports'],
                                      $agent,    
                                      (empty($value['job_title'])) ? __("Non classificabile") : __($value['job_title']) ,      
                                      (empty($value['profession_title'])) ? __("N/A") : __($value['profession_title']) ,                                
                                      $action,                                      
                                      $ref_user,
                                      (empty($value['birthdate'])) ? "N/A" : date('Y-m-d',$value['birthdate'])
                                  ];
                      if ($data['terms_filter_key']==0) {
                        if ($value['terms_accepted']==0) {
                          $report_collection[]=$report_collection_item;
                        }
                      }else {                                    
                      if (!$data['rating_filter_key'] && $data['sponsor_key']==-1) {
                        $report_collection[]=$report_collection_item;
                      }elseif($data['rating_filter_key']==$value['rating'] && $data['sponsor_key']==-1){
                        $report_collection[]=$report_collection_item;                        
                      }elseif(!$data['rating_filter_key'] && $data['sponsor_key']>0 && ($data['sponsor_key']==$sponsor_one || $data['sponsor_key']==$sponsor_two)){
                        $report_collection[]=$report_collection_item;                        
                      }elseif($data['rating_filter_key']==$value['rating'] && $data['sponsor_key']>0 && ($data['sponsor_key']==$sponsor_one || $data['sponsor_key']==$sponsor_two)){
                        $report_collection[]=$report_collection_item;                        
                      }elseif(!$data['rating_filter_key'] && $data['sponsor_key']==0 && $value['sponsor_count']==0){
                        $report_collection[]=$report_collection_item;                        
                      }
                    }
                    
                  }
                }
              $payload = [
                  "data" => $report_collection,
                  "count" => $prospect_jobs,
                  "query_join" => $query_join,
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
  public function loadprospectphonebookAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
              $migareference     = new Migareference_Model_Migareference();            
              $prospect_list     = $migareference->getAllProspect($data['app_id']);

              $file            = fopen("app/local/modules/Migareference/resources/propertyaddresses/reportphonenumbers_".$data['app_id'].".csv","w");

              $mobile=[
                __("Name"),
                __("Surname"),
                __("Mobile"),
                __("Job_id"),
                __("Disable Phone Grade Period Check"),
                __("Blacklist this contact, dont allow new reports")
              ];
              fputcsv($file, $mobile);

              $report_collection = [];
            foreach ($prospect_list as $key => $value) {
                if (($data['job_filter_key']==$value['job_id'] || $data['job_filter_key']==0)) {
                  $edit_action = '<button class="btn btn-info" onclick="editProspectPhone('.$value['migarefrence_prospect_id'].')">'."<i class='fa fa-edit'></i>".'</button>';
                  if($value['is_blacklist']==2) {
                      $blacklist_action = '<button class="btn btn-info" onclick="setBlackList(1,'.$value['migarefrence_prospect_id'].')">'.__('Yes').'</button>';
                  }else {
                    $blacklist_action = '<button class="btn btn-info" onclick="setBlackList(2,'.$value['migarefrence_prospect_id'].')">'.__('No').'</button>';
                  }
                  if($value['is_exclude']==2) {
                      $exclude_action = '<button class="btn btn-info" onclick="setExcludeList(1,'.$value['migarefrence_prospect_id'].')">'.__('Yes').'</button>';
                  }else {
                    $exclude_action = '<button class="btn btn-info" onclick="setExcludeList(2,'.$value['migarefrence_prospect_id'].')">'.__('No').'</button>';
                  }
                  $refreDetail = '<button class="btn btn-info" disabled onclick="prospectRefPhnDetail()">'.__("NULL").'</button>';
                  if (!empty($value['migareference_invoice_settings_id'])) {
                    $refreDetail = '<button class="btn btn-info" onclick="prospectRefPhnDetail('.$value['migareference_invoice_settings_id'].',2)">'.$value['invoice_name']." ".$value['invoice_surname'].'</button>';
                  }
                $logitem = $migareference->getLatCommunication($value['migarefrence_prospect_id']);
                $lastcontactdate = !empty($logitem[0]['created_at']) ? new DateTime($logitem[0]['created_at']) : new DateTime($value['phone_creat_date']);
                $now = new DateTime();
                $interval = $now->diff($lastcontactdate);
                $datediff = $interval->days;
                $is_exclude=($value['is_exclude']==1) ? 0 : 1 ;
                $is_blacklist=($value['is_blacklist']==1) ? 0 : 1 ;
                    $mobile=[
                      $value['name'],
                      $value['surname'],
                      $value['mobile'],
                      $value['job_id']
                    ];
                    fputcsv($file, $mobile);
                    if (strpos($value['name'], '*') === false) {
                      $report_collection_item=[
                        $edit_action,
                        $value['name'],
                        $value['surname'],
                        $value['mobile'],
                        (empty($value['job_title'])) ? __("Non classificabile") : __($value['job_title']) ,
                        $value['email'],
                        $refreDetail,                        
                        "-".($datediff)."d",
                        date('d-m-Y',strtotime($value['prospect_created_at'])),
                      ];
                      if (!$data['rating_filter_key']) {
                        $report_collection[]=$report_collection_item;
                      }elseif($data['rating_filter_key']==$value['rating']){
                        $report_collection[]=$report_collection_item;                        
                      }
                    }
                  }
              }
              fclose($file);
              $payload = [
                  "data" => $report_collection,
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
  public function genratreferrerdbAction() {      
          try {
              $migareference   = new Migareference_Model_Migareference();            
              $app_id          = $this->getApplication()->getId();
              $referr_detail   = $migareference->get_referral_users($app_id);
              $dir_image       = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              $pre_settings    = $migareference->preReportsettigns($app_id);  
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image=$dir_image . "/features/migareference";
              $file            = fopen($dir_image."/referrer_db.csv","w");                            
              $mobile=[
                __("Ref. ID"),
                __("Ref. Name"),
                __("Ref. Surname"),
                __("Ref. Email"),
                __("Ref. Mobile"),
                __("Ref. Birth Date"),                
                __("Ref. Job Title"),                
                __("Ref. Sector Title"),                
                __("Ref. Credits"),                
                __("Ref. Reports (Total)"),                
                __("Ref. Rating (1-5)"),                
                __("Ref. Engagement (1-10)"),                               
                __("Ref. T&C"),                               
                __("Ref. GDPR"),                               
                __("Ref. Note"),                               
                __("Reciprocity Note"),                               
                __("Agent ID"),
                __("Agent Name"),
                __("Agent Surname"),
                __("Agent Email"),
                __("Agent Mobile"),             
              ];
              if ($pre_settings[0]['enable_main_address']==1) {   
                  $mobile[]=__("Ref. Country");
                  $mobile[]=__("Ref. Country Code");
                  $mobile[]=__("Ref. Province");                    
                  $mobile[]=__("Ref. Province Code");                    
                  }
              fputcsv($file, $mobile);            
            foreach ($referr_detail as $key => $value) {     
                    $agent_data=[];              
                    if ($value['migareference_app_agents_id']!==null) {
                      $agent_data=$migareference->get_referral_agent($value['migareference_app_agents_id']);                
                    }           
                    $mobile=[
                      $value['user_id'],
                      $value['name'],
                      $value['surname'],
                      $value['email'],
                      $value['mobile'],
                      (empty($value['birthdate'])) ? "" : date('d-m-Y', $value['birthdate']),
                      (empty($value['job_title'])) ? __("Non classificabile") : __($value['job_title']),                                            
                      (empty($value['profession_title'])) ? __("N/A") : __($value['profession_title']),                                            
                      (empty($value['credits'])) ? 0 : $value['credits'],
                      $value['total_reports'],                      
                      $value['rating'],
                      $value['engagement_level'],                      
                      ($value['terms_accepted']) ? __("Yes") : __("No"),                                                                 
                      (empty($value['ref_consent_timestmp'])) ? "" : date('d-m-Y H:i:s', strtotime($value['ref_consent_timestmp'])),                                            
                      $value['note'],
                      $value['reciprocity_notes'],
                      $value['sponsor_id'],
                      (COUNT($agent_data)) ?  $agent_data[0]['firstname']: "",                                           
                      (COUNT($agent_data)) ?  $agent_data[0]['lastname']: "",                                           
                      (COUNT($agent_data)) ?  $agent_data[0]['email']: "",                                           
                      (COUNT($agent_data)) ?  $agent_data[0]['mobile']: "",                                           
                    ];
                    if ($pre_settings[0]['enable_main_address']==1) {                         
                      $mobile[]=$value['country'];                    
                      $mobile[]=$value['country_code'];                    
                      $mobile[]=$value['province'];                    
                      $mobile[]=$value['province_code'];                    
                    }
                    fputcsv($file, $mobile);
                  }              
              fclose($file);
              $payload = [
                  "data" => $dir_image,
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function genratsibuserdbAction() {      
          try {
              $migareference   = new Migareference_Model_Migareference();            
              $app_id          = $this->getApplication()->getId();              
              $dir_image       = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image=$dir_image . "/features/migareference";
              $file            = fopen($dir_image."/sibuser_db.csv","w");
              $contact_users = $migareference->getContactsUsers($app_id);              
              $mobile=[
                __("ID"),
                __("Name"),
                __("Sur Name"),
                __("Mobile"),
                __("Email")  ,              
                __("Created At")                
              ];
              fputcsv($file, $mobile);          
              foreach($contact_users as $user) {                
                  $mobile = [                      
                      $user['customer_id'],
                      $user['firstname'],
                      $user['lastname'],
                      $user['mobile'],
                      $user['email'],
                      $user['created_at']
                  ];
                  fputcsv($file, $mobile);
              }                                          
              fclose($file);
              $payload = [
                  "data" => $dir_image,
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function genratprospectdbAction() {      
          try {
              $migareference   = new Migareference_Model_Migareference();            
              $app_id          = $this->getApplication()->getId();
              $pre_settings    = $migareference->preReportsettigns($app_id);            
              $prospect_jobs   = $migareference->reportCsv($app_id);
              $dir_image       = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image=$dir_image . "/features/migareference";
              $file            = fopen($dir_image."/prospect_db.csv","w");
              $earn_lable = ($pre_settings[0]['reward_type']==1) ? __("Total Commision") : __("Total Credits") ;
              $mobile=[                
                __("Prospect Name"),
                __("Prospect SurName"),
                __("Prospect Mobile"),                
              ];
              fputcsv($file, $mobile);            
            foreach ($prospect_jobs as $key => $value) {                
              $earn_value = ($pre_settings[0]['reward_type']==1) ? $value['total_earn'] : $value['total_credits'] ;
                    $mobile=[                                            
                      $value['owner_name'],
                      $value['owner_surname'],
                      $value['owner_mobile'],                      
                    ];
                    fputcsv($file, $mobile);
                  }              
              fclose($file);
              $payload = [
                  "data" => $dir_image,
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function genratreportsdbAction() {      
          try {
              $migareference   = new Migareference_Model_Migareference();            
              $app_id          = $this->getApplication()->getId();
              $pre_settings    = $migareference->preReportsettigns($app_id);            
              $prospect_jobs   = $migareference->reportCsv($app_id);
              $field_data      = $migareference->getreportfield($app_id);
              $dir_image       = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image=$dir_image . "/features/migareference";
              $file            = fopen($dir_image."/reports_db.csv","w");
              $earn_lable = ($pre_settings[0]['reward_type']==1) ? __("commission_fee") : __("credits") ;
              
              
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
              $label_list=[];
              $value_list=[];                                          
              $mobile[]=__("report_no");                            
              foreach ($field_data as $keyy => $valuee) {                
                if ($valuee['type']==1 && $valuee['is_visible']==1) {                                   
                  $mobile[]=$static_fields[$valuee['field_type_count']]['name'];                    
                }elseif($valuee['is_visible']==1) {
                  $mobile[]='custom_field_'.$valuee['field_type_count'].", ";                                 
                }
              }
                $mobile[]=$earn_lable;
                $mobile[]=__("owner_id");
                $mobile[]=__("prospect_gdpr_stamp");
                $mobile[]=__("report_status");
                $mobile[]=__("status_id");
                $mobile[]=__("created_at");
                $mobile[]=__("last_modification");              
                $mobile[]=__("referrer_id");
                $mobile[]=__("referrer_name");
                $mobile[]=__("referrer_surname");
                $mobile[]=__("referrer_mobile");         
                $mobile[]=__("referrer_email");         
                $mobile[]=__("referrer_ext_uid");         
                $mobile[]=__("referrer_dob");         
                $mobile[]=__("referrer_rating");         
                $mobile[]=__("referrer_engagement");                                
                $mobile[]=__("referrer_job");
                $mobile[]=__("referrer_note");                      
                $mobile[]=__("reciprocity_notes");                      
                $mobile[]=__("referrer_terms_condition");                      
                $mobile[]=__("referrer_gdpr");                      
                // if ($pre_settings[0]['enable_main_address']==1) {   
                  $mobile[]=__("referrer_country");
                  $mobile[]=__("referrer_country_code");
                  $mobile[]=__("referrer_province");                    
                  $mobile[]=__("referrer_province_code");                    
                // } 
                $mobile[]=__("agent_id");
                $mobile[]=__("agent_name");
                $mobile[]=__("agent_surname");                                
                $mobile[]=__("agent_email");                                
                $mobile[]=__("agent_mobile");                                
              fputcsv($file, $mobile);            
              foreach ($prospect_jobs as $key => $value) {                
                $mobile = array();
                $mobile[]=$value['report_no'];
                foreach ($field_data as $keyy => $valuee) {
                  $report_id=$value['migareference_report_id'];
                  $edititem= $migareference->getReport($app_id,$report_id);
                  $field_data_values = unserialize( $edititem[0]['extra_dynamic_fields']);                
                  if ($valuee['type']==1 && $valuee['is_visible']==1) {                  
                    $name=$static_fields[$valuee['field_type_count']]['name'];
                    $field_value = (!empty($edititem[0][$name])) ? $edititem[0][$name] : "" ;
                    $longitude=$edititem[0]['longitude'];
                    $latitude=$edititem[0]['latitude'];
                    $mobile[]=$this->manageinputypevaluesignatureAction($app_id,$valuee['field_type'],$name,$valuee['field_option'],0,$field_value,$longitude,$latitude,$valuee['option_type'],$valuee['default_option_value'],0);
                  }elseif($valuee['is_visible']==1) {                  
                    $name="extra_".$valuee['field_type_count'];                  
                    $field_value = (!empty($field_data_values[$name])) ? $field_data_values[$name] : "" ;
                    $longitude=$field_data_values[0]['longitude_'.$valuee['field_type_count']];
                    $latitude=$field_data_values[0]['latitude_'.$valuee['field_type_count']];
                    if ($valuee['options_type']==1) {$country_id=$field_value;}
                    $mobile[]=$this->manageinputypevaluesignatureAction($app_id,$valuee['field_type'],$name,$valuee['field_option'],$valuee['field_type_count'],$field_value,$longitude,$latitude,$valuee['options_type'],$valuee['default_option_value'],$country_id);
                  }
                }
                $earn_value = ($pre_settings[0]['reward_type']==1) ? $value['total_earn'] : $value['total_credits'] ;                                                                                      
                $mobile[]=$earn_value;
                $mobile[]=$value['prospect_id'];
                $mobile[]= (empty($value['consent_timestmp'])) ? '' : date('d-m-Y H:i:s',strtotime($value['consent_timestmp'])) ;
                $mobile[]=$value['status_title'];
                $mobile[]=$value['currunt_report_status'];
                $mobile[]=date('d-m-Y H:i:s',strtotime($value['report_created_at']));
                $mobile[]=date('d-m-Y H:i:s',strtotime($value['last_modification_at']));                
                $mobile[]=$value['user_id'];
                $mobile[]=$value['invoice_name'];
                $mobile[]=$value['invoice_surname'];
                $mobile[]=$value['invoice_mobile'];
                $mobile[]=$value['referrer_email'];                      
                $mobile[]=$value['ext_uid'];                      
                $mobile[]=(empty($value['birthdate'])) ? '' : date('d-m-Y',$value['birthdate']) ;                      
                $mobile[]=$value['rating'];
                $mobile[]=$value['engagement_level'];
                $mobile[]=$value['job_title'];
                $mobile[]=$value['note'];     
                $mobile[]=$value['reciprocity_notes'];     
                $mobile[]=($value['terms_accepted']==1) ? 1 : 0 ;     
                $mobile[]=(empty($value['ref_consent_timestmp'])) ? '' : date('d-m-Y H:i:s',strtotime($value['ref_consent_timestmp'])) ;                       
                $mobile[]=$value['country'];
                $mobile[]=$value['country_code'];
                $mobile[]=$value['province'];
                $mobile[]=$value['province_code'];                                 
                $mobile[]=$value['agent_id'];
                $mobile[]=$value['agent_name'];
                $mobile[]=$value['agent_surname'];
                $mobile[]=$value['agent_email'];
                $mobile[]=$value['agent_mobile'];                                                                                   
                fputcsv($file, $mobile);
              }              
              fclose($file);
              $payload = [
                  "data" => $dir_image,
                  "data1" => $label_list,
                  "data2" => $value_list,
                  "prospect_jobs" => $prospect_jobs,
              ];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
  public function genratrewebhooktagsAction() {      
          try {
                $migareference   = new Migareference_Model_Migareference();            
                $app_id          = $this->getApplication()->getId();                            
                $field_data      = $migareference->getreportfield($app_id);                              
                $pre_settings    = $migareference->preReportsettigns($app_id); 
                $earn_lable      = ($pre_settings[0]['reward_type']==1) ? "commission" : "credits";
                $tags_list=__("WEBHOOK Fields Label:");                          
                $tags_list.="report_no".", " ;      
                $static_fields[1]['name']="property_type";
                $static_fields[2]['name']="sales_expectations";
                $static_fields[3]['name']="address";
                $static_fields[4]['name']="owner_name";
                $static_fields[5]['name']="owner_surname";
                $static_fields[6]['name']="owner_mobile";
                $static_fields[7]['name']="note";                    
                foreach ($field_data as $keyy => $valuee) {                
                  if ($valuee['type']==1 && $valuee['is_visible']==1) { //for static fields we will mask some understandable tags like nome to owner_name
                    $tags_list.=$static_fields[$valuee['field_type_count']]['name'].", ";                    
                  }elseif($valuee['is_visible']==1) {
                    $tags_list.='custom_field_'.$valuee['field_type_count'].", ";
                  }
                }
                // $tags_list.=$earn_lable.", ";
                $tags_list.='commission_fee'.", ";
                $tags_list.="referrer_id".", ";
                $tags_list.="referrer_name".", ";
                $tags_list.="referrer_surname".", ";
                $tags_list.="referrer_mobile".", ";
                $tags_list.="referrer_email".", ";                
                $tags_list.="referrer_ext_uid".", ";                
                $tags_list.= ($pre_settings[0]['enable_main_address']==1) ? "referrer_country, referrer_country_code, referrer_province, referrer_province_code, " : '' ;
                $tags_list.="agent_id".", ";
                $tags_list.="agent_name".", ";                
                $tags_list.="agent_surname".", ";
                $tags_list.="agent_email".", ";
                $tags_list.="report_status".", ";
                // $tags_list.="report_id".", ";
                $tags_list.="status_id".", ";
                $tags_list.="created_at".", ";
                $tags_list.="last_modification".", ";
                $tags_list.="referrer_job".", ";
                $tags_list.="referrer_note".", ";
                $tags_list.="reciprocity_notes".", ";
                $tags_list.="referrer_gdpr".", ";
                $tags_list.="prospect_gdpr_stamp".", ";
                $tags_list.="owner_id".", ";//actually its prospect_id to be alligned with other tags we use owner_id label
                $tags_list.="rd_name".", ";
                $tags_list.="rd_phone".", ";
                $tags_list.="rd_email".", ";
                $tags_list.="rd_calendar_url".", ";
                $payload = [
                  'success' => true,
                  'data' => $tags_list,
                  'tags_list_codes' => $tags_list_codes,
                  'tag_list_values' => $tag_list_values,
                  'webhook_url' => $webhook_url,

              ];                          
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => __($e->getMessage())
              ];
          }      
      $this->_sendJson($payload);
  }
   public function getcommlogAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference       = new Migareference_Model_Migareference();
            $comunilog           = $migareference->getCommunicatioLog($data['app_id'],$data['key']);
            $comm_log_collection = [];
            foreach ($comunilog as $key => $value) {
              $disabled = ($value['log_type']=="Enrollment") ? 'disabled' : '' ;
              $delete = '<button '.$disabled.' class="btn btn-danger" onclick="deleteCommLog('.$value['migareference_communication_logs_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>';
                $comm_log_collection[]=[
                          $delete,
                          $value['user_id'],
                          date('d-m-Y H:i',strtotime($value['created_at'])),
                          $value['log_type'],
                          $value['note']
                        ];
            }
              $payload = [
                  "data" => $comm_log_collection
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
  public function getreportreminderAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $referral_usrs     = $migareference->getReportReminder($data['app_id']);
            $report_collection = [];
            foreach ($referral_usrs as $key => $value) {
              $edit   = '<button style=float: left; class="btn btn-info" onclick="editRepotReminder('.$value['migarefrence_report_reminder_types_id'].')">'."<i class='fa fa-pencil' rel=''></i>".'</button>';
              $delete = '<button class="btn btn-danger" onclick="deleteRepotReminder('.$value['migarefrence_report_reminder_types_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>';
              $badge  = ($value['rep_rem_badge']==1) ? __("Yes") : __("NO") ;
              $type   = ($value['rep_rem_type']==1) ? __("Report") : __("Automation") ;
              $notifcation=__("Email/PUSH");
              if ($value['rep_rem_target_notification']==2) {
                $notifcation=__("Email");
              } else if($value['rep_rem_target_notification']==3){
                $notifcation=__("PUSH");
              }
              $report_collection[]=[
                          $edit." ".$delete,
                          $value['rep_rem_title'],
                          $notifcation,
                          $badge,
                          $type,
                          $value['created_at']
                        ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function getreferrerremindersAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference       = new Migareference_Model_Migareference();
            $app_id=$data['app_id'];
            $trigger_id=$data['trigger'];
            $assigned_to=$data['assigned_to'];
            $current_reminder_status=$data['reminder_status'];
            $all_reminders       = $migareference->getFilteredReferrerReminders($app_id,$trigger_id,$assigned_to,$current_reminder_status);
            $remindercollection  = [];
            foreach ($all_reminders as $key => $value) {
              $remindercollection[]=[
                  $value['migareference_automation_log_id'],
                  '<input type="checkbox" onchange="reminderResetSelection(this.value)" name="reminderCheckbox[]" value="' . $value['migareference_automation_log_id'] . '">',
                  $value['auto_rem_title'],
                  // $value['auto_rem_title'],
                  $value['invoice_name']." ".$value['invoice_surname'],
                  $value['report_no'],
                  $value['current_reminder_status'],
                  $value['automation_trigger_stamp']
                ];
            }
              $payload = [
                  "data" => $remindercollection,
                  "all_reminders" => $all_reminders
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
  public function reminderresetlogAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference       = new Migareference_Model_Migareference();
            $app_id=$data['app_id'];
            $reset_reminder_log       = $migareference->getResetLogs($app_id);
            $remindercollection  = [];
            foreach ($reset_reminder_log as $key => $value) {
              $remindercollection[]=[
                  $value['migareference_reminder_reset_logs_id'],                  
                  $value['firstname']." ".$value['lastname'],
                  $value['total_count'],
                  date('Y-m-d',strtotime($value['created_at']))                  
                ];
            }
              $payload = [
                  "data" => $remindercollection
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
  public function manuallydonereminderAction(){
    try {
          $migareference = new Migareference_Model_Migareference();
          $reminder_id   = $this->getRequest()->getParam('reminder_id');
          $report_id     = $this->getRequest()->getParam('report_id');
          $user_id       = $this->getRequest()->getParam('referrer_id');
          $app_id        = $this->getRequest()->getParam('app_id');
          $autom_log['app_id']       = $app_id;
          $autom_log['reminder_id']  = $reminder_id;
          $autom_log['report_id']    = $report_id;
          $autom_log['user_id']      = $user_id;
          $autom_log['receipent']    = "Admin Manual";
          $autom_log['email_log_id'] = 0;
          $autom_log['push_log_id']  = 0;
          $migareference->saveRepoRemLog($autom_log);
            $payload = [
                "success" => true,
                "message" => __("Successfully update Reminder.")
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        $this->_sendJson($payload);
  }
  public function postponereminderitemAction(){
          $migareference = new Migareference_Model_Migareference();
          $public_key    = $this->getRequest()->getParam('reminder_id');
          $reminder_item = $migareference->editreminder($public_key);
          $reminder_date_time=$reminder_item[0]['reminder_date_time'];
          $item['event_date']=date('Y-m-d',strtotime($reminder_date_time));
          $item['event_time']=date('H:i',strtotime($reminder_date_time));
          $item['reminder_before_type']=$reminder_item[0]['reminder_before_type'];;
          $item['event_type']=$reminder_item[0]['event_type'];;
          $item['reminder_content']=$reminder_item[0]['reminder_content'];;
          $item['reminder_id']=$reminder_item[0]['migarefrence_reminders_id'];;
          header('Content-type:application/json');
          $responsedata = json_encode($item);
          print_r($responsedata);
          exit;
  }
  public function getreportemindersAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
          $migareference = new Migareference_Model_Migareference();
          $app_id        = $data['app_id'];
          $all_reminders = $migareference->getReportReminders($app_id);
          $remindercollection = [];
          foreach ($all_reminders as $key => $value) {
            $reminder_id  = $value['migarefrence_reminders_id'];
            $find_in_log  = $migareference->findInRepoRemLog($reminder_id);
            $postpone_reminder = '<button title="'.__('Postpone Reminder').' ?'.'" class="btn btn-danger" onclick="postponeReminder('.$reminder_id.')">'."<i class='fa fa-bell' rel=''></i>".'</button>';
            $done_reminder = '<button title="'.__('Manually Done Reminder').' ?'.'" class="btn btn-success" onclick="manuallyDoneReminder('.$reminder_id.','.$value['migareference_report_id'].','.$value['user_id'].')">'."<i class='fa fa-check' rel=''></i>".'</button>';
            if (count($find_in_log)) {
              $postpone_reminder = '<button disabled title="'.__('Postpone Reminder').' ?'.'" class="btn btn-danger" onclick="postponeReminder('.$reminder_id.')">'."<i class='fa fa-bell' rel=''></i>".'</button>';
              $done_reminder = '<button disabled title="'.__('Manually Done Reminder').' ?'.'" class="btn btn-success" onclick="manuallyDoneReminder('.$reminder_id.','.$value['migareference_report_id'].','.$value['user_id'].')">'."<i class='fa fa-check' rel=''></i>".'</button>';
            }
            $go_to_report = '<button title="'.__('Go To Report').' ?'.'" class="btn btn-secondary" onclick="gotoReportDetail('.$value['migareference_report_id'].')">'."<i class='fa fa-share' rel=''></i>".'</button>';
            $remindercollection[]=[
                // $go_to_report." ".$done_reminder." ".$postpone_reminder,
                $go_to_report,
                $value['rep_rem_title'],
                $value['event_date_time'],
                $value['owner_name']." ".$value['owner_surname'],
                $value['owner_mobile'],
                $value['reminder_current_status'],
            ];
          }
              $payload = [
                  "data" => $remindercollection
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
  public function getautoreminderAction() {
      if ($data = $this->getRequest()->getQuery()) {
          try {
            $migareference     = new Migareference_Model_Migareference();
            $auto_reminders    = $migareference->getReportReminderAuto($data['app_id']);
            $automation_trigger= $migareference->getAutomationTriggers($data['app_id']);
            $report_collection = [];
            foreach ($auto_reminders as $key => $value) {
              $edit   = '<button style=float: left; class="btn btn-info" onclick="editAutoRepotReminder('.$value['migarefrence_report_reminder_auto_id'].')">'."<i class='fa fa-pencil' rel=''></i>".'</button>';
              $delete = '<button class="btn btn-danger" onclick="deleteAutoRepotReminder('.$value['migarefrence_report_reminder_auto_id'].')">'."<i class='fa fa-remove' rel=''></i>".'</button>';
              $status = '<button title="'.__('Enable Reminder').' ?'.'" class="btn btn-danger" onclick="enableAutoReminder(1,'.$value['migarefrence_report_reminder_auto_id'].')">'."<i class='fa fa-ban' rel=''></i>".'</button>';
              if ($value['auto_rem_status']==1) {
                $status = '<button title="'.__('Disable Reminder').' ?'.'" class="btn btn-info" onclick="enableAutoReminder(2,'.$value['migarefrence_report_reminder_auto_id'].')">'."<i class='fa fa-ban' rel=''></i>".'</button>';
              }
              $reminder_action=($value['auto_rem_action']==1) ? __("Reminder") : __("Engagement") ;
              $trigger_count=0;
              $report_collection[]=[
                        $value['migarefrence_report_reminder_auto_id'],
                        $edit." ".$delete." ".$status,
                        $automation_trigger[$value['auto_rem_trigger']],
                        $value['auto_rem_title'],
                        $reminder_action,
                        $value['total_trigger'],
                        date('Y-m-d',strtotime($value['created_at']))
                      ];
            }
              $payload = [
                  "data" => $report_collection
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
  public function invitemessageAction() {
      if ($report_id= $this->getRequest()->getParam('report_id')) {
          try {
            $migareference  = new Migareference_Model_Migareference();
            $utilities  = new Migareference_Model_Utilities();
            $app_id         = $this->getApplication()->getId();
            $reportitem           = $migareference->getReportItem($app_id,$report_id);            
            $bitly_crede          = $migareference->getBitlycredentails($app_id);
            $pre_settings         = $migareference->preReportsettigns($app_id);            
            $gdpr_settings        = $migareference->get_gdpr_settings($app_id);
            $default              = new Core_Model_Default();
            $base_url             = $default->getBaseUrl();
            $consent_link         = $reportitem[0]['consent_bitly'];            
            if ($reportitem[0]['consent_bitly']=='') {
              $consent_link       = $base_url . "/migareference/consent?appid=".$app_id.'&rep='.$reportitem[0]['migareference_report_id'];
              $consent_link       = $utilities->shortLink($consent_link);
              $datas['app_id']									= $app_id;
              $datas['migareference_report_id'] = $reportitem[0]['migareference_report_id'];
              $datas['consent_bitly']					  = $consent_link;
              // $migareference->updatepropertyreport($datas);
            }            
            $tags                 = [
                                      '@@report_owner@@',
                                      '@@referrer_name@@',                                      
                                      '@@consent_link@@'
                                    ];
            $strings              = [
                                      $reportitem[0]['owner_name']." ".$reportitem[0]['owner_surname'],
                                      $reportitem[0]['invoice_name']." ".$reportitem[0]['invoice_surname'],                                      
                                      " ".$consent_link." "
                                    ];
            $consent_invit_msg_body=str_replace($tags, $strings, $gdpr_settings[0]['consent_invit_msg_body']);
              $payload = [
                  "data"           =>$consent_invit_msg_body,
                  "prospectmobile" =>$reportitem[0]['owner_mobile'],
                  "reportitem" =>$reportitem,
                  "consent_invit_msg_body" =>$gdpr_settings[0]['consent_invit_msg_body'],
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
  public function getprznotificationAction() {
      if ($param1= $this->getRequest()->getParam('param1')) {
          try {
            $migareference  = new Migareference_Model_Migareference();
            $app_id         = $this->getApplication()->getId();
            $datas          = $migareference->getprznotification($app_id,$param1);
            $default        = new Core_Model_Default();
            $base_url       = $default->getBaseUrl();
            $datas[0]['operation_type'] = (!empty($datas)) ? 1 : 0 ;
            $datas[0]['ref_prz_custom_file_url']=$base_url."/images/application/".$app_id."/features/migareference/".$datas[0]['ref_prz_custom_file'];
            $datas[0]['agt_prz_custom_file_url']=$base_url."/images/application/".$app_id."/features/migareference/".$datas[0]['agt_prz_custom_file'];
              $payload = [
                  "data" => $datas[0]
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
  // GDPR
  public function gdprsaveAction()
  {
    if ($datas = $this->getRequest()->getPost()) {
        try {
            $app_id = $datas['app_id'];
            if (!$app_id) {
                throw new Exception('An error occurred while saving. Please try again later.');
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            } else {
                $migareference = new Migareference_Model_Migareference();
                $migareference->gdprsave($datas);
            }
            $html = [
              'success'         => true,
              'message'         => __('Successfully GDPR applied.'),
              'message_timeout' => 0,
              'message_button'  => 0,
              'message_loader'  => 0
            ];
        } catch (Exception $e) {
            $html = [
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_button' => 1,
              'message_loader' => 1
            ];
        }
        $this->_sendJson($html);
    }
  }
  public function saveadminapiAction()
  {
    if ($data = $this->getRequest()->getPost()) {
        try {
            $app_id = $data['app_id'];
            if (!$app_id) {
                throw new Exception('An error occurred while saving. Please try again later.');
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            } else {
                $reportapi = new Migareference_Model_Reportapi();
                $reportapi->updateApiAdmin($data['admin_id']);
            }
            $html = [
              'success'         => true,
              'message'         => __('Successfully data saved.'),
              'message_timeout' => 0,
              'message_button'  => 0,
              'message_loader'  => 0
            ];
        } catch (Exception $e) {
            $html = [
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_button' => 1,
              'message_loader' => 1
            ];
        }
        $this->_sendJson($html);
    }
  }
  public function savecreditsadminapiAction()
  {
    if ($data = $this->getRequest()->getPost()) {
        try {
            $app_id = $data['app_id'];
            if (!$app_id) {
                throw new Exception('An error occurred while saving. Please try again later.');
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            } else {
                $reportapi = new Migareference_Model_Reportapi();
                $reportapi->updateCreditsApiAdmin($data['credits_api_admin']);
            }
            $html = [
              'success'         => true,
              'message'         => __('Successfully data saved.'),
              'message_timeout' => 0,
              'message_button'  => 0,
              'message_loader'  => 0
            ];
        } catch (Exception $e) {
            $html = [
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_button' => 1,
              'message_loader' => 1
            ];
        }
        $this->_sendJson($html);
    }
  }
  public function savemanualconsentAction()
  {
    if ($datas = $this->getRequest()->getPost()) {
        try {
            $app_id = $datas['app_id'];
            if (!$app_id) {
                throw new Exception('An error occurred while saving. Please try again later.');
            }
            $errors = "";
            if (empty($datas['consentdatestamp'])) {
                $errors .= __('You must select date stamp.') . "<br/>";
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            } else {
                $migareference = new Migareference_Model_Migareference();                
                // Save Prospect Consent
                $prospect['app_id']					= $app_id;          
                $prospect['gdpr_consent_ip']		= '';
                $prospect['gdpr_consent_timestamp'] = date('Y-m-d H:i:s',strtotime($datas['consentdatestamp']));
                $prospect['gdpr_consent_source']    = 'Manual Consent';
                
                $report_item = $migareference->get_report_by_key($datas['report_id']);
                $migareference->update_prospect($prospect,$report_item[0]['prospect_id'],0,0);//Also save log if their is change in Rating,Job,Notes                    
                // End Consent
            }
            $html = [
              'success'         => true,
              'message'         => __('Successfully GDPR applied.'),
              'message_timeout' => 0,
              'message_button'  => 0,
              'message_loader'  => 0
            ];
        } catch (Exception $e) {
            $html = [
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_button' => 1,
              'message_loader' => 1
            ];
        }
        $this->_sendJson($html);
    }
  }
}
