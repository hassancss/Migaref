<?php
class Migareference_ConsentController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default       = new Core_Model_Default();
    $layout        = new Siberian_Layout();
    $migareference = new Migareference_Model_Migareference();
		$appid         = 0;
		$userid        = 0;

    $appid         = $this->_getParam('appid', false);
    $reportid      = $this->_getParam('rep', false);
		$layout->setBaseRender('base', 'migareference/application/consentcollection.phtml', 'core_view_default');

    $pre_report_settings  = $migareference->preReportsettigns($appid);
    $application          = $migareference->application($appid);
    $report_data          = $migareference->getReport($appid,$reportid);
    $gdpr_settings        = $migareference->get_gdpr_settings($appid);
    $base_url             = $default->getBaseUrl();
		$global_privacy_link=$base_url."/migareference/globalpolicy?app_id=".$appid;
		$public_privacy='<a target="_blank" href=';		
		$public_privacy.=($pre_report_settings[0]['enable_privacy_global_settings']==1) ? $global_privacy_link : $pre_report_settings[0]['confirm_report_privacy_link'] ;
		$public_privacy.=' >'.__('Here').'</a>';    
    $tags                 = [
                              '@@agent_name@@',
                              '@@app_name@@',
                              '@@report_owner@@',
                              '@@report_owner_phone@@',
                              '@@app_privacy_link@@'
                            ];
    $strings              = [
                              $report_data[0]['sponsor_firstname']." ".$report_data[0]['sponsor_lastname'],
                              $application[0]['name'],
                              $report_data[0]['owner_name']." ".$report_data[0]['owner_surname'],
                              $report_data[0]['owner_mobile'],
                              $public_privacy
                            ];

    $gdpr_settings[0]['consent_col_page_body'] = str_replace($tags, $strings, $gdpr_settings[0]['consent_col_page_body']);

		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
      ->setAppid($_GET['appid'])
      ->setPagetitle(__($gdpr_settings[0]['consent_col_page_title']))
      ->setPageheader(__($gdpr_settings[0]['consent_col_page_header']).$public)
      ->setSubmessage(__($gdpr_settings[0]['consent_col_page_body']))
      ->setConfirmurl($default->getBaseUrl()."/migareference/consentconfirm?appid=".$appid."&rep=".$reportid)
			->setPlatform($default->getBaseUrl());
		echo $layout->render();
        die;
	}
}
