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
        $config['user_prompt'] = 'Create a call script on a relational basis with the aim of immediately creating empathy with the referrer, with the final aim of remembering to refer potential customers to us. For this purpose, I give you some information about the Referrer we are calling, which was previously collected from the caller:
            Name: @@referrer_name@@
            Job: @@job@@            
            Sector: @@sector@@
            Relational Notes: @@relational_notes@@
            Reciprocity Notes: @@reciprocity_notes@@
            Province: @@province@@
            Date of Birth: @@birth_date@@            
            The script must be informal and comprihensive, like conversation between Referrer and Caller, you can talk about informations of the province where referrer live, if we are near the birthday we can referr on that else skip it, and other relationalship argument that you think are useful, interrupt the script without writing further with the phrase Im calling you because this month we are looking for.';        

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
