<?php
class Migareference_Mobile_OpenaiController extends Application_Controller_Mobile_Default
{    

    public function getcallscriptAction(){
        try {
                /*
                * Dynamic Supported Tags
                * @@referrer_name@@
                * @@job@@
                * @@sector@@
                * @@relational_notes@@
                * @@reciprocity_notes@@
                * @@province@@
                * @@birth_date@@
                */
                $phonebook_id = $this->getRequest()->getParam('phobebook_id');
                $app_id   = $this->getApplication()->getId();                                            
                $openai_config = (new Migareference_Model_OpenaiConfig())->findAll(['app_id'=> $app_id])->toArray();        
                $phonebook_item = (new Migareference_Model_Migareference())->getSinglePhonebook($phonebook_id);        
                
                $prompt = $openai_config[0]['system_prompt']." ".$openai_config[0]['user_prompt'];
                // Replace dynamic tags with actual values
                if (empty($phonebook_item[0]['birthdate']) || $phonebook_item[0]['birthdate']==0) {
                    $birht_date='Not Available';    
                }else {
                    $birht_date = date('d-m-Y', $phonebook_item[0]['birthdate']);
                }
                $prompt = str_replace("@@referrer_name@@", $phonebook_item[0]['invoice_name'].' '.$phonebook_item[0]['invoice_surname'], $prompt);
                $prompt = str_replace("@@job@@", $phonebook_item[0]['job_title'], $prompt);
                $prompt = str_replace("@@sector@@", $phonebook_item[0]['profession_title'], $prompt);
                $prompt = str_replace("@@relational_notes@@", $phonebook_item[0]['note'], $prompt);
                $prompt = str_replace("@@reciprocity_notes@@", $phonebook_item[0]['reciprocity_notes'], $prompt);
                $prompt = str_replace("@@province@@", $phonebook_item[0]['province'], $prompt);
                $prompt = str_replace("@@birth_date@@", $birht_date, $prompt);                
                $prompt = str_replace("@@birthdate@@", $birht_date, $prompt);                
                $data = [
                    "model" => $openai_config[0]['call_script_ai_model'],  // Correct model name
                    "temperature" => (float)$openai_config[0]['openai_temperature'],
                    "top_p" => 1,
                    "max_tokens" => (integer)$openai_config[0]['openai_token'],
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
                // "prompt": "Create a call script on a relational basis with the aim of immediately creating empathy with the referrer, with the final aim of reminding them to refer potential customers to us. For this purpose, I will provide some information about the person we are calling, which was previously collected:/nName: John Doe/nJob: Sales Manager/nSector: Retail Relational Notes: Interested in new marketing strategies Province: Ontario Date of Birth: 01/15/1980 Caller Name: Andrea Caller Company: Migastone /n The script should be informal and include personal touches related to the provided information. For example, mention local events in the province, refer to the birthday (January 15, 1980) if it is near, and other relationship-building topics. End the script with the phrase \'I\'m calling you because this month we are looking for...\'. The response should be a conversation between John and the caller, with correct references to the birthday date.Dont restart response and every call trun should be comprihensive trun",
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url,
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

                $ai_response = curl_exec($curl);
                $curl_error = curl_error($curl);  // Capture any cURL errors
                curl_close($curl);                
                // //replace \n with <br> for new line                

                if ($curl_error) {
                    $response=$curl_error;
                    // Log the API error
                    $log = [
                        'app_id' => $app_id,
                        'referrer_id' => $phonebook_item[0]['user_id'],
                        'calling_method' => 'testcallscript_mab',                        
                        'response_type' => 'error',
                        'prompt' => $prompt,
                        'response' => $curl_error
                    ];                                                           
                }else {
                    $ai_response = json_decode($ai_response, true);                    
                    // //replace \n with <br>
                    $formated_response=str_replace("\n","<br>",$ai_response['choices'][0]['message']['content']);
                    $token_used = $ai_response['usage']['total_tokens'];
                    $log = [
                        'app_id' => $app_id,
                        'referrer_id' => $phonebook_item[0]['user_id'],
                        'calling_method' => 'testcallscript_mab',                        
                        'token_used' => $token_used,
                        'response_type' => 'success',
                        'prompt' => $prompt,
                        'response' => $formated_response
                    ]; 
                }                
                (new Migareference_Model_OpenaiConfig())->addCallScriptLog($log);
            $payload = [
                "success" => true,
                "app_id"  => $app_id,                
                "ai_response"  => $ai_response,                                
                "response"  => $formated_response,                
                "token_used"  => $token_used,                
                "prompt"  => $prompt,                
                "phonebook_item"  => $phonebook_item,                
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
                "app_id"  => $app_id
            ];
        }
        $this->_sendJson($payload);
  }

}