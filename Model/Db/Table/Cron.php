<?php
use \Chirp\Cryptor;
class Migareference_Model_Db_Table_Cron extends Core_Model_Db_Table
{
    protected $_name = "migareference_ledger_cron";
	protected $_primary = "ledger_cron_id";
	/**
     * @param string $base_url
     * @return bool
     */
    private function _cronManager($base_url = "") {
		//Add the logic to run it after 2 days instead everyday as this method is being called everyday
		$cron_object = new Migareference_Model_Cron();
		$last_cron = $cron_object->findAll([], 'ledger_cron_id DESC', 1);
		if (count($last_cron)) {
			$last_cron = $last_cron[0];
			$last_cron_date = new DateTime($last_cron->getStartedAt());
			$current_date = new DateTime();
	
			// Calculate the difference in days
			$interval = $last_cron_date->diff($current_date);
			$days_difference = $interval->days;
	
			// Skip execution if the difference is less than the required days (e.g., 2 days)
			if ($days_difference < 2) {
				return 'Skipped: Cron job was executed recently.';
			}
		}
		//Solana (whole file is changed)
		$cron_object = new Migareference_Model_Cron();
		$cron_object->setData(['started_at' => date('Y-m-d H:i:s')])->save();
		$setting = new Migareference_Model_Setting();
		$setting->find(1);
		if ($setting->getSettingId() && $setting->getMigachainToken() && $setting->getMigachainClientId()) {
			$ledger_object = new Migareference_Model_Ledger();
			$ledgers = $ledger_object->findAll();
			$notarized_reports_ids = [];
			if (count($ledgers)) {
				foreach ($ledgers as $ledger) {
					$notarized_reports_ids[] = $ledger->getReportId();
				}
			}
			$where = '';
			if(count($notarized_reports_ids)) {
				$notarized_reports_ids_by_comma = implode(',' , $notarized_reports_ids);
				$where = " AND migareference_report.migareference_report_id NOT IN(".$notarized_reports_ids_by_comma.")";
			}
			$reports = $this->_db->fetchAll("SELECT
													migareference_report.migareference_report_id AS report_id,
													migareference_report.report_no,
													migareference_report.app_id,
													application.name AS app_name,
													migareference_invoice_settings.invoice_name AS referral_name,
													migareference_invoice_settings.invoice_surname AS referral_surname,
													migareference_report.owner_name,
													migareference_report.owner_surname,
													migareference_invoice_settings.blockchain_password AS encryption_key,
													migareference_report.created_at AS report_created_at
												FROM
													migareference_invoice_settings,
													migareference_report,
													customer,
													application
												WHERE
													migareference_invoice_settings.user_id = migareference_report.user_id AND
													migareference_invoice_settings.user_id = customer.customer_id AND
													migareference_report.app_id = application.app_id AND
													-- migareference_report.currunt_report_status = 1 AND
													migareference_report.is_notarized <> 1 AND migareference_invoice_settings.blockchain_password IS NOT NULL $where");

			if(count($reports)) {
				$new_reports_ids = [];
				foreach($reports as $key => $report) {
					$report['ledger_cron_id'] = $cron_object->getId();
					$report['created_at'] = date('Y-m-d H:i:s');
					$ledger_object = new Migareference_Model_Ledger();
					$ledger_object->setData($report)->save();
					$new_reports_ids[] = $report['report_id'];
				}
				
				$ledger_object = new Migareference_Model_Ledger();
				$ledgers = $ledger_object->findAll([], 'app_id ASC');
				
				if (count($ledgers)) {
					$xml = "<xml>\n";
					$xml .= "	<platform_url>".__get('main_domain')."</platform_url>\n";
					$xml .= "	<reports>\n";
					foreach ($ledgers as $ledger) {
						$cryptor_report_hash = new Cryptor($ledger->getAppId() ."_". $ledger->getAppName() ."_". $ledger->getReportNo());
						$report_hash = $cryptor_report_hash->encrypt($ledger->getAppId() ."_". $ledger->getAppName() ."_". $ledger->getReportNo() ."_". $ledger->getReferralName() ."_". $ledger->getReferralSurname() ."_". $ledger->getOwnerName() ."_". $ledger->getOwnerSurname() ."_". $ledger->getReportCreatedAt());
						$cryptor = new Cryptor($ledger->getEncryptionKey());
						$xml .= "		<report>\n";
						$xml .= "			<app_id>".$ledger->getAppId()."</app_id>\n";
						$xml .= "			<app_name>".$ledger->getAppName()."</app_name>\n";
						$xml .= "			<report_no>".$ledger->getReportNo()."</report_no>\n";
						$xml .= "			<referral_name>".$cryptor->encrypt($ledger->getReferralName())."</referral_name>\n";
						$xml .= "			<referral_surname>".$cryptor->encrypt($ledger->getReferralSurname())."</referral_surname>\n";
						$xml .= "			<prospect_name>".$cryptor->encrypt($ledger->getOwnerName())."</prospect_name>\n";
						$xml .= "			<prospect_surname>".$cryptor->encrypt($ledger->getOwnerSurname())."</prospect_surname>\n";
						$xml .= "			<report_created_at>".$ledger->getReportCreatedAt()."</report_created_at>\n";
						$xml .= "			<report_hash>".$report_hash."</report_hash>\n";
						$xml .= "		</report>\n";
					}
					$xml .= "	</reports>\n";
					$xml .= "</xml>";
					$xml_file_name = time() . "_" . $cron_object->getId() . "_" . count($ledgers) . "_" . count($reports) . "_reports.xml";
					$xml_file = fopen(Core_Model_Directory::getBasePathTo("/images/backoffice/{$xml_file_name}"), "w") or die("Unable to open file!");
					
					fwrite($xml_file, $xml);
					fclose($xml_file);

					if (file_exists(Core_Model_Directory::getBasePathTo("/images/backoffice/{$xml_file_name}"))) {
						$api = new Migareference_Model_Api([
							'token' => $setting->getMigachainToken(),
							'client_id' => $setting->getMigachainClientId(),
						]);
						$response = $api->notarized(Core_Model_Directory::getBasePathTo("/images/backoffice/{$xml_file_name}"));
						if (isset($response['success']) && $response['success'] && isset($response['response']) && $response['response']->response == 'success' && (isset($response['response']->solana_txt_hash) && $response['response']->solana_txt_hash) && (isset($response['response']->ipfs_address) && $response['response']->ipfs_address) && (isset($response['response']->solana_sha256) && $response['response']->solana_sha256)) {
							$response_code = $response['response']->response;
							$message = $response['response']->message;
							$solana_txt_hash = $response['response']->solana_txt_hash;
							$ipfs_address = $response['response']->ipfs_address;
							$solana_sha256 = $response['response']->solana_sha256;
							$eth_address_url = 'N/A';
							$cron_object->setData([
								'ledger_cron_id' => $cron_object->getId(),
								'xml_file_name' => $xml_file_name,
								'xml_payload' => $xml,
								'api_request' => serialize([
									'request[token]' => $setting->getMigachainToken(),
									'request[client_id]' => $setting->getMigachainClientId(),
									'request[uploadfile]' => Core_Model_Directory::getBasePathTo("/images/backoffice/{$xml_file_name}")
								]),
								'api_response' => serialize($response),
								'response' => $response_code,
								'message' => $message,
								'eth_address' => $solana_txt_hash,
								'ipfs_address' => $ipfs_address,
								'eth_sha_hash' => $solana_sha256,
								'eth_address_url' => $eth_address_url,
							])->save();
							foreach ($new_reports_ids as $report_id) {
								$this->_db->update('migareference_report', [
									'eth_address' => $solana_txt_hash,
									'ipfs_address' => $ipfs_address,
									'eth_sha_hash' => $solana_sha256,
									'eth_address_url' => $eth_address_url,
									'notarized_at' => date('Y-m-d H:i:s'),
									'is_notarized' => 1
								], [
									'migareference_report_id = ?' => $report_id
								]);
							}
						} else {
							$response_code = 'failure';
							$message = $response['message'];
							$cron_object->setData([
								'ledger_cron_id' => $cron_object->getId(),
								'xml_file_name' => $xml_file_name,
								'xml_payload' => $xml,
								'api_request' => serialize([
									'request[token]' => $setting->getMigachainToken(),
									'request[client_id]' => $setting->getMigachainClientId(),
									'request[uploadfile]' => Core_Model_Directory::getBasePathTo("/images/backoffice/{$xml_file_name}")
								]),
								'api_response' => serialize($response),
								'response' => $response_code,
								'message' => $message
							])->save();
							foreach ($new_reports_ids as $report_id) {
								$ledger_object_delete = new Migareference_Model_Ledger();
								$ledger_object_delete->find(['report_id' => $report_id]);
								$ledger_object_delete->delete();
							}
							$cron_object_email = new Migareference_Model_Cron();
							$failed_crons = $cron_object_email->findAll([
								'is_notified < ?' => 1,
								'response <> ?' => 'success'
							], 'started_at ASC');
							$current_time = strtotime('now');
							if (count($failed_crons)) {
								$backoffice_user_object = new Backoffice_Model_User();
								$admins = $backoffice_user_object->findAll();
								if (count($admins)) {
									$failed_cron_ids = [];
									foreach ($failed_crons as $failed_cron) {
										$failed_cron_ids[] = $failed_cron->getId();
									}
									foreach ($admins as $admin) {
										$email_message = __("Dear") . " " . ($admin->getFirstname() ? $admin->getFirstname() : __('Admin')) . ','.
										__('Migachain CRON has been failed %s times on the platform %s. Following are the CRON ids that you need to check: %s.', count($failed_crons), __get('main_domain'), implode(',', $failed_cron_ids)).
										__('MIGASTONE SUPPORT TEAM.');
										$mail = new Siberian_Mail();
										$mail->setBodyHtml(nl2br($email_message));
										$mail->addTo($admin->getEmail(), $admin->getFirstname());
										$mail->setSubject(__('Migachain CRON Failure Notification'));
										$mail->send();
									}
									foreach ($failed_crons as $failed_cron) {
										$cron_notified_object = new Migareference_Model_Cron();
										$cron_notified_object->setData([
											'ledger_cron_id' => $failed_cron->getId(),
											'is_notified' => 1
										])->save();
									}
								}
							}
						}
					}
				}
			} else {
				$cron_object->setData([
					'ledger_cron_id' => $cron_object->getId(),
					'response' => 'success',
					'message' => __('No new reports found.')
				])->save();
			}
		} else {
			$cron_object->setData([
				'ledger_cron_id' => $cron_object->getId(),
				'response' => 'failure',
				'message' => __('API token and client id not found.')
			])->save();
		}
		$cron_object->setData([
			'ledger_cron_id' => $cron_object->getId(),
			'ended_at' => date('Y-m-d H:i:s')
		])->save();

		//Cron for managing the existing processed referrals///////////////////////////////
		$communication_logs = $this->_db->fetchAll("SELECT DISTINCT(migareference_communication_logs.phonebook_id), migarefrence_phonebook.app_id, migareference_invoice_settings.user_id, migareference_communication_logs.created_at FROM migarefrence_phonebook JOIN migareference_invoice_settings ON migareference_invoice_settings.migareference_invoice_settings_id = migarefrence_phonebook.invoice_id JOIN migareference_communication_logs ON migarefrence_phonebook.migarefrence_phonebook_id = migareference_communication_logs.phonebook_id LEFT JOIN migareference_processed_referrer ON migareference_invoice_settings.user_id = migareference_processed_referrer.referrer_id WHERE migarefrence_phonebook.type = 1 AND (migareference_communication_logs.log_type = 'Rating' OR migareference_communication_logs.log_type = 'Note' OR migareference_communication_logs.log_type = 'Reciprocity Notes') AND migareference_processed_referrer.referrer_id IS NULL GROUP BY migareference_invoice_settings.user_id ORDER BY migareference_communication_logs.created_at ASC");

		if (count($communication_logs)) {
			foreach ($communication_logs as $communication_log) {
				if (!$communication_log['app_id']) continue;
				$save_processed_referrer = (new Migareference_Model_ProcessedReferrer())
											->setData([
												'app_id' => $communication_log['app_id'],
												'referrer_id' => $communication_log['user_id'],
												'processed_date' => $communication_log['created_at'],
											])
											->save();

			}
		}
		///////////////////////////////////////////////////////////////////////////////////
        return true;
    }
	/**
     * @return bool
     */
    public static function xmlCron() {
        $default = new Core_Model_Default();
		$model = new Migareference_Model_Db_Table_Cron();
		$migareference = new Migareference_Model_Db_Table_Migareference();
		$status = $model->_cronManager($default->getBaseUrl());
		$merge=$migareference->mergePhonebookWithInvoice(); 
        return $merge;
	}
}
