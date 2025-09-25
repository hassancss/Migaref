<?php
class Migareference_Model_Db_Table_Default extends Core_Model_Db_Table {    
    public function setDefaultCreditsApiNotification($app_id=0)
    {
        $migareference    = new Migareference_Model_Db_Table_Migareference();
        $data['app_id']        = $app_id;
        $data['ref_credits_api_enable_notification']        = 1;
        $data['ref_credits_api_notification_type']        = 1;
        $data['ref_credits_api_push_title']        = "Notifica Modifica Saldo Crediti App Segnalazione";
        $data['ref_credits_api_push_text']        = "Abbiamo registrato una nuova movimentazione sul tuo saldo crediti della nostra APP di Segnalazione. Ti preghiamo di loggarti e controllare. Ti abbiamo inviato una mail di riepilogo.";
        $data['ref_credits_api_open_feature']        = 1;
        $data['ref_credits_api_feature_id']        = 0;
        $data['ref_credits_api_custom_url']        = '';
        $data['ref_credits_api_custom_file']        = 'credits_noti_push.jpg';//@;
        $data['ref_credits_api_email_title']        = 'Notifica Modifica Saldo Crediti App Segnalazione';
        $data['ref_credits_api_email_text']        = '<p><font style="vertical-align:inherit"><font style="vertical-align:inherit">Hi @@referrer_name@@, </font></font><br />
        <br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">we are contacting you because you are a referrer of our APP @@app_link@@ and today we have registered a change in the commission CREDITS account concerning your account.&nbsp;</font></font></p>
        
        <p><font style="vertical-align:inherit"><font style="vertical-align:inherit">The change made is this: </font></font><br />
        <br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">Credits: @@credit@@ </font></font><br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">Movement Type: @@credit_type@@ </font></font><br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">Movement Description: @@credit_description@@ </font></font><br />
        <br />
        <strong><font style="vertical-align:inherit"><font style="vertical-align:inherit">Your new credit balance is: @@credit_balance@@</font></font></strong></p>
        
        <p><strong><font style="vertical-align:inherit"><font style="vertical-align:inherit">We invite you to connect to our APP to check your balance and previous transactions </font></font><br />
        <a href="http://@@app_link@@" target="_blank"><font style="vertical-align:inherit"><font style="vertical-align:inherit">CLICK HERE</font></font></a></strong><br />
        <br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">To your success </font></font><br />
        <font style="vertical-align:inherit"><font style="vertical-align:inherit">Customer Service Team</font></font></p>
        ';
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_credits_notification", $data);        
        $migareference->copyImages($data['app_id'],$data,'');
    }
    public function defaultReminderSettings($app_id=0)
    {
        // Reminder Types Default Values
      $migareference    = new Migareference_Model_Db_Table_Migareference();
      $migarefrence_report_reminder_types = array(
        array(        
        'rep_rem_title' => 'Appuntamento',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '1',
        'rep_rem_icon_file' => 'reminder.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Promemoria Appuntamento con @@report_owner@@',
        'rep_rem_push_text' => 'Ciao, oggi hai un appuntamento con il sig. @@report_owner@@ telefono @@report_owner_phone@@ relativo alla segnalazione nr. @@report_no@@ alle ore @@time_reminder@@ . Vai nella sezione Segnalazione dell\'APP',
        'rep_rem_open_feature' => '0',
        'rep_rem_feature_id' => '29',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'event_2_referral.jpg',
        'rep_rem_before_push_hours' => '1',
        'rep_rem_email_title' => 'Promemoria Appuntamento',
        'rep_rem_email_text' => '<p>Ciao, oggi hai un appuntamento con il sig. @@report_owner@@ telefono @@report_owner_phone@@ relativo alla segnalazione nr. @@report_no@@ alle ore @@time_reminder@@ . Vai nella sezione Segnalazione dell&#39;APP</p>',        
        ),
        array(        
        'rep_rem_title' => 'Ricontatto',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '2',
        'rep_rem_icon_file' => 'reminder_phone.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Ci sono Segnalatori da ricontattare!',
        'rep_rem_push_text' => 'Ciao, il sistema ti segnala che ci sono promemoria di ricontatto da gestire',
        'rep_rem_open_feature' => '0',
        'rep_rem_feature_id' => '29',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'automation_rem_type_2.jpg',
        'rep_rem_before_push_hours' => '1',
        'rep_rem_email_title' => 'Contatta il segnalatore: @@referral_name@@',
        'rep_rem_email_text' => '<p>Ciao.<br /><br />I marketing relazionale si basa proprio sul costruire una relazione, il sistema ti segnala di ricontattare&nbsp;il segnalatore&nbsp;@@referral_name@@ in quanto &egrave; da un po&#39; che non lo senti, il suo telefono &egrave;&nbsp;@@referral_phone@@, la sua email &egrave;&nbsp;@@referral_email@@.<br /><br />Qualche consiglio sulla tua chiamata, fai sempre un po&#39; di small talk con lui, chiedi sempre se puoi essergli di aiuto ricordando che sei in contatto con decine di persone ogni giorno e infine chiedigli come va con le segnalazioni, se ha avuto difficolt&agrave; o dubbi.&nbsp;<br /><br />Buon lavoro</p><p>&nbsp;</p>',        
        ),
        array(        
        'rep_rem_title' => 'Benvenuto',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '2',
        'rep_rem_icon_file' => 'automation_rem_type_title_3.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Abbaiamo nuovi segnalatori da gestire!',
        'rep_rem_push_text' => 'Ciao, oggi abbiamo nuovi segnalatori da gestire, controlla la sezione PROMEMORIA della tua APP di Segnalazione.',
        'rep_rem_open_feature' => '0',
        'rep_rem_feature_id' => '29',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'automation_rem_type_3.jpeg',
        'rep_rem_before_push_hours' => '1',
        'rep_rem_email_title' => 'Chiamata Benvenuto nuovo segnalatore @@referral_name@@',
        'rep_rem_email_text' => '<p>Ciao, oggi il sig.&nbsp;@@referral_name@@&nbsp;si &egrave; registrato nell&#39;APP e dobbiamo chiamarlo per dargli il benvenuto e fissare la chiamata di prequalifica. Il suo telefono &egrave; @@referral_phone@@</p>',        
        ),
        array(        
        'rep_rem_title' => 'Buon Compleanno',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '2',
        'rep_rem_icon_file' => 'automation_rem_type_title_4.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Oggi abbiamo da gestire degli AUGURI di buon compleanno',
        'rep_rem_push_text' => 'Ciao Admin,oggi abbiamo degli auguri di buon compleanno da gestire nei promemoria della tua APP di segnalazione.',
        'rep_rem_open_feature' => '1',
        'rep_rem_feature_id' => '25',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'automation_rem_type_4.jpg',
        'rep_rem_before_push_hours' => '1',
        'rep_rem_email_title' => 'Augura il buon compleanno a @@referral_name@@',
        'rep_rem_email_text' => '<p>Ciao Admin,</p><p>oggi &egrave; il compleanno di&nbsp;@@referral_name@@ e devi sentirlo per fargli gli auguri. Chiamalo al numero&nbsp;@@referral_phone@@ oppure mandagli un whastapp se non risponde. La sua email &egrave;&nbsp;@@referral_email@@<br /><br />A presto&nbsp;<br />APP AUTOMATION</p>',        
        ),
         array(
        'rep_rem_title' => 'RItardo Aggiornamento! ',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '2',
        'rep_rem_icon_file' => 'automation_rem_type_title_5.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Segnalazione gestita in ritardo!',
        'rep_rem_push_text' => 'Ciao! Risulta che la segnalazione nr. @@report_no@@ relativa al sig./sig.ra @@report_owner@@ effettuata dal segnalatore @@referral_name@@ non ha avuto un cambio di STATO nei tempi previsti.',
        'rep_rem_open_feature' => '1',
        'rep_rem_feature_id' => '25',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'automation_rem_type_5.jpeg',
        'rep_rem_before_push_hours' => '0',
        'rep_rem_email_title' => 'Ritardo Aggiornamento Stato Segnalazione!',
        'rep_rem_email_text' => '<p>Ciao!&nbsp;<br /> Risulta che la segnalazione nr.&nbsp;@@report_no@@ relativa al sig./sig.ra&nbsp;@@report_owner@@ effettuata dal segnalatore @@referral_name@@ &nbsp;non ha avuto un cambio di STATO nei tempi previsti.<br /><br />Procedere a risolvere il problema quanto prima.</p>',
         ),
         array(
        'rep_rem_title' => 'Ricontatto Utente (No Segnalatore)',
        'app_id' => $app_id,
        'rep_rem_target_notification' => '1',
        'rep_rem_badge' => '1',
        'rep_rem_type' => '2',
        'rep_rem_icon_file' => 'reminder_phone.png',
        'rep_rem_before_email_hours' => '1',
        'rep_rem_push_title' => 'Richiama @@referral_name@@',
        'rep_rem_push_text' => 'L\'Utente @@referral_name@@ si è registrato nell\'APP 48h fa, ma non si è registrato ancora come Segnalatore nella sezione SEGNALA >> PROFILO. Chiamalo per capire se vuole essere dei nostri e fissare una call di prequalifica.',
        'rep_rem_open_feature' => '1',
        'rep_rem_feature_id' => '25',
        'rep_rem_custom_url' => '',
        'rep_rem_custom_file' => 'automation_rem_type_2.jpg',
        'rep_rem_before_push_hours' => '0',
        'rep_rem_email_title' => 'Ricontatto Utente @@referral_name@@ ancora non segnalatore',
        'rep_rem_email_text' => '<p>Ciao,&nbsp;</p><p>l&#39;Utente&nbsp;@@referral_name@@ si &egrave; registrato nell&#39;APP 48h fa, ma non si &egrave; registrato ancora come Segnalatore nella sezione SEGNALA &gt;&gt; PROFILO. Chiamalo per capire se vuole essere dei nostri e fissare una call di prequalifica.<br /><br />A presto&nbsp;<br />APP SYSTEM</p>',
         )
        );
        $type_keys=[];
        foreach ($migarefrence_report_reminder_types as $key => $value) {
            $value['created_at']    = date('Y-m-d H:i:s');
            $this->_db->insert("migarefrence_report_reminder_types", $value);
            $type_keys[]=$this->_db->lastInsertId();
            $migareference->copyImages($value['app_id'],$value,'');
          }
        // Reminder AUtomations
        $migarefrence_report_reminder_auto = array(
            array(
            'auto_rem_title' => 'Compleanno Segnalatore 3-5 Stars',
            'app_id' => $app_id,
            'auto_rem_trigger' => '5',
            'auto_rem_action' => '1',
            'auto_rem_type' => '4',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '3',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '60',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
        ),
            array(
            'auto_rem_title' => 'Chiamata di Benvenuto Segnalatore',
            'app_id' => $app_id,
            'auto_rem_trigger' => '3',
            'auto_rem_action' => '1',
            'auto_rem_type' => '3',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '1',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '60',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
        ),
            array(
            'auto_rem_title' => 'Call Back 30 gg dei contatti 5 Stars',
            'app_id' => $app_id,
            'auto_rem_trigger' => '6',
            'auto_rem_action' => '1',
            'auto_rem_type' => '2',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '5',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '30',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
        ),
            array(
            'auto_rem_title' => 'Call Back 45 gg dei contatti 4 Stars',
            'app_id' => $app_id,
            'auto_rem_trigger' => '6',
            'auto_rem_action' => '1',
            'auto_rem_type' => '2',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '4',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '45',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
        ),
            array(
            'auto_rem_title' => 'Call Back 60 gg dei contatti 3 Stars',
            'app_id' => $app_id,
            'auto_rem_trigger' => '6',
            'auto_rem_action' => '1',
            'auto_rem_type' => '2',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '3',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '60',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
        ),
            array(
            'auto_rem_title' => 'Call Back 180 gg dei contatti 2 Stars',
            'app_id' => $app_id,
            'auto_rem_trigger' => '6',
            'auto_rem_action' => '1',
            'auto_rem_type' => '2',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '2',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '180',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => ''
            ),
            array(
            'app_id' => $app_id,
            'auto_rem_title' => 'Nuova Segnalazione Non Gestita!',
            'auto_rem_trigger' => '4',
            'auto_rem_action' => '1',
            'auto_rem_type' => '6',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '1',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => '1',
            ),
            array(
            'app_id' => $app_id,
            'auto_rem_title' => 'Appuntamento Fissato NON gestito!',
            'auto_rem_trigger' => '4',
            'auto_rem_action' => '1',
            'auto_rem_type' => '6',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '15',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => '9'
            ),
            array(
            'app_id' => $app_id,
            'auto_rem_title' => 'Posticipa Trattativa NON gestita!',
            'auto_rem_trigger' => '4',
            'auto_rem_action' => '1',
            'auto_rem_type' => '2',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '30',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => '6',
            ),
            array(
            'app_id' => $app_id,
            'auto_rem_title' => 'Utente NON ancora Segnalatore',
            'auto_rem_trigger' => '10',
            'auto_rem_action' => '1',
            'auto_rem_type' => '8',
            'auto_rem_status' => '1',
            'auto_rem_min_rating' => '1',
            'auto_rem_max_rating' => '5',
            'auto_rem_fix_rating' => '1',
            'auto_rem_rating' => '1',
            'auto_rem_engagement' => '1',
            'auto_rem_reports' => '5',
            'auto_rem_days' => '60',
            'auto_rem_trigger_hour' => '7',
            'auto_rem_report_trigger_status' => '',
            )
            );
         foreach ($migarefrence_report_reminder_auto as $key => $value) {
             $value['created_at']    = date('Y-m-d H:i:s');
             if ($key==0) {
                    $value['auto_rem_type']=$type_keys[3];
                    $this->_db->insert("migarefrence_report_reminder_auto", $value);            
                }elseif ($key==1) {
                    $value['auto_rem_type']=$type_keys[2];                
                    $this->_db->insert("migarefrence_report_reminder_auto", $value);            
                }elseif ($key==2 || $key==3 || $key==4 || $key==5) {
                    $value['auto_rem_type']=$type_keys[1];                
                    $this->_db->insert("migarefrence_report_reminder_auto", $value);            
                }elseif ($key==6 || $key==7 || $key==8) {
                    //Find Relvent status ID                    
                    if ($key==6) {
                        $staus=$migareference->checkStatus($app_id,1,1,0,0);
                    }elseif($key==7){
                        $staus=$migareference->checkStatus($app_id,0,0,1,1);
                    }elseif($key==8){
                        $staus=$migareference->checkStatus($app_id,0,0,1,2);
                    }
                    $value['auto_rem_type']=$type_keys[4];                
                    if (count($staus)>0) {
                        $value['auto_rem_report_trigger_status'] = $staus[0]['migareference_report_status_id'];
                        $this->_db->insert("migarefrence_report_reminder_auto", $value);            
                    }
                }elseif ($key==9) {
                    $value['auto_rem_type']=$type_keys[5];
                    $this->_db->insert("migarefrence_report_reminder_auto", $value); 
                }                
          }
    }
    public function prospectTransform($app_id=0)
    {        
        $migareference    = new Migareference_Model_Db_Table_Migareference();
        // Alter table migarefrence_prospect to start PK from 10000000
        $query="ALTER TABLE `migarefrence_prospect` AUTO_INCREMENT=1000000";
        $report_list   = $this->_db->query($query);
        // Check migarefrence_prospect table already data mean the function is executed
        $query="SELECT * FROM `migarefrence_prospect` WHERE migarefrence_prospect.app_id=$app_id";
        $all_prospect   = $this->_db->fetchAll($query);
        // To Handle new apps
        $query="SELECT * FROM migareference_report as rep WHERE rep.status=1 AND rep.app_id=$app_id ORDER BY rep.migareference_report_id";
        $report_list   = $this->_db->fetchAll($query);
        if (!COUNT($all_prospect) && COUNT($report_list)>0) {
            $query="SELECT * FROM `migareference_communication_logs` WHERE app_id=$app_id";
            $comm_log   = $this->_db->fetchAll($query);
            // Copy the communication log table to CSV
            $dir_image       = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image=$dir_image . "/features/migareference";
            $csvFile            = fopen($dir_image."/communication_log.csv","w");        
            // Write headers to the CSV file
            $csvHeaders = array_keys($comm_log[0]);
            fputcsv($csvFile, $csvHeaders);
            // Write data rows to the CSV file
            foreach ($comm_log as $row) {
                fputcsv($csvFile, $row);
            }
            fclose($csvFile);
            // Get All repots
            $query="SELECT * FROM migareference_report as rep WHERE rep.status=1 ORDER BY rep.migareference_report_id";
            $report_list   = $this->_db->fetchAll($query);
        //delete previous prospect        
        //$this->_db->delete('migarefrence_phonebook_previous',['type = ?' => 2]);
        //Fetch all prospect and add them to Phonebook table along wiht unique password and update back to Report table        
        $count='';
        foreach ($report_list as $key => $value) {
            $password         = $this->randomPassword(10);
            //Filter phoonebook type=2 and phone=repoort mobile 
            $mobile=$value['owner_mobile'];
            $query="SELECT * FROM `migarefrence_phonebook`
                    WHERE migarefrence_phonebook.mobile='$mobile'
                    AND migarefrence_phonebook.type=2";
            $phonebook_prospect_found   = $this->_db->fetchAll($query);
            // Find in new
            $query="SELECT * FROM `migarefrence_prospect`
                    WHERE migarefrence_prospect.mobile='$mobile'";
            $new_prospect_found   = $this->_db->fetchAll($query);
            // if phonebook(old) found than  add it to the prospect table with random password and update back to Report tabl with prospect id 
            if (count($phonebook_prospect_found)) { 
                $prospect['app_id']       = $phonebook_prospect_found[0]['app_id'];
                $prospect['name']         = $phonebook_prospect_found[0]['name'];
                $prospect['surname']      = $phonebook_prospect_found[0]['surname'];
                $prospect['email']        = $phonebook_prospect_found[0]['email'];
                $prospect['mobile']       = $phonebook_prospect_found[0]['mobile'];
                $prospect['note']         = $phonebook_prospect_found[0]['note'];
                $prospect['job_id']       = $phonebook_prospect_found[0]['job_id'];
                $prospect['rating']       = $phonebook_prospect_found[0]['rating'];
                $prospect['is_blacklist'] = $phonebook_prospect_found[0]['is_blacklist'];
                $prospect['password']     = $password;
                $prospect['created_at']   = $phonebook_prospect_found[0]['created_at'];                                                
            }else {
                $prospect['app_id']     = $value['app_id'];
                $prospect['name']       = $value['owner_name'];
                $prospect['surname']    = $value['owner_surname'];
                $prospect['mobile']     = $value['owner_mobile'];                
                $prospect['rating']     = 1;
                $prospect['password']   = $password;
                $prospect['created_at'] = date('Y-m-d H:i:s');              
            }
            //if new found that just update the report table with new prospect id               
            if (COUNT($new_prospect_found)) {
                $prospect_id=$new_prospect_found[0]['migarefrence_prospect_id'];
                if(!empty($value['consent_timestmp']) || $value['consent_timestmp']!=null){
                    $prospect_gdpr['gdpr_consent_source']     = $value['consent_source'];
                    $prospect_gdpr['gdpr_consent_ip']         = $value['consent_ip'];
                    $prospect_gdpr['gdpr_consent_timestamp']  = $value['consent_timestmp'];
                    $this->_db->update("migarefrence_prospect", $prospect_gdpr,['migarefrence_prospect_id = ?' => $prospect_id]);                            
                }
            }else {
                // Add data to prospect table
                $prospect['gdpr_consent_source']     = $value['consent_source'];
                $prospect['gdpr_consent_ip']         = $value['consent_ip'];
                $prospect['gdpr_consent_timestamp']  = $value['consent_timestmp'];
                $this->_db->insert("migarefrence_prospect", $prospect);                
                $prospect_id=$this->_db->lastInsertId();
            }              
            // If Communication logs found than update 
            if (COUNT($phonebook_prospect_found)) {
                // Find Communication log
                $phonebook_id=$phonebook_prospect_found[0]['migarefrence_phonebook_id'];                

                $query="SELECT * FROM `migareference_communication_logs` WHERE phonebook_id=$phonebook_id";
                $comm_log   = $this->_db->fetchAll($query);
                $com_log_item=[];
                if (COUNT($comm_log)) {                    
                    $com_log_item['phonebook_id']=$prospect_id;                                        
                    $this->_db->update("migareference_communication_logs", $com_log_item,['phonebook_id = ?' => $phonebook_id]);                            
                }else {
                    $com_log_item=[
                        'app_id'       => $value['app_id'],
                        'phonebook_id' => $prospect_id,
                        'user_id'      => $value['user_id'],
                        'log_type'     => "Enrollment",                        
                        'created_at'   => $phonebook_prospect_found[0]['created_at']
                    ];
                    $this->_db->insert("migareference_communication_logs", $com_log_item);                                
                }
            } 
            // Update back to Report table with corrosponding prospect_id
            $report_id=$value['migareference_report_id'];
            $report_item['prospect_id']=$prospect_id;
            $this->_db->update("migareference_report", $report_item,['migareference_report_id = ?' => $report_id]);                            
        }
        }
        
        // return $count;
    }
    public function randomPassword($length=0) {
        $alphabet = "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    // TEMP:Date:(8/28/2023), Type: One time Execution, TASK: Management of multiple agents
    function copyAgents($app_id=0){
        $query="SELECT inv.migareference_invoice_settings_id, inv.user_id, inv.sponsor_id
        FROM migareference_invoice_settings AS inv
        JOIN customer ON customer.customer_id = inv.user_id
        LEFT JOIN migareference_referrer_agents ON inv.user_id = migareference_referrer_agents.referrer_id
        WHERE inv.app_id = $app_id AND inv.sponsor_id != 0
        AND migareference_referrer_agents.referrer_id IS NULL";
        $referrer_list   = $this->_db->fetchAll($query);//$referrer_list which have agents
       
        if (COUNT($referrer_list)) { //referrer have agents that need to copy and alredy query is not executed
            foreach ($referrer_list as $key => $value) {
                $data['app_id']    = $app_id;
                $data['referrer_id']    = $value['user_id'];
                $data['agent_id']    = $value['sponsor_id'];                
                // $data['agent_type']    =1;//default will be one
                $data['created_at']    = date('Y-m-d H:i:s');
                $this->_db->insert("migareference_referrer_agents", $data);
            }
        }

    }
    // TEMP:Date(10/12/2023) Type: One time Execution Require TASK: Add missing referrers data to phonebook
    function copyReftoPhonebook($app_id=0){        
        $query_option="SELECT *,migareference_invoice_settings.app_id as appp_id,customer.email as customer_email
        FROM `migareference_invoice_settings`
        JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
        LEFT JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id = migareference_invoice_settings.migareference_invoice_settings_id
        WHERE migarefrence_phonebook.invoice_id IS NULL";

        $referrer_list   = $this->_db->fetchAll($query_option);//$referrer_list which are not in  phoenbook        
        if (COUNT($referrer_list)) {
            foreach ($referrer_list as $key => $value) {
                $phonebook['app_id']      = $value['appp_id'];
                $phonebook['name']        = $value['invoice_name'];
                $phonebook['surname']     = $value['invoice_surname'];
                $phonebook['mobile']      = $value['invoice_mobile'];
                $phonebook['email']       = $value['customer_email'];
                $phonebook['invoice_id']  = $value['migareference_invoice_settings_id'];
                $phonebook['job_id']      = 0;
                $phonebook['user_id']     = $value['user_id'];
                $phonebook['type']        = 1;
                $phonebook['created_at']  = date('Y-m-d H:i:s');
                $migareference    = new Migareference_Model_Db_Table_Migareference();                
                $migareference->savePhoneBook($phonebook);
            }
        }

    }
}
