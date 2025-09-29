<?php
/**
 * Class Migareference_Model_Migareference
 */
class Migareference_Model_Migareference extends Core_Model_Default
{
    /**
     * Migareference_Model_Migareference constructor.
     * @param array $param
     * @throws Zend_Exception
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Migareference_Model_Db_Table_Migareference';
        return $this;
    }
    public function getAappadminagentdata($appadminid = 0)
    {
        return $this->getTable()->getAappadminagentdata($appadminid);
    }
    public function sectorByTitle($title = '')
    {
        return $this->getTable()->sectorByTitle($title);
    }
    public function jobByTitle($title = '')
    {
        return $this->getTable()->jobByTitle($title);
    }
    public function templateStatus($staus_list = [],$type=0)
    {
        return $this->getTable()->templateStatus($staus_list,$type);
    }
    public function getInvoiceItem($invoice_id = 0)
    {
        return $this->getTable()->getInvoiceItem($invoice_id);
    }
    public function defaultGdprTemplates($id = 0)
    {
        return $this->getTable()->defaultGdprTemplates($id);
    }
    public function migarefrenceApps()
    {
        return $this->getTable()->migarefrenceApps();
    }
    public function referrer_to_prospect($invoice_id = 0)
    {
        return $this->getTable()->referrer_to_prospect($invoice_id);
    }
    public function useraccountSettings($app_id = 0)
    {
        return $this->getTable()->useraccountSettings($app_id);
    }
    public function compatibleCountries($app_id = 0)
    {
        return $this->getTable()->compatibleCountries($app_id);
    }
    public function reportCsv($app_id = 0)
    {
        return $this->getTable()->reportCsv($app_id);
    }
    public function default_update_app_content($app_id = 0)
    {
        return $this->getTable()->default_update_app_content($app_id);
    }
    public function reportWebhookStrings($app_id = 0,$report_id=0)
    {
        return $this->getTable()->reportWebhookStrings($app_id,$report_id);
    }
    public function agentAppProvinces($app_id = 0)
    {
        return $this->getTable()->agentAppProvinces($app_id);
    }
    public function referrerAppProvinces($app_id = 0)
    {
        return $this->getTable()->referrerAppProvinces($app_id);
    }
    public function syncDob($app_id = 0)
    {
        return $this->getTable()->syncDob($app_id);
    }
    public function compatibleNewNoteFiled($app_id = 0)
    {
        return $this->getTable()->compatibleNewNoteFiled($app_id);
    }
    public function landingReportconsent($app_id = 0)
    {
        return $this->getTable()->landingReportconsent($app_id);
    }
    public function application($app_id = 0)
    {
        return $this->getTable()->application($app_id);
    }
    public function phonebookReportStats($app_id = 0,$invoice_id=0)
    {
        return $this->getTable()->phonebookReportStats($app_id,$invoice_id);
    }
    public function getProspectItem($app_id = 0,$prospect_id=0)
    {
        return $this->getTable()->getProspectItem($app_id,$prospect_id);
    }
    public function getGeoProvince($province_id = 0,$app_id=0)
    {
        return $this->getTable()->getGeoProvince($province_id,$app_id);
    }
    public function deleteReferrerAgent($app_id,$agent_id = 0)
    {
        return $this->getTable()->deleteReferrerAgent($app_id,$agent_id);
    }
    public function loadeProvinceReferrers($app_id,$province_id=0,$agent_id = 0)
    {
        return $this->getTable()->loadeProvinceReferrers($app_id,$province_id,$agent_id);
    }
    public function getagentProvinces($app_id=0,$country_id=0,$province_id_list=[])
    {
        return $this->getTable()->getagentProvinces($app_id,$country_id,$province_id_list);
    }
    public function deleteAgnetProvince($app_id=0,$customer_id=0,$country_id=0)
    {
        return $this->getTable()->deleteAgnetProvince($app_id,$customer_id,$country_id);
    }
    public function getAllocatedProvinces($app_id=0,$country_id=0,$province_id=0)
    {
        return $this->getTable()->getAllocatedProvinces($app_id,$country_id,$province_id);
    }
    public function updateuseraccountSettings($value_id=0,$data=[])
    {
        return $this->getTable()->updateuseraccountSettings($value_id,$data);
    }
    public function agentProvinces($app_id=0,$customer_id=0)
    {
        return $this->getTable()->agentProvinces($app_id,$customer_id);
    }
    public function deleteexternaladdress($key = 0)
    {
        return $this->getTable()->deleteexternaladdress($key);
    }
    public function exportStatus($app_id = 0)
    {
        return $this->getTable()->exportStatus($app_id);
    }
    public function get_app_content_two($app_id = 0)
    {
        return $this->getTable()->get_app_content_two($app_id);
    }
    public function archiveReminder($id = 0)
    {
        return $this->getTable()->archiveReminder($id);
    }
    public function reminderResetLog($data = [])
    {
        return $this->getTable()->reminderResetLog($data);
    }
    public function geoProvinceCountries($app_id = 0)
    {
        return $this->getTable()->geoProvinceCountries($app_id);
    }
    public function getResetLogs($app_id = 0)
    {
        return $this->getTable()->getResetLogs($app_id);
    }
    public function get_all_agents($app_id = 0)
    {
        return $this->getTable()->get_all_agents($app_id);
    }
    public function getGeoCountries($app_id = 0)
    {
        return $this->getTable()->getGeoCountries($app_id);
    }
    public function deleteSponsor($referrer_id = 0)
    {
        return $this->getTable()->deleteSponsor($referrer_id);
    }
    public function getGeoCountryProvicnes($app_id = 0,$country_id=0)
    {
        return $this->getTable()->getGeoCountryProvicnes($app_id,$country_id);
    }
    public function getGeoCountry($country_id = 0,$app_id=0)
    {
        return $this->getTable()->getGeoCountry($country_id,$app_id);
    }
    public function getReferrerAgents($app_id = 0,$referrer_id=0)
    {
        return $this->getTable()->getReferrerAgents($app_id,$referrer_id);
    }
    public function agentGeoProvince($app_id = 0,$province_id=0)
    {
        return $this->getTable()->agentGeoProvince($app_id,$province_id);
    }
    public function agentMultiGeoProvince($app_id = 0,$province_id=0)
    {
        return $this->getTable()->agentMultiGeoProvince($app_id,$province_id);
    }
    public function updateAgent($data=[],$app_id=0,$customer_id=0)
    {
        return $this->getTable()->updateAgent($data,$app_id,$customer_id);
    }
    public function getSponsorList($app_id=0,$referrer_id=0)
    {
        return $this->getTable()->getSponsorList($app_id,$referrer_id);
    }
    public function updateSponsor($data=[],$customer_id=0)
    {
        return $this->getTable()->updateSponsor($data,$customer_id);
    }
    public function default_jobs($app_id = 0)
    {
        return $this->getTable()->default_jobs($app_id);
    }
    public function default_professions($app_id = 0)
    {
        return $this->getTable()->default_professions($app_id);
    }
    public function phonebookcompatibility($app_id = 0)
    {
        return $this->getTable()->phonebookcompatibility($app_id);
    }    
    public function getAllPhoneBook($app_id = 0)
    {
        return $this->getTable()->getAllPhoneBook($app_id);
    }
    public function get_custom_status_reports($app_id = 0)
    {
        return $this->getTable()->get_custom_status_reports($app_id);
    }
    public function getBlackListPhoneNumers($app_id = 0)
    {
        return $this->getTable()->getBlackListPhoneNumers($app_id);
    }
    public function informatToFormatePhoneNumbers($app_id = 0)
    {
        return $this->getTable()->informatToFormatePhoneNumbers($app_id);
    }
    public function defaultGeoCountrieProvinces($app_id = 0)
    {
        return $this->getTable()->defaultGeoCountrieProvinces($app_id);
    }
    public function getGeoCountrieProvinces($app_id = 0,$country_id=0)
    {
        return $this->getTable()->getGeoCountrieProvinces($app_id,$country_id);
    }
    public function sendTestSms($messagebody="",$to="",$app_id=0)
    {
        return $this->getTable()->sendTestSms($messagebody,$to,$app_id);
    }
    public function deleteexternalphonenumber($key = 0)
    {
        return $this->getTable()->deleteexternalphonenumber($key);
    }
    public function getprznotification($app_id=0, $type=0)
    {
        return $this->getTable()->getprznotification($app_id,$type);
    }
    public function getReferrerWebhookLog($app_id=0)
    {
        return $this->getTable()->getReferrerWebhookLog($app_id);
    }
    public function getReportWebhookLog($app_id=0)
    {
        return $this->getTable()->getReportWebhookLog($app_id);
    }
    public function getReminderWebhookLog($app_id=0)
    {
        return $this->getTable()->getReminderWebhookLog($app_id);
    }
    public function deleteGeo($country_id=0, $province_id=0)
    {
        return $this->getTable()->deleteGeo($country_id,$province_id);
    }
    public function triggerWebhook($url='', $log_params=[])
    {
        return $this->getTable()->triggerWebhook($url,$log_params);
    }
    public function isPhoneEmailExist($app_id=0, $email='',$mobile='',$type=0)
    {
        return $this->getTable()->isPhoneEmailExist($app_id,$email,$mobile,$type);
    }
    public function getallprznotification($app_id=0)
    {
        return $this->getTable()->getallprznotification($app_id);
    }
    public function getCreditsApiNotification($app_id=0)
    {
        return $this->getTable()->getCreditsApiNotification($app_id);
    }
    public function prz_default_notifications($app_id=0,$value_id=0)
    {
        return $this->getTable()->prz_default_notifications($app_id,$value_id);
    }
    public function importstatus($app_id=0,$value_id=0)
    {
        return $this->getTable()->importstatus($app_id,$value_id);
    }
    public function getSingleuser($app_id=0, $user_id=0)
    {
        return $this->getTable()->getSingleuser($app_id,$user_id);
    }
    public function getSingleuserByEmail($app_id=0, $email='')
    {
        return $this->getTable()->getSingleuserByEmail($app_id,$email);
    }
    public function agmingAgentGroup($app_id=0, $user_id=0)
    {
        return $this->getTable()->agmingAgentGroup($app_id,$user_id);
    }
    public function get_referral_agent($agent_key = 0)
    {
        return $this->getTable()->get_referral_agent($agent_key);
    }
    // Utilities
    public function lastReport($app_id = 0)
    {
        return $this->getTable()->lastReport($app_id);
    }
    public function lastReferrer($app_id = 0)
    {
        return $this->getTable()->lastReferrer($app_id);
    }
    public function getAllProspect($app_id = 0)
    {
        return $this->getTable()->getAllProspect($app_id);
    }
    public function getmanageredeemprize($app_id = 0)
    {
        return $this->getTable()->getmanageredeemprize($app_id);
    }
    public function reportpage($app_id = 0)
    {
        return $this->getTable()->reportpage($app_id);
    }
    public function getreportfield($app_id = 0)
    {
        return $this->getTable()->getreportfield($app_id);
    }
    public function updateredeemstatus($id = 0,$status=0)
    {
        return $this->getTable()->updateredeemstatus($id,$status);
    }
    public function getaddresses($app_id = 0)
    {
        return $this->getTable()->getaddresses($app_id);
    }
    public function deleteaddresses($app_id = 0)
    {
        return $this->getTable()->deleteaddresses($app_id);
    }
    public function getProfessions($app_id = 0)
    {
        return $this->getTable()->getProfessions($app_id);
    }
    public function getJobs($app_id = 0)
    {
        return $this->getTable()->getJobs($app_id);
    }
    public function insertaddresses($data = [])
    {
        return $this->getTable()->insertaddresses($data);
    }
    public function saveAgnetProvince($data = [])
    {
        return $this->getTable()->saveAgnetProvince($data);
    }
    public function saveSharelogs($data = [])
    {
        return $this->getTable()->saveSharelogs($data);
    }
    public function insertphonenumber($data = [])
    {
        return $this->getTable()->insertphonenumber($data);
    }
    public function insertjob($data = [])
    {
        return $this->getTable()->insertjob($data);
    }
    public function insertProfession($data = [])
    {
        return $this->getTable()->insertProfession($data);
    }
    public function prizestatus($data = [])
    {
        return $this->getTable()->prizestatus($data);
    }
    public function savePrizenotificationvePush($data = [])
    {
        return $this->getTable()->savePrizenotificationvePush($data);
    }
    public function saveCreditsApiNotification($data = [])
    {
        return $this->getTable()->saveCreditsApiNotification($data);
    }
    public function updateCreditsApiNotification($data = [])
    {
        return $this->getTable()->updateCreditsApiNotification($data);
    }
    public function addCountry($data = [])
    {
        return $this->getTable()->addCountry($data);
    }
    public function addProvince($data = [])
    {
        return $this->getTable()->addProvince($data);
    }
    public function updateCountry($data = [])
    {
        return $this->getTable()->updateCountry($data);
    }
    public function updateProvince($data = [])
    {
        return $this->getTable()->updateProvince($data);
    }
    public function updateGdprSetings($data = [])
    {
        return $this->getTable()->updateGdprSetings($data);
    }
    public function savePhoneBook($data = [])
    {
        return $this->getTable()->savePhoneBook($data);
    }
    public function updatePrizenotificationvePush($data = [])
    {
        return $this->getTable()->updatePrizenotificationvePush($data);
    }
    public function createUser($data = [])
    {
        return $this->getTable()->createUser($data);
    }
    public function updateCustomerdob($user_id=0,$data = [])
    {
        return $this->getTable()->updateCustomerdob($user_id,$data);
    }
    public function savekey($data = [])
    {
        return $this->getTable()->savekey($data);
    }
    public function addAppcontenttwo($data = [])
    {
        return $this->getTable()->addAppcontenttwo($data);
    }
    public function updateAppcontenttwo($data = [])
    {
        return $this->getTable()->updateAppcontenttwo($data);
    }
    public function loadpropertyaddresses($app_id = 0)
    {
        return $this->getTable()->loadpropertyaddresses($app_id);
    }
    public function getProspectJobs($app_id = 0,$type=0)
    {
        return $this->getTable()->getProspectJobs($app_id,$type);
    }
    public function getAgentReferrerPhonebook($app_id = 0,$user_id=0)
    {
        return $this->getTable()->getAgentReferrerPhonebook($app_id,$user_id);
    }
    public function getAgentProspectPhonebook($app_id = 0,$user_id=0)
    {
        return $this->getTable()->getAgentProspectPhonebook($app_id,$user_id);
    }    
    public function deltereferrer($app_id = 0,$data=[])
    {
        return $this->getTable()->deltereferrer($app_id,$data);
    }
    public function getReferrerJobs($app_id = 0,$type=0)
    {
        return $this->getTable()->getReferrerJobs($app_id,$type);
    }
    public function loadpropertyaphonenumbers($app_id = 0)
    {
        return $this->getTable()->loadpropertyaphonenumbers($app_id);
    }
    public function getDeclinedFixRating($app_id = 0)
    {
        return $this->getTable()->getDeclinedFixRating($app_id);
    }
    public function welcomeEmailTags()
    {
        return $this->getTable()->welcomeEmailTags();
    }
    public function getphonenumbers($app_id = 0)
    {
        return $this->getTable()->getphonenumbers($app_id);
    }
    public function get_siberianuser($app_id = 0)
    {
        return $this->getTable()->get_siberianuser($app_id);
    }
    public function getCustomer($app_id = 0,$email="")
    {
        return $this->getTable()->getCustomer($app_id,$email);
    }
    public function getCustomerMobile($app_id = 0,$mobile="")
    {
        return $this->getTable()->getCustomerMobile($app_id,$mobile);
    }
    public function getmobilereports($app_id = 0,$mobile="")
    {
        return $this->getTable()->getmobilereports($app_id,$mobile);
    }
    public function getaddressreports($app_id = 0,$mobile="")
    {
        return $this->getTable()->getaddressreports($app_id,$mobile);
    }
    public function getPageTitle($app_id = 0, $value_id = 0)
    {
        return $this->getTable()->getPageTitle($app_id, $value_id);
    }
    public function get_sponsor_agent($app_id = 0, $agent_key = 0)
    {
        return $this->getTable()->get_sponsor_agent($app_id, $agent_key);
    }
    public function count_agent_reports($app_id = 0, $agent_key = 0)
    {
        return $this->getTable()->count_agent_reports($app_id, $agent_key);
    }
    public function updateSponsoragent($app_id=0, $user_id = 0, $sponsor_id = 0)
    {
        return $this->getTable()->updateSponsoragent($app_id, $user_id, $sponsor_id);
    }
    public function defaultPreSettings($app_id = 0, $value_id = 0,$type="")
    {
        return $this->getTable()->defaultPreSettings($app_id, $value_id,$type);
    }
    public function getMaxorder($app_id = 0)
    {
        return $this->getTable()->getMaxorder($app_id);
    }
    public function loadtablestats($data = [])
    {
        return $this->getTable()->loadtablestats($data);
    }
    public function loadtablestatsUsers($data = [])
    {
        return $this->getTable()->loadtablestatsUsers($data);
    }
    public function saveShortnercredentials($data = [])
    {
        return $this->getTable()->saveShortnercredentials($data);
    }
    public function save_app_content($app_id = 0)
    {
        return $this->getTable()->save_app_content($app_id);
    }
    public function temp_upate_app_content($app_id = 0)
    {
        return $this->getTable()->temp_upate_app_content($app_id);
    }
    public function get_leadger_customer($app_id=0,$referrer_id=0)
    {
        return $this->getTable()->get_leadger_customer($app_id,$referrer_id);
    }
    public function getBitlycredentails($app_id=0)
    {
        return $this->getTable()->getBitlycredentails($app_id);
    }
    public function getprize($app_id = 0)
    {
        return $this->getTable()->getprize($app_id);
    }
    public function getSocialsharesUser($user_id = 0)
    {
        return $this->getTable()->getSocialsharesUser($user_id);
    }
    public function getprizewithredeem($app_id = 0, $user_id=0)
    {
        return $this->getTable()->getprizewithredeem($app_id,$user_id);
    }
    public function getSponsorCustomer($app_id = 0, $user_id=0)//deprecated 09-20-2023 as now we have multiple agents New Method is getSponsorList
    {
        return $this->getTable()->getSponsorCustomer($app_id,$user_id);//deprecated 09-20-2023 as now we have multiple agents New Method is getSponsorList
    }
    public function getSingleRedeemPrize($app_id=0,$user_id=0,$prize_id=0)
    {
        return $this->getTable()->getSingleRedeemPrize($app_id,$user_id,$prize_id);
    }
    public function get_prize_entry_count($redeemed_id=0,$app_id=0,$prize_id=0,$user_id=0)
    {
        return $this->getTable()->get_prize_entry_count($redeemed_id,$app_id,$prize_id,$user_id);
    }
    public function getredeemprizelist($app_id = 0, $user_id=0)
    {
        return $this->getTable()->getredeemprizelist($app_id,$user_id);
    }
    public function get_credit_balance($app_id = 0, $user_id=0)
    {
        return $this->getTable()->get_credit_balance($app_id,$user_id);
    }
    public function get_leadger($app_id = 0, $user_id=0)
    {
        return $this->getTable()->get_leadger($app_id,$user_id);
    }
    public function isAddressunique($app_id = 0, $data=[])
    {
        return $this->getTable()->isAddressunique($app_id,$data);
    }
    public function isexternalAddressunique($app_id = 0, $data=[])
    {
        return $this->getTable()->isexternalAddressunique($app_id,$data);
    }
    public function getSinglePrize($id = 0)
    {
        return $this->getTable()->getSinglePrize($id);
    }
    public function get_prize_compatible($app_id = 0)
    {
        return $this->getTable()->get_prize_compatible($app_id);
    }
    public function saveprize($data=[])
    {
        return $this->getTable()->saveprize($data);
    }
    public function temp_upate_app_redeem_prizes($data=[])
    {
        return $this->getTable()->temp_upate_app_redeem_prizes($data);
    }
    public function temp_upate_blacklist_phonenumbers($id=0,$data=[])
    {
        return $this->getTable()->temp_upate_blacklist_phonenumbers($id,$data);
    }
    public function saveLedger($data=[])
    {
        return $this->getTable()->saveLedger($data);
    }
    public function saveRedeemed($data=[])
    {
        return $this->getTable()->saveRedeemed($data);
    }
    public function updateprize($data=[])
    {
        return $this->getTable()->updateprize($data);
    }
    public function updateAppcontent($data = [])
    {
        return $this->getTable()->updateAppcontent($data);
    }
    public function get_app_content($app_id = 0)
    {
        return $this->getTable()->get_app_content($app_id);
    }
    public function get_gdpr_settings($app_id = 0)
    {
        return $this->getTable()->get_gdpr_settings($app_id);
    }
    public function get_report_by_key($key=0)
    {
        return $this->getTable()->get_report_by_key($key);
    }
    public function getReferrerByKey($key=0)
    {
        return $this->getTable()->getReferrerByKey($key);
    }
    public function getTranslations()
    {
        return $this->getTable()->getTranslations();
    }
    public function readTranslationsFile()
    {
        return $this->getTable()->readTranslationsFile();
    }
    public function writeDbTranslations($data=[])
    {
        return $this->getTable()->writeDbTranslations($data);
    }
    public function updateStatusbyKey($data=[],$key=0)
    {
        return $this->getTable()->updateStatusbyKey($data,$key);
    }
    public function updateReportfieldbyKey($data=[],$key=0)
    {
        return $this->getTable()->updateReportfieldbyKey($data,$key);
    }
    public function update_phonebook($data=[],$key=0,$change_by=0,$user_type=0)
    {
        return $this->getTable()->update_phonebook($data,$key,$change_by,$user_type);
    }
    public function update_prospect($data=[],$key=0,$change_by=0,$user_type=0)
    {
        return $this->getTable()->update_prospect($data,$key,$change_by,$user_type);
    }
    public function updateReportProspect($data=[],$prospect_id=0)
    {
        return $this->getTable()->updateReportProspect($data,$prospect_id);
    }
    public function isProspectExist($app_id=0,$mobile='')
    {
        return $this->getTable()->isProspectExist($app_id,$mobile);
    }
    public function updateStatus($datas=[])
    {
        return $this->getTable()->updateStatus($datas);
    }
    public function getAgentdata( $user_id = 0)
    {
        return $this->getTable()->getAgentdata($user_id);
    }
    public function getReferrers( $app_id = 0)
    {
        return $this->getTable()->getReferrers($app_id);
    }
    public function getRefReports( $app_id=0,$user_id = 0)
    {
        return $this->getTable()->getRefReports($app_id,$user_id);
    }
    public function checkDeletedreferrer( $app_id = 0)
    {
        return $this->getTable()->checkDeletedreferrer($app_id);
    }
    public function get_earnings($app_id = 0, $user_id = 0)
    {
        return $this->getTable()->get_earnings($app_id, $user_id);
    }
    public function findReportField($key = 0)
    {
        return $this->getTable()->findReportField($key);
    }
    public function checkGcm($user_id = 0,$app_id = 0 )
    {
        return $this->getTable()->checkGcm($user_id,$app_id);
    }
    public function get_one_standard_status($app_id = 0,$standard_index=0 )
    {
        return $this->getTable()->get_one_standard_status($app_id,$standard_index);
    }
    public function reportStatusByKey($pkid=0 )
    {
        return $this->getTable()->reportStatusByKey($pkid);
    }
    public function checkApns($user_id = 0,$app_id = 0 )
    {
        return $this->getTable()->checkApns($user_id,$app_id);
    }
    public function saveLog($data=[])
    {
        return $this->getTable()->saveLog($data);
    }
    public function saveCronnotification($data=[])
    {
        return $this->getTable()->saveCronnotification($data);
    }
    public function savePreReport($data=[])
    {
        return $this->getTable()->savePreReport($data);
    }
    public function updatePreReport($data=[])
    {
        return $this->getTable()->updatePreReport($data);
    }
    public function saveGdprSetings($data=[])
    {
        return $this->getTable()->saveGdprSetings($data);
    }
    public function isMobileunique($app_id=0,$date="",$mobile="")
    {
        return $this->getTable()->isMobileunique($app_id,$date,$mobile);
    }
    public function isinternalAddressunique($app_id=0,$data="",$date="")
    {
        return $this->getTable()->isinternalAddressunique($app_id,$data,$date);
    }
    public function getLastvisit($app_id = 0, $user_id = 0)
    {
      return $this->getTable()->getLastvisit($app_id, $user_id);
    }
    public function getPushlog($app_id = 0)
    {
      return $this->getTable()->getPushlog($app_id);
    }
    public function getEmaillog($app_id = 0)
    {
      return $this->getTable()->getEmaillog($app_id);
    }
    public function getReportlog($app_id = 0, $report_id = 0)
    {
      return $this->getTable()->getReportlog($app_id, $report_id);
    }
    public function countNotifications($app_id = 0, $datetime = "",$user_id=0)
    {
      return $this->getTable()->countNotifications($app_id, $datetime,$user_id);
    }
    public function preReportsettigns($app_id = 0)
    {
      return $this->getTable()->preReportsettigns($app_id);
    }
    public function get_referral_users($app_id = 0)
    {
      return $this->getTable()->get_referral_users($app_id);
    }
    public function get_opt_referral_users($app_id=0,$join='')
    {
      return $this->getTable()->get_opt_referral_users($app_id,$join);
    }
    public function getReportStatus($app_id=0)
    {
      return $this->getTable()->getReportStatus($app_id);
    }
    public function get_all_customers($app_id=0)
    {
      return $this->getTable()->get_all_customers($app_id);
    }
    public function is_admin($app_id=0,$user_id=0)
    {
      return $this->getTable()->is_admin($app_id,$user_id);
    }
    public function getSponsor($app_id=0,$user_id=0)
    {
      return $this->getTable()->getSponsor($app_id,$user_id);
    }
    public function isBlackList($app_id=0,$mobile='')
    {
      return $this->getTable()->isBlackList($app_id,$mobile);
    }
    public function is_agent($app_id=0,$user_id=0)
    {
      return $this->getTable()->is_agent($app_id,$user_id);
    }
     public function findAgentByEmail($app_id=0,$email=0)
    {
      return $this->getTable()->findAgentByEmail($app_id,$email=0);
    }

    public function havereport($app_id=0,$user_id=0)
    {
      return $this->getTable()->havereport($app_id,$user_id);
    }
    public function statusreports($app_id=0,$status_id=0)
    {
      return $this->getTable()->statusreports($app_id,$status_id);
    }
    public function deleteAdmin($app_id=0,$user_id=0)
    {
      return $this->getTable()->deleteAdmin($app_id,$user_id);
    }
    public function deleteSocialshare($app_id=0,$user_id=0)
    {
      return $this->getTable()->deleteSocialshare($app_id,$user_id);
    }
    public function deleteAgent($app_id=0,$user_id=0)
    {
      return $this->getTable()->deleteAgent($app_id,$user_id);
    }
    public function deleteAgentProvinces($app_id=0,$user_id=0)
    {
      return $this->getTable()->deleteAgentProvinces($app_id,$user_id);
    }
    public function getAdmins($app_id=0)
    {
      return $this->getTable()->getAdmins($app_id);
    }
    public function getContactsUsers($app_id=0)
    {
      return $this->getTable()->getContactsUsers($app_id);
    }
    public function get_customer_agents($app_id=0)
    {
      return $this->getTable()->get_customer_agents($app_id);
    }
    public function get_partner_agents($app_id=0)
    {
      return $this->getTable()->get_partner_agents($app_id);
    }
    public function get_socialshares($app_id=0)
    {
      return $this->getTable()->get_socialshares($app_id);
    }
    public function get_migration_log($app_id=0)
    {
      return $this->getTable()->get_migration_log($app_id);
    }
    public function deleteMigrationlog($app_id=0)
    {
      return $this->getTable()->deleteMigrationlog($app_id);
    }
    public function deleteReport($id=0)
    {
      return $this->getTable()->deleteReport($id);
    }
    public function saveEarning($data=[])
    {
      return $this->getTable()->saveEarning($data);
    }
    public function saveMigrationlog($data=[])
    {
      return $this->getTable()->saveMigrationlog($data);
    }
    public function updateMigrationlog($data=[])
    {
      return $this->getTable()->updateMigrationlog($data);
    }
    public function saveStatus($data=[])
    {
      return $this->getTable()->saveStatus($data);
    }
    public function saveComment($data=[])
    {
      return $this->getTable()->saveComment($data);
    }
    public function updateEmail($data=[])
    {
      return $this->getTable()->updateEmail($data);
    }
    public function updateNotificationevent($data=[])
    {
      return $this->getTable()->updateNotificationevent($data);
    }
    public function updatePush($app_id=0,$data=[])
    {
      return $this->getTable()->updatePush($app_id,$data);
    }
    public function saveAdmin($data=[])
    {
      return $this->getTable()->saveAdmin($data);
    }
    public function saveAgent($data=[])
    {
      return $this->getTable()->saveAgent($data);
    }
    public function saveSocialshare($data=[])
    {
      return $this->getTable()->saveSocialshare($data);
    }
    // How to use //
    public function addhowto($data=[])
    {
        return $this->getTable()->addhowto($data);
    }
    public function gethowto($app_id=0)
    {
        return $this->getTable()->gethowto($app_id);
    }
    public function updatehowto($data=[])
    {
        return $this->getTable()->updatehowto($data);
    }
    // Propety Settings
    public function checkDuplication($data=[],$id=0)
    {
        return $this->getTable()->checkDuplication($data,$id);
    }
    public function validateProspectMobile($app_id=0,$grace_date='',$owner_mobile='',$referrer_id=0)
    {
        return $this->getTable()->validateProspectMobile($app_id,$grace_date,$owner_mobile,$referrer_id);
    }
    public function savePropertysettings($data=[])
    {
        return $this->getTable()->savePropertysettings($data);
    }
    public function gdprsave($data=[])
    {
        return $this->getTable()->gdprsave($data);
    }
    public function updatePropertysettings($data=[],$id=0)
    {
        return $this->getTable()->updatePropertysettings($data,$id);
    }
    public function getpropertysettings($app_id=0,$user_id=0)
    {
        return $this->getTable()->getpropertysettings($app_id,$user_id);
    }
    public function getCustomstatusreports($app_id=0)
    {
        return $this->getTable()->getCustomstatusreports($app_id);
    }
    public function getStatus($app_id=0,$status_id=0)
    {
        return $this->getTable()->getStatus($app_id,$status_id);
    }
    // Propety Report
    public function savepropertyreport($data=[])
    {
        return $this->getTable()->savepropertyreport($data);
    }
    public function updatepropertyreport($data=[])
    {
        return $this->getTable()->updatepropertyreport($data);
    }
    public function get_all_reports($data=[])
    {
        return $this->getTable()->get_all_reports($data);
    }
    public function getReportList($data=[])
    {
        return $this->getTable()->getReportList($data);
    }
    public function ref_get_all_reports($data=[])
    {
        return $this->getTable()->ref_get_all_reports($data);
    }
    public function getReport($app_id=0,$report_id=0)
    {
        return $this->getTable()->getReport($app_id,$report_id);
    }
    public function getReportItem($app_id=0,$report_id=0)
    {
        return $this->getTable()->getReportItem($app_id,$report_id);
    }
    public function deleteProfessionNA()
    {
        return $this->getTable()->deleteProfessionNA();
    }
    public function getReportSponsor($app_id=0,$report_id=0)
    {
        return $this->getTable()->getReportSponsor($app_id,$report_id);
    }
    public function getApiReport($app_id=0,$report_no=0)
    {
        return $this->getTable()->getApiReport($app_id,$report_no);
    }
    public function getEventNotificationTemplats($app_id=0,$event_id=0)
    {
        return $this->getTable()->getEventNotificationTemplats($app_id,$event_id);
    }
    public function getAdminCustomers($app_id=0)
    {
        return $this->getTable()->getAdminCustomers($app_id);
    }
    public function getRefferalCustomers($app_id=0,$refferal_user_id=0)
    {
        return $this->getTable()->getRefferalCustomers($app_id,$refferal_user_id);
    }
    public function defaultTriggers($app_id=0,$value_id=0,$type="")
    {
        return $this->getTable()->defaultTriggers($app_id,$value_id,$type);
    }
    public function get_last_report_no()
    {
        return $this->getTable()->get_last_report_no();
    }
    public function sendPush($data=[],$app_id=0,$user_id=0)
    {
        return $this->getTable()->sendPush($data,$app_id,$user_id);
    }
    public function sendMail($data=[],$app_id=0,$user_id=0)
    {
        return $this->getTable()->sendMail($data,$app_id,$user_id);
    }
    public function sendSms($data=[],$app_id=0,$user_id=0)
    {
        return $this->getTable()->sendSms($data,$app_id,$user_id);
    }
    public function savePush($app_id = 0, $data = [])
    {
        return $this->getTable()->savePush($app_id, $data);
    }
    public function get_standard($app_id=0)
    {
        return $this->getTable()->get_standard($app_id);
    }
    public function saveEmail( $data = [])
    {
        return $this->getTable()->saveEmail( $data);
    }
    public function saveNotificationevent( $data = [])
    {
        return $this->getTable()->saveNotificationevent( $data);
    }
    public function loadPush($app_id = 0)
    {
        return $this->getTable()->loadPush($app_id);
    }
    public function loadEmail($app_id = 0)
    {
        return $this->getTable()->loadEmail($app_id);
    }
    public function getPush($app_id = 0, $value_id = 0)
    {
        return $this->getTable()->getPush($app_id, $value_id);
    }
    public function getEmail($app_id = 0, $value_id = 0)
    {
        return $this->getTable()->getEmail($app_id, $value_id);
    }
    public function insert_notes($data=[])
    {
        return $this->getTable()->insert_notes($data);
    }
    public function sendPush2($data=[])
    {
        return $this->getTable()->sendPush2($data);
    }
    public function deletenote($public_key=0)
    {
        return $this->getTable()->deletenote($public_key);
    }
    public function uploadApplicationFile($app_id=0,$file='',$remove=0)
    {
        return $this->getTable()->uploadApplicationFile($app_id,$file,$remove);
    }
    public function editnote($public_key=0)
    {
        return $this->getTable()->editnote($public_key);
    }
    public function update_notes($id = 0, $data = [])
    {
        return $this->getTable()->update_notes($id, $data);
    }
    public function insert_reminder($data=[])
    {
        return $this->getTable()->insert_reminder($data);
    }
    public function get_reminder($app_id = 0, $report_id = 0)
    {
        return $this->getTable()->get_reminder($app_id, $report_id);
    }
    public function getSingleReportReminder($app_id = 0, $report_id = 0)
    {
        return $this->getTable()->getSingleReportReminder($app_id, $report_id);
    }
    public function getCommunicatioLog($app_id = 0, $phonebook_if = 0)
    {
        return $this->getTable()->getCommunicatioLog($app_id, $phonebook_if);
    }
    public function get_all_reminder($app_id = 0)
    {
        return $this->getTable()->get_all_reminder($app_id);
    }
    public function findInRepoRemLog($reminder_id = 0)
    {
        return $this->getTable()->findInRepoRemLog($reminder_id);
    }
    public function getReportReminders($app_id = 0)
    {
        return $this->getTable()->getReportReminders($app_id);
    }    
    public function markasDon($log_id = 0,$status=0)
    {
        return $this->getTable()->markasDon($log_id,$status);
    }    
    public function getDayBook($app_id = 0,$receiver_id=0,$status='')
    {
        return $this->getTable()->getDayBook($app_id,$receiver_id,$status);
    }    
    public function deleteCommunicationLog($log_if=0)
    {
        return $this->getTable()->deleteCommunicationLog($log_if);
    }
    public function getLatCommunication($phonebook_if=0)
    {
        return $this->getTable()->getLatCommunication($phonebook_if);
    }
    public function editreminder($public_key=0)
    {
        return $this->getTable()->editreminder($public_key);
    }
    public function daysDiffrence($date='',$seting=0)
    {
        return $this->getTable()->daysDiffrence($date,$seting);
    }
    public function autoReminderItem($public_key=0)
    {
        return $this->getTable()->autoReminderItem($public_key);
    }
    public function update_reminder($id = 0, $data = [])
    {
        return $this->getTable()->update_reminder($id, $data);
    }
    public function updateAutoReminder($id = 0, $data = [])
    {
        return $this->getTable()->updateAutoReminder($id, $data);
    }
    public function insert_reminder_type($data=[])
    {
        return $this->getTable()->insert_reminder_type($data);
    }
    public function saveRepoRemLog($data=[])
    {
        return $this->getTable()->saveRepoRemLog($data);
    }
    public function getReportReminder($app_id = 0)
    {
        return $this->getTable()->getReportReminder($app_id);
    }
    public function getReferrerReminders($app_id = 0)
    {
        return $this->getTable()->getReferrerReminders($app_id);
    }
    public function getReferrerRemindersNonCanceled($app_id = 0)
    {
        return $this->getTable()->getReferrerRemindersNonCanceled($app_id);
    }
    public function getReferrerRemindersCanceled($app_id = 0,$limit=0)
    {
        return $this->getTable()->getReferrerRemindersCanceled($app_id,$limit);
    }
    public function getFilteredReferrerReminders($app_id=0,$trigger_id=0,$assigned_to=0,$current_reminder_status='')
    {
        return $this->getTable()->getFilteredReferrerReminders($app_id,$trigger_id,$assigned_to,$current_reminder_status);
    }
    public function getSingleReminderType($id = 0)
    {
        return $this->getTable()->getSingleReminderType($id);
    }
    public function getStaticIons()
    {
        return $this->getTable()->getStaticIons();
    }
    public function getSinglePhonebook($id = 0)
    {
        return $this->getTable()->getSinglePhonebook($id);
    }
    public function prospectRefPhnDetail($id = 0)
    {
        return $this->getTable()->prospectRefPhnDetail($id);
    }
    public function getInvoicePhonebook($id = 0)
    {
        return $this->getTable()->getInvoicePhonebook($id);
    }
    public function update_reminder_type($id = 0, $data = [])
    {
        return $this->getTable()->update_reminder_type($id, $data);
    }
    public function automationTriggersEffect($data = [])
    {
        return $this->getTable()->automationTriggersEffect( $data);
    }
    public function deleteReminderType($public_key=0)
    {
        return $this->getTable()->deleteReminderType($public_key);
    }
    public function deletejob($public_key=0)
    {
        return $this->getTable()->deletejob($public_key);
    }
    public function deleteProfession($public_key=0)
    {
        return $this->getTable()->deleteProfession($public_key);
    }
    public function getsingejob($public_key=0)
    {
        return $this->getTable()->getsingejob($public_key);
    }
    public function getsingeprofession($public_key=0)
    {
        return $this->getTable()->getsingeprofession($public_key);
    }
    public function get_custom_reminder_settings($app_id=0)
    {
        return $this->getTable()->get_custom_reminder_settings($app_id);
    }
    public function saveReportReminder($data=[])
    {
        return $this->getTable()->saveReportReminder($data);
    }
    public function saveReportReminderAuto($data=[])
    {
        return $this->getTable()->saveReportReminderAuto($data);
    }
    public function addSponsor($data=[])
    {
        return $this->getTable()->addSponsor($data);
    }
    public function updateReportReminderAuto($id=0,$data=[])
    {
        return $this->getTable()->updateReportReminderAuto($id,$data);
    }
    public function getReportReminderAuto($app_id=0)
    {
        return $this->getTable()->getReportReminderAuto($app_id);
    }
    public function fixRemindeerdefault()
    {
        return $this->getTable()->fixRemindeerdefault();
    }
    public function getReportReminderType($app_id=0,$type=0)
    {
        return $this->getTable()->getReportReminderType($app_id,$type);
    }
    public function getSingleReminderAuto($id=0)
    {
        return $this->getTable()->getSingleReminderAuto($id);
    }
    public function deleteReportReminderAuto($id=0)
    {
        return $this->getTable()->deleteReportReminderAuto($id);
    }
    public function updateReportReminder($id=0,$data=[])
    {
        return $this->getTable()->updateReportReminder($id,$data);
    }
    public function updateProfession($id=0,$data=[])
    {
        return $this->getTable()->updateProfession($id,$data);
    }
    public function insert_stats()
    {
        return $this->getTable()->insert_stats();
    }
    public function loadgraphstats($data=[])
    {
        return $this->getTable()->loadgraphstats($data);
    }
    public function saveCommunicationLog($data=[])
    {
        return $this->getTable()->saveCommunicationLog($data);
    }
    public function getAutomationTriggers($app_id=0)
    {
        return $this->getTable()->getAutomationTriggers($app_id);
    }
    public function automationTriggers()
    {
        return $this->getTable()->automationTriggers();
    }
    public function automationTriggersCron()
    {
        return $this->getTable()->automationTriggersCron();
    }
    public function autoReminderResponse()
    {
        return $this->getTable()->autoReminderResponse();
    }
    // Deep Link
        /**
    * @param $valueId
    * @return array
    */
    public function getInappStates($valueId)
    {
        $inAppStates = [          
            [
                'state' => 'property-settingsv2',
                'offline' => false,
                'params' => [
                    'value_id' => $valueId,
                ],
            ],
        ];
        return $inAppStates;
    }
}
