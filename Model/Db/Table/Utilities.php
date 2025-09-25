<?php
class Migareference_Model_Db_Table_Utilities extends Core_Model_Db_Table
{
     //return array['agent_id','agent_name','agent_email'] 
    //*{Sort by agent Surname ASC, Add extra default entry of 'i dont know' if enabled}	
    public function getAllAgents($app_id = 0)
    {
        $query_option = "SELECT 
        customer.customer_id AS agent_id,
        CONCAT(customer.lastname, ' ', customer.firstname) AS agent_name,
        customer.email
        FROM 
            `migareference_app_agents`
        JOIN 
            customer ON customer.customer_id = migareference_app_agents.user_id        
        WHERE 
            migareference_app_agents.app_id = $app_id
        GROUP BY 
            customer.customer_id
        ORDER BY 
            customer.lastname;
        ";
        $res_option   = $this->_db->fetchAll($query_option);
        // Prepend the default element
        $pre_report   = (new Migareference_Model_Db_Table_Migareference())->preReportsettigns($app_id);
        if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
            $default_element = [
                'agent_id' => 0,
                'agent_name' => __("I dont know"),
                'agent_email' => ''
            ];
            array_unshift($res_option, $default_element);
        }
        return $res_option;
    }
     //return array['agent_id','agent_name','agent_email'] 
    //*{Sort by agent Surname ASC, Add extra default entry of 'i dont know' if enabled}	
    public function getTypeOneAgents($app_id = 0)
    {
        $query_option = "SELECT 
        customer.customer_id AS agent_id,
        CONCAT(customer.firstname, ' ', customer.lastname) AS agent_name,
        customer.email
        FROM 
            `migareference_app_agents`
        JOIN 
            customer ON customer.customer_id = migareference_app_agents.user_id  AND migareference_app_agents.agent_type=1      
        WHERE 
            migareference_app_agents.app_id = $app_id
        GROUP BY 
            customer.customer_id
        ORDER BY 
            customer.lastname;
        ";
        $res_option   = $this->_db->fetchAll($query_option);
        // Prepend the default element
        $pre_report   = (new Migareference_Model_Db_Table_Migareference())->preReportsettigns($app_id);
        if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
            $default_element = [
                'agent_id' => 0,
                'agent_name' => __("I dont know"),
                'agent_email' => ''
            ];
            array_unshift($res_option, $default_element);
        }
        return $res_option;
    }
     //return array['agent_id','agent_name','agent_email'] 
    //*{Sort by agent Surname ASC, Add extra default entry of 'i dont know' if enabled}	
    public function getTypeTwoAgents($app_id = 0)
    {
        $query_option = "SELECT 
        customer.customer_id AS agent_id,
        CONCAT(customer.firstname, ' ', customer.lastname) AS agent_name,
        customer.email
        FROM 
            `migareference_app_agents`
        JOIN 
            customer ON customer.customer_id = migareference_app_agents.user_id  AND migareference_app_agents.agent_type=2      
        WHERE 
            migareference_app_agents.app_id = $app_id
        GROUP BY 
            customer.customer_id
        ORDER BY 
            customer.lastname;
        ";
        $res_option   = $this->_db->fetchAll($query_option);
        // Prepend the default element
        $pre_report   = (new Migareference_Model_Db_Table_Migareference())->preReportsettigns($app_id);
        if ($pre_report[0]['enable_mandatory_agent_selection']==2) {
            $default_element = [
                'agent_id' => 0,
                'agent_name' => __("I dont know"),
                'agent_email' => ''
            ];
            array_unshift($res_option, $default_element);
        }
        return $res_option;
    }
    public function getAllJobs($app_id=0,$add_prepend_new_job=0)
    {
      $query_option = "SELECT migareference_jobs_id AS job_id,job_title
                       FROM `migareference_jobs`
                       WHERE `app_id`=$app_id 
                       ORDER  BY `migareference_jobs`.`job_title`  ASC";
      $res_option   = $this->_db->fetchAll($query_option);
      // Prepend the default element      
          $default_element = [
            'job_id' => 0,
            'job_title'=> __("Non classifiable")
        ];
        array_unshift($res_option, $default_element);
        if ($add_prepend_new_job) {
            $default_element=[
                'job_id'=> -1,
                'job_title'=> __("Add New Job"),
              ];
        array_unshift($res_option, $default_element);
        }
      return $res_option;
    }
    public function getAllProfessions($app_id=0)
    {
      $query_option = "SELECT migareference_professions_id AS profession_id,profession_title
                       FROM `migareference_professions`
                       WHERE `app_id`=$app_id 
                       ORDER  BY `profession_title`  ASC";
        $res_option   = $this->_db->fetchAll($query_option);
      // Append the default element      
          $default_element = [
            'profession_id' => 0,
            'profession_title'=> __("N/A")
        ];
        array_push($res_option, $default_element);
      return $res_option;
    }
    /*
    * Detect changes in the referrer data
    * Possible DB tables to attempt to detect changes in: Customer, migareference_invoice_settings, migareference_phonebook
    */
    public function detectReferrerChanges($currentData=[], $newData=[],$app_id=0) {
        $properties = [
            'invoice_name',
            'invoice_surname',
            'invoice_mobile',
            'rating',
            'email',
            'birthdate',
            'job_id',
            'profession_id',
            'ref_consent_timestmp',
            'sponsor_one_id',
            'sponsor_one_email',
            'sponsor_one_firstname',
            'sponsor_one_lastname',
            'note',
            'reciprocity_notes'
        ];
        $chnages_detect=false; //only for test purpose
        foreach ($properties as $property) {
            if ($currentData[0][$property] !== $newData[0][$property]) {
                $chnages_detect=true; // Change detected
                $pre_settings   = (new Migareference_Model_Db_Table_Migareference())->preReportsettigns($app_id);                                      
                if ($pre_settings[0]['enable_new_ref_webhooks']==1 && $pre_settings[0]['enable_new_ref_webhooks_update']==1) {                                                            
                    $webhook_url = (new Migareference_Model_Db_Table_Webhook())->referrerWebhookParamsTemplate($app_id,$newData[0]['user_id'],'update');
                    $webhook_log_params['app_id']  = $app_id;                  
                    $webhook_log_params['user_id'] = $newData[0]['user_id'];    
                    $webhook_log_params['type']    = 'referrer';              
                    (new Migareference_Model_Db_Table_Migareference())->triggerWebhook($webhook_url,$webhook_log_params);//*Update Referrer Webhook
                }
                break;
            }
        }
         return $webhook_url;
    }
}