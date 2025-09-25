<?php
class Migareference_Mobile_PhonebookController extends Application_Controller_Mobile_Default
{    

    public function getaimatchingAction(){    
        try {      
          $phonebook = new Migareference_Model_Phonebook();

          $app_id       = $this->getApplication()->getId();          
          $referrer_id  = $this->getRequest()->getParam('referrer_id');                      
           

          $available_matching = $phonebook->availableMatching($app_id,$referrer_id);
          $matched_matching   = $phonebook->matchedMatching($app_id,$referrer_id);
          $discard_matching   = $phonebook->discardMatching($app_id,$referrer_id);
          $last_matching_call = $phonebook->lastMatchingCall($app_id,$referrer_id);//last matching success call

          // Available Matchin
          // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
          $available_matching_data=__("No data found");
          if (count($available_matching)) {
            $available_matching_data="";            
            foreach ($available_matching as $key => $value) {
              $available_matching_data.="<div class='card'>";
                $available_matching_data.="<div class='row' style='border-bottom:1px solid #e4e4e4;'>";
                  $available_matching_data.="<div class='col' style='text-align:left;'>";
                    $available_matching_data.="<strong>"."UID"." #".$value['customer_id']."</strong>";
                  $available_matching_data.="</div>";
                  $available_matching_data.="<div class='col' style='text-align:right;'>";
                  $available_matching_data.="<button class='button button-positive button-small' style='font-weight: 900;font-size: large;' ng-clic='matchCustomer(".$value['migareference_matching_network_id'].")'>"."<strong> + </strong>"."</button>";
                  $available_matching_data.="</div>";
                $available_matching_data.="</div>";
                $available_matching_data.="<div class='row' style='text-align:left;'>";
                $available_matching_data.="<div class='col'>";
                $available_matching_data.=__("Name");
                $available_matching_data.="</div>";
                $available_matching_data.="<div class='col'>";
                    $available_matching_data.="<strong>".$value['firstname']." ".$value['lastname']."</strong>";                  
                  $available_matching_data.="</div>";
                $available_matching_data.="</div>";
                $available_matching_data.="<div class='row'>";
                  $available_matching_data.="<div class='col' style='text-align:left;'>";
                    $available_matching_data.=__("Email");
                  $available_matching_data.="</div>";
                  $available_matching_data.="<div class='col' style='text-align:left;'>";
                    $available_matching_data.="<strong>".$value['email']."</strong>";
                  $available_matching_data.="</div>";
                $available_matching_data.="</div>";
                $available_matching_data.="<div class='row'>";
                  $available_matching_data.="<div class='col' style='text-align:left;'>";
                    $available_matching_data.=$value['matching_description'];
                  $available_matching_data.="</div>";
                $available_matching_data.="</div>";              
              $available_matching_data.="</div>";            
            }
          }
          // Matched Matching
          // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
          $matched_matching_data=__("No data found");
          if (count($matched_matching)) {            
            $matched_matching_data="";
            foreach ($matched_matching as $key => $value) {
              $matched_matching_data.="<div class='card'>";
              $matched_matching_data.="<div class='row' style='border-bottom:1px solid #e4e4e4;'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.="<strong>"."UID"." #".$value['customer_id']."</strong>";
                $matched_matching_data.="</div>";
                $matched_matching_data.="<div class='col' style='text-align:right;'>";
                $matched_matching_data.="<button class='button button-positive button-small' style='font-weight: 900;font-size: large;' ng-click='unMatchCustomer(".$value['migareference_matching_network_id'].")'>"."<strong> - </strong>"."</button>";
                $matched_matching_data.="</div>"; 
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row' style='text-align:left;'>";
              $matched_matching_data.="<div class='col'>";
              $matched_matching_data.=__("Name");
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='col'>";
                  $matched_matching_data.="<strong>".$value['firstname']." ".$value['lastname']."</strong>";                  
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.=__("Email");
                $matched_matching_data.="</div>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.="<strong>".$value['email']."</strong>";
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.=$value['matching_description'];
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";              
            $matched_matching_data.="</div>"; 
            }
          }
          // Discard Matching
          // we have to return a table of html customer_id, first_name lastname, eamil, matching_description, Action Button
          $matched_matching_data=__("No data found");
          if (count($discard_matching)) {            
            $matched_matching_data="";
            foreach ($discard_matching as $key => $value) {
              $matched_matching_data.="<div class='card'>";
              $matched_matching_data.="<div class='row' style='border-bottom:1px solid #e4e4e4;'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.="<strong>"."UID"." #".$value['customer_id']."</strong>";
                $matched_matching_data.="</div>";
                $matched_matching_data.="<div class='col' style='text-align:right;'>";
                $matched_matching_data.="<button class='button button-positive button-small' style='font-weight: 900;font-size: large;' ng-click='unMatchCustomer(".$value['migareference_matching_network_id'].")'>"."<strong> - </strong>"."</button>";
                $matched_matching_data.="</div>"; 
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row' style='text-align:left;'>";
              $matched_matching_data.="<div class='col'>";
              $matched_matching_data.=__("Name");
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='col'>";
                  $matched_matching_data.="<strong>".$value['firstname']." ".$value['lastname']."</strong>";                  
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.=__("Email");
                $matched_matching_data.="</div>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.="<strong>".$value['email']."</strong>";
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";
              $matched_matching_data.="<div class='row'>";
                $matched_matching_data.="<div class='col' style='text-align:left;'>";
                  $matched_matching_data.=$value['matching_description'];
                $matched_matching_data.="</div>";
              $matched_matching_data.="</div>";              
            $matched_matching_data.="</div>"; 
            }
          }
            
          // Last Matching Call
          // we have to return lastmatching call date and time
          $last_matching_call_data=__("Last Update").": "."0000-00-00 00:00:00";
          $is_first_call_done=false;
          if (count($last_matching_call)) {
            $last_matching_call_data=__("Last Update").": ".$last_matching_call[0]['created_at'];
            $is_first_call_done=true;
          }
          // Total Token Used
          $token_used = 0;
          if (count($last_matching_call)) {
            $token_used=__("Token Used").": ".$last_matching_call[0]['token_used'];
          }     
            $payload = [
              'success'         => true,
              'message'         => __('Successfully saved.'),
              'message_timeout' => 0,
              'message_button'  => 0,
              'message_loader'  => 0,                
              'available_matching_data'  => $available_matching_data,
              'available_matching_data_collection'  => $available_matching,
              'matched_data'=>$matched_matching_data,
              'matched_data_collection'=>$matched_matching,
              'discard_data_collection'=>$discard_matching,
              'last_matching_call'=>$last_matching_call_data,
              'is_first_call_done'=>$is_first_call_done,
              'token_used'=>$token_used
            ];
        } catch (Exception $e) {
            $payload = [
              'error'          => true,
              'message'        => __($e->getMessage()),
              'message_button' => 1,
              'message_loader' => 1,                
            ];
        }        
        $this->_sendJson($payload);
    }

    public function refreshaimatchingAction(){        
            try {      
              $phonebook = new Migareference_Model_Phonebook();
  
              $app_id        = $this->getApplication()->getId();          
              $phonebook_id  = $this->getRequest()->getParam('phonebook_id');          
              $calling_method= 'refreshaimatching_app';
  
              $response=$phonebook->referrerMatching($app_id,$phonebook_id,$calling_method);
                $html = [
                  'success'         => true,
                  'message'         => __('Successfully saved.'),
                  'message_timeout' => 0,
                  'message_button'  => 0,
                  'message_loader'  => 0,
                  'response'  => $response,
                ];
            } catch (Exception $e) {
                $html = [
                  'error'          => true,
                  'message'        => __($e->getMessage()),
                  'message_button' => 1,
                  'message_loader' => 1,
                  'response'  => $response,
                ];
            }
            $this->_sendJson($html);
    }
    public function matchcustomerAction(){      
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->matchCustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
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
    public function removecustomerAction(){      
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->removeCustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
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
    public function discardcustomerAction(){      
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->discardCustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
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
  public function unmatchcustomerAction(){      
          try {      
            $phonebook = new Migareference_Model_Phonebook();

            $app_id       = $this->getApplication()->getId();          
            $matching_network_id  = $this->getRequest()->getParam('matching_network_id');                      

            $response=$phonebook->unmatchcustomer($app_id,$matching_network_id);
           
              $html = [
                'success'         => true,
                'message'         => __('Successfully saved.'),
                'message_timeout' => 0,
                'message_button'  => 0,
                'message_loader'  => 0,                                
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