<?php
class Migareference_ProinterfaceController extends Migareference_Controller_Default {
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
		$layout->setBaseRender('base', 'migareference/prointerface/reports.phtml', 'core_view_default');
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
}
