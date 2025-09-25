<?php
class Migareference_GlobalpolicyController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default       = new Core_Model_Default();
        $layout        = new Siberian_Layout();
        $migareference = new Migareference_Model_Migareference();		
		$userid        = 0;
        $appid         = $this->_getParam('app_id', 0);    
		$layout->setBaseRender('base', 'migareference/application/globalpolicy.phtml', 'core_view_default');
        $pre_report_settings  = $migareference->preReportsettigns($appid);    
		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
            ->setPagetitle(__("Privacy Policy"))                    
            ->setGlobalpolicy(__($pre_report_settings[0]['privacy_global_settings']))                    
			->setPlatform($default->getBaseUrl());
		echo $layout->render();
        die;
	}
}
