<?php
class Migareference_Public_LandingpageController extends Migareference_Controller_Default {
  public function getsettingsAction(){
    if (isset($_GET['app_id'])) {
      $migarefrence = new Migareference_Model_Migareference();
      $app_id=$_GET['app_id'];
      $user_id=$_GET['user_id'];
      $pre_report_settings    = $migarefrence->preReportsettigns($app_id);
      $app_theme              = $migarefrence->get_app_content($app_id);
      $html = array(
          "success" => "ok",
          "other"=>$app_id,
          "pre_settings"=>$pre_report_settings[0],
          "app_theme"=>$app_theme[0]
      );
    }else {
      $html = array(
          "success" => "no",
      );
    }
    $this->_sendHtml($html);
  }
  public function savepropertyAction(){
    if (isset($_GET['app_id'])) {
      $app_id=$_GET['app_id'];
      $errors="";
      $data=$_POST;
      $migareference       = new Migareference_Model_Migareference();
      $pre_report_settings = $migareference->preReportsettigns($app_id);
      $errors       = "";
      $data['report_source']=2;//For Landing page Report
      if (empty($data['property_type']) && $data['enable_property_type']==1) {
         $errors .= __('You must select Report Type.') . "<br/>";
      }
      if ($data['confirm_report_privacy']==2) {
         $errors .= __('You must Accept Privacy statement.') . "<br/>";
      }
      if ($data['authorized_call_back']==2) {
         $errors .= __('You must authorized to allow for commercial purpose.') . "<br/>";
      }
      if (empty($data['address']) || empty($data['longitude'])) {
        if ($data['enable_property_address']==1) {
          $errors .= __('Please add a valid address.') . "<br/>";
        }
      }
      if (empty($data['owner_name']) || !preg_match('/^\w{3,}$/', $data['owner_name'])) {
         $errors .= __('Please add a valid Owner Name.') . "<br/>";
      }
      if (empty($data['owner_surname']) || !preg_match('/^\w{3,}$/', $data['owner_surname'])) {
         $errors .= __('Please add a valid Owner Surname.') . "<br/>";
      }
      if (empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])) {
         $errors .= __('Please add a Valid Phone Number.') . "<br/>";
      }else {
        if ($pre_report_settings[0]['is_unique_mobile']==1) {
          $days=$pre_report_settings[0]['grace_days'];
          $date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
          $mobile_duplication=$migareference->isMobileunique($date,$data['owner_mobile']);
          if (count($mobile_duplication)) {
            $errors .= __('This Mobile Number already exists.') . "<br/>";
          }
        }
      }
      if (empty($errors)) {
        unset($data['enable_property_type']);
        unset($data['enable_property_sales_expectaion']);
        unset($data['enable_property_address']);
        $repo_data           = $migareference->get_last_report_no();
        $invoice_settings    = $migareference->getpropertysettings($app_id,$data['user_id']);
        if (!count($invoice_settings)) {
          $error=__("You must setup setting first");
                throw new Exception($error);
        }
        // If owner not set Settings default will be
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
          $data['last_modification_by']=$data['user_id'];
          $data['last_modification_at']=date('Y-m-d H:i:s');
          if (!count($repo_data)) {
            $data['report_no']=1000;
          }else {
            $data['report_no']=$repo_data[0]['report_no']+1;
          }
          $report_id = $migareference->savepropertyreport($data);
          //*************Send Notification***************
          if ($report_id>0) {
            // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
            $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_id,$data['currunt_report_status'],$data['last_modification_by'],'LANDINGPAGE-END','create');                                              
          }        
      }
      $html = array(
          "success" => "ok",
          "other"=>$_POST,
          "validerror"=>$errors       
      );
    }else {
      $html = array(
          "success" => "no",
      );
    }
    $this->_sendJson($html);
  }
}
