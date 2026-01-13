<?php

class Migareference_Model_Db_Table_Phonebook extends Core_Model_Db_Table {

    protected $_name = 'migarefrence_phonebook';
    protected $_primary = 'migarefrence_phonebook_id';

    public function getReferrersMissingRelationshipNote($app_id = 0, $limit = 10)
    {
        $app_id = (int)$app_id;
        $limit = (int)$limit;

        $query = "SELECT ph.*, jobs.job_title, prof.profession_title
                  FROM migarefrence_phonebook AS ph
                  LEFT JOIN migareference_jobs AS jobs ON jobs.migareference_jobs_id = ph.job_id
                  LEFT JOIN migareference_professions AS prof ON prof.migareference_professions_id = ph.profession_id
                  WHERE ph.app_id = $app_id AND ph.type = 1 AND (ph.note IS NULL OR TRIM(ph.note) = '')
                  LIMIT $limit";

        return $this->_db->fetchAll($query);
    }

    // AI Matching
     /*
        Sample Prompt:
        Analyze the following CSV data and identify a suitable business partner for a "Consulente Aziendale". The partner should have a good network in the business and be known for high-quality referrals. Prioritize matches based on rating and consider notes on relationships and reciprocity for a comprehensive match.
        CSV Data:
        "email","job_title","note","reciprocity_notes","rating"
        "studio.npalmisano@gmail.com","Imprenditore","Nicola ama i cani e i gatti","Ricerca partner per espandere la propria rete di networking business relazionale Net Group, in special modo commercialisti o consulenti","3"
        "eugenio.bitelli@ayor-italia.it","Consulente Aziendale","Nostro cliente con Ayor, ma anche un collega molto presente. Ha un figlio di nome Andrea","Referenze sempre più interessanti e di qualità.","5"
        "coscar.work@gmail.com","Consulente Serv. Pagamento","Commercio e servizi HOBBIES:Ballo da sala HOBBY IN DETTAGLIO: N/A PARTECIPA AD EVENTI:Ogni trimestre","Presente e affidabile, è anche Referral director","4"
        "cavalieri.62@gmail.com","Consulente Aziendale","NULL","E' un segnalatore entrato grazie a Xriba. Propositivo, agente di commercio, già molto attivo! Ha voglia di imparare ed è curioso tanto che ci ha già fatto una buonissima referenza.","5"
        "Pwevag@gmail.com","Avvocato","Conosce fondi investimento moda, salute. Collabora con Merging Acquisition M&A che investono in aziende da 1 milione in su Buona rete su Milano e in tutta Italia. Consulenza settore CFO e Legale alle aziende, collabora con aziende che offrono temporary management. Il suo ragazzo sta per iscriversi all'albo dei CFO. ","Aziende da 1 milione in su, che desiderano crescere, fare un exit, cambio generazionale, quotarsi in borsa. ","4"
        Recommend a partner whose profile best matches the criteria for a successful business partnership with a "Consulente Aziendale".
    */  
    /*
        *AI Matching Script
        *RULE 1: If Matching Script is Enabled
        *RULE 2: If Rating is >0
        *RULE 3: If Job is not empty or null
        *RULE 4: If Call is not already made (is_matching_call_made:pending,error,success,completed)
    */ 
    /*
        *Params
        *$app_id
        *$phonebook_id Phonebook id of the root referrer id
        *$calling_method Method name from where this method is called (Phonebook_update, Refresh_call_)
    */
    public function referrerMatching($app_id, $phonebook_id, $calling_method) {
        try {
            // Initialize retry count
            $retryCount = 0;
            $maxRetries = 1;
            $token_used = 0;
            $ai_response = null;
            $matches = [];
            $errorOccurred = false;
            $log = [];
    
            $openaiConfig = new Migareference_Model_OpenaiConfig();
            $migareference = new Migareference_Model_Db_Table_Migareference();
            $phonebook = new Migareference_Model_Db_Table_Phonebook();
    
            $openai_config = $openaiConfig->findAll(['app_id' => $app_id])->toArray();
            $phonebook_item = $migareference->getSinglePhonebook($phonebook_id);
            $max_matches = $openai_config[0]['max_matches'];
            $grace_days  = $openai_config[0]['grace_period_matches'];
            $exclude_list=[];//Temporary list to store excluded referrers
            $network_exluded_referrer_list = $this->calculateNetworkGracePeriod($app_id,$max_matches,$grace_days);
    
            // Ensure we can proceed with the API call
            if (count($openai_config) && $openai_config[0]['is_matching_api_enabled'] == 1 && $phonebook_item[0]['rating'] > 0 && $phonebook_item[0]['job_id']) {
                $referrer_id=$phonebook_item[0]['customer_id'];
                // Get Referrer Agents
                $referrer_agents = $migareference->getSponsorList($app_id, $referrer_id);
                $agent_show_full_phonebook=false;
                $agent_id=0;
                foreach ($referrer_agents as $key => $value) {
                    if ($value['full_phonebook']==1) {
                        $agent_show_full_phonebook=true;
                        $agent_id=$value['agent_id'];
                        break;
                    }
                }
                if ($agent_show_full_phonebook) {
                    $profiled_referrers = $migareference->getProfiledAgentReferrers($app_id, $phonebook_id,$agent_id);
                }else {
                    $profiled_referrers = $migareference->getProfiledReferrers($app_id, $phonebook_id);
                }    
                // Prepare the prompt
                $prompt = 'We have a partner in our company who is doing the job. We want to introduce him to another partner of ours, analyze the list and give me five possible matches along with a brief. The fields provided are EMAIL to identify the partner, JOB field is the job of the partner, and STARS is a rating from 0 to 5. To choose the matching, give priority to those with a higher star rating.' . PHP_EOL;
                $prompt .= 'Partner Details:' . $phonebook_item[0]['email'] . ',' . $phonebook_item[0]['job_title'] . ',' . $phonebook_item[0]['note'] . ',' . $phonebook_item[0]['reciprocity_notes'] . ',' . $phonebook_item[0]['rating'] . PHP_EOL;
                $prompt .= 'Other Partners CSV' . PHP_EOL;
                $csvString = '"email","job_title","note","reciprocity_notes","rating"' . PHP_EOL;
                
                foreach ($profiled_referrers as $key => $value) {
                    // Exlusion criteria
                    // RULE 1:(Per Referrer) if the referrer is exist for current referrer with any status we will exlude that referrer 
                    // RULE 2: (Global Rule) Grace Period (Grace Period is the logic where we will not match more than X times a referrer within Y days)
                    $network_exist=$this->isNetworkExist($app_id, $phonebook_item[0]['customer_id'], $value['customer_id']);
                    $is_network_exluded_referrer = array_search($value['customer_id'], array_column($network_exluded_referrer_list, 'network_referrer_id'));                    
                    if (!COUNT($network_exist) && $is_network_exluded_referrer===false) {                        
                        $csvString .= '"' . $value['email'] . '","' . $value['job_title'] . '","' . $value['note'] . '","' . $value['reciprocity_notes'] . '","' . $value['rating'] . '"' . PHP_EOL;
                    }else {
                        $exclude_list[]=$value['customer_id'];
                    }
                }
    
                $prompt .= $csvString;
                $prompt .= "response should be in the given formate as below along with partner original emails not the dummy emails/n:
                    1. Email:xxx@gmail.com,Brief:about why you select this matchi/n
                    2. Email:xxx@gmail.com,Brief:about why you select this matchi/n
                    3.. Email:xxx@gmail.com,Brief:about why you select this matchi/n
                    ...".PHP_EOL;
                //Specify that their should not any additional information in response becuase we have to extract data from the patters
                $prompt.="don't provide any additional information in response"." "."and Respond in ".$openai_config[0]['matching_preffered_lan'];
                
                do {
                    // OpenAI API Call
                    // $data = [
                    //     "model" => "gpt-4o-mini",
                    //     "temperature" => (float)$openai_config[0]['openai_temperature'],                        
                    //     "top_p" => 1,
                    //     "prompt" => $prompt
                    // ];
                    // OpenAI API Call
                    $data = [
                        "model" => $openai_config[0]['matching_ai_model'],  // Correct model name
                        "temperature" => (float)$openai_config[0]['openai_temperature'],
                        "top_p" => 1,
                        "messages" => [  // Chat model expects 'messages' instead of 'prompt'
                            [
                                "role" => "system",
                                "content" => "You are a helpful assistant."
                            ],
                            [
                                "role" => "user",
                                "content" => $prompt  // User's prompt goes here
                            ]
                        ]
                    ];

                    // added by MALIK Start
                    $api_key = $openai_config[0]['openai_apikey'];
                    $api_url = 'https://api.openai.com/v1/chat/completions';
                    if ($openai_config[0]['gpt_api'] == 'perplexity') {
                        $api_url = 'https://api.perplexity.ai/chat/completions';
                        $api_key = $openai_config[0]['perplexity_apikey'];
                    }
                    // added by MALIK End
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $api_url, //updated by Malik at 2023-10-03
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($data),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: ' . 'Bearer ' . $api_key
                        ),
                    ));
    
                    $ai_response = curl_exec($curl);
                    $curl_error = curl_error($curl);  // Capture any cURL errors
                    curl_close($curl);
    
                    if ($curl_error) {
                        // Log the API error
                        $log = [
                            'app_id' => $app_id,
                            'referrer_id' => $phonebook_item[0]['customer_id'],
                            'calling_method' => $calling_method,
                            'response_type' => 'error',
                            'prompt' => $prompt,
                            'response' => $curl_error
                        ];
                        $this->addLog($log);
                        $retryCount++;
                        continue; // Retry if there's an API error
                    }
    
                    // Decode the API response
                    $ai_response = json_decode($ai_response, true);
                    $token_used = $ai_response['usage']['total_tokens'];
                    $matches = [];
    
                    // Check if the response contains matches
                    if (isset($ai_response['choices'][0]['message']['content'])) {
                        $text = $ai_response['choices'][0]['message']['content'];

                        // Truncate the initial text before the first match (starting with "1. Email:")
                        $start_pos = strpos($text, '1. Email:');

                        // Check if the position is found, and then slice the text
                        if ($start_pos !== false) {
                            $text = substr($text, $start_pos);
                        }    
                        // Use regex to extract matches (email and brief)
                        // preg_match_all('/\d+\.\s*Email:\s*([^,]+),\s*Brief:\s*(.*?)(?=\n\d+\.|$)/s', $text, $matches);
                        preg_match_all('/\s*\d+\.\s*Email:\s*([^,]+),\s*Brief:\s*(.*?)(?=\n\s*\d+\.|$)/s', $text, $matches);
                    }
    
                    // Check if we have valid matches
                    $email='';//only for debugging
                    if (!empty($matches[1])) {
                        //  DISCARD ALL MATCHING RECORDS
                        $this->discardAllMatching($app_id, $phonebook_item[0]['customer_id']);
                        foreach ($matches[1] as $index => $email) {
                            $brief = $matches[2][$index];
                            $email = trim($email);
                            $customer = $migareference->getSingleuserByEmail($app_id, $email);
    
                            // Prepare data for insertion
                            if (!empty($customer)) {
                                $matching_record = [
                                    'app_id' => $app_id,
                                    'referrer_id' => $phonebook_item[0]['customer_id'],
                                    'network_referrer_id' => $customer[0]['customer_id'],
                                    'matching_description' => $brief
                                ];
        
                                $this->addMatching($matching_record);
                            }else {
                                // Log the error
                                $log = [
                                    'app_id' => $app_id,
                                    'referrer_id' => $phonebook_item[0]['customer_id'],
                                    'calling_method' => $calling_method,
                                    'token_used' => $token_used,
                                    'response_type' => 'error',
                                    'prompt' => $prompt,
                                    'response' => 'No customer found with email: ' . $email. '@'.$prompt
                                ];
                                // $this->addLog($log);
                            }
                        }
                        // Log successful match
                        $log = [
                            'app_id' => $app_id,
                            'referrer_id' => $phonebook_item[0]['customer_id'],
                            'calling_method' => $calling_method,
                            'token_used' => $token_used,
                            'response_type' => 'success',
                            'prompt' => $prompt,
                            'response' => $ai_response['choices'][0]['message']['content'],
                        ];                        
                        break; // Exit retry loop if we get valid matches
                    } else {
                        // No matches found, increment retry count
                        $retryCount++;
                        $log = [
                            'app_id' => $app_id,
                            'referrer_id' => $phonebook_item[0]['customer_id'],
                            'calling_method' => $calling_method,
                            'response_type' => 'no matches',
                            'prompt' => $prompt,
                            'response' => $ai_response['choices'][0]['message']['content']
                        ];
                    }
                } while ($retryCount < $maxRetries && empty($matches));
                $this->addLog($log);  // Log after retries or success
            }
    
            // Return the payload
            $payload = [
                "success" => !$errorOccurred,
                "app_id" => $app_id,
                "prompt" => $prompt,
                "ai_response" => $ai_response,
                "matching_record" => isset($matching_record) ? $matching_record : [],
                "matches" => $matches,
                "profiled_referrers" => $profiled_referrers,
                "exclude_list" => $exclude_list,
            ];
    
        } catch (\Exception $e) {
            // Log the exception
            $log = [
                'app_id' => $app_id,
                'referrer_id' => $phonebook_item[0]['customer_id'],
                'calling_method' => $calling_method,
                'response_type' => 'error',
                'response' => $e->getMessage()." ".$email
            ];
            $this->addLog($log);
    
            // Return error payload
            $payload = [
                'error' => true,
                'message' => $e->getMessage(), 
                "prompt" => $prompt,
                "ai_response" => $ai_response,               
                "matches" => $matches,               
                "email" => $email,               
            ];
        }
    
        return $payload;
    }
    
    
  public function calculateNetworkGracePeriod($app_id=0,$max_matches=0,$grace_days=0){
    $query_option = "SELECT 
    migareference_matching_network.network_referrer_id,
    COUNT(migareference_matching_network.network_referrer_id) AS total_matches,
    DATEDIFF(NOW(),migareference_matching_network.updated_at) AS days_diff
    FROM `migareference_matching_network`
    WHERE migareference_matching_network.app_id=$app_id AND migareference_matching_network.status!='discard'
    GROUP BY migareference_matching_network.network_referrer_id
    HAVING total_matches>=$max_matches AND days_diff<=$grace_days";
    return $this->_db->fetchAll($query_option);
    
  }
  public function isNetworkExist($app_id,$referrer_id,$network_referrer_id){
    $query_option = "SELECT 
    * FROM `migareference_matching_network`
    WHERE app_id=$app_id AND referrer_id=$referrer_id AND network_referrer_id=$network_referrer_id";
    return $this->_db->fetchAll($query_option);
    
  }
  public function addMatching($matching_record=[]){
    return  $this->_db->insert("migareference_matching_network", $matching_record);
  }
  public function addLog($log=[]){
    return  $this->_db->insert("migareference_matching_logs", $log);
  }
  public function discardAllMatching($app_id=0,$referrer_id=0){
    $updated_at = date('Y-m-d H:i:s');
    $this->_db->update("migareference_matching_network", ["status"=>"discard",'updated_at'=>$updated_at], "app_id=$app_id AND referrer_id=$referrer_id");    
  }
  public function unmatchcustomer($app_id=0,$matching_network_id=0){
    return  $this->_db->delete("migareference_matching_network", "app_id=$app_id AND migareference_matching_network_id=$matching_network_id");
  }
  public function removecustomer($app_id=0,$matching_network_id=0){
    return  $this->_db->delete("migareference_matching_network", "app_id=$app_id AND migareference_matching_network_id=$matching_network_id");
  }
  public function matchCustomer($app_id=0,$matching_network_id=0){
    return  $this->_db->update("migareference_matching_network", ["status"=>"matched"], "app_id=$app_id AND migareference_matching_network_id=$matching_network_id");
  }
  public function discardCustomer($app_id=0,$matching_network_id=0){
    $updated_at = date('Y-m-d H:i:s');
    return  $this->_db->update("migareference_matching_network", ["status"=>"discard",'updated_at'=>$updated_at], "app_id=$app_id AND migareference_matching_network_id=$matching_network_id");
  }
  public function availableMatching($app_id=0,$referrer_id=0){
    $query_option = "SELECT 
    * FROM `migareference_matching_network`
    JOIN customer ON customer.customer_id = migareference_matching_network.network_referrer_id 
    JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=customer.customer_id
    JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
    WHERE migareference_matching_network.app_id=$app_id AND referrer_id=$referrer_id AND migareference_matching_network.status='available'
    GROUP BY migareference_matching_network.network_referrer_id";
    return $this->_db->fetchAll($query_option);
  }
  public function matchedMatching($app_id=0,$referrer_id=0){
    $query_option = "SELECT 
    * FROM `migareference_matching_network`
    JOIN customer ON customer.customer_id = migareference_matching_network.network_referrer_id 
    JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=customer.customer_id
    JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
    WHERE migareference_matching_network.app_id=$app_id AND referrer_id=$referrer_id AND migareference_matching_network.status='matched'
    GROUP BY migareference_matching_network.network_referrer_id";
    return $this->_db->fetchAll($query_option);
  }
  public function discardMatching($app_id=0,$referrer_id=0){
    $query_option = "SELECT *,migareference_matching_network.updated_at AS matching_updated_at
    FROM `migareference_matching_network`
    JOIN customer ON customer.customer_id = migareference_matching_network.network_referrer_id 
    JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=customer.customer_id
    JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
    WHERE migareference_matching_network.app_id=$app_id AND referrer_id=$referrer_id AND migareference_matching_network.status='discard'
    GROUP BY migareference_matching_network.network_referrer_id";
    return $this->_db->fetchAll($query_option);
  }
  public function lastMatchingCall($app_id=0,$referrer_id=0){
    $query_option = "SELECT 
    * FROM `migareference_matching_logs`    
    WHERE app_id=$app_id AND referrer_id=$referrer_id AND response_type='success' ORDER BY created_at DESC LIMIT 1";
    return $this->_db->fetchAll($query_option);
  }
}
