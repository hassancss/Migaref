<?php
class Migareference_LandingreportController extends Migareference_Controller_Default {
	public static $platform_url = '';
	public function indexAction() {
		$default = new Core_Model_Default();
		$errors = '';
		$success = '';
		$appid=0;
		$userid=0;
		$agentid=0;
		$reportby=0;
		$type=0;
		$layout = new Siberian_Layout();
		$migareference        = new Migareference_Model_Migareference();
		$layout->setBaseRender('base', 'migareference/application/landingreport.phtml', 'core_view_default');
		$layout
			->getBaseRender()
			->setErrors($errors)
			->setSuccess($success)
			->setPlatform($default->getBaseUrl());
			if ($data = $this->getRequest()->getPost()) {								
				$log_item=[
					'app_id'=>$data['app_id'],
					'user_id'=>$data['user_id'],
					'log_type'=>'visit'            
				];
				$migareference->saveSharelogs($log_item);        
				$app_content  = $migareference->get_app_content($data['app_id']);
				$gdpr_settings= $migareference->get_gdpr_settings($data['app_id']);
				$layout
					->getBaseRender()
					->setAppid($data['app_id'])
					->setErrors($errors)
					->setSuccess($success)
					->setPagetitle(__($gdpr_settings[0]['landing_page_title']))
					->setUserid($data['user_id']);
			}else {
				$app_content  = $migareference->get_app_content($_GET['app_id']);
				$gdpr_settings= $migareference->get_gdpr_settings($_GET['app_id']);
				$log_item=[
					'app_id'=>$_GET['app_id'],
					'user_id'=>$_GET['user_id'],
					'log_type'=>'visit'            
				];
				$migareference->saveSharelogs($log_item);        
				$layout
					->getBaseRender()
					->setPagetitle(__($gdpr_settings[0]['landing_page_title']))
					->setAppid($_GET['app_id'])
					->setUserid($_GET['user_id'])
					->setAgentid($_GET['agent_id'])
					->setReportby($_GET['report_by'])
					->setReportcustomtype($_GET['report_custom_type'])
					->setReportid(0)
					->setType($_GET['type']);
			}
		echo $layout->render();
        die;
	}
	public function loadgeoproviceAction() {
      if ($datas = $this->getRequest()->getPost()) {
          try {
            $migareference = new Migareference_Model_Migareference();
            $dataGeoConPro = $migareference->getGeoCountryProvicnes($datas['app_id'],$datas['country_id']);
            $collection    = [];
            foreach ($dataGeoConPro as $key => $value) {
              $collection[]=[
                        'app_id'=>$value['app_id'],
                        'country_id'=>$value['country_id'],
                        'created_at'=>$value['created_at'],
                        'migareference_geo_provinces_id'=>$value['migareference_geo_provinces_id'],
                        'province'=>$value['province']
                      ];
            }
            $payload = [
                  "collection" => $collection
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
	public function savereportAction(){
		try {
			$data                 = $this->getRequest()->getPost();
			$migareference        = new Migareference_Model_Migareference();		      
			$admin_data           = $migareference->is_admin($data['app_id'],$data['user_id']);
			if (empty($data['app_id']) || empty($data['user_id']) || count($admin_data)) {
				throw new Exception(__("Something went wrong please try again!"));
			}
			$app_id               = $data['app_id'];
			$errors               = "";
			$default              = new Core_Model_Default();
			$base_url             = $default->getBaseUrl();
			$app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
			$pre_report_settings  = $migareference->preReportsettigns($app_id);
			$field_data           = $migareference->getreportfield($app_id);
			$taxID                = $this->randomTaxid();
			$address_error				= false;
			$static_fields[1]="property_type";
			$static_fields[2]="sales_expectations";
			$static_fields[3]="address";
			$static_fields[4]="owner_name";
			$static_fields[5]="owner_surname";
			$static_fields[6]="owner_mobile";
			$static_fields[7]="note";
			// validation rules for dynamic report Fileds
			foreach ($field_data as $key => $value) {
				$name="extra_".$value['field_type_count'];
				if ($value['type']==2 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$name])) {
					$errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
				}elseif ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && empty($data[$static_fields[$value['field_type_count']]])) {
					$errors .= __('You must add valid value for')." ".$value['label']. "<br/>";
				}
				if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='address' && $pre_report_settings[0]['enable_unique_address']==1) {
					$address['address']  = $data['address'];
					$address['longitude']= $data['longitude'];
					$address['latitude'] = $data['latitude'];
					$days=$pre_report_settings[0]['address_grace_days'];
					$date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
					$internal_address_duplication=$migareference->isinternalAddressunique($app_id,$address,$date);
					$external_address_duplication=$migareference->isexternalAddressunique($app_id,$address);
					if (count($internal_address_duplication) || count($external_address_duplication)) {
						if ($pre_report_settings[0]['block_address_report']==2) {
							$address_error=true;
						}else {
							$errors .= __('This Property Address already exists.') . "<br/>";
						}
					}
				}
				// Explicitly check for email
				if ($value['type']==2 && $value['is_visible']==1 && $value['field_type']==7 && !empty($data[$name]) && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
					$errors .= __('Email is not correct. Please add a valid email address') . "<br/>";
				}
				if ($value['type']==1 && $value['is_visible']==1 && $value['is_required']==1 && !empty($static_fields[$value['field_type_count']]) && $static_fields[$value['field_type_count']]=='owner_mobile' && $pre_report_settings[0]['is_unique_mobile']==1) {
					$days=$pre_report_settings[0]['grace_days'];
					$date=date('Y-m-d H:i:s', strtotime('-'.$days.' day', strtotime(date('Y-m-d H:i:s'))));
					$mobile_duplication=$migareference->isMobileunique($app_id,$date,$data['owner_mobile']);
					$mobile_blacklist=$migareference->isBlackList($app_id,$data['owner_mobile']);

					if (strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile'])
					|| (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')){
						$errors .= __('Please add a valid Mobile or Phone Number.') . "<br/>";
					}elseif(count($mobile_duplication)) {
						$errors .= __('This Mobile Number already exists.') . "<br/>";
					}elseif (count($mobile_blacklist)) {
            			$errors .= __('This Mobile Number has been Blacklisted.') . "<br/>";
          			}
				}
			}
			if ($data['confirm_report_privacy']==2) {
				 $errors .= __('You must Accept Privacy statement.') . "<br/>";
			}
			if ($pre_report_settings[0]['authorized_call_back_visibility'] && $data['authorized_call_back'] == 2) { //updated by imran
				 $errors .= __('You must authorized to allow for commercial purpose.') . "<br/>";
			}
			// End dynamic filed validation rules
			if (!empty($errors)) {
				throw new Exception($errors);
			}else {
				$repo_data        = $migareference->get_last_report_no();
				$invoice_settings = $migareference->getpropertysettings($app_id,$data['user_id']);
				$password         = $this->randomPassword();
				$taxID            = $this->randomTaxid();
				$user_data        = $migareference->getAgentdata($data['user_id']);
				// if only siberian user save agrrement settings with default tax_id
					if ($temp_options[1]==1) {
						$inv_settings['app_id']=$app_id;
						$inv_settings['user_id']=$data['user_id'];
						$inv_settings['blockchain_password']=$password;
						$inv_settings['invoice_name']=$user_data[0]['firstname'];
						$inv_settings['invoice_surname']=$user_data[0]['lastname'];
						$inv_settings['invoice_mobile']=$user_data[0]['mobile'];
						$inv_settings['tax_id']=$taxID;
						$inv_settings['terms_accepted']=1;
						$inv_settings['sponsor_id'] = (isset($data['agent_id'])) ? $data['agent_id'] : 0 ;
						$inv_settings['special_terms_accepted']=1;
						$inv_settings['terms_artical_accepted']=1;
						$inv_settings['privacy_accepted']=1;
						$inv_settings['privacy_artical_accepted']=1;
						$migareference->savePropertysettings($inv_settings);
					}
				// If owner not set Pre Settings default will be
				$data['commission_type']= 0;
				$data['commission_fee'] = 5000;
				$data['reward_type']    = $pre_report_settings[0]['reward_type'];
				$data['commission_type']= $pre_report_settings[0]['commission_type'];
				if (count($pre_report_settings) && $pre_report_settings[0]['reward_type']==1) {
						$data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_amount'] : 0 ;
				} else {
					$data['commission_fee'] = ($pre_report_settings[0]['commission_type']==2) ? $pre_report_settings[0]['fix_commission_credits'] : 0 ;
				}
					$data['app_id']=$app_id;
					$staus_data = $migareference->get_one_standard_status($app_id,1);//Standard index
					$data['currunt_report_status']=$staus_data[0]['migareference_report_status_id'];
					$data['last_modification']=$status_data[0]['status_title'];
					$data['last_modification_by']=$data['user_id'];
					$data['last_modification_at']=date('Y-m-d H:i:s');
					if (!count($repo_data)) {
						$data['report_no']=1000;
					}else {
						$data['report_no']=$repo_data[0]['report_no']+1;
					}
					$report_entry['report_no']=$data['report_no'];
					$report_entry['app_id']=$app_id;
					$report_entry['user_id']=$data['user_id'];
					$report_entry['property_type']=$data['property_type'];
					$report_entry['sales_expectations']=$data['sales_expectations'];
					$report_entry['report_custom_type']=($data['report_custom_type']===null) ? 0 : $data['report_custom_type'] ;
					$report_entry['commission_type']=$pre_report_settings[0]['commission_type'];
					$report_entry['reward_type']=$pre_report_settings[0]['reward_type'];
					$report_entry['commission_fee']=$data['commission_fee'];
					$report_entry['is_reminder_sent']=0;
					$report_entry['address']=$data['address'];
					$report_entry['longitude']=$data['longitude'];
					$report_entry['latitude']=$data['latitude'];
					$report_entry['owner_name']=$data['owner_name'];
					$report_entry['owner_surname']=$data['owner_surname'];
					$report_entry['owner_mobile']=$data['owner_mobile'];
					$report_entry['owner_hot']=$data['owner_hot'];
					$report_entry['note']=$data['note'];
					$report_entry['currunt_report_status']=$staus_data[0]['migareference_report_status_id'];
					$report_entry['last_modification']=$status_data[0]['status_title'];
					$report_entry['last_modification_by']=$data['user_id'];
					$report_entry['created_by']=(isset($data['report_by'])) ? $data['report_by'] : 0 ;
					$report_entry['last_modification_at']=date('Y-m-d H:i:s');
					$report_entry['extra_dynamic_fields']=serialize($data);
					$report_entry['extra_dynamic_field_settings']=serialize($field_data);
					$report_entry['report_source']=2;//For Landing Page
					$report_id = $migareference->savepropertyreport($report_entry);
					$log_item=[
						'app_id'=>$app_id,
						'user_id'=>$data['user_id'],
						'log_type'=>'report'            
					];
					$migareference->saveSharelogs($log_item);   
					if ($report_id>0) {
						// Add note for report
						if (!empty($data['report_note'])) {
							$note['app_id']        = $app_id;
							$note['user_id']       = 0;//The user or Admin who add report just for record no role anywhere;
							$note['report_id']     = $report_id;
							$note['notes_content'] = $data['report_note'];
							$migareference->insert_notes($note);
						}
						// Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
						$notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_id,$report_entry['currunt_report_status'],$report_entry['last_modification_by'],'LANDINGPAGE-END','create');                                              
					}
				}
				$payload = [
						'success' => true,
						'address_error' => $address_error,
						'notifcation_response' => $notifcation_response,
						'report_id' => $report_id,
						'message' => __('Successfully report saved.'),
						'address_error_message' => __('Warning! Address already used in another report. Please be aware that it is possible someone else already submitted the same report.'),
						'message_loader' => 0,
						'message_button' => 0,
						'message_timeout' => 2
				];
			} catch (\Exception $e) {
					$payload = [
							'success' => false,
							'message' => __($e->getMessage()),
							'extra'=>$field_data,
							'notifcation_response'=>$notifcation_response,
					];
			}
			$this->_sendJson($payload);
		}
	public function reportformbuilderAction() {
    try{
				$app_id        = $this->getRequest()->getParam('app_id');
        $migareference = new Migareference_Model_Migareference();
        $pre_settings  = $migareference->preReportsettigns($app_id);
        $siberian_usrs = $migareference->get_siberianuser($app_id);
        $field_data    = $migareference->getreportfield($app_id);
        $default       = new Core_Model_Default();
        $base_url      = $default->getBaseUrl();
        $pre_settings[0]['base_url']       = $base_url;
        $pre_settings[0]['invite_message'] = trim($pre_settings[0]['invite_message']);
          $static_fields[1]['name']="property_type";
          $static_fields[2]['name']="sales_expectations";
          $static_fields[3]['name']="address";
          $static_fields[4]['name']="owner_name";
          $static_fields[5]['name']="owner_surname";
          $static_fields[6]['name']="owner_mobile";
          $static_fields[7]['name']="note";
          $field="";
          foreach ($field_data as $key => $value) {
            $display=($value['is_visible']==1) ? "" : "none" ;
			$required = ($value['is_required']==1) ? "*" : "" ;
            if ($value['type']==1) {
                  $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';                  
				  $field.='<label for='.$static_fields[$value['field_type_count']]['name'].'>'.__($value['label']).' '.$required.' </label>';
                  $field.='</div><div class="col-sm-12" >';
                  $field.=$this->manageinputypeAction($app_id,$value['field_type'],$static_fields[$value['field_type_count']]['name'],$value['field_option'],0,$value['options_type'],$value['default_option_value']);
            }else {
              $field.='<div class="form-group" style="display:'.$display.'"><div class="col-sm-4">';
              $name="extra_".$value['field_type_count'];
              $field.='<label for='.$name.'>'.__($value['label']).' '.$required.'</label>';
              $field.='</div><div class="col-sm-12" >';
              $field.=$this->manageinputypeAction($app_id,$value['field_type'],$name,$value['field_option'],$value['field_type_count'],$value['options_type'],$value['default_option_value']);
            }
          }
					// GDPR Policy fields
					$base_url=$default->getBaseUrl();
					$global_privacy_link=$base_url."/migareference/globalpolicy?app_id=".$app_id;
					//added by imran start
					$gdpr1 ='';
					if (!empty($pre_settings[0]['custom_landing_report_text'])) {
						$gdpr1 = '<div class="input-group">';
						$gdpr1 .= '<label class="input-label">';
						$gdpr1 .= $pre_settings[0]['custom_landing_report_text'];
						$gdpr1 .= '</label>';
						$gdpr1 .= '</div>';
					}
					//added by imran end
					$gdpr1.='<div class="input-group">'; //updated by imran
						$gdpr1.='<label class="input-label">';
							$gdpr1.=$pre_settings[0]['confirm_report_privacy_label'].' *';
							$gdpr1.='<a target="_blank" href=';
							$gdpr1.= ($pre_settings[0]['enable_privacy_global_settings']==1) ? $global_privacy_link : $pre_settings[0]['confirm_report_privacy_link'] ; 
							$gdpr1.=' >'.__('Here').'</a>';
						$gdpr1.='</label>';
						$gdpr1.='<div class="row" style="width:100%">';
							$gdpr1.='<div class="" style="width:100%;padding:17px;">';
								$gdpr1.='<div class="" style="width:100px;float:left;">';
									$gdpr1.=__('I Accept');
								$gdpr1.='</div>';
								$gdpr1.='<div class="" style="width:100px;float:right;">';
									$gdpr1.='<input class="" value="1" type="radio"  name="confirm_report_privacy">';
								$gdpr1.='</div>';
							$gdpr1.='</div>';
							$gdpr1.='<div class="" style="width:100%;padding:17px;">';
								$gdpr1.='<div class="" style="width:100px;float:left;">';
									$gdpr1.=__('I Dont Accept');
								$gdpr1.='</div>';
								$gdpr1.='<div class="" style="width:100px;float:right;">';
									$gdpr1.='<input class="" checked="checked" value="2" type="radio"  name="confirm_report_privacy">';
								$gdpr1.='</div>';
							$gdpr1.='</div>';
						$gdpr1.='</div>';
					$gdpr1.='</div>';
					//updated by imran start
					$gdpr2 = '';
					if (count($pre_settings) && $pre_settings[0]['authorized_call_back_visibility']) {
						$gdpr2.='<div class="input-group">';
							$gdpr2.='<label class="input-label">';
								$gdpr2.=$pre_settings[0]['authorized_call_back_label'].' *';
							$gdpr2.='</label>';
							$gdpr2.='<div class="row" style="width:100%">';
								$gdpr2.='<div class="" style="width:100%;padding:17px;">';
									$gdpr2.='<div class="" style="width:100px;float:left;">';
									$gdpr2.=__('I Accept');
									$gdpr2.='</div>';
									$gdpr2.='<div class="" style="width:100px;float:right;">';
										$gdpr2.='<input class="" value="1" type="radio"  name="authorized_call_back">';
									$gdpr2.='</div>';
								$gdpr2.='</div>';
								$gdpr2.='<div class="" style="width:100%;padding:17px;">';
									$gdpr2.='<div class=""  style="width:100px;float:left;">';
									$gdpr2.=__('I Dont Accept');
									$gdpr2.='</div>';
									$gdpr2.='<div class="" style="width:100px;float:right;">';
										$gdpr2.='<input class="" checked="checked" value="2" type="radio"  name="authorized_call_back">';
									$gdpr2.='</div>';
							$gdpr2.='	</div>';
						$gdpr2.='	</div>';
						$gdpr2.='</div>';
					}
					//updated by imran end
					$field.=$gdpr1.$gdpr2;
          $payload = [
              'success' => true,
              'message' => __('User status has been updated successfully.'),
              'message_loader' => 0,
              'message_button' => 0,
              'message_timeout' => 2,
              'form_builder' => $field,
              'other'=>$siberian_usrs,
              'osther'=>$app_id
          ];
    } catch (\Exception $e) {
        $payload = [
            'error' => true,
            'message' => __($e->getMessage())
        ];
    }
    $this->_sendJson($payload);
  }
	public function randomTaxid() {
		$alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 6; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
		}
		// return implode($pass); //turn the array into a string
		return "";
}
  public function randomPassword() {
	$alphabet = "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 10; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
// public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$option_type=0,$option_default="")
// {
// 	$extra_input_template="";
// 	$migareference  = new Migareference_Model_Migareference();
// 	if ($type==1) {
// 		$extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
// 	} else if($type==2) {
// 		$extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input-flat" placeholder="" /></div></div>';
// 	}else if($type==3) {
// 		$option_value=0;
// 		switch ($option_type) {
// 			case 0:
// 				$temp_options=explode('@',$options);
// 				$extra_input_template.='<select id="'.$ng_model.'"  class="input-flat" name="'.$ng_model.'">';
// 				foreach ($temp_options as $key => $value) {
// 					$option_value++;
// 					$extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
// 				}
// 				$extra_input_template.="</select></div></div>";
// 				break;
// 			case 1://Country List
// 				$geoCountries              = $migareference->getGeoCountries($app_id);
// 				$df_opt=explode("@",$option_default);
// 				$extra_input_template.='<select onChange=loadProvicnes(0,1) id="'.$ng_model.'"  class="input-flat country_default" name="'.$ng_model.'">';
// 				foreach ($geoCountries as $key => $value) {
// 					if ($df_opt[0]!=$value['migareference_geo_countries_id']) {
// 						$extra_input_template.="<option value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
// 					}else {
// 						$extra_input_template.="<option selected value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
// 					}
// 				}
// 				$extra_input_template.="</select></div></div>";
// 				break;
// 			case 2:
// 			$df_opt=explode("@",$option_default);
// 			$dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$option_default[0]);
// 			$extra_input_template.='<select id="'.$ng_model.'"  class="input-flat province_default" name="'.$ng_model.'">';
// 			foreach ($dataGeoConPro as $key => $value) {
// 				if ($df_opt[1]!=$value['migareference_geo_provinces_id']) {
// 					$extra_input_template.="<option value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
// 				}else {
// 					$extra_input_template.="<option selected value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
// 				}
// 			}
// 			$extra_input_template.="</select></div></div>";
// 				break;
// 			default:
// 				$extra_input_template.="<option value=''></option>";
// 				break;
// 		}
//
// 	}else if($type==4) {
// 		$latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
// 		$extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="" class="input-flat" placeholder="Google Location" />';
// 		$extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value=''>";
// 		$extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value=''> </div></div>";
// 	}else if($type==5) {
// 		$extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input-flat"></textarea></div></div>';
// 	}
// 	return $extra_input_template;
// }
	public function manageinputypeAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$option_type=0,$option_default=""){
		$extra_input_template="";
		$migareference  = new Migareference_Model_Migareference();
		if ($type==1) {
			$extra_input_template.='<input type="text"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
		} else if($type==2) {
			$extra_input_template.='<input type="number"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
		}else if($type==3) {
			$option_value=0;
			switch ($option_type) {
			case 0:
			$temp_options=explode('@',$options);
			$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1" name="'.$ng_model.'">';
			foreach ($temp_options as $key => $value) {
				$option_value++;
				$extra_input_template.="<option value='".$option_value."'>".__($value)."</option>";
			}
			$extra_input_template.="</select></div></div>";
				break;
			case 1://Country List
				$geoCountries              = $migareference->getGeoCountries($app_id);
				$df_opt=explode("@",$option_default);
				$extra_input_template.='<select onChange=loadProvicnes(0,1) id="'.$ng_model.'"  class="input--style-1 country_default"  name="'.$ng_model.'">';
				foreach ($geoCountries as $key => $value) {
					if ($df_opt[0]!=$value['migareference_geo_countries_id']) {
						$extra_input_template.="<option value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
					}else {
						$extra_input_template.="<option selected value='".$value['migareference_geo_countries_id']."'>".__($value['country'])."</option>";
					}
				}
				$extra_input_template.="</select></div></div>";
				break;
			case 2:
			$df_opt=explode("@",$option_default);
			$dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$df_opt[0]);
			$extra_input_template.='<select id="'.$ng_model.'"  class="input--style-1 default_province"  name="'.$ng_model.'">';
			foreach ($dataGeoConPro as $key => $value) {
				if ($df_opt[1]!=$value['migareference_geo_provinces_id']) {
					$extra_input_template.="<option value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
				}else {
					$extra_input_template.="<option selected value='".$value['migareference_geo_provinces_id']."'>".__($value['province'])."</option>";
				}
			}
			$extra_input_template.="</select></div></div>";
				break;
			default:
				$extra_input_template.="<option value=''></option>";
				break;

		}

		}else if($type==4) {
			$latlong_name = ($address_counter==0) ? "" : "_".$address_counter ;
			$extra_input_template.='<input onfocus="callforaddress('.$address_counter.')" type="text" name="'.$ng_model.'" id="address-new-report-'.$address_counter.'" value="" class="input--style-1" placeholder="Google Location" />';
			$extra_input_template.="<input id='new-report-longitude-".$address_counter."' type='hidden' name='longitude".$latlong_name."' value=''>";
			$extra_input_template.="<input id='new-report-latitude-".$address_counter."' type='hidden' name='latitude".$latlong_name."' value=''> </div></div>";
		}else if($type==5) {
			$extra_input_template.='<textarea  name="'.$ng_model.'" id="'.$ng_model.'" rows="3" cols="80" class="input--style-1"></textarea></div></div>';
		}elseif ($type==7) {
			$extra_input_template.='<input type="email"  name="'.$ng_model.'" id="'.$ng_model.'" value="" class="input--style-1" placeholder="" /></div></div>';
		}
		return $extra_input_template;
	}
}
