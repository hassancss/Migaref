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


}
 