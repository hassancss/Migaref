<?php

class Migareference_Mobile_QualificationController extends Application_Controller_Mobile_Default
{
public function getappcontentdataAction()
{
    try {
        $appId = $this->getApplication()->getId();
        $valueId = $this->getRequest()->getParam('value_id');  

        $migareference = new Migareference_Model_Migareference();
        $data = $migareference->get_app_content($appId); 

        $payload = [
            'success' => true,
            'data'    => $data
        ];
    } catch (Exception $e) {
        $payload = [
            'success' => false,            
            'message' => $e->getMessage()
        ];
    }

    $this->_sendJson($payload);
}
public function referrerfeaturecontentAction()
{
    try {
        $appId = $this->getApplication()->getId();        
        $userId = $this->getRequest()->getParam('user_id');
        $qualification = new Migareference_Model_Qualification();
        //Get referrer data to check referrer type
        $referrer = $qualification->getQualificationDetails($userId);
        if($referrer[0]['referrer_type']==1){
            //Fetch Featuer Moal Name
            $data = $qualification->getFeatureContent($referrer[0]['customer_list_id'],$appId);
        }else{
            $data = $qualification->getFeatureContent($referrer[0]['non_customer_list_id'],$appId);
        }
        $feature_model_name = $data[0]['model'];
        $featuer_value_id = $data[0]['value_id'];
        // create an instance dynamically
        $instance = new $feature_model_name();

        // call a method on it
        $result = $instance->getInappStates($featuer_value_id);

        $payload = [
            'success' => true,
            'data'    => $result,
            'referrer'    => $referrer,
            'dataresult'    => $data,
            'feature_model_name'    => $feature_model_name
        ];
    } catch (Exception $e) {
        $payload = [
            'success' => false,         
            'feature_model_name'    => $feature_model_name,   
            'message' => $e->getMessage()
        ];
    }

    $this->_sendJson($payload);
}


}
 