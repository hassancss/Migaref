<?php
/**
 * Class Migareference_Public_ApiController
 * API Response Codes
  *     // ErrCode=
  *     // “0” = Error, Token mismatch
  *     // “1” = Report added
  *     // “2” = Error, wrong data format
  *     // “3” = Error, Phone owner already present*
  *     // “4” = Error, unknow
 */
class Migareference_Public_ReportapiController extends Migareference_Controller_Default {
  public function savereportAction(){
    try{
      $reportapi            = new Migareference_Model_Reportapi();
      $migareference        = new Migareference_Model_Migareference();
      $utilities            = new Migareference_Model_Utilities();
      $default              = new Core_Model_Default();
      $base_url             = $default->getBaseUrl();
      $data                 = $this->getRequest()->getPost();      
      $taxID                = $utilities->randomTaxid();
      // Notification Allowed Tags
      $notificationTags     = [ "@@referral_name@@",
                                "@@report_owner@@",
                                "@@property_owner@@",
                                "@@report_owner_phone@@",
                                "@@property_owner_phone@@",
                                "@@report_no@@",
                                "@@commission@@",
                                "@@app_name@@",
                                "@@app_link@@"
                              ];
      // Validate TOKEN
      $data['token'] = trim($data['token']);
      if (empty($data['token']) || strlen($data['token'])!=35) {
        throw new Exception(__("Token Mismatchd"));
      }      
      $pre_report_settings=$reportapi->validateToken($data['token']);
      if (!count($pre_report_settings)) {
        throw new Exception(__("Token Mismatched"));
      }
      $app_id=$pre_report_settings[0]['app_id'];          
      // Validate Refrrer Email
      if (empty($data['referrer_email']) || !filter_var($data['referrer_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception(__("Referrer Email is missing or invalid."));
      }    
      // Check if customer found against the Referrer Email (Dperedate now we will check referrer_id)
      $referrerCustomer=$migareference->getSingleuserByEmail($app_id,$data['referrer_email']);
      // Validate Referrer Details
      if (!COUNT($referrerCustomer)) {
        if (empty($data['referrer_email']) || empty($data['referrer_name']) || empty($data['referrer_surname']) || empty($data['referrer_mobile']) || !preg_match('/^[a-zA-Z ]+$/', $data['referrer_name']) || !preg_match('/^[a-zA-Z ]+$/', $data['referrer_surname'])) {
          throw new Exception(__("Referrer Name, Surname, and Mobile are required to create a new referrer account. Please ensure that Name and Surname contain only letters and spaces."));
        }                
      }
      // validate owner details	  
      if(strlen($data['owner_mobile']) < 10 || strlen($data['owner_mobile']) > 14 || empty($data['owner_mobile']) || preg_match('@[a-z]@', $data['owner_mobile']) || (substr($data['owner_mobile'], 0, 1)!='+' && substr($data['owner_mobile'], 0, 2)!='00')) {          
        throw new Exception(__("Invalid format for owner phone. Please ensure the phone number is between 10 and 14 characters long and starts with a '+' or '00'."));
      }       
      if (empty($data['owner_name']) || empty($data['owner_surname']) || !preg_match('/^[a-zA-Z ]+$/', $data['owner_name']) || !preg_match('/^[a-zA-Z ]+$/', $data['owner_surname'])) {
        throw new Exception(__("Prospect Name and Surname are required. Please ensure the prospect name and surname contain only letters and spaces."));
      } 

      $app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
      $field_data           = $migareference->getreportfield($app_id);
      // Validation of custom or extra fields
      //from API parameter (user friendly) we receive custom fileds params as custom_field_x so first replace params to extra_x
      foreach ($data as $key => $value) {
        if (preg_match('/^custom_field_\d+$/', $key)) {
            $newKey = str_replace('custom_field_', 'extra_', $key);
            $data[$newKey] = $value;
            unset($data[$key]); // Remove the old key
        }
      }
      //validate extra or custom fields
      $typeDescriptions = [
        1 => ["type" => "Text", "format" => "Alphanumeric characters", "sample_value" => "SampleText123"],
        2 => ["type" => "Number", "format" => "Numeric characters", "sample_value" => "12345"],
        3 => ["type" => "Options", "format" => "Numeric values corresponding to option labels", "sample_value" => "1"],
        5 => ["type" => "Long Text", "format" => "Any characters", "sample_value" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."],
        6 => ["type" => "Birth Date", "format" => "Date format (e.g., DD-MM-YYYY)", "sample_value" => "01-01-1990"]
    ];
      foreach ($field_data as $key => $value) {
        $name="extra_".$value['field_type_count'];
        //check required
        if ($value['type']==2 && $value['is_visible']==1) {          
          if ($value['is_required']==1 && empty($data[$name])) {
            throw new Exception(__('You must add valid value for')." ".$value['label']);
          }
        // Validations as per type
        switch ($value['field_type']) {
          case 1:
            if (!preg_match('/^[a-zA-Z0-9 ]+$/', $data[$name])) {
              throw new Exception($value['label'].' '.__("must contain only Alphanumeric characters"));
            } 
            break;
          case 2:
            if (!preg_match('/^[0-9 ]+$/', $data[$name])) {
              throw new Exception($value['label'].' '.__("must contain only Numbers"));
            } 
            break;
          case 3:
            if (!preg_match('/^[0-9 ]+$/', $data[$name])) {
              throw new Exception($value['label'].' '.__("must contain only Numbers"));
            } 
            break;
          case 5:
            if ($utilities->containsSQLInjection($data[$name])) {
              throw new Exception($value['label'].' '.__(" contains potential SQL injection."));
          }
            break;
        }
        }  
        
      }
      // Overide Report Page Settings
      foreach ($field_data as $key => $value) {
        $field_data[$key]['is_visible']  = 2;
        $field_data[$key]['is_required'] = 2;
        if ($value['type']==1 && ($value['field_type_count']==4 || $value['field_type_count']==5 || $value['field_type_count']==6)) {
          $field_data[$key]['is_visible']  = 1;
          $field_data[$key]['is_required'] = 1;
        }
      }

      // Create or Update Sib user
      
        
        // If Email not found create a new user
        
        if (count($referrerCustomer)) {
          $user_id=$referrerCustomer[0]['customer_id'];
          if (!empty($data['referrer_name']) || !empty($data['referrer_surname'])) {
              // Updtae Sib User
              $customer['firstname']   = $data['referrer_name'];
              $customer['lastname']= $data['referrer_surname'];
          }
          $customer['privacy_policy'] = (!empty($data['referrer_gdpr']) && $data['referrer_gdpr']==1) ? 1 : 0 ;
          $migareference->updateCustomerdob($user_id,$customer);
        }else {          
            // Add New User
            $customer['app_id']  = $app_id;
            $customer['email']   = $data['referrer_email'];
            $customer['firstname']= $data['referrer_name'];
            $customer['lastname'] = $data['referrer_surname'];
            $customer['privacy_policy'] = (!empty($data['referrer_gdpr']) && $data['referrer_gdpr']==1) ? 1 : 0 ;
            $customer['password']= sha1($utilities->randomPassword());
            $user_id             = $migareference->createUser($customer);
          
        }
      
      
        $data['user_id']     = $user_id;
        $repo_data        = $migareference->get_last_report_no();
        $invoice_settings = $migareference->getpropertysettings($app_id,$data['user_id']);
        $password         = $utilities->randomPassword();
        $user_data        = $migareference->getAgentdata($data['user_id']);
        // if only siberian user save agrrement settings with default tax_id
          if (!count($invoice_settings)) {
            $inv_settings['app_id']=$app_id;
            $inv_settings['user_id']=$data['user_id'];
            $inv_settings['blockchain_password']=$password;
            $inv_settings['invoice_name']=$user_data[0]['firstname'];
            $inv_settings['invoice_surname']=$user_data[0]['lastname'];
            $inv_settings['invoice_mobile']=$user_data[0]['mobile'];
            $inv_settings['sponsor_id']=$agent_id;
            $inv_settings['tax_id']=$taxID;
            $inv_settings['terms_accepted']=1;
            $inv_settings['special_terms_accepted']=1;
            $inv_settings['terms_artical_accepted']=1;
            $inv_settings['privacy_accepted']=1;
            $inv_settings['privacy_artical_accepted']=1;
            $migareference->savePropertysettings($inv_settings);
            // Send Welcome Email to referrer
            if ($pre_report_settings[0]['enable_welcome_email']==1
                && !empty($pre_report_settings[0]['referrer_wellcome_email_title'])
                && !empty($pre_report_settings[0]['referrer_wellcome_email_body']))
              {
              $notificationTags=$migareference->welcomeEmailTags();
              $agent_user=$migareference->getSingleuser($app_id,0);
              $notificationStrings = [
                $user_data[0]['firstname']." ".$user_data[0]['lastname'],
                $user_data[0]['email'],
                $data['password'],
                $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                $app_link                            
              ];
              $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_title']);
              $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_body']);
              $email_data['type']        = 2;//type 2 for wellcome log
              $migareference->sendMail($email_data,$app_id,$data['user_id']);
            }
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
          $data['report_no'] = (!count($repo_data)) ? 1000 : $repo_data[0]['report_no']+1;
          $consent_timestmp  = (!empty($data['prospect_gdpr_timestamp'])) ? date('Y-m-d H:i:s', strtotime($data['prospect_gdpr_timestamp'])) : NULL ;

          $report_entry['report_no']                   = $data['report_no'];
          $report_entry['app_id']                      = $app_id;
          $report_entry['user_id']                     = $data['user_id'];
          $report_entry['commission_type']             = $pre_report_settings[0]['commission_type'];
          $report_entry['reward_type']                 = $pre_report_settings[0]['reward_type'];
          $report_entry['commission_fee']              = $data['commission_fee'];
          $report_entry['owner_name']                  = $data['owner_name'];
          $report_entry['owner_surname']               = $data['owner_surname'];
          $report_entry['owner_mobile']                = $data['owner_mobile'];
          $report_entry['note']                        = $data['note'];
          $report_entry['owner_hot']                   = 3;
          $report_entry['currunt_report_status']       = $staus_data[0]['migareference_report_status_id'];
          $report_entry['last_modification']           = $status_data[0]['status_title'];
          $report_entry['last_modification_by']        = $data['user_id'];
          $report_entry['last_modification_at']        = date('Y-m-d H:i:s');
      		$report_entry['consent_timestmp']            = $consent_timestmp;
      		$report_entry['consent_source']					     = 'API';
      		$report_entry['report_source']					     = 4;
          $report_entry['extra_dynamic_fields']        = serialize($data);
          $report_entry['extra_dynamic_field_settings']= serialize($field_data);
          $report_id = $migareference->savepropertyreport($report_entry);          
          if ($report_id>0) {
					  // Add note for report
            if (!empty($data['report_note'])) {
              $note['app_id']        = $app_id;
              $note['user_id']       = 0;
              $note['report_id']     = $save_data;
              $note['notes_content'] = $data['report_note'];
              $migareference->insert_notes($note);
            }
						// Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
						$notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_id,$report_entry['currunt_report_status'],$report_entry['last_modification_by'],'API-END','create');                                              
					}
          $payload = [
              'response'      => true,
              'description'  => __('Successfully report saved.'),
              'reportNo'     => $data['report_no']              
          ];
    } catch (\Exception $e) {
        $payload = [
            'response'      => false,
            'description'  => __($e->getMessage())                     
        ];
    }
      $this->_sendJson($payload);
  }
  function getapiparamsAction() {    
    try {
        $migareference = new Migareference_Model_Migareference(); 
        $reportapi = new Migareference_Model_Reportapi();     
        $data = $this->getRequest()->getPost();            
        
        // Validate TOKEN
        $data['token'] = trim($data['token']);
        if (empty($data['token']) || strlen($data['token']) != 35) {
            throw new Exception(__("Token Mismatchd"));
        }      
        $pre_report_settings = $reportapi->validateToken($data['token']);
        if (!count($pre_report_settings)) {
            throw new Exception(__("Token Mismatched"));
        }

        $app_id = $pre_report_settings[0]['app_id'];   
        $customFields = $migareference->getreportfield($app_id);
        $typeDescriptions = [
          1 => ["type" => "Text", "format" => "Alphanumeric characters", "sample_value" => "SampleText123"],
          2 => ["type" => "Number", "format" => "Numeric characters", "sample_value" => "12345"],
          3 => ["type" => "Options", "format" => "Numeric values corresponding to option labels", "sample_value" => "1"],
          5 => ["type" => "Long Text", "format" => "Any characters", "sample_value" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."],
          6 => ["type" => "Birth Date", "format" => "Date format (e.g., DD-MM-YYYY)", "sample_value" => "01-01-1990"]
      ];
      

        $parameters = [
          [
              "name" => "token",
              "type" => "String",
              "required" => true,
              "format" => "N/A",
              "sample_value" => "N/A",
              "description" => __("The API token received directly from the Admin.")
          ],
          [
              "name" => "owner_name",
              "type" => "String",
              "required" => true,
              "format" => "Alphabetic characters (a-z, A-Z)",
              "sample_value" => "John",
              "description" => __("The first name of the Prospect.")
          ],
          [
              "name" => "owner_surname",
              "type" => "String",
              "required" => true,
              "format" => "Alphabetic characters (a-z, A-Z)",
              "sample_value" => "Doe",
              "description" => __("The last name of the Prospect Owner.")
          ],
          [
              "name" => "owner_mobile",
              "type" => "String",
              "required" => true,
              "format" => "Format: +39xxxxxxxxxx, 00xxxxxxxxxx",
              "sample_value" => "+393333333333",
              "description" => __("The mobile number of the Prospect Owner.")
          ],
          [
              "name" => "note",
              "type" => "String",
              "required" => false,
              "format" => "N/A",
              "sample_value" => __("This is a sample note."),
              "description" => __("A descriptive note about the prospect.")
          ],
          [
              "name" => "referrer_email",
              "type" => "String",
              "required" => true,
              "format" => __("Email valid format (e.g., example@example.com)"),
              "sample_value" => "example@example.com",
              "description" => __("The Referrer Email serves as a Unique identifier for the Referrer user within the database.")
          ],
          [
              "name" => "referrer_name",
              "type" => "String",
              "required" => false,
              "format" => __("Alphabetic characters (a-z, A-Z)"),
              "sample_value" => "Jane",
              "description" => __("The first name of the Referrer. Required when creating a new Referrer.")
          ],
          [
              "name" => "referrer_surname",
              "type" => "String",
              "required" => false,
              "format" => __("Alphabetic characters (a-z, A-Z)"),
              "sample_value" => "Smith",
              "description" => __("The last name of the Referrer. Required when creating a new Referrer.")
          ],
          [
              "name" => "referrer_mobile",
              "type" => "String",
              "required" => false,
              "format" => "Format: +39xxxxxxxxxx, 00xxxxxxxxxx",
              "sample_value" => "+393456789012",
              "description" => __("The mobile number of the Referrer. Required when creating a new Referrer.")
          ],
          [
              "name" => "referrer_gdpr",
              "type" => "Bool (0 or 1)",
              "required" => false,
              "format" => "N/A",
              "sample_value" => "1",
              "description" => __("Specify whether the consent is collected or not.")
          ],
          [
            "name" => "prospect_gdpr_stamp",
            "type" => "Timestamp",
            "required" => true, // Assuming it's required for GDPR compliance
            "format" => "DD-MM-YYYY HH:MM:SS", // Update format to represent date and time
            "sample_value" => "01-01-2023 00:00:00", // Sample value in the specified format
            "description" => "The date and time when GDPR consent was collected from the prospect."
        ]
      ];
      
      foreach ($customFields as $index => $field) {
        if ($field['type']==2) {                
          if ($field['is_visible'] == 2) {
              // Field is disabled or not configured, provide a placeholder message
              $parameters[] = [
                  "name" => "custom_field_" .$field['field_type_count'],
                  "type" => __("Disabled or Not Configured"),
                  "required" => false,
                  "format" => "N/A",
                  "sample_value" => "N/A",
                  "description" => __("This field is disabled or not configured.")
              ];
          } else {
              $fieldName = "custom_field_" .$field['field_type_count'];
              $fieldType = $typeDescriptions[$field['field_type']]['type'];
              $isRequired = ($field['is_required'] == 1) ? true : false; // Convert 1 to true, 2 to false
              $format = $typeDescriptions[$field['field_type']]['format'];
              $sampleValue = $typeDescriptions[$field['field_type']]['sample_value'];;
              $description = $typeDescriptions[$field['field_type']]['description'];
              if ($field['field_type']==3) { //type 3 for options: in this case we have describe the valuse as per option type
                switch ($field['options_type']) { // Assuming 'option_type' represents the type of option: 0 for Custom Options, 1 for Countries, 2 for Provinces
                  case 0: // Custom Options
                      $description = __("Enter a numeric value corresponding to the option labels provided by the administrator, starting from 1.");
                      break;
                  case 1: // Countries
                      $description = __("Enter the UID (Unique Identifier) of the country as per the provided list.");
                      break;
                  case 2: // Provinces
                      $description = __("Enter the UID (Unique Identifier) of the province as per the provided list.");
                      break;
                }
              }        
              $parameters[] = [
                  "name" => $fieldName,
                  "type" => $fieldType,
                  "required" => $isRequired,
                  "format" => $format,
                  "sample_value" => $sampleValue,
                  "description" => $description
              ];
          }
        }
      }
        $payload=[
          "response"=> true,
          "message"=> "API call structure retrieved successfully.",
          "parameters"=> $parameters
        ];
         
    } catch (Exception $e) {
         $payload=["error" => true, "message" => $e->getMessage()];
    }
    $this->_sendJson($payload);
}

  public function updatereportAction(){    
      try {
            $migareference   = new Migareference_Model_Migareference();
            $reportapi       = new Migareference_Model_Reportapi();
            $default         = new Core_Model_Default();
            $base_url        = $default->getBaseUrl();
            $data            = $this->getRequest()->getPost();
            // Validate TOKEN
            if (empty($data['token'])) {
              throw new Exception("Token Mismatch");
            }else {
              $pre_report_settings=$reportapi->validateToken($data['token']);
              if (!count($pre_report_settings)) {
                throw new Exception("Token Mismatch");
              }else {
                $app_id=$pre_report_settings[0]['app_id'];
              }
            }
            $notificationTags= [  "@@referral_name@@",
                                  "@@report_owner@@",
                                  "@@property_owner@@",
                                  "@@report_owner_phone@@",
                                  "@@property_owner_phone@@",
                                  "@@report_no@@",
                                  "@@commission@@",
                                  "@@app_name@@",
                                  "@@app_link@@",
                                  "@@comment@@",
                                  "@@agent_name@@"
                                ];
            $app_link       = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
            // Report No Validation
            if (empty($data['status_id']) || preg_match('@[a-z]@', $data['status_id'])) {
              throw new Exception("Invalid Status ID");
            }else {
              $new_status_id=$data['status_id'];
              $status_data = $migareference->getStatus($app_id,$new_status_id);
              if (!count($status_data)) {
                throw new Exception("Invalid Status ID");
              }
            }
            if (empty($data['report_no']) || preg_match('@[a-z]@', $data['report_no'])) {
              throw new Exception("Invalid Report No");
            }else {
              $previous_item  = $migareference->getApiReport($app_id,$data['report_no']);
              if (!count($previous_item)) {
                throw new Exception("Invalid Report No");
              }
            }

            $report_item    = $previous_item[0];
            $checkmandate   = $migareference->reportStatusByKey($new_status_id,$report_item['migareference_report_id']);
            $checkmandate   = $checkmandate[0];
            $field_data     = unserialize( $report_item['extra_dynamic_field_settings']);
            /* Commision Validation
            * 1 Commision Type: (1 %commision,3 Commison Range)
            * Sandard Type 4 or Status is not Declined
            * Status is set to Acquired Commision Fee
            * Commission Fee is not set before
            */
            if ($checkmandate['is_acquired']==1
                && ($report_item['commission_type']==1
                || $report_item['commission_type']==3)
                && $checkmandate['standard_type']!=4
                && empty($report_item['commission_fee'])
                && empty($data['commission_fee'])
               ) {
                 throw new Exception("You must add commission fee.");
            }
            $earn_amount = ($data['commission_fee']>1) ? $data['commission_fee'] : $report_item['commission_fee'];
            if ($checkmandate['standard_type']==3 && $earn_amount<1) {
              throw new Exception("Commission fee is Missing");
            }
            // Comment Validation
            if ($checkmandate['is_comment']==1 && empty($data['comment'])) {
              throw new Exception("Comment fee is Missing");
            }
            // API Admin validation
            $api_admin   = $reportapi->getApiAdmin($app_id);
            if (!count($api_admin)) {
              throw new Exception("Invalid Admin Access");
            }
            $static_fields[1]="property_type";
            $static_fields[2]="sales_expectations";
            $static_fields[3]="address";
            $static_fields[4]="owner_name";
            $static_fields[5]="owner_surname";
            $static_fields[6]="owner_mobile";
            $static_fields[7]="note";
                // Update Report Status
                $status_data = $migareference->getStatus($app_id,$new_status_id);
                $api_admin   = $api_admin[0];
                if ($checkmandate['is_acquired']==1
                    && ($report_item['commission_type']==1
                    || $report_item['commission_type']==3)) {
                   $property_report['commission_fee']= (empty($data['commission_fee'])) ? $report_item['commission_fee'] : $data['commission_fee'] ;
                }
                $property_report['last_modification_by']   = $api_admin['user_id'];
                $property_report['last_modification_at']   = date('Y-m-d H:i:s');
                $property_report['is_reminder_sent']       = 0;
                $property_report['last_modification']      = $status_data[0]['status_title'];
                $property_report['currunt_report_status']  = $new_status_id;
                $property_report['migareference_report_id']= $report_item['migareference_report_id'];
                $save_data     = $migareference->updatepropertyreport($property_report);
                // Save Comment Text: To be used in Immidiately or Cron Notifaction
                if ($checkmandate['is_comment']==1) {
                  $comment_tb['app_id']       = $app_id;
                  $comment_tb['report_id']    = $report_item['migareference_report_id'];
                  $comment_tb['status_id']    = $new_status_id;
                  $comment_tb['comment']      = $data['comment'];
                  $migareference->saveComment($comment_tb);
                }

                if ($report_item['currunt_report_status']!=$new_status_id) {
                  // Save Staus Update Log
                  $log_data['app_id']    = $app_id;
                  $log_data['user_id']   = $property_report['last_modification_by'];
                  $log_data['log_source']= "API";
                  $log_data['report_id'] = $report_item['migareference_report_id'];
                  $log_data['log_type']  = "Update Status";
                  $log_data['log_detail']= "Update Status to ".$status_data[0]['status_title'];

                  $migareference->saveLog($log_data);

                  // Send Notification (1:Refferral Email 2:Agent Email  2:Referral Push  4: Reffrral PSUH)
						      $notifcation_response=(new Migareference_Model_Reportnotification())->sendNotification($app_id,$report_item['migareference_report_id'],$property_report['currunt_report_status'],$property_report['last_modification_by'],'API-END','update');                                              
                }
                // Save earnings if Property Sold
                if ($status_data['standard_type']==3  && $report_item['reward_type']==1) { //Euro Commison
                  $earning['app_id']            = $app_id;
                  $earning['refferral_user_id'] = $report_item['user_id'];
                  $earning['sold_user_id']      = $api_admin['user_id'];
                  $earning['user_type']         = 4;
                  $earning['report_id']         = $report_item['migareference_report_id'];
                  $earning['earn_amount']       = $earn_amount;
                  $earning['platform']          = "API";
                  $migareference->saveEarning($earning);
                }elseif($status_data['standard_type']==3 && $report_item['reward_type']==2) { //Credits
                  $earning['app_id']           = $app_id;
                  $earning['user_id']          = $data['referral_user_id'];
                  $earning['amount']           = $earn_amount;
                  $earning['entry_type']       = 'C';
                  $earning['trsansection_by']  = $api_admin['user_id'];
                  $earning['user_type']        = 4;
                  $earning['prize_id']         = 0;
                  $earning['report_id']        = $report_item['migareference_report_id'];
                  $earning['trsansection_description'] ="Report #".$report_item['report_no'];
                  $migareference->saveLedger($earning);
                }
            $html = [
              'response'      => true,
              'description'  => __('Successfully report updated.'),
              'reportNo'     => $data['report_no'],
              'statusId'     => $new_status_id
            ];
          } catch (Exception $e) {
              $html = [
                'response'      => false,
                'description'  => __($e->getMessage()),
              ];
            }
            $this->_sendJson($html);
          
    }
    public function manageinputypevaluesignatureAction($app_id=0,$type=0,$ng_model="",$options="",$address_counter=0,$field_value="",$longitude="",$latitude="",$option_type=0,$option_default="",$country_id=0)
  {
    $extra_input_template="";
    $migareference  = new Migareference_Model_Migareference();
    if ($type==1) {
      $extra_input_template.=str_replace(",", "", $field_value);      
    } else if($type==2) {
      $extra_input_template.=str_replace(",", "", $field_value);      
    }else if($type==3) {
      $option_value=0;
      switch ($option_type) {
        case 0:
        $temp_options=explode('@',$options);        
        foreach ($temp_options as $key => $value) {
          $option_value++;          
          if ($option_value==$field_value) {            
            $extra_input_template.=str_replace(",", "", $value); 
          }
        }        
          break;
        case 1://Country List
          $geoCountries              = $migareference->getGeoCountries($app_id);
          // $df_opt=explode("@",$option_default);          
          foreach ($geoCountries as $key => $value) {
            if ($field_value==$value['migareference_geo_countries_id']) {
             $extra_input_template.=str_replace(",", "", __($value['country']));  
            }
          }          
          break;
        case 2:
        // $df_opt=explode("@",$option_default);
        $dataGeoConPro  = $migareference->getGeoCountryProvicnes($app_id,$country_id);        
        foreach ($dataGeoConPro as $key => $value) {
          if ($field_value==$value['migareference_geo_provinces_id']) {
            $extra_input_template.=str_replace(",", "", __($value['province']));  
          }
        }        
          break;
        default:
          // $extra_input_template.="<option value=''></option>";
          break;
      }
    }else if($type==4) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }else if($type==5) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }else if($type==6) {
      $extra_input_template.=str_replace(",", "", $field_value);            
    }
    return $extra_input_template;
  }
}
