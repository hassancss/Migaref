<?php

class Migareference_Model_Db_Table_OpenaiConfig extends Core_Model_Db_Table {

    protected $_name = 'migareference_openai_config';
    protected $_primary = 'migareference_openai_config_id';
    public function setDefaultConfig($app_id=0){
        $config['app_id'] = $app_id;
        $config['openai_apikey'] = '';
        $config['openai_temperature'] = 1;
        $config['is_api_enabled'] = 2;
        $config['matching_preffered_lan'] = 'Italian';
        $config['openai_token'] = 400;
        $config['relationship_note_prompt'] = 'You are creating a concise Relationship Note (max 80 words) for a referrer. Use only the provided details unless the online identity is an extremely certain match. If you are not confident the online person matches, skip external research and rely on the provided data. Summarize key relationship cues, tone, and next-steps to help strengthen rapport. Provide your response as JSON: {"note": "<text>", "used_external_research": <true|false>}  Referrer: @@referrer_name@@ @@surname@@  Email: @@email@@  Phone: @@phone@@  Profession: @@job@@  Sector: @@sector@@';
        $config['user_prompt'] = 'Create a call script on a relational basis with the aim of immediately creating empathy with the referrer, with the final aim of remembering to refer potential customers to us. For this purpose, I give you some information about the Referrer we are calling, which was previously collected from the caller:
            Name: @@referrer_name@@
            Job: @@job@@
            Sector: @@sector@@
            Relational Notes: @@relational_notes@@
            Reciprocity Notes: @@reciprocity_notes@@
            Province: @@province@@
            Date of Birth: @@birth_date@@            
            The script must be informal and comprihensive, like conversation between Referrer and Caller, you can talk about informations of the province where referrer live, if we are near the birthday we can referr on that else skip it, and other relationalship argument that you think are useful, interrupt the script without writing further with the phrase Im calling you because this month we are looking for.';        
        // New defaults for affinity scoring configuration (added separately from call script settings).
        $config['affinity_enabled'] = 0;
        $config['affinity_model'] = 'gpt-4o-mini';
        $config['affinity_temperature'] = 1;
        $config['affinity_max_tokens'] = 600;
        $config['affinity_user_prompt'] = 'Score affinity between the primary referrer and each compare referrer. ' .
            'Use only the provided fields. ' .
            'Rubric: 1-3 = weak fit, 4-6 = moderate fit, 7-8 = strong fit, 9-10 = exceptional fit. ' .
            'Return ONLY JSON (NO markdown) in this exact shape: {"scores":[{"compare_id":123,"score":7}]}.';
        $config['affinity_system_prompt'] = null;

        $this->_db->insert("migareference_openai_config", $config); 
    }
    public function getModels($apiKey='')
    {
        $ch = curl_init("https://api.openai.com/v1/models");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,                
            )
        );

        $response = curl_exec($ch);
        $error = curl_error($ch); // Get error message, if any
        curl_close($ch);
        if ($response === false) {

            return false;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['data'])) {
            return $responseData['data'];
        }

        // Error handling for invalid response
        return false;
    }
    public function addCallScriptLog($data=[]){
        $this->_db->insert("migareference_callscript_logs", $data);
    }
    //Return last 30 matching logs
    public function callScriptLog($app_id=0){
        $query="SELECT * FROM migareference_callscript_logs WHERE app_id = $app_id ORDER BY migareference_callscript_logs_id DESC LIMIT 30";
        return $this->_db->fetchAll($query);
    }
    public function aiMatchingLog($app_id=0){
        $query="SELECT * FROM migareference_matching_logs WHERE app_id = $app_id ORDER BY migareference_matching_logs_id DESC LIMIT 30";
        return $this->_db->fetchAll($query);
    }
    //Return single record
    public function callscriptLogItem($uid=0){
        $query="SELECT * FROM migareference_callscript_logs WHERE migareference_callscript_logs_id = $uid";
        return $this->_db->fetchAll($query);
    }
    public function matchingLogItem($uid=0){
        $query="SELECT * FROM migareference_matching_logs WHERE migareference_matching_logs_id = $uid";
        return $this->_db->fetchAll($query);
    }
}
