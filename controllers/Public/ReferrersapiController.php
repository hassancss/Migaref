<?php                           
class Migareference_Public_ReferrersapiController extends Migareference_Controller_Default {
    public function referrerAction(){
    try {
      $reportapi            = new Migareference_Model_Reportapi();
      $migareference        = new Migareference_Model_Migareference();
      $utilities            = new Migareference_Model_Utilities();
      $default              = new Core_Model_Default();
      $base_url             = $default->getBaseUrl();
      $data                 = $this->getRequest()->getPost();   
      $app_link             = "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";  
      $invoice=[];       
      $data['token'] = trim($data['token']);
      if (empty($data['token']) || strlen($data['token'])!=35) {
        throw new Exception(__("Token Mismatchd"));
      }      
      $pre_report_settings=$reportapi->validateToken($data['token']);
      if (!count($pre_report_settings)) {
        throw new Exception(__("Token Mismatched"));
      }
      $app_id=$pre_report_settings[0]['app_id']; 
        // Name (Mandatory)
        if (empty($data['name']) || !preg_match('/^[a-zA-Z]+$/', $data['name'])) {
            throw new Exception(__("Name is missing or invalid. Please provide a valid name containing only letters."));
        }else{
            $invoice['invoice_name']=$data['name'];
        }

        // Surname (Mandatory)
        if (empty($data['surname']) || !preg_match('/^[a-zA-Z]+$/', $data['surname'])) {
            throw new Exception(__("Surname is missing or invalid. Please provide a valid surname containing only letters."));
        }else{
            $invoice['invoice_surname']=$data['surname'];
        }

        // Email (Mandatory)
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__("Email is missing or invalid. Please provide a valid email address."));
        }

      // Mobile (Optional, Default NULL)
        if (!empty($data['mobile'])) {
            // Check if mobile number starts with '+' or '00' and is between 10 and 14 characters long
            if (!preg_match('/^(00|\+)?\d{10,14}$/', $data['mobile'])) {
                throw new Exception(__("Mobile number format is invalid. Please provide a valid mobile number starting with '00' or '+', and containing 10 to 14 digits."));
            }else{
                $invoice['invoice_mobile']=$data['mobile'];
            }
        }else {
            $invoice['invoice_mobile']='00';
        }
        
        // Sponsor UID (Optional, Default 0) - Check if sponsor UID is provided and is numeric
        if (!empty($data['sponsor_uid'])) {
            if (!is_numeric($data['sponsor_uid'])) {
                throw new Exception(__("Sponsor UID must be a numeric value."));
            } else {
                // Check if the sponsor UID exists in the database
                $is_agent = $migareference->is_agent($app_id, $data['sponsor_uid']);
                if (count($is_agent)) {
                    $invoice['sponsor_id'] = $data['sponsor_uid']; // Assign provided sponsor UID
                } else {
                    throw new Exception(__("Sponsor UID does not exist or is not valid."));
                }
            }
        } else {
            $invoice['sponsor_id'] = 0; // Default value
        }


         // Rating (Optional, Default 1) - Validate if numeric and within the range of 1 to 5
        if (!empty($data['rating'])) {
            if (!is_numeric($data['rating']) || intval($data['rating']) < 1 || intval($data['rating']) > 5) {
                throw new Exception(__("Rating must be a numeric value between 1 and 5."));
            } else {
                $invoice['rating'] = $data['rating']; // Assign provided rating
            }
        } else {
            $invoice['rating'] = 1; // Default value
        }

                
       // Job UID (Optional, Default 0) - Check if job UID is provided and is numeric
        if (!empty($data['job_uid'])) {
            if (!is_numeric($data['job_uid'])) {
                throw new Exception(__("Job UID must be a numeric value."));
            } else {
                // Check if the job UID exists in the database
                $jobExists = $migareference->getsingejob($data['job_uid']);
                if (count($jobExists)) {
                    $invoice['job_id'] = $data['job_uid']; // Assign provided job UID
                } else {
                    throw new Exception(__("Job UID does not exist or is not valid."));
                }
            }
        } else {
            $invoice['job_id'] = 0; // Default value
        }
    
        
        // Sector UID (Optional, Default 0) - Check if sector UID is provided and is numeric
        if (!empty($data['sector_uid'])) {
            if (!is_numeric($data['sector_uid'])) {
                throw new Exception(__("Sector UID must be a numeric value."));
            } else {
                // Check if the sector UID exists in the database
                $sectorExists = $migareference->getsingeprofession($data['sector_uid']);
                if (!count($sectorExists)) {
                    throw new Exception(__("Sector UID does not exist or is not valid."));
                } else {
                    $invoice['profession_id'] = $data['sector_uid'];
                }
            }
        } else {
            $invoice['profession_id'] = 0;
        }
        
        // Birth Date (Optional, Default NULL)
        if (!empty($data['birth_date'])) {
            $timestamp = strtotime($data['birth_date']);
            if ($timestamp === false) {
                throw new Exception(__("Birth Date format is invalid. Please provide a valid date."));
            } else {
                $invoice['birth_date'] = $timestamp;
            }
        } else {
            $invoice['birth_date'] = 0;
        }
        // Country UID (Optional, Default NULL)
        if (!empty($data['country_uid'])) {
            if (!is_numeric($data['country_uid'])) {
                throw new Exception(__("Country UID must be a numeric value."));
            } else {
                // Check if the country UID exists in the database
                $countryExists = $migareference->getGeoCountry($data['country_uid'],$app_id);
                if (empty($countryExists)) {
                    throw new Exception(__("Country UID does not exist or is not valid."));
                } else {
                    $invoice['address_country_id'] = $data['country_uid'];
                }
            }
        } else {
            $invoice['address_country_id'] = 0;
        }

        // Province UID (Optional, Default NULL)
        if (!empty($data['province_uid'])) {
            if (!is_numeric($data['province_uid'])) {
                throw new Exception(__("Province UID must be a numeric value."));
            } else {
                // Check if the province UID exists in the database
                $provinceExists = $migareference->getGeoProvince($data['province_uid'],$app_id);
                if (empty($provinceExists)) {
                    throw new Exception(__("Province UID does not exist or is not valid."));
                } else {
                    $invoice['address_province_id'] = $data['province_uid'];
                }
            }
        } else {
            $invoice['address_province_id'] = 0;
        }
        
        // City (Optional, Default NULL)
        $invoice['address_city'] = !empty($data['city']) ? $data['city'] : null;

        // Street (Optional, Default NULL)
        $invoice['address_street'] = !empty($data['street']) ? $data['street'] : null;

        // Zip Code (Optional, Default NULL)
        $invoice['address_zipcode'] = !empty($data['zip_code']) ? $data['zip_code'] : null;

        // Relational Note (Optional, Default NULL)
        $invoice['note'] = !empty($data['relational_notes']) ? $data['relational_notes'] : null;
        // Apply SQL injection prevention if not null
        if (!empty($invoice['note']) && $utilities->containsSQLInjection($invoice['note'])) {
            throw new Exception(__("Invalid characters detected in relational note."));
        }

        // Reciprocity Note (Optional, Default NULL)
        $invoice['reciprocity_notes'] = !empty($data['reciprocity_notes']) ? $data['reciprocity_notes'] : null;
        // Apply SQL injection prevention if not null
        if (!empty($invoice['reciprocity_notes']) && $utilities->containsSQLInjection($invoice['reciprocity_notes'])) {
            throw new Exception(__("Invalid characters detected in reciprocity note."));
        }

        // Tax ID (Optional, Default auto 6 letters string)
        if (!empty($data['tax_id'])) {
            if (strlen($data['tax_id']) > 6) {
                throw new Exception(__("Tax ID must be a maximum of 6 characters."));
            } else {
                // Assign the provided tax ID to the invoice if it's not empty and within the length limit
                $invoice['tax_id'] = $data['tax_id'];
            }
        } else {
            // If tax ID is not provided, generate a random tax ID using the $utilities->randomTaxid() method
            $invoice['tax_id'] = $utilities->randomTaxid();
        }

        // ext_uid (Optional, Default empty, max 20 letters string)
        if (!empty($data['ext_uid'])) {
            if (strlen($data['ext_uid']) > 20) {
                throw new Exception(__("External UID must be a maximum of 20 characters."));
            } else {
                // Assign the provided tax ID to the invoice if it's not empty and within the length limit
                $invoice['ext_uid'] = $data['ext_uid'];
            }
        }

       // Blockchain Password (Optional, Default auto 10 characters string)
        if (!empty($data['blockchain_password'])) {
            if (strlen($data['blockchain_password']) > 10) {
                throw new Exception(__("Blockchain Password must be a maximum of 10 characters."));
            } else {
                // Assign the provided blockchain password to the invoice if it's not empty and within the length limit
                $invoice['blockchain_password'] = $data['blockchain_password'];
            }
        } else {
            // If blockchain password is not provided, generate a random password using the $utilities->randomPassword() method
            $invoice['blockchain_password'] = $utilities->randomPassword();
        }

        // First Password (Optional, Default auto 10 characters string)
        if (!empty($data['first_password'])) {
            if (strlen($data['first_password']) > 10) {
                throw new Exception(__("First Password must be a maximum of 10 characters."));
            } else {
                // Assign the provided first password to the invoice if it's not empty and within the length limit
                $invoice['first_password'] = $data['first_password'];
            }
        } else {
            // If first password is not provided, generate a random password using the $utilities->randomPassword() method
            $invoice['first_password'] = $utilities->randomPassword();
        }
        // GDPR (Optional, Default 0) - Boolean value
        if (isset($data['gdpr'])) {
            if ($data['gdpr'] ==1 || $data['gdpr'] ==0) {
                // If GDPR value is provided and is a valid boolean, assign it to the invoice
                $customer['privacy_policy'] = $data['gdpr'] ? 1 : 0;
            } else {
                // If GDPR value is provided but is not a valid boolean, throw an exception
                throw new Exception("GDPR value must be a valid boolean (1 or 0).");
            }
        } else {
            // If GDPR value is not provided, assign 0 as the default value for the privacy policy
            $customer['privacy_policy'] = 0;
        }

        // Check if customer found against the Referrer Email (Dperedate now we will check referrer_id)
        $referrerCustomer=$migareference->getSingleuserByEmail($app_id,$data['email']);
        $customer['firstname'] = $invoice['invoice_name'];
        $customer['lastname']= $invoice['invoice_surname'];                    
        $customer['mobile']= $invoice['invoice_mobile'];                    
        if (count($referrerCustomer)) {
            // Update Customer
            $user_id=$referrerCustomer[0]['customer_id'];                    
            $migareference->updateCustomerdob($user_id,$customer);
            $opreation=__("Update");
        }else {          
            // Add Customer
            $customer['app_id']  = $app_id;
            $customer['email']   = $data['email'];                        
            $customer['password']= sha1($data['first_password']);
            $user_id             = $migareference->createUser($customer);   
            $opreation=__("Create");     
        }

        $invoice_settings = $migareference->getpropertysettings($app_id,$user_id);
        if (!COUNT($invoice_settings)) { //Create Referrer
            $invoice['user_id']=$user_id;
            $invoice['app_id']=$app_id;
            $invoice['terms_accepted']=1;
            $invoice['special_terms_accepted']=1;
            $invoice['terms_artical_accepted']=1;
            $invoice['privacy_accepted']=1;
            $invoice['privacy_artical_accepted']=1;
            
            $inv_return=$migareference->savePropertysettings($invoice);            

            //if type is set and value is 1 then add agent
            if (isset($data['type']) && $data['type']==1) {
                $agent['app_id']  = $app_id;
                $agent['user_id'] = $user_id;
                $agent['agent_type'] =1;
                $migareference->saveAgent($agent);                  
            }

            // Send Welcome Email to referrer
            if ($pre_report_settings[0]['enable_welcome_email']==1
                && !empty($pre_report_settings[0]['referrer_wellcome_email_title'])
                && !empty($pre_report_settings[0]['referrer_wellcome_email_body']))
              {
              $notificationTags=$migareference->welcomeEmailTags();              
              $notificationStrings = [
                $invoice['invoice_name']." ".$invoice['invoice_surname'],
                $invoice['email'],
                $data['first_password'],
                "",
                $app_link                            
              ];
              $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_title']);
              $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_settings[0]['referrer_wellcome_email_body']);
              $email_data['type']        = 2;//type 2 for wellcome log
              $migareference->sendMail($email_data,$app_id,$invoice['user_id']);
            }
        }
            $referrer_link = $base_url."/migareference/landingreport?app_id=".$app_id."&user_id=".$user_id."&agent_id=".$invoice['sponsor_id']."&report_by=0&type=1&report_custom_type=1";
            $short_referrer_link=$utilities->shortLink($referrer_link);
            $payload = [
                'response'      => true,
                'description'  => __('Successfully Referrer created.'),         
                'referrerUid'=>$user_id,
                'referrerLink'=>$short_referrer_link,
                'referrerPassword'=>$data['first_password'],
                'opreationType'=>$opreation
            ];
      } catch (\Exception $e) {
          $payload = [
              'response'      => false,
              'description'  =>$e->getMessage(),                                                                
          ];
      }
      $this->_sendJson($payload);
    }    
    public function signupAction(){
        try {                
                $migareference = new Migareference_Model_Migareference();
                $utilities     = new Migareference_Model_Utilities();
                $data          = Siberian_Json::decode($this->getRequest()->getRawBody());                
                $base_url      = (new Core_Model_Default())->getBaseUrl();
                $app_id        = $this->getApplication()->getId();
                $pre_report    = $migareference->preReportsettigns($app_id);
                $user_account_settings = $migareference->useraccountSettings($app_id);

                $user_account_settings = json_decode($user_account_settings[0]['settings']);                
                $data['app_id']= $app_id;
                $errors        = "";
                $invoice       = [];    
                $temp     = [];           
                // Name (Mandatory)            
                if (empty($data['name'])) {
                   $errors .= __('Name cannot be empty.') . "<br/>";
                }
                // Surname (Mandatory)
                if (empty($data['surname'])) {
                   $errors .= __('Surname cannot be empty.') . "<br/>";
                }
                // Email (Mandatory)
                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {                    
                    $errors .= __('Email is missing or invalid.') . "<br/>";
                }
                // Mobile (Mandatory) + Check if mobile number starts with '+' or '00' and is between 10 and 14 characters long                                                           
                if (empty($data['mobile']) || !preg_match('/^(00|\+)?\d{10,14}$/', $data['mobile'])) {                    
                    $errors .= __("Mobile number is empty or invalid. Please provide a valid mobile number starting with '00' or '+', and containing 10 to 14 digits.") . "<br/>";
                }
                // Password (Mandatory)
                if (empty($data['password'])) {                    
                    $errors .= __('Password cannot be empty.') . "<br/>";
                }
                // // Terms (Mandatory)(Sib)   
                // if (isset($data['commercial_consent_accepted']) && ($data['commercial_consent_accepted']==false || $data['commercial_consent_accepted']!=1)) {
                //     $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
                // }  
                // // Privacy (Mandatory)(Sib)
                // if (isset($data['privacy_accepted']) && ($data['privacy_accepted']==false || $data['privacy_accepted']!=1)) {
                //     $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
                // }                              
                // // Privacy (Mandatory)(Migareference)
                // if (isset($data['ref_privacy_accepted']) && ($data['ref_privacy_accepted']==false || $data['ref_privacy_accepted']!=1)) {
                //     $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
                // }                              
                // // Special Terms (Mandatory)(Migareference)
                // if (isset($data['ref_special_terms_accepted']) && ($data['ref_special_terms_accepted']==false || $data['ref_special_terms_accepted']!=1)) {
                //     $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
                // }                              
                // // Terms & Condition (Mandatory)(Migareference)
                // if (isset($data['ref_tc_accepted']) && ($data['ref_tc_accepted']==false || $data['ref_tc_accepted']!=1)) {
                //     $errors .= __('You must Accept Term conditions to save settings.') . "<br/>";
                // }                              
              if (!empty($errors)) {
                throw new Exception($errors);
              }else{ //No Error Found
                //*** Create or Update Siberian User ***/
                // Check if customer found against the Referrer Email
                $referrerCustomer=$migareference->getSingleuserByEmail($app_id,$data['email']);
                $customer['firstname'] = $data['name'];
                $customer['lastname']= $data['surname'];                    
                $customer['mobile']= $data['mobile'];                    
                $customer['privacy_policy']= $data['privacy_accepted'];                    
                //DOB (Optional, Default 0)
                $birth_date = 0;
                if (!empty($data['birth_day']) && !empty($data['birth_month']) && !empty($data['birth_year'])) {
                    $b_date=$data['birth_day']."-".$data['birth_month']."-".$data['birth_year'];
                    $birth_date = date('Y-m-d',strtotime($b_date));
                } 
                $customer['birthdate'] = strtotime($birth_date);
                if (count($referrerCustomer)) {
                    // Update Customer
                    $user_id=$referrerCustomer[0]['customer_id'];                    
                    $temp[]=$customer;
                    $temp[]="Customer Updated";
                    $migareference->updateCustomerdob($user_id,$customer);                    
                }else {          
                    // Add Customer
                    $customer['app_id']  = $app_id;
                    $customer['email']   = $data['email'];                                            
                    $customer['password']= sha1($data['password']);
                    $temp[]=$customer;
                    $temp[]="Customer Created";
                    $user_id             = $migareference->createUser($customer);                         
                }
                //*** Create or Update Referrer + Phonebook ***/
                // Generate a random password for the blockchain password
                $invoice['blockchain_password'] = $utilities->randomPassword();
                $invoice['invoice_name'] = $data['name'];
                $invoice['invoice_surname'] = $data['surname'];                
                $invoice['invoice_mobile'] = $data['mobile'];
                $invoice['birth_date'] = $birth_date;                           
                // Tax ID Default ''
                $invoice['tax_id'] = (isset($data['tax_id']) && !empty($data['tax_id'])) ? $data['tax_id'] : '';              
                // Job|Profession (Optional, Default 0) 
                $invoice['job_id'] = (!empty($data['job_id'])) ? $data['job_id']['job_id'] : 0;              
                // Sector (Optional, Default 0) 
                $invoice['profession_id'] = (!empty($data['profession_id'])) ? $data['profession_id']['profession_id'] : 0;               
                //Vat ID (Optional, Default "")
                $invoice['vat_id'] = (!empty($data['vat_id'])) ? $data['vat_id'] : "";               
                $invoice_settings = $migareference->getpropertysettings($app_id,$user_id);
                if (!COUNT($invoice_settings)) { //Create Referrer
                    $temp[]="New Referrer Created";
                    $invoice['user_id']=$user_id;
                    $invoice['first_password']=$customer['password'];
                    $invoice['app_id']=$app_id;                    
                    $invoice['referrer_source']=5;//5 for Migalock registration                    
                    $invoice['terms_accepted']=$data['ref_tc_accepted'];
                    $invoice['special_terms_accepted']=$data['ref_special_terms_accepted'];
                    $invoice['terms_artical_accepted']=$data['ref_tc_accepted'];                    
                    $invoice['privacy_accepted']=$data['privacy_accepted'];
                    $invoice['privacy_artical_accepted']=$data['commercial_consent_accepted'];
                    //Agnets Management:
                    if (isset($data['province_id']) && !empty($data['province_id'])) {
                        $invoice['address_province_id']=$data['province_id']['province_id'];                                                
                    }
                    $invoice['sponsor_id']=0; //Default
                    $invoice['partner_sponsor_id']=0; //Default
                    if ($pre_report[0]['sponsor_type']==1 && !empty($data['sponsor_id'])) {  //sponsor type is Standard                        
                        $temp[]="Standard type";
                        $invoice['sponsor_id'] = $data['sponsor_id']['id'];                    
                    }elseif ($pre_report[0]['sponsor_type']==2 && isset($data['province_id'])) { //sponsor type is Geo Location                                                
                        $temp[]="Geo Location";
                        $agent_provonces=$migareference->agentMultiGeoProvince($data['app_id'],$data['province_id']['province_id']);
                        $agent_count=COUNT($agent_provonces);                                   
                        if($agent_count==1){
                            $temp[]="Single Agent".$agent_provonces[0]['user_id'];
                            $invoice['sponsor_id']=$agent_provonces[0]['user_id'];                        
                        }else if($agent_count==2){
                            $temp[]="Two Agents".$agent_provonces[0]['user_id']." ".$agent_provonces[1]['user_id'];
                            $invoice['sponsor_id']=$agent_provonces[0]['user_id'];
                            $invoice['partner_sponsor_id']=$agent_provonces[1]['user_id'];
                        }                                                
                    }
                    //End Agents Managements
                    $temp[]=$invoice;
                    $inv_return=$migareference->savePropertysettings($invoice);
                    // Manage Agents
                    
                    // Send Welcome Email to referrer
                    if ($pre_report[0]['enable_welcome_email']==1
                    && !empty($pre_report[0]['referrer_wellcome_email_title'])
                    && !empty($pre_report[0]['referrer_wellcome_email_body']))
                    {
                        $temp[]="Welcome Email Sent";
                        $notificationTags=$migareference->welcomeEmailTags();
                        $customer=$migareference->getSingleuser($app_id,$user_id);
                        $app_link= "<a href='" . $base_url . "/application/device/check/app_id/" . $app_id . "'>" . __('App Link') . "</a>";
                        if (isset($invoice['sponsor_id']) && !empty($invoice['sponsor_id']) ) {
                            $agent_user=$migareference->getSingleuser($app_id,$invoice['sponsor_id']);
                        }
                        $notificationStrings = [
                        $customer[0]['firstname']." ".$customer[0]['lastname'],
                        $customer[0]['email'],
                        $invoice['first_password'],
                        $agent_user[0]['firstname']." ".$agent_user[0]['lastname'],
                        $app_link
                        ];
                        $email_data['email_title'] = str_replace($notificationTags, $notificationStrings,$pre_report[0]['referrer_wellcome_email_title']);
                        $email_data['email_text']  = str_replace($notificationTags, $notificationStrings,$pre_report[0]['referrer_wellcome_email_body']);
                        $email_data['type']        = 2;//type 2 for wellcome log
                        $migareference->sendMail($email_data,$app_id,$user_id);
                    } 
                }else {   
                    $temp[]="Referrer Updated";                                                                   
                    /* Update Phonebook if their is any change in phonebook
                    * job_id
                    * profession_id
                    */
                    $phonne_book['job_id'] = $invoice['job_id'];
                    $phonne_book['profession_id'] = $invoice['profession_id'];                    
                    $phobook_item=$migareference->getInvoicePhonebook($invoice_settings[0]['migareference_invoice_settings_id']);
                    if (count($phobook_item)) {                                    
                      $phone_return=$migareference->update_phonebook($phonne_book,$phobook_item[0]['migarefrence_phonebook_id'],$chnage_by,1);//Also save log if their is change in Rating,Job,Notes                  
                    }                      
                    // Manage Referrer Agetns
                    $invoice_item=$migareference->getReferrerByKey($invoice_settings[0]['migareference_invoice_settings_id']);//get prev item before update                                        
                    $migareference->deleteSponsor($user_id); //Delete all previous agents                       
                    $referrer_agent['app_id']=$app_id;
                    $referrer_agent['referrer_id']=$user_id;
                    $referrer_agent['created_at']=date('Y-m-d H:i:s');
                    $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id']['id'] : 0 ;
                    if ($referrer_agent['agent_id']!=0) {
                        $migareference->addSponsor($referrer_agent);
                    } 
                //     if ($pre_report[0]['sponsor_type']==2) {  //sponsor type is Geo Location                  
                //       if ($invoice_item[0]['address_province_id']!=$data['address_province']) {                                                                              
                //         $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                //         if ($referrer_agent['agent_id']!=0) {
                //           $migareference->addSponsor($referrer_agent);
                //         }                
                //         $referrer_agent['agent_id']= (isset($data['partner_sponsor_id'])) ? $data['partner_sponsor_id'] : 0 ;
                //         if ($referrer_agent['agent_id']!=0) {
                //           $migareference->addSponsor($referrer_agent);        
                //         }                          
                //       }                        
                //     }else if($pre_report[0]['sponsor_type']==1){ //sponsor type is Standard                   
                //       $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
                //         if ($referrer_agent['agent_id']!=0) {
                //           $migareference->addSponsor($referrer_agent);
                //         } 
                //     }
                    //  Update Referrer Record            
                    $migareference->updatePropertysettings($invoice_data,$invoice_settings[0]['migareference_invoice_settings_id']);    
                    // Trigger webhook if their is change in Referrer
                    $referrer_new_data=$migareference->getpropertysettings($app_id,$user_id);                                        
                    $changes_detect=(new Migareference_Model_Utilities())->detectReferrerChanges($invoice_settings,$referrer_new_data,$app_id);//This will detect changes and trigger webhook if changes found                                                                                 
                  }
              }              
              
              $payload = [
                  'success' => true,
                  'message' => __("Settings Successfully saved."),                            
                  'data'    => $data,
                  'temp'    => $temp
                ];
              } catch (Exception $e) {
                $payload = [
                  'success' => false,
                  'message' => __($e->getMessage()),          
                  'data'    => $data,
                  'temp'    => $temp             
          ];
        }
        $this->_sendJson($payload);
      }
}
?>
