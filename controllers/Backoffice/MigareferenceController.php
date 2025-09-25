<?php
/**
 * Class Migareference_Backoffice_MigareferenceController
 */
class Migareference_Backoffice_MigareferenceController extends Backoffice_Controller_Default {
    public function loadAction() {
        $this->_sendJson([
            'title' => __('Migareference'),
            'icon' => 'fa fa-home'
        ]);
    }
    public function helpAction() {
        $help_url        = "";
        $info_message    = "";
        $success_message = "";
        $setting         = new Migareference_Model_Setting();
                           $setting->find(1);
        $stored_help_url = $setting->getHelpUrl();
        if(!empty($stored_help_url)) {
          $help_url        = $stored_help_url;
          $success_message = "You are using your custom help URL.";
        } else {
          $info_message = "You are using default help URL"."(https://www.migastone.com/migareference)";
        }
        $this->_sendHtml([
            "help_url" => $help_url,
            "info_message" => __($info_message),
            "success_message" => __($success_message),
        ]);
    }
    public function savehelpAction() {
        if($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                $setting         = new Migareference_Model_Setting();
                           $setting->find(1);
                if($setting->getSettingId()) {
							        $data["setting_id"] = $setting->getSettingId();
						       }
                $setting->setData($data)->save();                
                $data = [
                    "success" => 1,
                    "message" => __('Help URL updated successfully.')
                ];
            } catch(Exception $e) {
                $data = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    "message" => $data
                ];
            }
            $this->_sendHtml($data);
        }
    }
    public function loadtablestatsAction(){
      try {
        $from_date  = $this->getRequest()->getParam('from_date');
        $to_date    = $this->getRequest()->getParam('to_date');
        $from_date  = substr($from_date, -0,24);
        $from_date  = date_create($from_date);
        $data['from_date']=date_format($from_date,"Y-m-d H:i:s");
        $to_date    = substr($to_date, -0,24);
        $to_date    = date_create($to_date);
        $data['to_date'] = date_format($to_date,"Y-m-d H:i:s");
        $migareference   = new Migareference_Model_Migareference();
        $migarefrence_apps = $migareference->migarefrenceApps();
        $data_array = array();
        $count      = 1;
        $errorlog_data = $migareference->loadtablestats($data);
        foreach ($errorlog_data as  $value) {
            $data['app_id']=$value['app_id'];
            $userdata = $migareference->loadtablestatsUsers($data);
            $date=date( 'Y-m-d', strtotime($value['created_at']));
            $data_array[]= array(
                'id'               => $count++,
                'app_id'           => $value['app_id'],
                'app_name'         => $value['name'],
                'total_tokens'     => $userdata[0]['total_tokens'],
                'total_users'      => $userdata[0]['total_users'],
                'net_refreal'      => $value['net_refreal'],
                'active_reports'   => $value['active_reports'],
                'declined_reports' => $value['declined_reports'],
                'payable_reports'  => $value['payable_reports'],
                'paid_reports'     => $value['paid_reports'],
                'dates'            => $data
            );
        }
      } catch (\Exception $e) {
        $data = [
            "error" => 1,
            "message" => $e->getMessage()
        ];
      }
      $this->_sendHtml($data_array);
    }
    public function urlshortenerAction($long_url = "",$login="",$genericAccessToken=""){
      if($long_url)
      {
        $apiv4 = 'https://api-ssl.bitly.com/v4/bitlinks';
        $data = array(
            'long_url' => $long_url
        );
        $payload = json_encode($data);
        $header = array(
            'Authorization: Bearer ' . $genericAccessToken,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        );
        $ch = curl_init($apiv4);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         $bitlyresult = curl_exec($ch);
         return $bitlyresult=json_decode($bitlyresult);
      }
    }
    public function saveurlshortnercredentialsAction(){
      $data = Siberian_Json::decode($this->getRequest()->getRawBody());
      if($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
      try {
          $errors = "";
            if (empty($data['bitly_login'])) {
                 $errors .= __('User name is required.') . "<br/>";
            }
            if (empty($data['bitly_key'])) {
                  $errors .= __('Password is required.') . "<br/>";
            }
            if (!empty($errors)) {
                  throw new Exception($errors);
            }else {
                    $migareference   = new Migareference_Model_Migareference();
                    $conection       = $this->urlshortenerAction("https://www.google.com",$data['bitly_login'],$data['bitly_key']);
                    if ($conection->link!="") {
                      $last_id         = $migareference->saveShortnercredentials($data);
                    }else {
                      $last_id=100;
                    }
                  }
                  switch ($last_id) {
                      case 0:
                          $payload = array(
                                  "success" => 1,
                                  "message" => __("Credentails updated successfully.")
                              );
                          break;
                      case 1:
                          throw new Exception(__("An error occurred while saving. Please try again later."));
                          break;
                      case 2:
                          $payload = array(
                                  "success" => 1,
                                  "message" => __("Data successfully saved.")
                              );
                      break;
                      case 100:
                          throw new Exception(__("We are unable to connect API. Please try again latter."));
                        break;
                      default:
                          throw new Exception(__("An error occurred while saving. Please try again later."));
                          break;
                  }
          } catch (Exception $e) {
              $payload = array(
                  "error" => 1,
                  "message" => $e->getMessage()
              );
          }
      }else{
          $errors  ="";
          $errors .= __(print_r($data)) . "<br/>";
          $payload = array(
                      "error" => 1,
                      "message" => $errors
                      );
      }
      $this->_sendHtml($payload);
    }
    public function loadgraphAction(){
      $from_date=$this->getRequest()->getParam('from_date');
      $to_date=$this->getRequest()->getParam('to_date');
      $from_date = substr($from_date, -0,24);
      $from_date=date_create($from_date);
      $data['from_date']=date_format($from_date,"Y-m-d H:i:s");
      $to_date = substr($to_date, -0,24);
      $to_date=date_create($to_date);
      $data['to_date']=date_format($to_date,"Y-m-d H:i:s");
      // $data['from_date']=date('Y-m-d H:i:s', strtotime('-30 days', strtotime(date('Y-m-d H:i:s'))));
      // $data['to_date']  =$date=date('Y-m-d H:i:s');
      $migareference   = new Migareference_Model_Migareference();
      $errorlog_data   = $migareference->loadgraphstats($data);
      $data_array      = array();
      $count=0;
      $interval = DateInterval::createFromDateString('1 day');
      $start = new DateTime($data['from_date']);
      $end = new DateTime($data['to_date']);
      $period   = new DatePeriod( $start, $interval,$end);
      foreach ($period as $dt) {
        $data_array['stat_data_label'][]=[
          $dt->format("Y-m-d")
        ];
      }
      foreach ($errorlog_data as $key => $value) {
        $data_array['net_refreal'][]=[
            $value['net_refreal']
        ];
        $data_array['total_reports'][]=[
            $value['total_reports']
        ];
        $data_array['active_reports'][]=[
            $value['active_reports']
        ];
        $data_array['declined_reports'][]=[
            $value['declined_reports']
        ];
        $data_array['paid_reports'][]=[
            $value['paid_reports']
        ];
        $data_array['payable_reports'][]=[
            $value['payable_reports']
        ];
      }
      $data_array['stats_labels'][0]="Total Refrrel";
      $data_array['stats_labels'][1]="Total Reports ";
      $data_array['stats_labels'][2]="Active Reports";
      $data_array['stats_labels'][3]="Declined Reports";
      $data_array['stats_labels'][4]="Paid Reports";
      $data_array['stats_labels'][5]="Payable Reports";
      $data_array['data']=$errorlog_data;
      $this->_sendHtml($data_array);
	}
  public function svaesiberianusertaxidAction() {
        if($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if(empty($data["tax_id"])) {
                    throw new Exception("Tax ID can not be empty.");
				          }
						      $setting = new Migareference_Model_Setting();
						      $setting->find(1);
						      if($setting->getSettingId()) {
							        $data["setting_id"] = $setting->getSettingId();
						       }
						      $setting->setData($data)->save();
						      $payload = [
							             "success" => 1,
							              "message" => __('Tax ID successfully saved.')
						                ];
            } catch(Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($payload);
        }
	}
  public function loadsiberianusertaxidAction() {
		    $taxID = "";
        $info_message = "";
        $success_message = "";
        $setting = new Migareference_Model_Setting();
        $setting->find(1);
        if($setting->getSettingId() && $setting->getTaxId()) {
			       $taxID = $setting->getTaxId();
			       $default = new Core_Model_Default();
       		   $url = $default->getBaseUrl()."/migareference/public_cron/run";
			       $info_message = __("Note: You can set or run the cron manually on your own server using this URL %s.", $url);
        } else {
           $info_message = "You need to add valid token and client id in order to enable notarization of your reports.";
        }
        $this->_sendHtml([
			      "tax_id" => $taxID,
            "info_message" => __($info_message),
            "success_message" => __($success_message),
        ]);
	}
	//added by imran start
  public function loadmigachaincredentialsAction() {
		$migachain_token = "";
		$migachain_client_id = "";
        $info_message = "";
        $success_message = "";
        $setting = new Migareference_Model_Setting();
        $setting->find(1);
        if($setting->getSettingId() && $setting->getMigachainToken() && $setting->getMigachainClientId()) {
			$migachain_token = $setting->getMigachainToken();
			$migachain_client_id = $setting->getMigachainClientId();
			$default = new Core_Model_Default();
       		$url = $default->getBaseUrl()."/migareference/public_cron/run";
			$info_message = __("Note: You can set or run the cron manually on your own server using this URL %s.", $url);
        } else {
           $info_message = "You need to add valid token and client id in order to enable notarization of your reports.";
        }
        $this->_sendHtml([
			"migachain_token" => $migachain_token,
			"migachain_client_id" => $migachain_client_id,
            "info_message" => __($info_message),
            "success_message" => __($success_message),
        ]);
	}
	public function savemigachaincredentialsAction() {
        if($data = Siberian_Json::decode($this->getRequest()->getRawBody())) {
            try {
                if(empty($data["migachain_token"])) {
                    throw new Exception("Migachain token cannot be empty.");
				}
				if(empty($data["migachain_client_id"])) {
                    throw new Exception("Migachain client id cannot be empty.");
				}
				$api = new Migareference_Model_Api([
					'token' => $data["migachain_token"],
					'client_id' => $data["migachain_client_id"],
				]);
				$api_response = $api->validate();
				if ($api_response['success']) {
					if (in_array($api_response['response']->message, ['Valid with In-Active token.', 'Token Invalid.'])) {
						$payload = [
							'error' => 1,
							'message' => $api_response['response']->message,
						];
					} else {
						$setting = new Migareference_Model_Setting();
						$setting->find(1);
						if($setting->getSettingId()) {
							$data["setting_id"] = $setting->getSettingId();
						}
						$setting->setData($data)->save();
						$payload = [
							"success" => 1,
							"message" => __('API credentials are validated and stored successfully.'),
							'sds' => $api_response,
						];
					}
				} else {
					$payload = [
						'error' => 1,
						'message' => $api_response['message'],
					];
				}
            } catch(Exception $e) {
                $payload = [
                    "error" => 1,
                    "message" => $e->getMessage()
                ];
            }
            $this->_sendHtml($payload);
        }
	}
	public function ledgercronlogsAction() {
		$offset = $this->getRequest()->getParam('offset', null);
        $limit = Application_Model_Application::BO_DISPLAYED_PER_PAGE;
        $request = $this->getRequest();
        if ($range = $request->getHeader('Range')) {
            $parts = explode('-', $range);
            $offset = $parts[0];
            $limit = ($parts[1] - $parts[0]) + 1;
		}
		$params = [
            'offset' => $offset,
            'limit' => $limit
		];
		$filters = [];
        if ($_filter = $this->getRequest()->getParam("filter", false)) {
            $filters["(ledger_cron_id LIKE ? OR xml_file_name LIKE ? OR response LIKE ? OR message LIKE ? OR eth_address LIKE ? OR ipfs_address LIKE ? OR eth_sha_hash LIKE ? OR eth_address_url LIKE ? OR started_at LIKE ? OR ended_at LIKE ?)"] = "%{$_filter}%";
		}
		$order = $this->getRequest()->getParam("order", false);
        $by = filter_var($this->getRequest()->getParam("by", false), FILTER_VALIDATE_BOOLEAN);
        if ($order) {
            $order_by = ($by) ? "ASC" : "DESC";
            $order = sprintf("%s %s", $order, $order_by);
		}
		$cron_object = new Migareference_Model_Cron();
		$total = $cron_object->countAll($filters);
        if ($range = $request->getHeader('Range')) {
            $start = $parts[0];
            $end = ($total <= $parts[1]) ? $total : $parts[1];
            $this->getResponse()->setHeader("Content-Range", sprintf("%s-%s/%s", $start, $end, $total));
            $this->getResponse()->setHeader("Range-Unit", "items");
        }
		$logs = $cron_object->findAll($filters, $order, $params);
		$data = [
            'display_per_page' => $limit,
            'collection' => []
		];
		foreach ($logs as $log) {
            $data['collection'][] = $log->getData();
		}
		$this->_sendJson($data['collection']);
	}
	public function downloadxmlAction() {
		if ($ledger_cron_id = $this->getRequest()->getParam('ledger_cron_id')) {
			$cron_object = new Migareference_Model_Cron();
			$cron_object->find($ledger_cron_id);
			if ($cron_object->getId() && $cron_object->getXmlFileName()) {
				$file_name = $cron_object->getXmlFileName();
				header('Content-Type: text/xml');
				header('Content-Length: 202');
				header('Content-Disposition: attachment;filename="'.$file_name.'"');
				$fp=fopen(Core_Model_Directory::getBasePathTo("/images/backoffice/{$file_name}"),'r');
				fpassthru($fp);
				fclose($fp);
			}
			exit;
		}
	}
	public function ledgerlogsAction() {
		$offset = $this->getRequest()->getParam('offset', null);
        $limit = Application_Model_Application::BO_DISPLAYED_PER_PAGE;
        $request = $this->getRequest();
        if ($range = $request->getHeader('Range')) {
            $parts = explode('-', $range);
            $offset = $parts[0];
            $limit = ($parts[1] - $parts[0]) + 1;
		}
		$params = [
            'offset' => $offset,
            'limit' => $limit
		];
		$filters = [];
        if ($_filter = $this->getRequest()->getParam("filter", false)) {
            $filters["(ledger_id LIKE ? OR app_id LIKE ? OR app_name LIKE ? OR ledger_cron_id LIKE ? OR report_id LIKE ? OR report_no LIKE ? OR referral_name LIKE ? OR referral_surname LIKE ? OR owner_name LIKE ? OR owner_surname LIKE ? OR report_created_at LIKE ? OR created_at LIKE ?)"] = "%{$_filter}%";
		}
		$order = $this->getRequest()->getParam("order", false);
        $by = filter_var($this->getRequest()->getParam("by", false), FILTER_VALIDATE_BOOLEAN);
        if ($order) {
            $order_by = ($by) ? "ASC" : "DESC";
            $order = sprintf("%s %s", $order, $order_by);
		}
		$ledger_object = new Migareference_Model_Ledger();
		$total = $ledger_object->countAll($filters);
        if ($range = $request->getHeader('Range')) {
            $start = $parts[0];
            $end = ($total <= $parts[1]) ? $total : $parts[1];
            $this->getResponse()->setHeader("Content-Range", sprintf("%s-%s/%s", $start, $end, $total));
            $this->getResponse()->setHeader("Range-Unit", "items");
        }
		$logs = $ledger_object->findAll($filters, $order, $params);
		$data = [
            'display_per_page' => $limit,
            'collection' => []
		];
		foreach ($logs as $log) {
            $data['collection'][] = $log->getData();
		}
		$this->_sendJson($data['collection']);
	}
	//added by imran end
}
