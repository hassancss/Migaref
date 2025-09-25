<?php

class Migareference_OptinsettingController extends Application_Controller_Default
{
	public function savesettingAction() {
		if ($data = $this->getRequest()->getPost()) {
            
			try {
				$optin_setting = (new Migareference_Model_Optinsetting())->find([
					'app_id' => $data['app_id']
				]);

				if ($optin_setting->getId()) {
					$optin_setting->setData([
						'migareference_optin_setting_id' => $optin_setting->getId(),
						'optin_setting' => serialize($data['customize_option_form']),
					])->save();
				} else {
					$optin_setting_save = (new Migareference_Model_Optinsetting())->setData([
						'app_id' => $data['app_id'], 
						'optin_setting' => serialize($data['customize_option_form']),
					])->save();
				}

                $datas = [
                    'success' => 1,
                    'message' => __('Successfully saved data.'),
                ];
            } catch (Exception $e) {
                $datas = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                ];
            }

			$this->_sendJson($datas);
        }
	}
	public function loadoptinusersAction() {
		if ($data = $this->getRequest()->getQuery()) {
			try {
			  $optinForm   = new Migareference_Model_Optinform();
			  $optin_users = $optinForm->getOptinUsers($data['app_id']);                        
			  $user_collection = [];
			  foreach ($optin_users as $key => $value) {
				//disable buttons for users who have reports
				$disable_delete_buttons  = ($value['report_no']!=null) ? 'disabled' : '' ;            
				$disable_ip_buttons  = ($value['report_no']!=null || $value['referrer_ip']==null) ? 'disabled' : '' ;            
				$delete = '<button '.$disable_delete_buttons.' class="btn btn-danger" onclick="deleteUser('.$value['invoice_user_id'].','.$value['migareference_invoice_settings_id'].',0,\''.$value['referrer_ip'].'\')">'.__('Delete').'</button>';
				$delete_block = '<button '.$disable_ip_buttons.' class="btn btn-danger" onclick="deleteUser('.$value['invoice_user_id'].','.$value['migareference_invoice_settings_id'].',1,\''.$value['referrer_ip'].'\')">'.__('Delete & Block IP').'</button>';
				//add a checkbox to allow for bulk delete
				$delete = '<input type="checkbox" name="delete_user[]" value="'.$value['invoice_user_id'].'">'.$delete;
				$user_collection[]=[
								$value['invoice_user_id']." ".$delete,
								$value['invoice_name'].' '.$value['invoice_lastname'],
								$value['email'],
								$value['referrer_ip'],
								$value['optin_form_v'],
								date('d-m-Y H:i:s',strtotime($value['user_created_at'])),								
								$delete.' '.$delete_block
							  ];
				}
				$payload = [
					"data" => $user_collection
				];
			} catch (\Exception $e) {
				$payload = [
					'error' => true,
					'message' => __($e->getMessage())
				];
			}
		} else {
			$payload = [
				'error' => true,
				'message' => __('An error occurred during process. Please try again later.')
			];
		}
		$this->_sendJson($payload);
	}
	public function loadblockedipsAction() {
		if ($data = $this->getRequest()->getQuery()) {
			try {			                         
			  $ip_list = (new Migareference_Model_Optin_Firewall())->findAll(['app_id'=> $data['app_id']])->toArray();
			  $ip_collection = [];
			  foreach ($ip_list as $key => $value) {            
				$delete = '<button class="btn btn-danger" onclick="unblockIp('.$value['migareference_optin_firewall_id'].')">'.__('Delete').'</button>';				
				$ip_collection[]=[
								$value['migareference_optin_firewall_id'],								
								$value['ip_address'],								
								date('d-m-Y H:i:s',strtotime($value['created_at'])),								
								$delete
							  ];
				}
				$payload = [
					"data" => $ip_collection
				];
			} catch (\Exception $e) {
				$payload = [
					'error' => true,
					'message' => __($e->getMessage())
				];
			}
		} else {
			$payload = [
				'error' => true,
				'message' => __('An error occurred during process. Please try again later.')
			];
		}
		$this->_sendJson($payload);
	}
	public function deleteuserAction() {
		if ($user_id = $this->getRequest()->getParam('user_id')) {
			try {
					$is_block_ip = $this->getRequest()->getParam('is_block_ip');				  
					$invoice_id = $this->getRequest()->getParam('invoice_id');				  
					$app_id = $this->getRequest()->getParam('app_id');				  
					$ip_address = $this->getRequest()->getParam('ip_address');				  
					// Delete Customer					
					(new Customer_Model_Customer())->find(['customer_id'=> $user_id])->delete();                               
					// Delete Invoice Settings
					(new Migareference_Model_Referrer())->find(['migareference_invoice_settings_id'=> $invoice_id])->delete();                               
					// Delete Phonebook
					(new Migareference_Model_Phonebook())->find(['invoice_id'=> $invoice_id])->delete();                               
					// Block IP If is_block_ip==1
					if ($is_block_ip && $ip_address!='') {
						(new Migareference_Model_Optin_Firewall())->setData([
							'app_id' => $app_id,
							'ip_address' => $ip_address,
						])->save();
					}
					$payload = [
						'success' => true,
						'message' => __('Successfully Removed.'),
						'message_loader' => 0,
						'message_button' => 0,
						'message_timeout' => 2
					];
			} catch (\Exception $e) {
				$payload = [
					'error' => true,
					'message' => __($e->getMessage())
				];
			}
		} else {
			$payload = [
				'error' => true,
				'message' => __('An error occurred during process. Please try again later.')
			];
		}
		$this->_sendJson($payload);
	}
	public function unblockipAction() {
		if ($uid = $this->getRequest()->getParam('uid')) {
			try {					
					// Delete IP
					(new Migareference_Model_Optin_Firewall())->find(['migareference_optin_firewall_id'=> $uid])->delete();                               					
					$payload = [
						'success' => true,
						'message' => __('Successfully Removed.'),
						'message_loader' => 0,
						'message_button' => 0,
						'message_timeout' => 2
					];
			} catch (\Exception $e) {
				$payload = [
					'error' => true,
					'message' => __($e->getMessage())
				];
			}
		} else {
			$payload = [
				'error' => true,
				'message' => __('An error occurred during process. Please try again later.')
			];
		}
		$this->_sendJson($payload);
	}
}