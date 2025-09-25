<?php
class Migareference_Prize_NotificationController extends Application_Controller_Default{
    public function loadprizenotificationviewAction()
    {        
        try {
            // Pass the value_id to the layout
            $value_id = $this->getRequest()->getParam('option_value_id');            
            $resp = $this->getLayout()
            ->setBaseRender('emailform', 'migareference/application/prize/notification.phtml', 'admin_view_default')
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
    public function getnotificationsAction(){
        
        if ($request = $this->getRequest()->getQuery()) {
            try {
                $notification = new Migareference_Model_Prize_Notification();                        
                $notification_collection = [];
                $app_id=$request['app_id'];
                //Default notification list
                $notification_options=[1=>'Redeemed', 2=>'Rejected', 3=>'Accepted'];
                $sent_to_options=[1=>'Both Referrer & Admin/Agent', 2=>'Only Referrer', 3=>'Only Admin/Agent'];
                $notification_channel_options=[1=>'Both Email & PUSH', 2=>'Only Email', 3=>'Only PUSH'];
                foreach ($notification_options as $key => $value) {
                    $notifications_list=$notification->find(['app_id'=>$app_id,'type'=>$key]);                                  
                    $sent_to=$notification->getPrzNotificationToUser();                    
                    $channel=$notification->getRefPrzNotificationType();
                    $id=$notification->getMigarefrencePrizesNotificationId();
                    $edit_action = '<button class="btn btn-info" onclick="editNotification('.$id.')">'.__('Edit').'</button>';
                    $pause_action = '<button class="btn btn-danger" onclick="pauseNotification('.$id.')">'.__('Disable').'</button>';
                    // Sent to
                    $notification_collection[]=[
                        $key,
                        $value,
                        $sent_to_options[$sent_to],
                        $notification_channel_options[$channel],
                        $edit_action
                    ];
                }
                $payload = [
                    "data" => $notification_collection
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
    public function editnotificationAction(){
        
        if ($request = $this->getRequest()->getQuery()) {
            try {
                $notification   = new Migareference_Model_Prize_Notification();                                        
                $base_url       = (new Core_Model_Default())->getBaseUrl();                
                $app_id         = $this->getApplication()->getId();
                $notifications_list=$notification->findAll(['migarefrence_prizes_notification_id'=>$request['id']])->toArray();                                                                    
                $notifications_list[0]['ref_prz_custom_file_url']=$base_url."/images/application/".$app_id."/features/migareference/".$notifications_list[0]['ref_prz_custom_file'];
                $notifications_list[0]['agt_prz_custom_file_url']=$base_url."/images/application/".$app_id."/features/migareference/".$notifications_list[0]['agt_prz_custom_file'];
                $payload = ["data" => $notifications_list[0]];
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
    public function saveprizenotificationAction(){
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $app_id = $datas['app_id'];
                if (!$app_id) {
                    throw new Exception('An error occurred while saving. Please try again later.');
                }
                $errors = "";
                // Refreal validation            
                if ($datas['prz_notification_to_user']==1 || $datas['prz_notification_to_user']==2) {
                  if ($datas['ref_prz_notification_type']==1 || $datas['ref_prz_notification_type']==2) {
                      if (empty($datas['ref_prz_email_title'])) {
                          $errors .= __('Referral Email title cannot be empty.') . "<br/>";
                      }
                      if (empty($datas['ref_prz_email_text'])) {
                          $errors .= __('Referral Email message cannot be empty.') . "<br/>";
                      }
                  }
                  if ($datas['ref_prz_notification_type']==1 || $datas['ref_prz_notification_type']==3) {
                    if (empty($datas['ref_prz_push_title'])) {
                        $errors .= __('Referral PUSH title cannot be empty.') . "<br/>";
                    }
                    if (empty($datas['ref_prz_push_title'])) {
                        $errors .= __('Referral PUSH message cannot be empty.') . "<br/>";
                    }
                    if ($datas['ref_prz_open_feature']==1 && $datas['ref_prz_feature_id']==0 && empty($datas['ref_prz_custom_url'])) {
                        $errors .= __('Referral CUSTOM ULR cannot be empty.') . "<br/>";
                    }
                  }
                }
                // Agent validation
                if ($datas['prz_notification_to_user']==1 || $datas['prz_notification_to_user']==3) {
                  if ($datas['agt_prz_notification_type']==1 || $datas['agt_prz_notification_type']==2) {
                    if (empty($datas['agt_prz_email_title'])) {
                        $errors .= __('Agent Email title cannot be empty.') . "<br/>";
                    }
                    if (empty($datas['agt_prz_email_text'])) {
                        $errors .= __('Agent Email message cannot be empty.') . "<br/>";
                    }
                  }
                  if ($datas['agt_prz_notification_type']==1 || $datas['agt_prz_notification_type']==3) {
                    if (empty($datas['agt_prz_push_title'])) {
                        $errors .= __('Agent PUSH title cannot be empty.') . "<br/>";
                    }
                    if (empty($datas['agt_prz_push_title'])) {
                        $errors .= __('Agent PUSH message cannot be empty.') . "<br/>";
                    }
                    if ($datas['agt_prz_open_feature']==1 && $datas['agt_prz_feature_id']==0 && empty($datas['agt_prz_custom_url'])) {
                        $errors .= __('Agent CUSTOM ULR cannot be empty.') . "<br/>";
                    }
                  }
                }
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $migareference = new Migareference_Model_Migareference();
                    $operation=$datas['operation'];
                    unset($datas['operation']);
                    if ($operation=='create') {
                      $migareference->savePrizenotificationvePush($datas);
                    }else {
                      $migareference->updatePrizenotificationvePush($datas);
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
}
?>