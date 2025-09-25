<?php
class Migareference_ReportconfirmController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default       = new Core_Model_Default();
    	$layout        = new Siberian_Layout();
    	$migareference = new Migareference_Model_Migareference();
		$appid         = 0;
		$userid        = 0;
		$appid         = $this->_getParam('appid', false);
    	$reportid      = $this->_getParam('rep', false);
		$layout->setBaseRender('base', 'migareference/application/consentconfirm.phtml', 'core_view_default');
		$clientIP                       = $this->getclientipAction();
		$data['app_id']									= $appid;
		$data['migareference_report_id']= $reportid;
		$data['consent_ip']				= $clientIP;
		$data['consent_timestmp']       = date('Y-m-d H:i:s');
		$data['consent_source']			= 'Landing Page';
		$data['ignore_webhook']			= 1;
		$application          = $migareference->updatepropertyreport($data);
    	$app_content  		  = $migareference->get_app_content($appid);
		$gdpr_settings        = $migareference->get_gdpr_settings($appid);
		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
			->setAppid($appid)
      		->setPagetitle(__($gdpr_settings[0]['reportconfirm_page_title']))
      		->setSubmessage(__($gdpr_settings[0]['reportconfirm_page_message']))
			->setPlatform($default->getBaseUrl());
		echo $layout->render();
        die;
	}
	// Function to get the client IP address
	public function getclientipAction() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
}
