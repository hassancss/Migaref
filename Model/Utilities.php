<?php

class Migareference_Model_Utilities extends Core_Model_Default
{
    public function __construct($datas = []) {
        parent::__construct($datas);
          $this->_db_table = 'Migareference_Model_Db_Table_Utilities';
    } 
    public function getAllAgents($app_id = 0)
    {
        return $this->getTable()->getAllAgents($app_id);
    }
    public function getTypeOneAgents($app_id = 0)
    {
        return $this->getTable()->getTypeOneAgents($app_id);
    }
    public function getTypeTwoAgents($app_id = 0)
    {
        return $this->getTable()->getTypeTwoAgents($app_id);
    }
    public function getAllJobs($app_id = 0,$add_prepend_new_job=0)
    {
        return $this->getTable()->getAllJobs($app_id,$add_prepend_new_job);
    }
    public function getAllProfessions($app_id = 0)
    {
        return $this->getTable()->getAllProfessions($app_id);
    }
    public function shortLink($long_url = '')
    {       
        
              $curl = curl_init();
        
              curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://mssl.it/yourls-api.php',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => array(
                      'signature' => 'f4130577c5',//Yourls API Signature
                      'action' => 'shorturl',//Action to perform
                      'url' => $long_url,//URL to shorten or long url
                      'keyword' => $this->randomPassword(),
                      'username' => 'username',//Yourls API Username
                      'password' => 'migastone',//Yourls API Password
                      'format' => 'json'//Yourls API Password
                  ),
              ));
              
              $shortnerResponse = curl_exec($curl);
              curl_close($curl);  
        
              $shortnerResponse = json_decode($shortnerResponse);
              if ($shortnerResponse->statusCode !== 200 && $shortnerResponse->shorturl=='') { // request failed            
                    error_log('ShortURL API Error: HTTP '. ' Response: ' . $shortnerResponse->message);
                    return $long_url;//return long url
              }elseif (isset($shortnerResponse->shorturl)) {
                  return $shortnerResponse->shorturl;//Successfull short url return      
              }else {
                return $long_url;//return long url
              }      
          
          
    }
    public function detectReferrerChanges($currentData=[], $newData=[],$app_id=0)
    {
        return $this->getTable()->detectReferrerChanges($currentData,$newData,$app_id);
    }
    public function containsSQLInjection($input = '')
    {
        // Define patterns commonly used in SQL injection attempts
        $patterns = array(
            '/\b(SELECT|UPDATE|DELETE|INSERT INTO|DROP TABLE|CREATE TABLE|ALTER TABLE)\b/i', // SQL keywords
            '/\b(UNION|JOIN|WHERE|FROM|AND|OR|LIKE)\b/i', // SQL operators
            '/[\b\W]EXEC[\b\W]/i', // Stored procedure execution
            '/(--|#|\/\*)[\s\S]*([\r\n]|$)/', // Comments (--, #, /* */)
            '/\b(AND|OR|NOT)\b\s*[\d\'"\\\_\(\)]+(\s*(=|<>|<|>|<=|>=|LIKE|IN|IS|NOT))/', // Logical operators with values
            '/\b(AND|OR)\b\s*\w*\s*BETWEEN\s*\w*\s*AND\s*\w*/', // BETWEEN operator
            '/\b(AND|OR)\b\s*\w*\s*=\s*\w*/', // Equals operator
            '/(\b(DELETE|UPDATE|INSERT INTO)\b.*\b(ORDER BY|GROUP BY|HAVING)\b)/i' // Ordering/grouping/having within DML statements
        );
      
        // Check if input matches any of the patterns
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true; // SQL injection detected
            }
        }
      
        return false; // No SQL injection detected
    }
    public function randomTaxid()
    {
        $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        // return implode($pass); //turn the array into a string
        return implode($pass); //turn the array into a string
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
}
