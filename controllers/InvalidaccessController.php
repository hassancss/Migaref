<?php
class Migareference_InvalidaccessController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default       = new Core_Model_Default();
        $layout        = new Siberian_Layout();
        $migareference = new Migareference_Model_Migareference();				
		$appid         = $this->_getParam('appid', false);    
		$layout->setBaseRender('base', 'migareference/application/invalidaccess.phtml', 'core_view_default');		
        $app_content  				= $migareference->get_app_content($appid);

		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
            ->setPagetitle(__($app_content[0]['reportconfirm_page_title']))
            ->setSubmessage(__($app_content[0]['reportconfirm_page_message']))
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
