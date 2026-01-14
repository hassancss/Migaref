<?php
class Migareference_OpenaiController extends Application_Controller_Default{

    public function viewAction(){
        $application = $this->getApplication();
        $this->loadPartials();
    }
    public function saveconfigAction()
    {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $errors='';
                // updated by Malik Star on 2023-10-03
                if (empty($data['gpt_api'])) {
                    $errors .= __('Please select one of the API, OpenAi API or Perplexity API')."<br>";              
                }
                if ($data['gpt_api']=='perplexity') {
                    if (empty($data['perplexity_apikey'])) {
                        $errors .= __('Please enter the Perplexity API Key.')."<br>";                
                    }
                }
                if ($data['gpt_api']=='openai') {
                    if (empty($data['openai_apikey'])) {
                        $errors .= __('Please enter the OpenAI API Key.')."<br>";                
                    }
                }
                // if (empty($data['openai_apikey'])) {
                //     $errors .= __('Please enter the OpenAI API Key.')."<br>";                
                // } 
                // Updated by Malik end on 2023-10-03                                
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $openai = new Migareference_Model_OpenaiConfig();
                    $openai->setData($data)->save();
                }           
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
    }
    public function savecallscriptaiconfigAction()
    {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $errors=''; 
                if ($data['is_api_enabled']==1) {                    
                    if (empty($data['openai_temperature'])) {
                        $errors .= __('Please enter the OpenAI Temperature.')."<br>";                
                    }
                    if (empty($data['openai_token'])) {
                        $errors .= __('Please enter the OpenAI Token.')."<br>";                
                    }                
                    if (empty($data['user_prompt'])) {
                        $errors .= __('Please enter the User Prompt.')."<br>";                
                    }                               
                }               
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $openai = new Migareference_Model_OpenaiConfig();
                    $openai->setData($data)->save();
                }           
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
    }
    public function saveaffinityaiconfigAction()
    {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $errors='';
                // Copied from savecallscriptaiconfigAction and adapted for affinity scoring settings.
                if ((int) $data['affinity_enabled'] === 1) {
                    if (empty($data['affinity_model'])) {
                        $errors .= __('Please select the Affinity Model.')."<br>";
                    }
                    if ($data['affinity_temperature'] === '' || $data['affinity_temperature'] === null) {
                        $errors .= __('Please enter the Affinity Temperature.')."<br>";
                    } elseif (!is_numeric($data['affinity_temperature']) || $data['affinity_temperature'] < 0 || $data['affinity_temperature'] > 2) {
                        $errors .= __('Affinity Temperature must be between 0 and 2.')."<br>";
                    }
                    if ($data['affinity_max_tokens'] === '' || $data['affinity_max_tokens'] === null) {
                        $errors .= __('Please enter the Affinity Token Limit.')."<br>";
                    } elseif (!is_numeric($data['affinity_max_tokens']) || $data['affinity_max_tokens'] < 1 || $data['affinity_max_tokens'] > 4096) {
                        $errors .= __('Affinity Token Limit must be between 1 and 4096.')."<br>";
                    }
                    if (empty($data['affinity_user_prompt'])) {
                        $errors .= __('Please enter the Affinity Prompt.')."<br>";
                    }
                }
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $openai = new Migareference_Model_OpenaiConfig();
                    $openai->setData($data)->save();
                }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
    }
    public function savematchingaiconfigAction()
    {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $errors='';
                if ($data['is_matching_api_enabled']==1) {               
                    if (empty($data['max_matches'])) {
                        $errors .= __('Maximum Number of Matches can not be empty.')."<br>";                
                    }
                    if (empty($data['grace_period_matches'])) {
                        $errors .= __('Grace Period can not be empty.')."<br>";                          
                    }                                  
                }                               
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $openai = new Migareference_Model_OpenaiConfig();
                    $openai->setData($data)->save();
                }           
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
    }
    public function saverelationshipaiconfigAction()
    {
      if ($data = $this->getRequest()->getPost()) {
          try {
                $errors='';
                if (empty($data['relationship_note_prompt'])) {
                    $errors .= __('Please enter the Relationship Note Prompt.')."<br>";
                }
                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $openai = new Migareference_Model_OpenaiConfig();
                    $openai->setData($data)->save();
                }
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1
              ];
          }
          $this->_sendJson($html);
      }
    }
    public function testcallAction()
    {

          try {
                $app_id        = $this->getApplication()->getId();
                $openai_config = (new Migareference_Model_OpenaiConfig())->findAll(['app_id'=> $app_id])->toArray();        
                $api_key = $openai_config[0]['openai_apikey'];

                $prompt = $openai_config[0]['system_prompt']." ".$openai_config[0]['user_prompt'];
                // Replace dynamic tags with actual values
                $prompt = str_replace("@@referrer_name@@", "John Doe", $prompt);
                $prompt = str_replace("@@job@@", "Sales Manager", $prompt);
                $prompt = str_replace("@@sector@@", "Retail", $prompt);
                $prompt = str_replace("@@relational_notes@@", "Interested in new marketing strategies", $prompt);
                $prompt = str_replace("@@reciprocity_notes@@", "Interested in new marketing strategies", $prompt);
                $prompt = str_replace("@@province@@", "Ontario", $prompt);
                $prompt = str_replace("@@birth_date@@", "01/15/1980", $prompt);                                               
                $prompt = str_replace("@@birthdate@@", "01/15/1980", $prompt);                                               
                $data = [
                    "model" => $openai_config[0]['call_script_ai_model'],  // Correct model name
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


               
                // "prompt": "Create a call script on a relational basis with the aim of immediately creating empathy with the referrer, with the final aim of reminding them to refer potential customers to us. For this purpose, I will provide some information about the person we are calling, which was previously collected:/nName: John Doe/nJob: Sales Manager/nSector: Retail Relational Notes: Interested in new marketing strategies Province: Ontario Date of Birth: 01/15/1980 Caller Name: Andrea Caller Company: Migastone /n The script should be informal and include personal touches related to the provided information. For example, mention local events in the province, refer to the birthday (January 15, 1980) if it is near, and other relationship-building topics. End the script with the phrase \'I\'m calling you because this month we are looking for...\'. The response should be a conversation between John and the caller, with correct references to the birthday date.Dont restart response and every call trun should be comprihensive trun",
                
            // added by MALIK Start
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
                CURLOPT_POSTFIELDS =>json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$api_key                    
                ),
                ));

                $response = curl_exec($curl);
                $curl_error = curl_error($curl);  // Capture any cURL errors
                curl_close($curl);                
                if ($curl_error) {
                    $response=$curl_error;
                    // Log the API error
                    $log = [
                        'app_id' => $app_id,
                        'referrer_id' => 0,//??Its a test call with Dummy data
                        'calling_method' => 'testcallscript_mab',                        
                        'response_type' => 'error',
                        'prompt' => $prompt,
                        'response' => $curl_error
                    ];                                                           
                }else {
                    $response=json_decode($response);
                    //replace \n with <br>
                    $token_used = $response->usage->total_tokens;
                    $response=str_replace("\n","<br>",$response->choices[0]->message->content);
                    $log = [
                        'app_id' => $app_id,
                        'referrer_id' => 0,//??Its a test call with Dummy data
                        'calling_method' => 'testcallscript_mab',                        
                        'token_used' => $token_used,
                        'response_type' => 'success',
                        'prompt' => $prompt,
                        'response' => $response
                    ]; 
                }                
                (new Migareference_Model_OpenaiConfig())->addCallScriptLog($log); 
              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'response'        => $response,
                'data'        => $data,
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
                'app_id'  => $app_id,
              ];
          }
          $this->_sendJson($html);    
    }
    public function testaffinitybatchAction()
    {
          try {
                // Copied from testcallAction and adapted for affinity batch scoring.
                $app_id        = $this->getApplication()->getId();
                $openai_config = (new Migareference_Model_OpenaiConfig())->findAll(['app_id'=> $app_id])->toArray();
                if (!count($openai_config)) {
                    throw new Exception(__('Missing OpenAI configuration.'));
                }
                $openai_config = $openai_config[0];

                $api_key = $openai_config['openai_apikey'];
                $api_url = 'https://api.openai.com/v1/chat/completions';
                $provider = 'openai';
                if ($openai_config['gpt_api'] == 'perplexity') {
                    $api_url = 'https://api.perplexity.ai/chat/completions';
                    $api_key = $openai_config['perplexity_apikey'];
                    $provider = 'perplexity';
                }
                if (empty($api_key)) {
                    throw new Exception(__('Missing OpenAI API key.'));
                }

                $affinity = new Migareference_Model_Affinity();
                $eligible_ids = $affinity->getEligibleReferrerIds($app_id);
                if (count($eligible_ids) < 4) {
                    throw new Exception(__('Need at least 4 eligible referrers to run a test batch.'));
                }

                $primary_id = (int) $eligible_ids[0];
                $compare_ids = array_slice($eligible_ids, 1, 3);
                $profile_rows = $affinity->getReferrerProfiles($app_id, array_merge([$primary_id], $compare_ids));
                if (!isset($profile_rows[$primary_id])) {
                    throw new Exception(__('Primary referrer not found.'));
                }

                $scoringService = new Migareference_Model_AffinityScoringService();
                $primaryProfile = $this->buildAffinityProfile($profile_rows[$primary_id]);
                $compareProfiles = [];
                foreach ($compare_ids as $compare_id) {
                    if (!isset($profile_rows[$compare_id])) {
                        continue;
                    }
                    $compareProfiles[$compare_id] = $this->buildAffinityProfile($profile_rows[$compare_id]);
                }
                if (!count($compareProfiles)) {
                    throw new Exception(__('No valid compare profiles found.'));
                }

                $settings = [
                    'model' => $openai_config['affinity_model'] ?? 'gpt-4o-mini',
                    'temperature' => $openai_config['affinity_temperature'] ?? 1,
                    'max_tokens' => $openai_config['affinity_max_tokens'] ?? 600,
                    'api_key' => $api_key,
                    'api_url' => $api_url,
                    'system_prompt' => $openai_config['affinity_system_prompt'] ?? '',
                    'prompt_template' => $openai_config['affinity_user_prompt'] ?? '',
                ];

                $scores = $scoringService->scoreBatch($primaryProfile, $compareProfiles, $settings);
                $response = [
                    'scores' => $scores,
                    'primary_id' => $primary_id,
                    'compare_ids' => array_values($compare_ids),
                ];

              $html = [
                'success'         => true,
                'message'         => __('Successfully data saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,
                'provider'        => $provider,
                'raw_response'    => $scoringService->getLastRawResponse(),
                'parsed_scores'   => $response,
                'http_status'     => $scoringService->getLastHttpStatus(),
                'last_error'      => $scoringService->getLastError(),
              ];
          } catch (Exception $e) {
              $html = [
                'error'          => true,
                'message'        => __($e->getMessage()),
                'message_button' => 1,
                'message_loader' => 1,
              ];
          }
          $this->_sendJson($html);
    }
    public function callscriptlogsAction() {        
            try {       
              $logs_collection =[];
              $app_id        = $this->getApplication()->getId();              
              $openaiConfig  = new Migareference_Model_OpenaiConfig();              
              $call_script_logs  = $openaiConfig->callScriptLog($app_id);
              foreach ($call_script_logs as $key => $value) {            
                    $view_action = '<button style="color:#2196f3;" class="btn" onclick="aiLogsModal(' . $value['migareference_callscript_logs_id'] . ', \'callscript\')">' . "<i class='fa fa-eye' rel=''></i>" . '</button>';
                    $logs_collection[]=[
                        $value['migareference_callscript_logs_id'],
                        $value['response_type'],                        
                        $value['token_used'],
                        date('d-m-Y H:i:s', strtotime($value['created_at'])),
                        $view_action
                    ];
              }
                $payload = [
                    'data'=>$logs_collection
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => __($e->getMessage())
                ];
            }      
        $this->_sendJson($payload);
    }
    public function matchinglogsAction() {        
            try { 
              $logs_collection=[];             
              $app_id        = $this->getApplication()->getId();              
              $openaiConfig  = new Migareference_Model_OpenaiConfig();              
              $ai_matching_logs  = $openaiConfig->aiMatchingLog($app_id);
              foreach ($ai_matching_logs as $key => $value) {                
                $view_action = '<button style="color:#2196f3;" class="btn" onclick="aiLogsModal(' . $value['migareference_matching_logs_id'] . ', \'matching\')">' . "<i class='fa fa-eye' rel=''></i>" . '</button>';
                    $logs_collection[]=[
                        $value['migareference_matching_logs_id'],
                        $value['response_type'],                        
                        $value['token_used'],
                        date('d-m-Y H:i:s', strtotime($value['created_at'])),
                        $view_action
                    ];
              }
                $payload = [
                    'data'=>$logs_collection
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => __($e->getMessage())
                ];
            }      
        $this->_sendJson($payload);
    }
    public function ailogsdetailsAction(){
        if ($data = $this->getRequest()->getPost()) {
            try {      
                $openaiConfig  = new Migareference_Model_OpenaiConfig();              
  
              $app_id       = $this->getApplication()->getId();          
              $uid  = $this->getRequest()->getParam('uid');                      
              $type  = $this->getRequest()->getParam('type');                      
              if ($type=='callscript') {                
                $log_item = $openaiConfig->callscriptLogItem($uid);
              }else {
                $log_item = $openaiConfig->matchingLogItem($uid);
              }

                $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0,                
                  'prompt'  => $log_item[0]['prompt'],
                  'response'=>$log_item[0]['response'],                  
                ];
            } catch (Exception $e) {
                $html = [
                  'error'          => true,
                  'message'        => __($e->getMessage()),
                  'message_button' => 1,
                  'message_loader' => 1,                
                ];
            }
            $this->_sendJson($html);
        }
    }

    // Copied from Public/AffinityController to support affinity test batch formatting.
    private function buildAffinityProfile(array $row)
    {
        $note = $row['note'] ?? '';
        if ($note === '' && isset($row['phone_note'])) {
            $note = $row['phone_note'];
        }

        return [
            'name' => $this->stringValue($row['name'] ?? ''),
            'surname' => $this->stringValue($row['surname'] ?? ''),
            'job' => $this->stringValue($row['job_title'] ?? ''),
            'profession' => $this->stringValue($row['profession_title'] ?? ''),
            // Use names (not numeric IDs) so the model sees real locations.
            'province' => $this->stringValue($row['province_name'] ?? ''),
            'country' => $this->stringValue($row['country_name'] ?? ''),
            'notes' => $this->stringValue($note),
            'reciprocity_notes' => $this->stringValue($row['reciprocity_notes'] ?? ''),
            'rating' => isset($row['rating']) ? (int) $row['rating'] : 0,
        ];
    }

    private function stringValue($value)
    {
        if ($value === null) {
            return '';
        }
        return trim((string) $value);
    }
}
?>
