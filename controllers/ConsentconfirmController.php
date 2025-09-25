<?php
class Migareference_ConsentconfirmController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
	$default       = new Core_Model_Default();
    $layout        = new Siberian_Layout();
    $migareference = new Migareference_Model_Migareference();
	$appid         = 0;
	$userid        = 0;
    $appid         = $this->_getParam('appid', false);
    $reportid      = $this->_getParam('rep', false);
	$clientIP      = $this->getclientipAction();
	$layout->setBaseRender('base', 'migareference/application/consentconfirm.phtml', 'core_view_default');		
	// Save Prospect Consent
	$prospect['app_id']					= $app_id;          
	$prospect['gdpr_consent_ip']		= $clientIP;
	$prospect['gdpr_consent_timestamp'] = date('Y-m-d H:i:s');
	$prospect['gdpr_consent_source']    = 'External Page';
	
	$report_item = $migareference->get_report_by_key($reportid);
	$migareference->update_prospect($prospect,$report_item[0]['prospect_id'],0,0);//Also save log if their is change in Rating,Job,Notes                    
	// End Consent
    $pre_report_settings  = $migareference->preReportsettigns($appid);
    $application          = $migareference->application($appid);
	$gdpr_settings        = $migareference->get_gdpr_settings($appid);
    $tags                 = ['@@app_name@@'];
    $strings              = [$application[0]['name']];

    $gdpr_settings[0]['consent_thank_page_body'] = str_replace($tags, $strings, $gdpr_settings[0]['consent_thank_page_body']);

		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
			->setAppid($_GET['appid'])
			->setPagetitle(__($gdpr_settings[0]['consent_thank_page_title']))
			->setPageheader(__($gdpr_settings[0]['consent_thank_page_header']))
			->setSubmessage(__($gdpr_settings[0]['consent_thank_page_body']))
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
