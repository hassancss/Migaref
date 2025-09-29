<?php
/**
 * Class Migareference_Model_Db_Table_Migareference
 */
  use Push\Model\StandalonePush;
  use Siberian\Hook;
  require "app/local/modules/Migareference/libs/twilio/autoload.php";
class Migareference_Model_Db_Table_Migareference extends Core_Model_Db_Table
{
      public function getAutomationTriggers($app_id=0)
      {
            $automation_trigger[1]=__('Referrer posted a certain number of reports');
            $automation_trigger[2]=__('Referrer opens the APP one or more time in the past XX days');
            $automation_trigger[3]=__('First time a Referrer is registered');
            $automation_trigger[4]=__('A report still in one state for XX days');
            $automation_trigger[5]=__('Referrer birthday');
            $automation_trigger[6]=__('Referrer not called since XX days');
            $automation_trigger[7]=__('Referre called since XX days');
            $automation_trigger[8]=__('No changes in engagement rating of a referrer in the past XX days');
            //USe Invitation failed status only when invite is enables
            $pre_settings   = $this->preReportsettigns($app_id);                
            if (count($pre_settings) && $pre_settings[0]['is_visible_invite_prospectus']==1) {              
              $automation_trigger[9]=__('Monitor Failed Invitations');
            }
            $automation_trigger[10]=__('Referrers not accepted T&C (48h)');
            return $automation_trigger;
      }
      public function reportCsv($app_id=0)
      {
            $query_option_value="SELECT rep.migareference_report_id,
            rep.report_no,
            rep.owner_name,
            rep.owner_surname,
            rep.owner_mobile,
            rep.created_at as report_created_at,
            rep.last_modification_at,
            rep.prospect_id,
            rep.consent_timestmp,
            rep.currunt_report_status,
            rep.report_custom_type,
            invoice_name,
            invoice_surname,
            invoice_mobile,
            sponsor_id,
            terms_accepted,
            inv.user_id,
            ref_consent_timestmp,
            referrer.email as referrer_email,
            referrer.birthdate,
            migarefrence_phonebook.mobile,
            migareference_jobs.job_title,
            migarefrence_phonebook.note,
            migarefrence_phonebook.rating,
            migarefrence_phonebook.engagement_level,
            geop.province,
            geop.province_code,
            geoc.country,
            geoc.country_code,            
            sponsor_one.customer_id AS agent_id,
            sponsor_one.email AS agent_email,
            sponsor_one.firstname AS agent_name,
            sponsor_one.lastname AS agent_surname,
            sponsor_one.mobile AS agent_mobile,
            sponsor_two.customer_id AS sponsor_two_id,
            sponsor_two.email AS email,
            sponsor_two.firstname AS sponsor_two_firstname,
            sponsor_two.lastname AS sponsor_two_lastname,
            migareference_report_status.status_title,
            ROUND((SUM(CASE WHEN le.entry_type = 'C' THEN le.amount ELSE 0 END) - 
              SUM(CASE WHEN le.entry_type = 'E' THEN le.amount ELSE 0 END)) * 
              COUNT(le.report_id) / COUNT(le.report_id), 2) AS total_credits,
            ROUND(((SUM(ue.earn_amount)) * COUNT(ue.report_id)) / COUNT(ue.report_id), 2) AS total_earn
            FROM `migareference_report` AS rep
            JOIN migareference_invoice_settings AS inv ON inv.user_id=rep.user_id
            LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=inv.user_id
            LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=inv.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
            LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
            LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
            LEFT JOIN customer AS referrer ON referrer.customer_id=inv.user_id
            LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=rep.currunt_report_status
            LEFT JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
            LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
            LEFT JOIN migarefrence_ledger AS le ON rep.user_id=le.user_id AND rep.migareference_report_id=le.report_id
            LEFT JOIN migareference_user_earnings AS ue ON rep.user_id=ue.refferral_user_id AND rep.migareference_report_id=ue.report_id
            LEFT JOIN migareference_geo_provinces AS geop ON geop.migareference_geo_provinces_id = inv.address_province_id        
            LEFT JOIN migareference_geo_countries AS geoc ON geoc.migareference_geo_countries_id=inv.address_country_id
            WHERE rep.app_id=$app_id
            GROUP BY rep.migareference_report_id;";
            return $this->_db->fetchAll($query_option_value);
      }
      public function getRefInvitationLink($app_id=0,$referrer_id=0,$agent_id=0){
        // Sample link: https://beta.migastone.com/migareference/landingreport?app_id=1&user_id=140&agent_id=0&report_by=140&type=1&report_custom_type=1
        $default = new Core_Model_Default();
        $utilities = new Migareference_Model_Utilities();
        $base_url= $default->getBaseUrl();
        $long_url=$base_url."/migareference/landingreport?app_id=".$app_id."&user_id=".$referrer_id."&agent_id=".$agent_id."&report_by=".$referrer_id."&type=1&report_custom_type=1";
        $bitly_crede       = $this->getBitlycredentails($app_id);
        if (!empty($bitly_crede) && !empty($bitly_crede[0]['bitly_login']) && !empty($bitly_crede[0]['bitly_key'])) {            
          $long_url= $utilities->shortLink($long_url);          
        } 
        $data['email_formate']="<p style='font-size:16px'>".__("Puoi referenziare chiunque anche senza App, invita il tuo contatto a lasciare i suoi dati utilizzando il tuo referral link").": ".$long_url."</p>";
        return $data;
      }
      public function getReferralLink($app_id=0,$referrer_id=0,$agent_id=0){
        $utilities = new Migareference_Model_Utilities();
        // Sample link: https://beta.migastone.com/migareference/landingreport?app_id=1&user_id=140&agent_id=0&report_by=140&type=1&report_custom_type=1
        $default = new Core_Model_Default();
        $base_url= $default->getBaseUrl();
        $long_url=$base_url."/migareference/landingreport?app_id=".$app_id."&user_id=".$referrer_id."&agent_id=".$agent_id."&report_by=".$referrer_id."&type=1&report_custom_type=1";
        $bitly_crede       = $this->getBitlycredentails($app_id);
        if (!empty($bitly_crede) && !empty($bitly_crede[0]['bitly_login']) && !empty($bitly_crede[0]['bitly_key'])) {            
          $long_url= $utilities->shortLink($long_url);          
        }         
        return $long_url;
      }
      
      public function reportWebhookStrings($app_id=0,$report_id=0)
      {
            $query_option_value="SELECT rep.migareference_report_id,
            rep.report_no,
            rep.owner_name,
            rep.owner_surname,
            rep.owner_mobile,
            rep.user_id as user_id,
            rep.created_at as report_created_at,
            rep.last_modification_at,
            rep.report_source,
            rep.prospect_id,
            rep.consent_timestmp,
            ht.director_name,
            ht.director_email,
            ht.director_phone,
            ht.director_calendar_url,
            invoice_name,
            invoice_surname,
            terms_accepted,
            invoice_mobile,
            address_country_id,
            sponsor_id,
            address_province_id,
            migarefrence_phonebook.mobile,
            migareference_jobs.job_title,
            migarefrence_phonebook.note,
            migarefrence_phonebook.rating,
            migarefrence_phonebook.engagement_level,
            migarefrence_phonebook.reciprocity_notes,
            sponsor_one.customer_id AS sponsor_one_id,
            sponsor_one.email AS sponsor_one_email,
            sponsor_one.firstname AS sponsor_one_firstname,
            sponsor_one.lastname AS sponsor_one_lastname,
            sponsor_two.customer_id AS sponsor_two_id,
            sponsor_two.email AS email,
            sponsor_two.firstname AS sponsor_two_firstname,
            sponsor_two.lastname AS sponsor_two_lastname,
            migareference_report_status.status_title,
            migareference_report_status.migareference_report_status_id,
            ((Sum( Case When le.entry_type = 'C'   Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'E'  Then le.amount Else 0 End))*(COUNT( le.report_id)))/(COUNT( le.report_id)) total_credits,
            ((SUM(ue.earn_amount))*(COUNT(ue.report_id)))/(COUNT( ue.report_id)) AS total_earn
            FROM `migareference_report` AS rep
            JOIN migareference_invoice_settings AS inv ON inv.user_id=rep.user_id
            LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=inv.user_id
            LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=inv.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
            LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
            LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
            LEFT JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=rep.currunt_report_status
            LEFT JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
            LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
            LEFT JOIN migarefrence_ledger AS le ON rep.user_id=le.user_id AND rep.migareference_report_id=le.report_id
            LEFT JOIN migareference_user_earnings AS ue ON rep.user_id=ue.refferral_user_id AND rep.migareference_report_id=ue.report_id
            LEFT JOIN migareference_how_to AS ht ON ht.app_id=rep.app_id
            LEFT JOIN migareference_webhook_logs AS wlg ON wlg.report_id = rep.migareference_report_id AND wlg.response_type = 'success' AND   wlg.report_id IS NULL
            WHERE rep.app_id=$app_id AND rep.migareference_report_id=$report_id 
            GROUP BY rep.migareference_report_id";
            return $this->_db->fetchAll($query_option_value);
      }
      public function getStaticIons()
      {
            $default = new Core_Model_Default();
            $base_url= $default->getBaseUrl();
            $root=$base_url."/app/local/modules/Migareference/resources/appicons/";
            $statiIconList['filter_icon_all']=$root."filter_icon_all.png";
            $statiIconList['filter_icon_done']=$root."filter_icon_done.png";
            $statiIconList["filter_icon_pending"]=$root."filter_icon_pending.png";
            $statiIconList["profiled"]=$root."profiled.png";
            $statiIconList["not_profiled"]=$root."not_profiled.png";
            $statiIconList["filter_icon_cancele"]=$root."filter_icon_cancele.png";
            $statiIconList["filter_icon_postpone"]=$root."filter_icon_postpone.png";
            $statiIconList["terms_warrning"]=$root."terms_warrning.png";
            $statiIconList["star"]=$root."star.png";
            $statiIconList["notepad"]=$root."notepad.png";
            $statiIconList["fire"]=$root."fire.png";
            $statiIconList["logo_whatsapp"]=$root."logo-whatsapp.png";
            // Status Icons
            $statiIconList["new_report"]=$root."new_report.png";
            $statiIconList["mandate_required"]=$root."mandate_required.png";
            $statiIconList["paid"]=$root."paid.png";
            $statiIconList["declined"]=$root."declined.png";
            $statiIconList["optional"]=$root."optional.png";
            return $statiIconList;
      }
      public function welcomeEmailTags()
      {
            $notificationTags= [
                              "@@user_name@@",
                              "@@user_email@@",
                              "@@user_firstpassword@@",
                              "@@agent_name@@",
                              "@@app_link@@"
                            ];
            return $notificationTags;
      }
        /**
       * Persist a single qualification change into the historical log table.
       *
       * @param int         $app_id        The current application identifier.
       * @param int         $referrer_id   The referrer whose qualification changed.
       * @param int|null    $previous_id   Previously assigned qualification ID.
       * @param int|null    $new_id        Newly assigned qualification ID.
       * @param float|int   $total_credits Credits that triggered the change.
       * @param int         $value_id      Linked option value identifier (if available).
       * @param string      $action        Action keyword describing the transition.
       *
       * @return void
       */
      protected function logQualificationChange($app_id = 0,$referrer_id = 0,$previous_id = null,$new_id = null,$total_credits = 0,$value_id = 0,$action = '')
      {
        if (!$app_id || !$referrer_id || empty($action)) {
          return;
        }

        // Prepare a normalized row payload that captures the before/after state
        // alongside the total credits that justified the change for later audits.
        $data = [
          'app_id'                    => $app_id,
          'referrer_id'               => $referrer_id,
          'previous_qualification_id' => $previous_id ? intval($previous_id) : null,
          'new_qualification_id'      => $new_id ? intval($new_id) : null,
          'value_id'                  => intval($value_id),
          'total_credits'             => round(floatval($total_credits), 2),
          'action'                    => $action,
          'created_at'                => date('Y-m-d H:i:s'),
        ];

        $this->_db->insert('migareference_qualification_logs', $data);
      }
      /**
       * Load referrer credit totals that are relevant for qualification evaluation.
       *
       * The query keeps administrators out of the result set, optionally restricts
       * the ledger window to a rolling period, and can force specific referrers to
       * be returned even if they are below the current qualification thresholds.
       *
       * @param int        $app_id            Application identifier.
       * @param string     $from_date         Optional ledger start date.
       * @param float|null $minimum_required  Lower bound for total credits.
       * @param array      $required_referrers Additional referrer IDs to include.
       *
       * @return array<int,array<string,mixed>>
       */
      protected function fetchQualificationReferrerCredits($app_id = 0,$from_date = '',$minimum_required = null,$required_referrers = [])
      {
        if (!$app_id) {
          return [];
        }

        $params = [];
        $sql = "SELECT mis.user_id,
                       COALESCE(SUM(CASE WHEN le.entry_type = 'C' THEN le.amount ELSE -le.amount END), 0) AS total_credits
                FROM migareference_invoice_settings AS mis
                LEFT JOIN migareference_app_admins AS ad ON ad.user_id = mis.user_id
                LEFT JOIN migarefrence_ledger AS le ON le.user_id = mis.user_id AND le.app_id = mis.app_id";

        if (!empty($from_date)) {
          // Limit ledger aggregation to the configured rolling window when provided.
          $sql .= " AND le.created_at >= ?";
          $params[] = $from_date;
        }

        $sql .= "
                WHERE mis.app_id = ?
                  AND ad.user_id IS NULL
                GROUP BY mis.user_id";
        $params[] = $app_id;

        $having_parts = [];
        $having_params = [];

        if ($minimum_required !== null) {
          // Apply the lowest active threshold so we can skip obviously ineligible rows.
          $having_parts[] = "total_credits >= ?";
          $having_params[] = $minimum_required;
        }

        if (!empty($required_referrers)) {
          // Always retain current assignments even when their totals dipped below the minimum.
          $placeholders = implode(',', array_fill(0, count($required_referrers), '?'));
          $having_parts[] = "mis.user_id IN ($placeholders)";
          $having_params = array_merge($having_params, array_map('intval', $required_referrers));
        }

        if (!empty($having_parts)) {
          $sql .= " HAVING " . implode(' OR ', $having_parts);
          $params = array_merge($params, $having_params);
        }

        $sql .= " ORDER BY total_credits DESC";

        return $this->_db->fetchAll($sql, $params);
      }
      /**
       * Fetch the timestamp of the latest ledger change so idle apps can be skipped.
       *
       * @param int $app_id
       *
       * @return string|null
       */
      protected function getLastLedgerChangeAt($app_id = 0)
      {
        if (!$app_id) {
          return null;
        }

        return $this->_db->fetchOne(
          "SELECT MAX(created_at) FROM migarefrence_ledger WHERE app_id = ?",
          [$app_id]
        );
      }
      /**
       * Synchronise qualified referrers for a single application.
       *
       * @param int $app_id
       *
       * @return array<string,mixed>
       */
      protected function syncQualificationReferrersForApp($app_id = 0)
      {
        $result = [
          'status'    => 'skipped',
          'inserted'  => [],
          'updated'   => [],
          'removed'   => [],
          'evaluated' => 0,
          'assigned'  => 0,
        ];

        if (!$app_id) {
          return $result;
        }

        $pre_settings = $this->preReportsettigns($app_id);
        if (!count($pre_settings) || intval($pre_settings[0]['enable_qlf']) !== 1) {
          $result['status'] = 'qualification_disabled';
          return $result;
        }

        $qualification_where = "app_id = $app_id AND qlf_status = 1";

        $qualifications = $this->_db->fetchAll("SELECT * FROM migareference_qualifications WHERE $qualification_where ORDER BY qlf_credits DESC");
        if (!count($qualifications)) {
          $result['status'] = 'no_active_qualifications';
          return $result;
        }

        $qualification_map = [];
        foreach ($qualifications as $qualification_item) {
          $qualification_map[$qualification_item['migareference_qualifications_id']] = $qualification_item;
        }

        $grace_days = isset($pre_settings[0]['qlf_grace_days']) ? intval($pre_settings[0]['qlf_grace_days']) : 0;
        if ($grace_days === 0) {
          // Skip busy work when nothing changed recently and no rolling window applies.
          $idle_cutoff = date('Y-m-d H:i:s', strtotime('-60 minutes'));
          $last_change = $this->getLastLedgerChangeAt($app_id);
          if (empty($last_change) || $last_change < $idle_cutoff) {
            $result['status'] = 'skipped_idle';
            return $result;
          }
        }

        $from_date = '';
        if ($grace_days > 0) {
          $from_date = date('Y-m-d H:i:s', strtotime("-{$grace_days} days"));
        }

        $active_qualification_ids = array_keys($qualification_map);
        $current_rows = $this->_db->fetchAll("SELECT * FROM migareference_qualifications_referrers WHERE app_id = ?", [$app_id]);
        $current_by_referrer = [];
        foreach ($current_rows as $current_row) {
          $current_by_referrer[intval($current_row['referrer_id'])] = $current_row;
        }

        $minimum_required = null;
        foreach ($qualifications as $qualification_item) {
          $credits_threshold = floatval($qualification_item['qlf_credits']);
          if ($minimum_required === null || $credits_threshold < $minimum_required) {
            $minimum_required = $credits_threshold;
          }
        }
        if ($minimum_required !== null && $minimum_required <= 0) {
          $minimum_required = null;
        }

        $required_referrers = array_keys($current_by_referrer);
        $referrer_rows = $this->fetchQualificationReferrerCredits($app_id, $from_date, $minimum_required, $required_referrers);
        $referrer_map = [];
        foreach ($referrer_rows as $row) {
          $referrer_map[intval($row['user_id'])] = $row;
        }
        if (!empty($required_referrers)) {
          $missing_required = array_diff($required_referrers, array_keys($referrer_map));
          if (!empty($missing_required)) {
            $extra_rows = $this->fetchQualificationReferrerCredits($app_id, $from_date, null, $missing_required);
            foreach ($extra_rows as $extra_row) {
              $referrer_map[intval($extra_row['user_id'])] = $extra_row;
            }
          }
        }

        $referrer_credits = array_values($referrer_map);
        $result['evaluated'] = count($referrer_credits);

        // Determine the best qualification each referrer currently satisfies.
        $assignments = [];
        foreach ($referrer_credits as $ref_item) {
          $referrer_id = intval($ref_item['user_id']);
          $total_credits = floatval($ref_item['total_credits']);
          foreach ($qualifications as $qualification_item) {
            if ($total_credits >= floatval($qualification_item['qlf_credits'])) {
              $assignments[$referrer_id] = [
                'qualification_id' => intval($qualification_item['migareference_qualifications_id']),
                'value_id'         => intval(isset($qualification_item['value_id']) ? $qualification_item['value_id'] : 0),
                'total_credits'    => $total_credits,
              ];
              break;
            }
          }
        }

        $result['assigned'] = count($assignments);

        $now = date('Y-m-d H:i:s');
        $notifications_queue = [];

        // Insert or update rows for referrers that qualify for a tier right now.
        foreach ($assignments as $referrer_id => $assignment) {
          if (!isset($current_by_referrer[$referrer_id])) {
            $insert_data = [
              'app_id'          => $app_id,
              'value_id'        => $assignment['value_id'],
              'qualification_id'=> $assignment['qualification_id'],
              'referrer_id'     => $referrer_id,
              'created_at'      => $now,
              'updated_at'      => $now,
            ];
            $this->_db->insert("migareference_qualifications_referrers", $insert_data);
            $result['inserted'][] = $referrer_id;
            $this->logQualificationChange(
              $app_id,
              $referrer_id,
              null,
              $assignment['qualification_id'],
              $assignment['total_credits'],
              $assignment['value_id'],
              'qualified'
            );
            $notifications_queue[$referrer_id] = [
              'assignment' => $assignment,
              'previous_qualification_id' => null,
            ];
          } else {
            $existing_item = $current_by_referrer[$referrer_id];
            $update_data = [];
            $needs_update = false;

            if (intval($existing_item['qualification_id']) !== $assignment['qualification_id']) {
              $update_data['qualification_id'] = $assignment['qualification_id'];
              $needs_update = true;
            }

            if (intval($existing_item['value_id']) !== $assignment['value_id']) {
              $update_data['value_id'] = $assignment['value_id'];
              $needs_update = true;
            }

            if ($needs_update) {
              $update_data['updated_at'] = $now;
              $this->_db->update(
                "migareference_qualifications_referrers",
                $update_data,
                ['migareference_qualifications_referrers_id = ?' => $existing_item['migareference_qualifications_referrers_id']]
              );
              $result['updated'][] = $referrer_id;
              $previous_id = intval($existing_item['qualification_id']);
              $action = 'changed';
              if ($previous_id && $previous_id !== $assignment['qualification_id']) {
                $previous_credits = isset($qualification_map[$previous_id]) ? floatval($qualification_map[$previous_id]['qlf_credits']) : null;
                $new_credits = isset($qualification_map[$assignment['qualification_id']]) ? floatval($qualification_map[$assignment['qualification_id']]['qlf_credits']) : null;
                if ($previous_credits !== null && $new_credits !== null) {
                  if ($new_credits > $previous_credits) {
                    $action = 'promoted';
                  } elseif ($new_credits < $previous_credits) {
                    $action = 'demoted';
                  }
                }
              }
              $this->logQualificationChange(
                $app_id,
                $referrer_id,
                $previous_id,
                $assignment['qualification_id'],
                $assignment['total_credits'],
                $assignment['value_id'],
                $action
              );
              $notifications_queue[$referrer_id] = [
                'assignment' => $assignment,
                'previous_qualification_id' => intval($existing_item['qualification_id']),
              ];
            }
          }
        }

        // Remove referrers that no longer reach any of the active qualifications.
        foreach ($current_by_referrer as $referrer_id => $existing_item) {
          if (!isset($assignments[$referrer_id])) {
            $this->_db->delete(
              "migareference_qualifications_referrers",
              ['migareference_qualifications_referrers_id = ?' => $existing_item['migareference_qualifications_referrers_id']]
            );
            $result['removed'][] = $referrer_id;
            $total_credits = isset($referrer_map[$referrer_id]) ? floatval($referrer_map[$referrer_id]['total_credits']) : 0;
            $this->logQualificationChange(
              $app_id,
              $referrer_id,
              intval($existing_item['qualification_id']),
              null,
              $total_credits,
              isset($existing_item['value_id']) ? intval($existing_item['value_id']) : 0,
              'dequalified'
            );
          }
        }

        if (count($notifications_queue)) {
          $notification_model = new Migareference_Model_Notification();
          $notification_settings = $notification_model->getNotificationByAppId($app_id);

          if (!empty($notification_settings)) {
            $default = new Core_Model_Default();
            $base_url = $default->getBaseUrl();
            $application = $this->application($app_id);
            $app_name = (count($application) && isset($application[0]['name'])) ? $application[0]['name'] : '';

            // Fan out notifications to every referrer whose qualification changed.
            foreach ($notifications_queue as $referrer_id => $info) {
              $qualification_id = $info['assignment']['qualification_id'];
              if (!isset($qualification_map[$qualification_id])) {
                continue;
              }

              $qualification = $qualification_map[$qualification_id];
              $customer = $this->getSingleuser($app_id,$referrer_id);
              if (!count($customer)) {
                continue;
              }

              $customer = $customer[0];
              $icon_url = '';
              if (!empty($qualification['qlf_file'])) {
                $icon_url = $base_url . '/images/application/' . $app_id . '/features/migareference/' . $qualification['qlf_file'];
              }

              $tags = ['@@first_name@@','@@last_name@@','@@qualification_title@@','@@icon_url@@','@@app_name@@','app@@name@@'];
              $tag_values = [
                isset($customer['firstname']) ? $customer['firstname'] : '',
                isset($customer['lastname']) ? $customer['lastname'] : '',
                $qualification['qlf_name'],
                $icon_url,
                $app_name,
                $app_name,
              ];

              if (!empty($notification_settings['email_subject']) && !empty($notification_settings['email_text'])) {
                $email_data = [
                  'email_title'     => str_replace($tags, $tag_values, $notification_settings['email_subject']),
                  'email_text'      => str_replace($tags, $tag_values, $notification_settings['email_text']),
                  'calling_method'  => 'Qualification',
                ];
                if (!empty($notification_settings['bcc'])) {
                  $email_data['bcc_to_email'] = $notification_settings['bcc'];
                }
                $this->sendMail($email_data,$app_id,$referrer_id);
              }

              if (!empty($notification_settings['push_title']) && !empty($notification_settings['push_text'])) {
                $push_data = [
                  'open_feature'   => isset($notification_settings['ref_credits_api_open_feature']) ? intval($notification_settings['ref_credits_api_open_feature']) : 0,
                  'feature_id'     => isset($notification_settings['ref_credits_api_feature_id']) ? intval($notification_settings['ref_credits_api_feature_id']) : 0,
                  'custom_url'     => isset($notification_settings['ref_credits_api_custom_url']) ? $notification_settings['ref_credits_api_custom_url'] : '',
                  'cover_image'    => isset($notification_settings['logo_url']) ? $notification_settings['logo_url'] : '',
                  'app_id'         => $app_id,
                  'calling_method' => 'Qualification',
                  'push_title'     => str_replace($tags, $tag_values, $notification_settings['push_title']),
                  'push_text'      => str_replace($tags, $tag_values, $notification_settings['push_text']),
                ];
                $this->sendPush($push_data,$app_id,$referrer_id);
              }

              if (!empty($notification_settings['webhook'])) {
                $query_params = [
                  'app_id'                    => $app_id,
                  'referrer_id'               => $referrer_id,
                  'referrer_firstname'        => isset($customer['firstname']) ? $customer['firstname'] : '',
                  'referrer_lastname'         => isset($customer['lastname']) ? $customer['lastname'] : '',
                  'referrer_email'            => isset($customer['email']) ? $customer['email'] : '',
                  'qualification_id'          => $qualification['migareference_qualifications_id'],
                  'qualification_title'       => $qualification['qlf_name'],
                  'total_credits'             => $info['assignment']['total_credits'],
                  'previous_qualification_id' => $info['previous_qualification_id'],
                ];
                $separator = (strpos($notification_settings['webhook'], '?') === false) ? '?' : '&';
                $webhook_url = $notification_settings['webhook'] . $separator . http_build_query($query_params);
                $this->triggerWebhook($webhook_url,[
                  'app_id' => $app_id,
                  'user_id' => $referrer_id,
                  'type' => 'qualification',
                  'calling_method' => 'qualification',
                ]);
              }
            }
          }
        }

        $result['status'] = 'success';
        return $result;
      }
      /**
       * Execute the qualification synchronisation for every eligible application.
       *
       * @return array<int,array<string,mixed>>
       */
      public function syncQualificationReferrersForAllApps()
      {
        $results = [];

        // Load every application that has the qualification feature enabled so we only
        // process apps that actually expect referrer tiers to be synchronized.
        $rows = $this->_db->fetchAll(
          "SELECT app_id FROM migareference_pre_report_settings WHERE enable_qlf = 1"
        );

        if (!count($rows)) {
          return $results;
        }

        // Deduplicate applications in case multiple settings rows exist for the same
        // app; we only need to run the synchronization routine once per app.
        $app_ids = [];
        foreach ($rows as $row) {
          $app_id = intval($row['app_id']);
          if ($app_id) {
            $app_ids[$app_id] = true;
          }
        }

        // Execute the per-app synchronization routine for each eligible app and
        // capture their individual results keyed by app identifier.
        foreach (array_keys($app_ids) as $app_id) {
          $results[$app_id] = $this->syncQualificationReferrersForApp($app_id);
        }

        return $results;
      }
      public static function automationTriggerscron($callType='')//$callType='test' for testing or '' for live
      {
        $migareference    = new Migareference_Model_Db_Table_Migareference();        
        $qualifiaction_result = $migareference->syncQualificationReferrersForAllApps();
        return $qualifiaction_result;
        $callType= ($callType=='test') ? 'test' : 'live' ;
        // Get Enabled Automation Triggers
         $tempItem         = [];//only for testing
         $hours_now        = date('H');
         $temp_app_id      = 0;
         $pre_settings     = [];
         $referrers_list   = [];
         $today_date       = date('d-m-Y');           
         $default          = new Core_Model_Default();
         $base_url         = $default->getBaseUrl();
         $host_name        = gethostname();
         $host_ip          = shell_exec('nslookup ' . $host_name);
         //Now we have to send email once to all Addmin users and Agents if they exist (Instead to send email many times for each trigger)
         //1. Keep previous process same just comment previous email and push forwareder
         //2. Define a gloabl array that will keep all email, push and their respective user_id and type (admin or agent) along with content
         //3. Define a new method and pass above array to this method to send email and push
         //4. Send email and push to all users in one go
         //5. Keep the log of all email and push in a table         
         //Gloabl array
         $one_go_email_collection=[];
         try {
            $automation_triggers   = $migareference->getActiveAutomTriggers();
            foreach ($automation_triggers as $triggerItem) {              
              // if ($triggerItem['auto_rem_trigger_hour']==$hours_now) {              
              if (true) {              
                $auto_rem_action     = $triggerItem['auto_rem_action'];
                $auto_rem_trigger    = $triggerItem['auto_rem_trigger'];
                $migarefrence_report_reminder_auto_id = $triggerItem['migarefrence_report_reminder_auto_id'];
                $recap_email_enable  = $triggerItem['auto_rem_add_recap_email'];//per trigger
                $recap_email_template= $triggerItem['auto_rem_recap_email_template'];//per trigger template
                $auto_rem_title      = $triggerItem['auto_rem_title'];                
                $auto_rem_recap_email_header = "<strong>".$triggerItem['auto_rem_recap_email_header']."</strong>".$recap_email_template;
                $auto_rem_reports    = $triggerItem['auto_rem_reports'];
                $auto_rem_max_rating = $triggerItem['auto_rem_max_rating'];
                $auto_rem_min_rating = $triggerItem['auto_rem_min_rating'];
                $auto_rem_fix_rating = $triggerItem['auto_rem_fix_rating'];
                $auto_rem_type       = $triggerItem['auto_rem_type'];
                $auto_rem_days       = $triggerItem['auto_rem_days'];
                $auto_rem_percentage = $triggerItem['auto_rem_percent'];
                $auto_rem_engagement = $triggerItem['auto_rem_engagement'];
                $app_id              = $triggerItem['app_id'];
                $trigger_status      = $triggerItem['auto_rem_report_trigger_status'];
                
                // Check if the trigger_status contains '@'
               if (strpos($trigger_status, '@') !== false) {
                 // If yes, explode the string and remove the last element
                 $trigger_status = explode('@', $trigger_status);
                 array_pop($trigger_status);
               } else {
                 // If not, create an array with the single element
                 $trigger_status = [$trigger_status];
                }
                if ($temp_app_id!=$app_id) { //Per app methods and settings this help to optimize the algorithm for multiple apps
                  $temp_app_id    = $app_id;
                  $pre_settings   = $migareference->preReportsettigns($app_id);                
                  $admin_customers= $migareference->getAdminCustomers($app_id);
                  $referrers_list = $migareference->getAllReferralUsers($app_id);                      
                  // $customers_list = $migareference->getAllNewUsers($app_id,48,50);:Dperecated //get users whos register time is over 48 hours and less than 50 hours(50hours to avoid duplication of trigger)                     
                  $customers_list = $migareference->getAllNewUsers($app_id,48,50); //get refererrer who did not accpet terms within 48 HOurs
                  $application    = $migareference->application($app_id);
                  $app_name       = $application[0]['name'];                                        
                  // Mail Footer                
                  $footer="<br><br><small><small><small>Sender: App ID ".$app_id." APP Name: ".$app_name." DOMAIN: ".$base_url." IP: ".$_SERVER['SERVER_ADDR']."</small></small></small>";      
                }              
                switch ($auto_rem_trigger) {
                  case 1: //Referrer posted a certain number of reports                                        
                      $referrer_report_list=$migareference->getReferrerReports($auto_rem_reports,$app_id);
                      foreach ($referrer_report_list as $reportItem) {
                        if ($auto_rem_max_rating>=$reportItem['rating'] && $auto_rem_min_rating<=$reportItem['rating']) {                          
                          $user_id        = $reportItem['user_id'];                                                    
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                                                                              
                          if (!count($find_in_log))//NOT Triggereed Previously                                                    
                          {
                            //Store respone in one go array as per trigger ID
                            if ($callType=='live') {                              
                              $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);                            
                            }
                            $invoice_settings=$migareference->getpropertysettings($app_id,$user_id);              
                            // Recap Email
                            if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$invoice_settings[0],$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                'user_id'=>$user_id,
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'type'=>'referrer',
                                'data'=>$formated_data,
                              ];
                            }
                          }
                        }
                      }
                    break;
                  case 2: //Referrer opens the APP one or more time in the past XX days                      
                        foreach ($referrers_list as $referrerItem) {
                          if ($auto_rem_min_rating<=$referrerItem['rating'] && $auto_rem_max_rating>=$referrerItem['rating']) {                          
                            $user_id        = $referrerItem['user_id'];                                                
                            $last_visit     = $migareference->getLastvisit($app_id,$user_id);                        
                            $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                            //Last visit differene
                            $last_visit_days = $migareference->daysDiffrence($last_visit[0]['created_at'],$pre_settings[0]['working_days']);                        
                            // Get last trigger differenc
                            $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                            $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                                         
                            /* Automation Filter
                              // 1. Last Activity visit
                              // 2. Last Trigger                          
                              */
                            if ($auto_rem_days>=$last_visit_days &&  $auto_rem_days<=$last_trigger_days)
                            {
                              if ($callType=='live') { 
                                $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                              }
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                  'user_id'=>$user_id,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'type'=>'referrer',
                                  'data'=>$formated_data,
                                ];
                              }
                            }
                          }
                        }
                    break;
                  case 3: //First time a Referrer is registered

                        /* RULES List:
                        // 1. Only Useres Registered by self from app side Exlcude Admin Users
                        // 2. NOT Triggereed Previously
                        // 3. Rating Filter
                        // 4. Registered Yesterday  
                        */

                        foreach ($referrers_list as $referrerItem) {
                          $created_at   = date('d-m-Y', strtotime($referrerItem['created_at']));                          
                          $pre_date     = date('d-m-Y', strtotime('-1 day', strtotime(date('d-m-Y'))));                          
                          if($referrerItem['referrer_source']==1 
                          // && $created_at==$pre_date
                          && $auto_rem_min_rating<=$referrerItem['rating']
                          && $auto_rem_max_rating>=$referrerItem['rating']){                                                       
                            $user_id        = $referrerItem['user_id'];                                                  
                            $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                                                                            
                            /* Automation Filter
                            // 1. Only Useres Registered by self Exlcude Admin Users
                            // 2. NOT Triggereed Previously                        
                            */
                            if (!count($find_in_log))
                            {                            
                              //Store respone in one go array as per trigger ID
                              if ($callType=='live') { 
                                $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                              }
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                  'user_id'=>$user_id,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'type'=>'referrer',
                                  'data'=>$formated_data,
                                ];
                              }
                            }
                          }
                        }
                    break;
                  case 4: //A report still in one state for xx days
                      $report_list=$migareference->getAllReports($app_id);   
                      foreach ($report_list as $report_item) {
                        if (in_array($report_item['currunt_report_status'], $trigger_status) && $auto_rem_min_rating<=$report_item['rating'] && $auto_rem_max_rating>=$report_item['rating']) {                          
                              $report_id    = $report_item['migareference_report_id'];
                              $currunt_report_status    = $report_item['currunt_report_status'];
                              $user_id      = $report_item['user_id'];    
                              //count when last update of report was made
                              $activity_log = $migareference->getLastReportActivity($report_id,'Update Status');                            
                              $last_status_update_date = (count($activity_log)) ? $activity_log[0]['created_at'] : $report_item['report_created_at'] ;
                              $last_status_update = $migareference->daysDiffrence($last_status_update_date,$pre_settings[0]['working_days']);    
                              //count how many days before the tirgger was fired (when trigger 4 was fired for x report against x report status id (currunt_report_status))
                              $find_in_log  = $migareference->findInLogReportTrigger($auto_rem_trigger,$report_id,$currunt_report_status);

                              if ($auto_rem_days<=$last_status_update  &&  COUNT($find_in_log)==0) //per trigger per report status one trigger
                              {
                                if ($callType=='live') { 
                                  $migareference->doAutomation($triggerItem,$app_id,$user_id,$report_item,$pre_settings,$admin_customers,$footer);
                                }
                                if ($recap_email_enable==1) {    
                                  $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                  $formated_data  = $migareference->formateEmailTemplate('report',$report_item,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                  $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                    'user_id'=>$user_id,
                                    'sponsor_customers'=>$sponsor_customers,
                                    'trigger_title'=>$auto_rem_recap_email_header,
                                    'trigger_title_string'=>$trigger_title_string,
                                    'recap_email_description'=>$recap_email_template,
                                    'type'=>'report',
                                    'data'=>$formated_data,
                                  ];
                                }
                              }
                          }
                      }
                    break;
                  case 5: //Referrer Birthday                  
                    foreach ($referrers_list as $referrerItem) {
                      $todayDate = strtotime(date('Y-m-d')); 
                      $birthDateDayMonth = date('d-m', $referrerItem['birthdate']);
                      $todayDateDayMonth = date('d-m', $todayDate);
                    
                      if ($referrerItem['birthdate']!=0  && $birthDateDayMonth===$todayDateDayMonth && $auto_rem_min_rating<=$referrerItem['rating'] && $auto_rem_max_rating>=$referrerItem['rating']) {
                        $user_id           = $referrerItem['user_id'];                      
                        $find_in_log       = $migareference->findInLog($user_id,$auto_rem_trigger);                      
                        $last_trigger_days=100000000;
                        if (COUNT($find_in_log)) {
                          $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);   
                        }
                        if ($last_trigger_days>360)
                        {
                          if ($callType=='live') { 
                            $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                          }
                           // Recap Email
                           if ($recap_email_enable==1) {    
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                            $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                            $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                              'user_id'=>$user_id,
                              'sponsor_customers'=>$sponsor_customers,
                              'trigger_title'=>$auto_rem_recap_email_header,
                              'trigger_title_string'=>$trigger_title_string,
                              'recap_email_description'=>$recap_email_template,
                              'type'=>'referrer',
                              'data'=>$formated_data,
                            ];
                          }
                        }
                      }
                    }
                    break;
                  case 6: //Referrer not called since xx days.                  
                    foreach ($referrers_list as $referrerItem) {
                      if ($auto_rem_fix_rating==$referrerItem['rating']) {
                        $user_id     = $referrerItem['user_id'];                                                           
                        $call_log    = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                      
                        $last_call   = $migareference->daysDiffrence($call_log[0]['created_at'],$pre_settings[0]['working_days']);
                        $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                        $find_in_log  = $migareference->findInLog($user_id,$auto_rem_trigger);                      
                        $last_trigger_days=100000000;
                        if (COUNT($find_in_log)) {
                          $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);   
                        }                                                                                    
                        if ($auto_rem_days<=$last_call  &&  $auto_rem_days<=$last_trigger_days)
                        {
                          if ($callType=='live') {                                                        
                            $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                          }
                          // Recap Email
                          if ($recap_email_enable==1) {    
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                            $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                            $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                              'user_id'=>$user_id,
                              'sponsor_customers'=>$sponsor_customers,
                              'trigger_title'=>$auto_rem_recap_email_header,
                              'trigger_title_string'=>$trigger_title_string,
                              'recap_email_description'=>$recap_email_template,
                              'type'=>'referrer',
                              'data'=>$formated_data,
                            ];
                          }
                        }
                      }
                    }                  
                    break;
                  case 7: //Referrer called since xx days.                    
                      foreach ($referrers_list as $referrerItem) {
                        if ($auto_rem_min_rating<=$referrerItem['rating'] && $auto_rem_max_rating>=$referrerItem['rating']) {                        
                          $user_id        = $referrerItem['user_id'];                              
                          $call_log       = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                                                
                          $last_call = $migareference->daysDiffrence($call_log[0]['created_at'],$pre_settings[0]['working_days']);
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          if ($auto_rem_days>=$last_call &&  $auto_rem_days<=$last_trigger_days)
                          {
                            if ($callType=='live') { 
                              $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                            }
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                  'user_id'=>$user_id,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'type'=>'referrer',
                                  'data'=>$formated_data,
                                ];
                              }
                          }
                        }
                      }
                    break;
                  case 8: //No changes in engagement rating of a referrer in the past xx days.                    
                      foreach ($referrers_list as $referrerItem) {
                        if ($auto_rem_min_rating<=$referrerItem['rating'] && $auto_rem_max_rating>=$referrerItem['rating']) {                        
                          $user_id        = $referrerItem['user_id'];                                                
                          $engagement_log = $migareference->getLastEngagemnetActivity($invoice_id);                        
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                        
                          $last_engagement = $migareference->daysDiffrence($engagement_log[0]['created_at'],$pre_settings[0]['working_days']);
                          $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          // Get rating                        
                          if ($auto_rem_days<=$last_engagement &&  $auto_rem_days<=$last_trigger_days)
                          {
                            if ($callType=='live') { 
                              $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                            }
                            // Recap Email
                            if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                'user_id'=>$user_id,
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'type'=>'referrer',
                                'data'=>$formated_data,
                              ];
                            }
                          }
                      }
                      }
                    break;
                  case 9: //Monitor failed invitations
                      foreach ($referrers_list as $referrerItem) {
                          $user_id        = $referrerItem['user_id'];                                                
                          $invitaion_log = $migareference->invitationsCount($app_id,);                                                                                                      
                          $invitation_percent = (count($invitaion_log)>0) ? ($invitaion_log[0]['reports']/$invitaion_log[0]['share'])*100 : 0 ;
                          $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          // Get rating                        
                          if ($auto_rem_percentage<=$invitation_percent &&  $auto_rem_days<=$last_trigger_days)
                          {
                            if ($callType=='live') { 
                              $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);
                            }
                           // Recap Email
                           if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                'user_id'=>$user_id,
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'type'=>'referrer',
                                'data'=>$formated_data,
                              ];
                            }
                          }
                      }                      
                    break;
                   case 10: //Referrers who did not accept terms in 48 hours
                        foreach ($customers_list as $referrerItem) {
                          $user_id        = $referrerItem['customer_id'];                                                                            
                          if ($callType=='live') { 
                            $migareference->doAutomation($triggerItem,$app_id,$user_id,[],$pre_settings,$admin_customers,$footer);                            
                          }
                          // Recap Email
                          if ($recap_email_enable==1) {    
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                            $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                            $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                              'user_id'=>$user_id,
                              'sponsor_customers'=>$sponsor_customers,
                              'trigger_title'=>$auto_rem_recap_email_header,
                              'trigger_title_string'=>$trigger_title_string,
                              'recap_email_description'=>$recap_email_template,
                              'type'=>'referrer',
                              'data'=>$formated_data,
                            ];
                          }
                        }
                    break;
                }
              }
            }
            //If recap email is enabled
            if ($pre_settings[0]['recap_email_enable']==1) {
              $one_go_email_push = $migareference->oneGoAutomationReminderEmail($callType,$one_go_email_collection,$pre_settings,$admin_customers,$footer,$app_id);
              $tempItem[]['one_go_email_collection']=$one_go_email_collection;                          
              $tempItem[]['one_go_email_push']=$one_go_email_push;                          
            }else {
              $tempItem[]="Recap Email is disabled";
            }            
          } catch (Exception $e) {
           return $e->getMessage();
          }
          return $tempItem;
      }
      public static function TestautomationTriggerscron($param_app_id=0)
      {        
        // Get Enabled Automation Triggers
         $migareference    = new Migareference_Model_Db_Table_Migareference();        
         $tempItem         = [];//only for testing
         $hours_now        = date('H');
         $temp_app_id      = 0;
         $pre_settings     = [];
         $referrers_list   = [];
         $today_date       = date('d-m-Y');           
         $default          = new Core_Model_Default();
         $base_url         = $default->getBaseUrl();
         $host_name        = gethostname();
         $host_ip          = shell_exec('nslookup ' . $host_name);
         //Now we have to send email once to all Addmin users and Agents if they exist (Instead to send email many times for each trigger)
         //1. Keep previous process same just comment previous email and push forwareder
         //2. Define a gloabl array that will keep all email, push and their respective user_id and type (admin or agent) along with content
         //3. Define a new method and pass above array to this method to send email and push
         //4. Send email and push to all users in one go
         //5. Keep the log of all email and push in a table         
         //Gloabl array
         $one_go_email_collection=[];         
         try {
            $automation_triggers   = $migareference->getActiveAutomTriggers();
            foreach ($automation_triggers as $triggerItem) {              
              if ($triggerItem['auto_rem_trigger_hour']=$hours_now) {              
                $auto_rem_action     = $triggerItem['auto_rem_action'];
                $migarefrence_report_reminder_auto_id = $triggerItem['migarefrence_report_reminder_auto_id'];
                $auto_rem_trigger    = $triggerItem['auto_rem_trigger'];
                $recap_email_enable  = $triggerItem['auto_rem_add_recap_email'];//per trigger
                $recap_email_template= $triggerItem['auto_rem_recap_email_template'];//per trigger template
                $auto_rem_title      = $triggerItem['auto_rem_title'];
                $auto_rem_recap_email_header = "<strong>".$triggerItem['auto_rem_recap_email_header']."</strong>".$recap_email_template;
                $trigger_title_string=$triggerItem['auto_rem_recap_email_header'];
                $auto_rem_reports    = $triggerItem['auto_rem_reports'];
                $auto_rem_max_rating = $triggerItem['auto_rem_max_rating'];
                $auto_rem_min_rating = $triggerItem['auto_rem_min_rating'];
                $auto_rem_fix_rating = $triggerItem['auto_rem_fix_rating'];
                $auto_rem_type       = $triggerItem['auto_rem_type'];
                $auto_rem_days       = $triggerItem['auto_rem_days'];
                $auto_rem_percentage = $triggerItem['auto_rem_percent'];
                $auto_rem_engagement = $triggerItem['auto_rem_engagement'];
                $app_id              = $triggerItem['app_id'];
                $trigger_status      = $triggerItem['auto_rem_report_trigger_status'];
                
                // Check if the trigger_status contains '@'
               if (strpos($trigger_status, '@') !== false) {
                 // If yes, explode the string and remove the last element
                 $trigger_status = explode('@', $trigger_status);
                 array_pop($trigger_status);
               } else {
                 // If not, create an array with the single element
                 $trigger_status = [$trigger_status];
                }
                if ($temp_app_id!=$app_id && $app_id==$param_app_id) { //Per app methods and settings this help to optimize the algorithm for multiple apps
                  $temp_count_one++;
                  $temp_app_id    = $app_id;
                  $pre_settings   = $migareference->preReportsettigns($app_id);                
                  $admin_customers= $migareference->getAdminCustomers($app_id);
                  $referrers_list = $migareference->getAllReferralUsers($app_id);                      
                  $start_index = 0; // Starting from the first element
                  $length = 3;     // Number of elements to extract
                  $referrers_list = array_slice($referrers_list, $start_index, $length);
                  // $customers_list = $migareference->getAllNewUsers($app_id,48,50);:Dperecated //get users whos register time is over 48 hours and less than 50 hours(50hours to avoid duplication of trigger)                     
                  $customers_list = $migareference->getAllNewUsers($app_id,0,500000); //get refererrer who did not accpet terms within 48 HOurs
                  $customers_list = array_slice($customers_list, $start_index, $length);
                  $application    = $migareference->application($app_id);
                  $app_name       = $application[0]['name'];                                        
                  // Mail Footer                
                  $footer="<br><br><small><small><small>Sender: App ID ".$app_id." APP Name: ".$app_name." DOMAIN: ".$base_url." IP: ".$_SERVER['SERVER_ADDR']."</small></small></small>";      
                }              
                switch ($auto_rem_trigger) {
                  case 1: //Referrer posted a certain number of reports                                        
                    $referrer_report_list = array_slice($migareference->getReferrerReports($auto_rem_reports, $app_id), $start_index, $length);
                      foreach ($referrer_report_list as $reportItem) {
                        if ($auto_rem_max_rating=$reportItem['rating'] && $auto_rem_min_rating=$reportItem['rating']) {                          
                          $user_id        = $reportItem['user_id'];                                                    
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                                                                              
                          if (!count($find_in_log) || count($find_in_log))//NOT Triggereed Previously                                                    
                          {
                            //Store respone in one go array as per trigger ID                            
                            $invoice_settings=$migareference->getpropertysettings($app_id,$user_id);              
                            // Recap Email
                            if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$invoice_settings[0],$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                'user_id'=>$user_id,
                                'type'=>'referrer',
                                'raw_data'=>$invoice_settings[0],
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'data'=>$formated_data,
                              ];
                            }
                          }
                        }
                      }
                    break;
                  case 2: //Referrer opens the APP one or more time in the past XX days                      
                        foreach ($referrers_list as $referrerItem) {
                          if ($auto_rem_min_rating=$referrerItem['rating'] && $auto_rem_max_rating=$referrerItem['rating']) {                          
                            $user_id        = $referrerItem['user_id'];                                                
                            $last_visit     = $migareference->getLastvisit($app_id,$user_id);                        
                            $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                            //Last visit differene
                            $last_visit_days = $migareference->daysDiffrence($last_visit[0]['created_at'],$pre_settings[0]['working_days']);                        
                            // Get last trigger differenc
                            $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                            $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                                         
                            /* Automation Filter
                              // 1. Last Activity visit
                              // 2. Last Trigger                          
                              */
                            if ($auto_rem_days=$last_visit_days &&  $auto_rem_days=$last_trigger_days)
                            {                              
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                  'user_id'=>$user_id,
                                  'type'=>'referrer',
                                  'raw_data'=>$referrerItem,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'data'=>$formated_data,
                                ];
                              }
                            }
                          }
                        }
                    break;
                  case 3: //First time a Referrer is registered

                        /* RULES List:
                        // 1. Only Useres Registered by self from app side Exlcude Admin Users
                        // 2. NOT Triggereed Previously
                        // 3. Rating Filter
                        // 4. Registered Yesterday  
                        */

                        foreach ($referrers_list as $referrerItem) {
                          $created_at   = date('d-m-Y', strtotime($referrerItem['created_at']));                          
                          $pre_date     = date('d-m-Y', strtotime('-1 day', strtotime(date('d-m-Y'))));                          
                          if($referrerItem['referrer_source']==1 
                          // && $created_at==$pre_date
                          && $auto_rem_min_rating=$referrerItem['rating']
                          && $auto_rem_max_rating=$referrerItem['rating']){                                                       
                            $user_id        = $referrerItem['user_id'];                                                  
                            $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                                                                            
                            /* Automation Filter
                            // 1. Only Useres Registered by self Exlcude Admin Users
                            // 2. NOT Triggereed Previously                        
                            */
                            if (!count($find_in_log) || count($find_in_log))
                            {                            
                              //Store respone in one go array as per trigger ID                              
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                  'user_id'=>$user_id,
                                  'type'=>'referrer',
                                  'raw_data'=>$referrerItem,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'data'=>$formated_data,
                                ];
                              }
                            }
                          }
                        }
                    break;
                  case 4: //A report still in one state for xx days
                      $report_list=$migareference->getAllReports($app_id);   
                      $report_list = array_slice($report_list, $start_index, $length);                      
                      foreach ($report_list as $report_item) {
                        if ($auto_rem_min_rating=$report_item['rating'] && $auto_rem_max_rating=$report_item['rating']) {                          
                              $report_id    = $report_item['migareference_report_id'];
                              $currunt_report_status    = $report_item['currunt_report_status'];
                              $user_id      = $report_item['user_id'];    
                              //count when last update of report was made
                              $activity_log = $migareference->getLastReportActivity($report_id,'Update Status');                            
                              $last_status_update_date = (count($activity_log)) ? $activity_log[0]['created_at'] : $report_item['report_created_at'] ;
                              $last_status_update = $migareference->daysDiffrence($last_status_update_date,$pre_settings[0]['working_days']);    
                              //count how many days before the tirgger was fired (when trigger 4 was fired for x report against x report status id (currunt_report_status))
                              $find_in_log  = $migareference->findInLogReportTrigger($auto_rem_trigger,$report_id,$currunt_report_status);

                              if ($auto_rem_days=$last_status_update) //per trigger per report status one trigger
                              {                                
                                if ($recap_email_enable==1) {                                      
                                  $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                  $formated_data  = $migareference->formateEmailTemplate('report',$report_item,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                  $one_go_email_collection[$migarefrence_report_reminder_auto_id][]=[
                                    'user_id'=>$user_id,
                                    'type'=>'report',
                                    'raw_data'=>$report_item,
                                    'sponsor_customers'=>$sponsor_customers,
                                    'trigger_title'=>$auto_rem_recap_email_header,
                                    'trigger_title_string'=>$trigger_title_string,
                                    'recap_email_description'=>$recap_email_template,
                                    'data'=>$formated_data,
                                  ];
                                }
                              }
                          }
                      }
                    break;
                  case 5: //Referrer Birthday                  
                    foreach ($referrers_list as $referrerItem) {
                      $todayDate = strtotime(date('Y-m-d')); 
                      $birthDateDayMonth = date('d-m', $referrerItem['birthdate']);
                      $todayDateDayMonth = date('d-m', $todayDate);
                    
                      if ($referrerItem['birthdate']=0  && $birthDateDayMonth=$todayDateDayMonth && $auto_rem_min_rating=$referrerItem['rating'] && $auto_rem_max_rating=$referrerItem['rating']) {
                        $user_id           = $referrerItem['user_id'];                      
                        $find_in_log       = $migareference->findInLog($user_id,$auto_rem_trigger);                      
                        $last_trigger_days=100000000;
                        if (COUNT($find_in_log)) {
                          $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);   
                        }
                        if ($last_trigger_days>360)
                        {                          
                           // Recap Email
                           if ($recap_email_enable==1) {    
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                            $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                            $one_go_email_collection[5][]=[
                              'user_id'=>$user_id,
                              'type'=>'referrer',
                              'raw_data'=>$referrerItem,
                              'sponsor_customers'=>$sponsor_customers,
                              'trigger_title'=>$auto_rem_recap_email_header,
                              'trigger_title_string'=>$trigger_title_string,
                              'recap_email_description'=>$recap_email_template,
                              'data'=>$formated_data,
                            ];
                          }
                        }
                      }
                    }
                    break;
                  case 6: //Referrer not called since xx days.                  
                    foreach ($referrers_list as $referrerItem) {
                      if ($auto_rem_fix_rating=$referrerItem['rating']) {
                        $user_id     = $referrerItem['user_id'];                                                           
                        $call_log    = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                      
                        $last_call   = $migareference->daysDiffrence($call_log[0]['created_at'],$pre_settings[0]['working_days']);
                        $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                        $find_in_log  = $migareference->findInLog($user_id,$auto_rem_trigger);                      
                        $last_trigger_days=100000000;
                        if (COUNT($find_in_log)) {
                          $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);   
                        }                                                                                    
                        if ($auto_rem_days=$last_call  &&  $auto_rem_days=$last_trigger_days)
                        {                                                                                 
                          // Recap Email
                          if ($recap_email_enable==1) {    
                            $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                            $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                            $one_go_email_collection[6][]=[
                              'user_id'=>$user_id,
                              'type'=>'referrer',
                              'raw_data'=>$referrerItem,
                              'sponsor_customers'=>$sponsor_customers,
                              'trigger_title'=>$auto_rem_recap_email_header,
                              'trigger_title_string'=>$trigger_title_string,
                              'recap_email_description'=>$recap_email_template,
                              'data'=>$formated_data,
                            ];
                          }
                        }
                      }
                    }                  
                    break;
                  case 7: //Referrer called since xx days.                    
                      foreach ($referrers_list as $referrerItem) {
                        if ($auto_rem_min_rating=$referrerItem['rating'] && $auto_rem_max_rating=$referrerItem['rating']) {                        
                          $user_id        = $referrerItem['user_id'];                              
                          $call_log       = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                                                
                          $last_call = $migareference->daysDiffrence($call_log[0]['created_at'],$pre_settings[0]['working_days']);
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          if ($auto_rem_days=$last_call &&  $auto_rem_days=$last_trigger_days)
                          {                            
                              // Recap Email
                              if ($recap_email_enable==1) {    
                                $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                                $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                                $one_go_email_collection[7][]=[
                                  'user_id'=>$user_id,
                                  'type'=>'referrer',
                                  'raw_data'=>$referrerItem,
                                  'sponsor_customers'=>$sponsor_customers,
                                  'trigger_title'=>$auto_rem_recap_email_header,
                                  'trigger_title_string'=>$trigger_title_string,
                                  'recap_email_description'=>$recap_email_template,
                                  'data'=>$formated_data,
                                ];
                              }
                          }
                        }
                      }
                    break;
                  case 8: //No changes in engagement rating of a referrer in the past xx days.                    
                      foreach ($referrers_list as $referrerItem) {
                        if ($auto_rem_min_rating=$referrerItem['rating'] && $auto_rem_max_rating=$referrerItem['rating']) {                        
                          $user_id        = $referrerItem['user_id'];                                                
                          $engagement_log = $migareference->getLastEngagemnetActivity($invoice_id);                        
                          $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);                        
                          $last_engagement = $migareference->daysDiffrence($engagement_log[0]['created_at'],$pre_settings[0]['working_days']);
                          $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          // Get rating                        
                          if ($auto_rem_days=$last_engagement &&  $auto_rem_days=$last_trigger_days)
                          {                            
                            // Recap Email
                            if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[8][]=[
                                'user_id'=>$user_id,
                                'type'=>'referrer',
                                'raw_data'=>$referrerItem,
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'data'=>$formated_data,
                              ];
                            }
                          }
                      }
                      }
                    break;
                  case 9: //Monitor failed invitations
                      foreach ($referrers_list as $referrerItem) {
                          $user_id        = $referrerItem['user_id'];                                                
                          $invitaion_log = $migareference->invitationsCount($app_id,);                                                                                                      
                          $invitation_percent = (count($invitaion_log)>0) ? ($invitaion_log[0]['reports']/$invitaion_log[0]['share'])*100 : 0 ;
                          $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                          $log_stamp   = (strpos($find_in_log[0]['updated_at'], "0000") !== false) ? $find_in_log[0]['created_at'] : $find_in_log[0]['created_at'];
                          $last_trigger_days = $migareference->daysDiffrence($log_stamp,$pre_settings[0]['working_days']);                                                                                                                                      
                          // Get rating                        
                          if ($auto_rem_percentage=$invitation_percent &&  $auto_rem_days=$last_trigger_days)
                          {                            
                           // Recap Email
                           if ($recap_email_enable==1) {    
                              $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                              $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                              $one_go_email_collection[9][]=[
                                'user_id'=>$user_id,
                                'type'=>'referrer',
                                'raw_data'=>$referrerItem,
                                'sponsor_customers'=>$sponsor_customers,
                                'trigger_title'=>$auto_rem_recap_email_header,
                                'trigger_title_string'=>$trigger_title_string,
                                'recap_email_description'=>$recap_email_template,
                                'data'=>$formated_data,
                              ];
                            }
                          }
                      }                      
                  break;
                  case 10: //Referrers who did not accept terms in 48 hours
                    foreach ($customers_list as $referrerItem) {
                      $user_id        = $referrerItem['customer_id'];                                                                                                  
                      // Recap Email
                      if ($recap_email_enable==1) {    
                        $sponsor_customers  = $migareference->getSponsorList($app_id,$user_id);
                        $formated_data  = $migareference->formateEmailTemplate('referrer',$referrerItem,$recap_email_template,$pre_settings);//@params: Type: (referrer,report),$data: hold the dynamic tag value,$email_template: email template
                        $one_go_email_collection[10][]=[
                          'user_id'=>$user_id,
                          'type'=>'referrer',
                          'raw_data'=>$referrerItem,
                          'sponsor_customers'=>$sponsor_customers,
                          'trigger_title'=>$auto_rem_recap_email_header,
                          'trigger_title_string'=>$trigger_title_string,
                          'recap_email_description'=>$recap_email_template,
                          'data'=>$formated_data,
                        ];
                      }
                    }
                  break;
                }
              }
            }
            //If recap email is enabled
            if ($pre_settings[0]['recap_email_enable']==1) {
              $one_go_email_push = $migareference->oneGoAutomationReminderEmail('test',$one_go_email_collection,$pre_settings,$admin_customers,$footer,$app_id);
              $tempItem[]['one_go_email_collection']=$one_go_email_collection;                          
              $tempItem[]['one_go_email_push']=$one_go_email_push;                                                             
            }else {
              $tempItem[]="Recap Email is disabled";
            }            
          } catch (Exception $e) {
           return $e->getMessage();
          }
          return $tempItem;
      }
      public function formateEmailTemplate($type,$data,$recap_email_template,$pre_settings){   
        switch ($type) {
          case 'referrer':                  
            // Referrer: ID, NAME, SURNAME, EMAIL, PHONE 
            if ($pre_settings[0]['recap_email_lang']==1) { //1 for italian              
              return "<div style='padding-left:10px;'><p>Referente: ".$data['user_id']." ,".$data['invoice_name']." ".$data['invoice_surname']." ,".$data['email']." ,".$data['invoice_mobile']."</p></div>";                                                            
            }else {
              return "<div style='padding-left:10px;'><p>Referrer: ".$data['user_id']." ,".$data['invoice_name']." ".$data['invoice_surname']." ,".$data['email']." ,".$data['invoice_mobile']."</p></div>";                                                            
            }
            break;
          case 'report':
            if ($pre_settings[0]['recap_email_lang']==1) { //1 for italian
              $report="<div style='padding-left:10px;'><strong>Segnala NO: ".$data['report_no']."</strong><br>";                                                            
              $report.="<p>Segnala lo stato: ".$data['status_title']."</p>";
              $report.="<p>Segnala il proprietario: ".$data['owner_name']." ".$data['owner_surname']." ,".$data['owner_mobile']."</p>";
              $report.="<p>Referente: ".$data['user_id']." ,".$data['invoice_name']." ".$data['invoice_surname']." ,".$data['email']." ,".$data['invoice_mobile']."</p></div>";              
            }else {              
              $report="<div style='padding-left:10px;'><strong>Report NO: ".$data['report_no']."</strong><br>";                                                            
              $report.="<p>Report Status: ".$data['status_title']."</p>";
              $report.="<p>Owner: ".$data['owner_name']." ".$data['owner_surname']." ,".$data['owner_mobile']."</p>";
              $report.="<p>Referrer: ".$data['user_id']." ,".$data['invoice_name']." ".$data['invoice_surname']." ,".$data['email']." ,".$data['invoice_mobile']."</p></div>";
            }                                                                  
            return $report;
            break;
        }                       
      }
      public function oneGoAutomationReminderEmail($call_type,$one_go_email_collection,$pre_settings,$admin_customers,$footer,$app_id){        
        try {
          $migareference   = new Migareference_Model_Db_Table_Migareference();
          $default         = new Core_Model_Default();
          $base_url        = $default->getBaseUrl();
          $email_receivers = [];                  
          //Sort one_go_email_collection by trigger ID
          ksort($one_go_email_collection);
          foreach ($one_go_email_collection as $key => $value) {          
            
            $receiptns     = [];  
            $$trigger_title='';
            foreach ($value as $keyy=>$item) {                        
              $trigger_title="<br>".$item['trigger_title']."<br>";            
              $title_info=$item['trigger_title_string'];
              $type=$item['type'];
              $recap_email_description=$item['recap_email_description'];
              $user_id = $item['user_id'];
              $data    = $item['data'];
              $sponsor_customers    = $item['sponsor_customers'];            
              if (COUNT($sponsor_customers)>0 &&  $pre_settings[0]['agent_can_manage_reminder_automation']==1) {  //Send notification to Agents
                  foreach ($sponsor_customers as $sponsor_key => $sponsor_value) {                  
                    $receiptns[$sponsor_value['customer_id']]=$receiptns[$sponsor_value['customer_id']].$data;
                  }
              }else { //admin              
                $receiptns[100000]=$receiptns[100000].$data;
              }            
            }
            //Save data for reminder day book
            if ($call_type=='test' || $call_type=='live') { // @@For production keep only type live to avoid rough data
              $day_book['app_id']=$app_id;
              $day_book['receiver_id']= (COUNT($value['sponsor_customers'])) ? $value['sponsor_customers'][0]['customer_id'] : 10000 ;             
              $day_book['data']=json_encode($value);
              $day_book['title_info']=$title_info;
              $day_book['description']=$recap_email_description;
              $day_book['type']=$type;
              $this->_db->insert("migareference_reminder_daybook", $day_book);
            }
            //At first index add $trigger_title
            foreach ($receiptns as $rec_key => $rec_value) {
              $email_receivers[$rec_key]=$email_receivers[$rec_key].$trigger_title.$rec_value; 
            }                    
          }
          // //Send emails as per receiptns
          if ($call_type=='test') {
            $sent=false;
            foreach ($email_receivers as $key => $value) {              
              $email_text=$value;                           
                  if (!$sent) {
                    $mail = new Siberian_Mail();
                    $mail->setBodyHtml($value);
                    $mail->addTo($pre_settings[0]['recap_email_bcc'], "Migareference");
                    $mail->setSubject($pre_settings[0]['recap_email_subject'].$pre_settings[0]['recap_email_bcc']);
                    $mail->send();
                    $sent=true;
                  }                                               
            }            
          }else {
            foreach ($email_receivers as $key => $value) {
              $email_text=$value;
              if ($key==100000) {
                foreach ($admin_customers as $keyy => $valuee) {
                  $email_data['email_title']=$pre_settings[0]['recap_email_subject'];
                  $email_data['bcc_to_email']=$pre_settings[0]['recap_email_bcc'];
                  $email_data['email_text']=$email_text;
                  $this->sendMail($email_data,$app_id,$valuee['customer_id']);
                }
              }
            }
          }          
          return $email_receivers;        ;
        } catch (\Throwable $th) {
          return "Error".$th->getMessage();
        }
       
      }
      public function automationTriggersEffect($triggerItem=[])
      {
        // Get Enabled Automation Triggers

             $migareference       = new Migareference_Model_Db_Table_Migareference();
             $tempItem            = [];
             $auto_rem_action     = $triggerItem['auto_rem_action'];
             $auto_rem_trigger    = $triggerItem['auto_rem_trigger'];
             $auto_rem_reports    = $triggerItem['auto_rem_reports'];
             $auto_rem_min_rating = $triggerItem['auto_rem_min_rating'];
             $auto_rem_max_rating = $triggerItem['auto_rem_max_rating'];
             $auto_rem_fix_rating = $triggerItem['auto_rem_fix_rating'];
             $auto_rem_type       = $triggerItem['auto_rem_type'];
             $auto_rem_days       = $triggerItem['auto_rem_days'];
             $auto_rem_engagement = $triggerItem['auto_rem_engagement'];
             $app_id              = $triggerItem['app_id'];
             $trigger_status      = $triggerItem['auto_rem_report_trigger_status'];
             $trigger_status      = explode('@',$trigger_status);
             array_pop($trigger_status);
             switch ($auto_rem_trigger) {
               case 1: //Referrer posted a certain number of reports
                    $referrer_report_list=$migareference->getReferrerReports($auto_rem_reports,$app_id);
                    foreach ($referrer_report_list as $reportItem) {
                        $user_id        = $reportItem['user_id'];
                        $invoice_id     = $reportItem['migareference_invoice_settings_id'];
                        $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                        $phonebook_item = $migareference->findPhonebookItem($invoice_id,$reportItem);

                        /* Automation Filter
                        // 1. NOT Triggereed Previously
                        // 2. Rating Filter
                        */
                        if (!count($find_in_log)
                        && $auto_rem_max_rating>=$phonebook_item['rating']
                        && $auto_rem_min_rating<=$phonebook_item['rating']
                        && $reportItem['app_id']==$app_id)
                        {
                          $tempItem[]=$reportItem;
                        }
                    }
                 break;
               case 2: //Referrer opens the APP one or more time in the past XX days
                    $referrers_list=$migareference->getAllReferralUsers($app_id);
                    foreach ($referrers_list as $referrerItem) {
                      $user_id        = $referrerItem['user_id'];
                      $invoice_id     = $referrerItem['migareference_invoice_settings_id'];
                      $last_visit     = $migareference->getLastvisit($app_id,$user_id);
                      $pre_settings   = $migareference->preReportsettigns($app_id);
                      $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                      //Last visit differene
                      $last_visit_days = $migareference->daysDiffrence($last_visit[0]['created_at'],$pre_settings[0]['working_days']);
                      // Get last trigger differenc
                      $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                      $phonebook_item = $migareference->findPhonebookItem($invoice_id,$referrerItem);

                      /* Automation Filter
                        // 1. Last Activity visit
                        // 2. Last Trigger
                        // 3. Rating
                        */
                      if ($auto_rem_days>=$last_visit_days
                      &&  $auto_rem_days<=$last_trigger_days
                      && $auto_rem_max_rating>=$phonebook_item['rating']
                      && $auto_rem_min_rating<=$phonebook_item['rating']
                      && $app_id==$referrerItem['app_id'])
                      {
                          $referrerItem['last_visit']=$last_visit_days;
                          $tempItem[]=$referrerItem;
                      }
                    }
                 break;
               case 3: //First time a Referrer is registered
                    $referrers_list=$migareference->getAllReferralUsers($app_id);
                    foreach ($referrers_list as $referrerItem) {
                      $user_id        = $referrerItem['user_id'];
                      $invoice_id     = $referrerItem['migareference_invoice_settings_id'];
                      $pre_settings   = $migareference->preReportsettigns($app_id);
                      $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                      $phonebook_item = $migareference->findPhonebookItem($invoice_id,$referrerItem);

                      /* Automation Filter
                      // 1. Only Useres Registered by sel Exlcude Admin Users
                      // 2. NOT Triggereed Previously
                      // 3. Rating Filter
                      */
                      if ($referrerItem['referrer_source']==1
                      && !count($find_in_log)
                      && $auto_rem_max_rating>=$phonebook_item['rating']
                      && $auto_rem_min_rating<=$phonebook_item['rating']
                      && $app_id==$referrerItem['app_id'])
                      {
                        $tempItem[]=$referrerItem;
                      }
                    }
                 break;
               case 4: //A report still in one state for xx days
                $report_list=$migareference->getAllReports($app_id);
                foreach ($report_list as $report_item) {
                  if (in_array($report_item['currunt_report_status'], $trigger_status)) {
                    $report_id    = $report_item['migareference_report_id'];
                    $user_id      = $report_item['user_id'];
                    $invoice_id   = $report_item['migareference_invoice_settings_id'];                    
                    $activity_log = $migareference->getLastReportActivity($report_id,'Update Status');
                    $find_in_log  = $migareference->findInLog($user_id,$auto_rem_trigger);
                    //Last Status Update Diffrene
                    $last_status_update = (count($activity_log)) ? $activity_log[0]['created_at'] : $report_item['created_at'] ;
                    $last_status_update = $migareference->daysDiffrence($last_status_update,$pre_settings[0]['working_days']);
                    // Get last trigger differenc
                    $last_trigger_days  = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                    // Get rating
                    $phonebook_item = $migareference->findPhonebookItem($invoice_id,$report_item);
                          if ($auto_rem_days<=$last_status_update
                          &&  $auto_rem_days<=$last_trigger_days
                          && $auto_rem_max_rating>=$phonebook_item['rating']
                          && $auto_rem_min_rating<=$phonebook_item['rating']
                          && $report_item['app_id']==$app_id)
                          {
                            $report_item['last_visit']=$last_status_update;
                            // $report_item['email']=$report_item['currunt_report_status'];
                            // $report_item['invoice_name']=$trigger_status;
                            $tempItem[]=$report_item;
                          }
                      }
                  }
                 break;
               case 5: //Referrer Birthday
                $referrers_list=$migareference->getAllReferralUsers($app_id);
                foreach ($referrers_list as $referrerItem) {
                  if ($referrerItem['birthdate']!=0) {
                    $user_id           = $referrerItem['user_id'];
                    $invoice_id        = $referrerItem['migareference_invoice_settings_id'];
                    $find_in_log       = $migareference->findInLog($user_id,$auto_rem_trigger);                    
                    $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                    $phonebook_item    = $migareference->findPhonebookItem($invoice_id,$referrerItem);
                    $birht_date        = date('d-m', $referrerItem['birthdate']);
                    $today_date        = date('d-m');

                    /* Automation Filter
                      // 1. Match Birth day
                      // 2. Rating Filter
                      // 3. To Avoid Resend if CRON run many time in same day
                      */
                    if ($auto_rem_max_rating>=$phonebook_item['rating']
                    && $auto_rem_min_rating<=$phonebook_item['rating']
                    && $last_trigger_days>360
                    && $app_id==$referrerItem['app_id'])
                    {
                      $tempItem[]=$referrerItem;
                    }
                  }
                }
                 break;
               case 6: //Referrer not called since xx days.
                $referrers_list=$migareference->getAllReferralUsers($app_id);
                foreach ($referrers_list as $referrerItem) {
                    $user_id     = $referrerItem['user_id'];
                    $invoice_id  = $referrerItem['migareference_invoice_settings_id'];
                    $call_log    = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                 
                    $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);

                    $last_call = $call_log[0]['created_at'] ;
                    $last_call = $migareference->daysDiffrence($last_call,$pre_settings[0]['working_days']);
                    $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                    // Get rating
                    $phonebook_item = $migareference->findPhonebookItem($invoice_id,$referrerItem);
                    if ($auto_rem_days<=$last_call
                    &&  $auto_rem_days<=$last_trigger_days
                    && $auto_rem_fix_rating==$phonebook_item['rating']
                    && $app_id==$referrerItem['app_id'])
                    {
                      $referrerItem['last_visit']=$last_call;
                      // $referrerItem['email']=$last_call."@".$invoice_id."@".$call_log[0]['created_at'];
                      $tempItem[]=$referrerItem;
                    }
                }
                break;
               case 7: //Referrer called since xx days.
                  $referrers_list=$migareference->getAllReferralUsers($app_id);
                  foreach ($referrers_list as $referrerItem) {
                      $user_id        = $referrerItem['user_id'];
                      $invoice_id     = $referrerItem['migareference_invoice_settings_id'];
                      $call_log    = $migareference->getLastCallActivity($referrerItem['migarefrence_phonebook_id']);                     
                      $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                        //Last Status Update Diffrene
                      $last_call = $call_log[0]['created_at'] ;
                      $last_call = $migareference->daysDiffrence($last_call,$pre_settings[0]['working_days']);
                      $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                      // Get rating
                      $phonebook_item = $migareference->findPhonebookItem($invoice_id,$referrerItem);
                      if ($auto_rem_days>=$last_call
                      &&  $auto_rem_days<=$last_trigger_days
                      && $auto_rem_max_rating>=$phonebook_item['rating']
                      && $auto_rem_min_rating<=$phonebook_item['rating']
                      && $app_id==$referrerItem['app_id'])
                      {
                        $referrerItem['email']=$last_call;
                        $tempItem[]=$referrerItem;
                      }
                  }
                 break;
               case 8: //No changes in engagement rating of a referrer in the past xx days.
                  $referrers_list=$migareference->getAllReferralUsers($app_id);
                  foreach ($referrers_list as $referrerItem) {
                      $user_id        = $referrerItem['user_id'];
                      $invoice_id     = $referrerItem['migareference_invoice_settings_id'];
                      $engagement_log = $migareference->getLastEngagemnetActivity($invoice_id);                      
                      $find_in_log    = $migareference->findInLog($user_id,$auto_rem_trigger);
                        //Last Status Update Diffrene
                      $last_engagement = $engagement_log[0]['created_at'] ;
                      $last_engagement = $migareference->daysDiffrence($last_engagement,$pre_settings[0]['working_days']);
                      $find_in_log = $migareference->findInLog($user_id,$auto_rem_trigger);
                      $last_trigger_days = $migareference->daysDiffrence($find_in_log[0]['created_at'],$pre_settings[0]['working_days']);
                      // Get rating
                      $phonebook_item = $migareference->findPhonebookItem($invoice_id,$referrerItem);
                      if ($auto_rem_days<=$last_engagement
                      &&  $auto_rem_days<=$last_trigger_days
                      && $auto_rem_max_rating>=$phonebook_item['rating']
                      && $auto_rem_min_rating<=$phonebook_item['rating']
                      && $app_id==$referrerItem['app_id'])
                      {
                        $tempItem[]=$referrerItem;
                      }
                  }
                 break;
             }
        return $tempItem;
      }
      public function doAutomation($triggerItem=[],$app_id=0,$user_id=0,$report_item=[],$pre_report=[],$admin_customers=[],$footer='')
      {
           $migareference       = new Migareference_Model_Db_Table_Migareference();
           $auto_rem_action     = $triggerItem['auto_rem_action'];
           $auto_rem_trigger    = $triggerItem['auto_rem_trigger'];
           $auto_rem_title      = $triggerItem['auto_rem_title'];
           $auto_rem_reports    = $triggerItem['auto_rem_reports'];
           $auto_rem_max_rating = $triggerItem['auto_rem_rating'];
           $auto_rem_type       = $triggerItem['auto_rem_type'];
           $report_reminder_auto_id = $triggerItem['migarefrence_report_reminder_auto_id'];
           $auto_rem_days       = $triggerItem['auto_rem_days'];
           $auto_rem_engagement = $triggerItem['auto_rem_engagement'];
           $invoice_id          = $triggerItem['migareference_invoice_settings_id'];
           $enable_webhooks     = $pre_report[0]['enable_webhooks'];
           $mails               = [];
           $autom_log['report_id']       = 0;
           $autom_log['report_status_id']= 0;
           $report_id=0;
           //Now we have to return data for the one go array along with formated email and push and receipent along with type of reciepetent Agent or Admin
           $one_go_email_push_template=[];
           if (!empty($report_item)) {
             $report_id      = $report_item['migareference_report_id'];
             $autom_log['report_id']       = $report_item['migareference_report_id'];
             $autom_log['report_status_id']= $report_item['currunt_report_status'];
            }
           $autom_log['app_id']          = $app_id;
           $autom_log['trigger_id']      = $auto_rem_trigger;
           $autom_log['user_id']         = $user_id;
           $autom_log['trigger_type_id'] = $auto_rem_type;
           $autom_log['report_reminder_auto_id'] = $report_reminder_auto_id;
          if($auto_rem_action==2) {//Set Engagement
              $phonebook_item = $migareference->findPhonebookItem($invoice_id,$triggerItem);
              $autom_log['phonebook_id']=$migareference->updateEngagementAuto($phonebook_item,$auto_rem_engagement);
          }elseif ($auto_rem_action==1) {//Send Reminder
            $reminder_type=$migareference->getSingleReminderType($auto_rem_type);
            if (count($reminder_type)) {
              $invoice_settings=$migareference->getpropertysettings($app_id,$user_id);              
              $report_no='';
              $report_owner='';
              if ($report_id) {
                $report_item=$migareference->get_report_by_key($report_id);
                $report_no=$report_item[0]['report_no'];
                $report_owner=$report_item[0]['owner_name']." ".$report_item[0]['owner_surname'];
              }
              // Map Custom Tags for Email and PUSH reminder notification
              $invoice=$invoice_settings[0];
              $notificationTags= ["@@time_reminder@@","@@referral_name@@","@@referral_phone@@","@@referral_email@@","@@report_no@@","@@report_owner@@"];
              $notificationStrings = [
                date('HH:mm dd-mm-yyyy'),
                $invoice['invoice_name']." ".$invoice['invoice_surname'],
                $invoice['invoice_mobile'],
                $invoice['email'],
                $report_no,
                $report_owner
              ];              
                            
              $reminder_type[0]['rep_rem_email_title'] = str_replace($notificationTags, $notificationStrings, $reminder_type[0]['rep_rem_email_title']);
              $reminder_type[0]['rep_rem_email_text'] = str_replace($notificationTags, $notificationStrings, $reminder_type[0]['rep_rem_email_text']);
              $reminder_type[0]['rep_rem_push_title'] = str_replace($notificationTags, $notificationStrings, $reminder_type[0]['rep_rem_push_title']);
              $reminder_type[0]['rep_rem_push_text'] = str_replace($notificationTags, $notificationStrings, $reminder_type[0]['rep_rem_push_text']);

              // Map Custom Tags for webhooks (*Setup tags based on active for corrosponding triggers)        
              $webhook_tags= [
               "@@referrer_id@@",
              "@@referrer_name@@",
              "@@referrer_surname@@",
              "@@referrer_email@@",
              "@@referrer_phone@@",
              "@@agent_id@@",
              "@@agent_name@@",
              "@@agent_surname@@",
              "@@agent_email@@",
              "@@agent_phone@@",
              "@@user_name@@",
              "@@user_surname@@",
              "@@user_email@@",
              "@@user_phone@@",
              "@@reminder_uid@@"];
              $webhook_tags_string = [                
                $invoice['user_id'],
                $invoice['invoice_name'],
                $invoice['invoice_surname'],
                $invoice['email'],
                $invoice['invoice_mobile'],                
                $invoice['sponsor_one_id'],
                $invoice['sponsor_one_firstname'],
                $invoice['sponsor_one_lastname'],
                $invoice['sponsor_one_email'],
                $invoice['sponsor_one_mobile'],                
                $invoice['firstname'],                
                $invoice['lastname'],                
                $invoice['email'],
                $invoice['mobile'],
                $report_reminder_auto_id
              ];  
              if ($enable_webhooks==1 && $triggerItem['auto_rem_webhook_url']!='') {                       
                  //  Setup webhook log
                  $webhook_log['app_id']          = $app_id;
                  $webhook_log['user_id']         = $user_id;
                  $webhook_log['trigger_id']      = $auto_rem_trigger;
                  $webhook_log['report_id']       = $report_id;
                  $webhook_log['reminder_to']     = 0;
                  $webhook_log['trigger_type_id'] = $auto_rem_type;
                  $webhook_log['type']            = 'Webhook';
                  $webhook_log['webhook_type']    = 'reminder';
                  $webhook_log['report_reminder_auto_id'] = $report_reminder_auto_id;

                  $webhook_url=$triggerItem['auto_rem_webhook_url'];
                  // $webhook_url="https://typedwebhook.tools/webhook/7b6bd404-26c4-4e2f-9dde-7f505ba99d85
                  // $webhook_url= str_replace($webhook_tags, $webhook_tags_string, $webhook_url);
                  // Combine the indexes and values into an associative array
                  $queryParams = array_combine($webhook_tags, $webhook_tags_string);
                  // / Build the complete URL with query parameters
                  $webhook_url = $webhook_url . '?' . http_build_query($queryParams);
                  $customer_data= $migareference->triggerWebhook($webhook_url,$webhook_log);//Reminder Webhook
              }
              // Get agent list for Referrer
              $sponsor_customers   = $migareference->getSponsorList($app_id,$invoice_settings[0]['user_id']);
              if (count($invoice_settings) && COUNT($sponsor_customers)>0 && $pre_report[0]['agent_can_manage_reminder_automation']==1) {  //Send notification to Agents
                  foreach ($sponsor_customers as $key => $value) {
                    $mails['agent'][]=$value['email'];
                    $customer_data= $migareference->getSingleuser($app_id,$value['customer_id']);
                    $autom_log['reminder_to'] = $value['customer_id'];//Agent ID
                    // $migareference->sendAutomationMail($reminder_type[0],$app_id,$value,$footer);
                    // $find_in_log  = $migareference->findInPushLog($app_id,$value['customer_id'],$reminder_type[0]['rep_rem_push_title']);
                    if (!count($find_in_log)) {
                      // $migareference->sendAutomationPush($reminder_type[0],$app_id,$value['customer_id']);
                    }
                    $migareference->saveAutoLog($autom_log); 
                  }                    
                }else { //Send notification to Admin                    
                    $autom_log['reminder_to'] = 1000000;//Indicate Admin
                    foreach ($admin_customers as $admin) {                  
                      $mails['admin'][]=$admin['email'];
                      // $migareference->sendAutomationMail($reminder_type[0],$app_id,$admin,$footer);                      
                      // $find_in_log = $migareference->findInPushLog($app_id,$admin['customer_id'],$reminder_type[0]['rep_rem_push_title']);
                      if (!count($find_in_log)) {
                        // $migareference->sendAutomationPush($reminder_type[0],$app_id,$admin['customer_id']);
                      }
                    }
                    $migareference->saveAutoLog($autom_log);
                }
            }
          }          
          return $mails;
      }
    //Thiis method is called to trigger the webhook for the all three types of Webhooks (*Reminder*, Referrer, Report)
    public function triggerWebhook($webhookUrl, $logParams) {
      $migareference = new Migareference_Model_Db_Table_Migareference();
      $responseDetails = [
          'success' => false,
          'detail' => '',
          'httpCode' => 0
      ];      
          $ch = curl_init($webhookUrl);
          curl_setopt($ch, CURLOPT_HTTPGET, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
          $responseSuccessful = false;
          try {
              $response = curl_exec($ch);
              if ($response === false) {
                  throw new Exception('cURL Error: ' . curl_error($ch));
              }
              $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              $responseDetails['httpCode'] = $httpCode;
  
              if ($httpCode >= 400) {
                  throw new Exception('HTTP Error: ' . $httpCode);
              }
  
              $responseDetails['detail'] = 'Success';
              $responseDetails['success'] = true;
              $responseSuccessful = true; // Mark response as successful
          } catch (Exception $e) {
              $responseDetails['detail'] = 'Error: ' . $e->getMessage();
          } finally {
              curl_close($ch);
          }
  
          // Log the attempt
          $logData = [
              'app_id' => $logParams['app_id'],
              'user_id' => $logParams['user_id'],
              'report_id' => isset($logParams['report_id']) ? $logParams['report_id'] : 0,
              'reminder_list_uid' => isset($logParams['report_reminder_auto_id']) ? $logParams['report_reminder_auto_id'] : 0,
              'webhook_type' => $logParams['type'],//referrer, report, reminder              
              'calling_method' => $logParams['calling_method'],//newReport, updateReport, testReport
              'target_url' => $webhookUrl,
              'response_type' => $responseSuccessful ? 'success' : 'fail',
              'response_message' => $responseDetails['detail'],
              'http_code' => $responseDetails['httpCode']
          ];
          $migareference->saveWebhookLog($logData);
  
  
          if (!$responseDetails['success']) { // If the webhook failed 3 times, send an email to the admin
            $failed_count = (isset($logParams['count'])) ? $logParams['count'] : 0 ; //0 mean its first call
            if ($failed_count>=2) {
                // Send email to admins if all retries fail
                $admin_customers = $migareference->getAdminCustomers($logParams['app_id']);//Admin Users
                $webhook_referrer_msg_template= (new Migareference_Model_Webhookerrortemplate())->findAll(['app_id'=>$logParams['app_id'],'webhook_type'=>$logParams['type']])->toArray();
                if ($webhook_referrer_msg_template[0]['is_enabled']==1) {          
                  foreach ($admin_customers as $key => $value) {
                    $email_data['email_title']= $webhook_referrer_msg_template[0]['email_title'];
                    $email_data['email_text'] = $webhook_referrer_msg_template[0]['email_message'];
                    if ($logParams['type']=='report') {
                      // Custom Tags for Referrer:  @@report_no@@, @@error_message@@
                      $report_item=$migareference->get_report_by_key($logParams['report_id']);
                      $email_data['email_text']= str_replace("@@report_no@@",$report_item[0]['report_no'],$email_data['email_text']);
                    }else if ($logParams['type']=='referrer') {
                      // Custom Tags for Report:  @@referrer_id@@, @@error_message@@
                      $email_data['email_text']= str_replace("@@referrer_id@@",$logParams['user_id'],$email_data['email_text']);
                    }else if ($logParams['type']=='reminder') {
                      // Custom Tags for Report:  @@reminder_id@@,
                      $email_data['email_text']= str_replace("@@reminder_id@@",$logParams['report_reminder_auto_id'],$email_data['email_text']);
                    }
                    $email_data['email_text']= str_replace("@@error_message@@",$responseDetails['detail'],$email_data['email_text']);
                    $this->sendMail($email_data,$logParams['app_id'],$value['customer_id']);
                  }
                }
            } 
          }
  }
  
  public function getReferrerWebhookLog($app_id=0){
    $query_option = "SELECT * ,wlogs.created_at AS log_created_at
      FROM `migareference_webhook_logs` AS wlogs
      JOIN customer ON customer.customer_id=wlogs.user_id
      WHERE wlogs.app_id=$app_id 
      AND wlogs.webhook_type='referrer'
      AND wlogs.created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
      ORDER BY wlogs.created_at DESC";
    return $this->_db->fetchAll($query_option);           
  }
  public function getReportWebhookLog($app_id=0){
    $query_option = "SELECT * ,wlogs.created_at AS log_created_at
      FROM `migareference_webhook_logs` AS wlogs
      JOIN migareference_report ON migareference_report.migareference_report_id=wlogs.report_id
      WHERE wlogs.app_id=$app_id 
      AND wlogs.webhook_type='report'
      AND wlogs.created_at >= DATE_SUB(NOW(), INTERVAL 10 DAY)
      ORDER BY wlogs.created_at DESC";
    return $this->_db->fetchAll($query_option);           
  }
  public function getReminderWebhookLog($app_id=0){
    $query_option = "SELECT * ,wlogs.created_at AS log_created_at
      FROM `migareference_webhook_logs` AS wlogs      
      WHERE wlogs.app_id=$app_id 
      AND wlogs.webhook_type='reminder'
      AND wlogs.created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
      ORDER BY wlogs.created_at DESC";
    return $this->_db->fetchAll($query_option);           
  }
  public function fetchFailedReportWebhooks($maxRetries) {
    $query = "SELECT *, COUNT(report_id) as count
    FROM `migareference_webhook_logs` 
    WHERE (`response_type` = 'fail' OR calling_method='retryFailedReport')
    AND `webhook_type` = 'report'
    GROUP BY report_id
    HAVING count > 0 AND count <= $maxRetries";
    return $this->_db->fetchAll($query);
}
  public function fetchFailedReferrerWebhooks($maxRetries) {
    $query = "SELECT *, COUNT(user_id) as count
    FROM `migareference_webhook_logs` 
    WHERE `response_type` = 'fail' 
    AND `webhook_type` = 'referrer'
    GROUP BY user_id
    HAVING count > 0 AND count <= $maxRetries";
    return $this->_db->fetchAll($query);
}
  public function fetchFailedReminderWebhooks($maxRetries) {
    $query = "SELECT *, COUNT(user_id) as count
    FROM `migareference_webhook_logs` 
    WHERE `response_type` = 'fail' 
    AND `webhook_type` = 'reminder'
    GROUP BY user_id
    HAVING count > 0 AND count <= $maxRetries";
    return $this->_db->fetchAll($query);
}
  public function retryFailedWebhooks() {
    $maxRetries = 3;  
    $migareference = new Migareference_Model_Db_Table_Migareference();
    // Fetch failed webhooks
    $failedReportWebhooks = $migareference->fetchFailedReportWebhooks($maxRetries);
    $failedReferrerWebhooks = $migareference->fetchFailedReferrerWebhooks($maxRetries);
    $failedReminderWebhooks = $migareference->fetchFailedReminderWebhooks($maxRetries);
    // Retry each failed webhook
    foreach ($failedReportWebhooks as $webhook) {
        $migareference->triggerWebhook($webhook['target_url'], [
            'app_id' => $webhook['app_id'],
            'user_id' => $webhook['user_id'],
            'report_id' => $webhook['report_id'],
            'type' => $webhook['webhook_type'],
            'calling_method' => 'retryFailedReport',
            'count'=>$webhook['count']
        ]);
    }
    foreach ($failedReferrerWebhooks as $webhook) {
        $migareference->triggerWebhook($webhook['target_url'], [
            'app_id' => $webhook['app_id'],
            'user_id' => $webhook['user_id'],
            'report_id' => $webhook['report_id'],
            'type' => $webhook['webhook_type'],
            'count'=>$webhook['count']
        ]);
    }
    foreach ($failedReminderWebhooks as $webhook) {
        $migareference->triggerWebhook($webhook['target_url'], [
            'app_id' => $webhook['app_id'],
            'user_id' => $webhook['user_id'],
            'report_id' => $webhook['report_id'],
            'type' => $webhook['webhook_type'],
            'count'=>$webhook['count']
        ]);
    }
}
  public function saveWebhookLog($log=[]){
    $this->_db->insert("migareference_webhook_logs", $log);
  }
      public function sendPush2($data=[])
      {
          try {
             //Get How it works details or Referral Directore details
              $howto_data=$this->gethowto($data['app_id']);
              $howto_data=$howto_data[0];
              // Replace following tags in Email Text @@rd_name@@, @@rd_email@@, @@rd_phone@@, @@rd_calendar_url@@
              $data['push_message']=str_replace("@@rd_name@@", $howto_data['director_name'], $data['push_message']);
              $data['push_message']=str_replace("@@rd_email@@", $howto_data['director_email'], $data['push_message']);
              $data['push_message']=str_replace("@@rd_phone@@", $howto_data['director_phone'], $data['push_message']);
              $data['push_message']=str_replace("@@rd_calendar_url@@", $howto_data['director_calendar_url'], $data['push_message']);
              $application = (new \Application_Model_Application())->find($data['app_id']);
              // Required for the Message
              $values = [
                  'app_id' => $data['app_id'], // The application ID, required
                  'value_id' => null, // The value ID, optional
                  'title' => $data['push_title'], // Required
                  'body' => $data['push_message'], // Required
                  'big_picture_url' => $data['big_picture_url'], // Required
                  'send_after' => null,
                  'delayed_option' => null,
                  'delivery_time_of_day' => null,
                  'is_for_module' => true, // If true, the message is linked to a module, push will not be listed in the admin
                  'is_test' => false, // If true, the message is a test push, it will not be listed in the admin
                  'open_feature' => false, // If true, the message will open a feature, it works with feature_id
                  'feature_id' => null, // The feature ID, required if open_feature is true
                  // 'big_picture'=>empty($data['cover']) ? null : '/' . $data['app_id'] . '/features/migaprontelevator/' . $data['cover']
              ];
              
              $scheduler = new Push2\Model\Onesignal\Scheduler($application);
              $scheduler->buildMessageFromValues($values);
              $scheduler->sendToCustomer($data['customer_id']); // This part will automatically sets the player_id and is_individual to true
              $payload = [
                  'success' => true,
                  'message' => p__('push2', 'Push sent'),
              ];
              $responce = ['success'=>true,'message_id'=>$scheduler->message->getId()];
          } catch (onesignal\client\ApiException $e) {
              $body = Siberian\Json::decode($e->getResponseBody());
              $payload = [
                  'error' => true,
                  'message' => "<b>[OneSignal]</b><br/>" . $body["errors"][0],
              ];
              $responce = ['success'=>false];
          } catch (\Exception $e) {
              $payload = [
                  'error' => true,
                  'message' => $e->getMessage(),
              ];
              $responce = ['success'=>false];
          }
          return $payload;
      }
      public function tempPhonePrefx()
      {
          $query_option = "SELECT * FROM `migareference_invoice_settings` WHERE 1";
          $customer_item = $this->_db->fetchAll($query_option);
      
          foreach ($customer_item as $key => $value) {
              // Check if mobile number doesn't start with '+39'
              if (!$this->startsWith($value['invoice_mobile'], '+39')) {
                  // Update customer table with '+39' prefixed mobile number
                  $updatedMobile = '+39' . $value['invoice_mobile'];
                  $migareference_invoice_settings_id = $value['migareference_invoice_settings_id'];
      
                  // Update the database
                  $updateSql = "UPDATE `migareference_invoice_settings` SET `invoice_mobile` = '$updatedMobile' WHERE `migareference_invoice_settings_id` = $migareference_invoice_settings_id";
                  $this->_db->query($updateSql);
      
                  // Update the local data as well
                  $customer_item[$key]['invoice_mobile'] = $updatedMobile;
              }
          }
      }
      public function markasDon($log_id=0,$status='')
      {        
        $updateSql = "UPDATE `migareference_reminder_daybook` SET `status` = '$status' WHERE `migareference_reminder_daybook_id` = $log_id";
        $this->_db->query($updateSql);      
      }
      
      // Function to check if a string starts with a specific prefix
      public function startsWith($haystack, $needle)
      {
          $length = strlen($needle);
          return (substr($haystack, 0, $length) === $needle);
      }
      public function findPhonebookItem($id=0,$data=[])
      {
        $migareference    = new Migareference_Model_Db_Table_Migareference();
        $query_option = "SELECT *  FROM `migarefrence_phonebook` WHERE `invoice_id` = $id AND `type` = 1";
        $phonebook_item   = $this->_db->fetchAll($query_option);
        if (!count($phonebook_item)) { //Phonebook item is missing save item
                                      $migareference->setPhoneBook($data);
                      $phonebook_item = $migareference->getInvoicePhonebook($id);
                    }
        return $phonebook_item[0];
      }

      public function invitationsCount($app_id=0,$date='')
      {        
        $query_option = "SELECT 
                          COUNT(IF( migareference_invitation_logs.log_type = 'share', migareference_invitation_logs.log_type, NULL)) AS share,
                          COUNT(IF( migareference_invitation_logs.log_type = 'visit', migareference_invitation_logs.log_type, NULL)) AS visit,
                          COUNT(IF( migareference_invitation_logs.log_type = 'report', migareference_invitation_logs.log_type, NULL)) AS report
                          FROM `migareference_invitation_logs` 
                          WHERE migareference_invitation_logs.`app_id` = $app_id 
                          AND date(migareference_invitation_logs.created_at)>='$date'";
        $result   = $this->_db->fetchAll($query_option);       
        return $result;
      }
      public function setPhoneBook($data=[])
      {
          $phonebook['app_id']      = $data['app_id'];
          $phonebook['name']        = $data['invoice_name'];
          $phonebook['surname']     = $data['invoice_surname'];
          $phonebook['mobile']      = $data['invoice_mobile'];
          $phonebook['email']       = $data['email'];
          $phonebook['invoice_id']  = $data['migareference_invoice_settings_id'];
          $phonebook['job_id']      = 0;
          $phonebook['user_id']     = $data['user_id'];
          $phonebook['type']        = 1;
          $phonebook['created_at']  = date('Y-m-d H:i:s');                    
          return $this->savePhoneBook($phonebook);
      }
      public function daysDiffrence($date='',$settings=0)
      {   
        if ($settings==2) {
              $last_visit_date = strtotime($date);
              $last_visit_days = time() - $last_visit_date;
              $days = round($last_visit_days / (60 * 60 * 24));
            }else if($settings==1){
              $start = new DateTime($date);
              $end = new DateTime(date('Y-m-d H:i:s'));
              // otherwise the  end date is excluded (bug?)
              $end->modify('+1 day');
              $interval = $end->diff($start);
              // total days
              $days = $interval->days;
              // create an iterateable period of date (P1D equates to 1 day)
              $period = new DatePeriod($start, new DateInterval('P1D'), $end);                
              foreach($period as $dt) {
                  $curr = $dt->format('D');
                  // substract if Saturday or Sunday
                  if ($curr == 'Sat' || $curr == 'Sun') {
                      $days--;
                  }            
              }
            }
          
          return $days;        
      }      
      public function saveAutoLog($data=[])
      {
          $data['created_at']  = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_automation_log", $data);
      }
      public function reminderResetLog($data=[])
      {          
          $this->_db->insert("migareference_reminder_reset_logs", $data);
      }
      public function saveSharelogs($data=[])
      {
          $data['created_at']  = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_invitation_logs", $data);
      }
      public function saveRepoRemLog($data=[])
      {
          $data['created_at']  = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_report_reminder_log", $data);
      }
      public function updateEngagementAuto($phonebook_item = [],$engagement_level)
      {    $set_engage=0;
          switch ($engagement_level) {
            case 1:
              $set_engage=$set_engage+1;
              break;
            case 2:
              $set_engage=$set_engage+2;
              break;
            case 3:
              $set_engage=$set_engage-1;
              break;
            case 4:
              $set_engage=$set_engage-2;
              break;
          }
          $phonebook_item['engagement_level'] = $phonebook_item['engagement_level']+$set_engage;
          $phonebook_item['engagement_level'] = ($phonebook_item['engagement_level']>10) ? 10 : $phonebook_item['engagement_level'] ;
          $phonebook_item['engagement_level'] = ($phonebook_item['engagement_level']<1) ? 1 : $phonebook_item['engagement_level'] ;
          $this->update_phonebook($phonebook_item,$phonebook_item['migarefrence_phonebook_id'],9999,0);//Also save log if their is change in Rating,Job,Notes
          $log_item=[
              'app_id'       => $phonebook_item['app_id'],
              'phonebook_id' => $phonebook_item['migarefrence_phonebook_id'],
              'user_id'      => $phonebook_item['user_id'],
              'log_type'     => "Engagment"
          ];
        $this->saveCommunicationLog($log_item);
          return $phonebook_item['migarefrence_phonebook_id'];
      }
      /**
       * @param int $app_id
       * @param int $value_id
       * @return string
       */
      public function getPageTitle($app_id = 0, $value_id = 0)
      {
          $page_title = "";
          $query_option_value = "SELECT tabbar_name, option_id FROM application_option_value WHERE value_id = $value_id AND app_id = $app_id";
          $res_option_value = $this->_db->fetchAll($query_option_value);
          if ($res_option_value[0]['tabbar_name'] != NULL) {
              $page_title = $res_option_value[0]['tabbar_name'];
          } else {
              $option_id = $res_option_value[0]['option_id'];
              $query_option = "SELECT name FROM application_option WHERE option_id = $option_id";
              $res_option = $this->_db->fetchAll($query_option);
              $page_title = $res_option[0]['name'];
          }
          return $page_title;
      }
      public function getDayBook($app_id = 0, $receiver_id = 0,$status='')
      {          
          $query_option_value = "SELECT * 
          FROM `migareference_reminder_daybook`
          WHERE `app_id`=$app_id AND `receiver_id`=$receiver_id AND `status`='$status'";          
          return $this->_db->fetchAll($query_option_value);
      }
      public function getGeoCountrieProvinces($app_id = 0,$country_id=0)
      {
        if ($country_id==0) {
          $query_option_value = "SELECT *
          FROM  migareference_geo_provinces
          JOIN migareference_geo_countries ON migareference_geo_countries.migareference_geo_countries_id=migareference_geo_provinces.country_id
          WHERE migareference_geo_countries.`app_id`=$app_id";
        }else {
          $query_option_value = "SELECT *
          FROM `migareference_geo_countries`
          JOIN migareference_geo_provinces ON migareference_geo_countries.migareference_geo_countries_id=migareference_geo_provinces.country_id
          WHERE migareference_geo_countries.`app_id`=$app_id AND migareference_geo_countries.migareference_geo_countries_id=$country_id";
        }
          return $this->_db->fetchAll($query_option_value);
      }
      public function getReferrerReports($report_count=0,$app_id=0)
      {
          $query_option_value = "SELECT inv.`migareference_invoice_settings_id`,
                                        inv.app_id,
                                        inv.invoice_name,
                                        inv.invoice_surname,
                                        inv.invoice_mobile,
                                        inv.user_id,
                                        inv.sponsor_id,
                                        cs.email,
                                        ph.rating,
                               			    ph.migarefrence_phonebook_id,
                                        count(rp.migareference_report_id) as totalreports
                                 FROM migareference_invoice_settings as inv
                                 JOIN migareference_report as rp ON rp.user_id=inv.user_id
                                 JOIN customer as cs ON cs.customer_id=inv.user_id
                                 LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=inv.migareference_invoice_settings_id AND ph.type=1
                                 WHERE inv.app_id=$app_id
                                 GROUP BY inv.user_id
                                 HAVING COUNT(rp.migareference_report_id)>=$report_count
                                 ORDER BY inv.app_id,inv.user_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getActiveAutomTriggers()
      {
          $query_option_value = "SELECT * FROM `migarefrence_report_reminder_auto`
                                 WHERE auto_rem_status=1 ORDER BY app_id ASC";
          return $this->_db->fetchAll($query_option_value);
      }
      public function findInPushLog($app_id=0,$user_id=0,$title)
      {
          $date=date('Y-m-d');
          $query_option_value = "SELECT *
            FROM `migareference_push_log`
            WHERE `app_id`=$app_id AND `user_id`=$user_id
            AND `notification_title`='$title'
            AND Date(`created_at`)='$date'";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getGlobalSettings()
      {
          $query_option_value = "SELECT * FROM `migareference_setting`
                                 WHERE 1";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getGeoCountry($country_id=0,$app_id=0)
      {
          $query_option_value = "SELECT * FROM `migareference_geo_countries` WHERE `migareference_geo_countries_id` = $country_id AND `app_id` = $app_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function lastReport($app_id=0)
      {
          $query_option_value = "SELECT *  FROM `migareference_report` WHERE `app_id` = $app_id ORDER BY created_at DESC LIMIT 1";
          return $this->_db->fetchAll($query_option_value);
      }
      public function lastReferrer($app_id=0)
      {
          $query_option_value = "SELECT *  
          FROM `migareference_invoice_settings` 
          JOIN migarefrence_phonebook ON migarefrence_phonebook.app_id=migareference_invoice_settings.app_id AND migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
          WHERE migareference_invoice_settings.`app_id` = $app_id ORDER BY migareference_invoice_settings.created_at DESC LIMIT 1";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getProfiledReferrers($app_id=0,$phonbok_id=0)
      {        
          $query_option_value = "SELECT *  
          FROM `migareference_invoice_settings` 
          JOIN migarefrence_phonebook ON migarefrence_phonebook.app_id=migareference_invoice_settings.app_id AND migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id AND migarefrence_phonebook.job_id>0 && migarefrence_phonebook.rating>0 && migarefrence_phonebook.migarefrence_phonebook_id!=$phonbok_id          
          JOIN migareference_jobs ON migarefrence_phonebook.job_id=migareference_jobs.migareference_jobs_id          
          JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id          
          WHERE migareference_invoice_settings.`app_id` = $app_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getProfiledAgentReferrers($app_id=0,$phonbok_id=0,$agent_id=0)
      {        
          $query_option_value = "SELECT *  
          FROM `migareference_invoice_settings` 
          JOIN migarefrence_phonebook ON migarefrence_phonebook.app_id=migareference_invoice_settings.app_id AND migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id AND migarefrence_phonebook.job_id>0 && migarefrence_phonebook.rating>0 && migarefrence_phonebook.migarefrence_phonebook_id!=$phonbok_id          
          JOIN migareference_jobs ON migarefrence_phonebook.job_id=migareference_jobs.migareference_jobs_id          
          JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id          
          JOIN migareference_referrer_agents ON migareference_referrer_agents.referrer_id=migareference_invoice_settings.user_id AND   migareference_referrer_agents.agent_id=$agent_id
          WHERE migareference_invoice_settings.`app_id` = $app_id
          GROUP BY migareference_invoice_settings.user_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function jobByTitle($title = '')
      {
          // Avoid running the query if the title is empty
          if (empty($title)) {
              return []; // or throw an exception based on your use case
          }
      
          // Use parameterized queries to prevent SQL injection
          $query_option_value = "SELECT * 
                                 FROM migareference_jobs
                                 WHERE LOWER(job_title) = LOWER(:title)";
                                 
          $params = ['title' => $title];
          
          return $this->_db->fetchAll($query_option_value, $params);
      }
      public function sectorByTitle($title = '')
{
    // Avoid running the query if the title is empty
    if (empty($title)) {
        return []; // or throw an exception based on your use case
    }

    // Use parameterized queries to prevent SQL injection
    $query_option_value = "SELECT * 
                           FROM migareference_professions
                           WHERE LOWER(profession_title) = LOWER(:title)";
                           
    $params = ['title' => $title];
    
    return $this->_db->fetchAll($query_option_value, $params);
}

      public function getGeoProvince($province_id=0,$app_id=0)
      {
          $query_option_value = "SELECT *  FROM `migareference_geo_provinces` WHERE `migareference_geo_provinces_id` = $province_id AND `app_id` = $app_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function findInLog($user_id = 0,$trigger_id=0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_automation_log`
                                 WHERE `user_id`=$user_id AND `trigger_id`=$trigger_id ORDER BY created_at DESC";
          return $this->_db->fetchAll($query_option_value);
      }
      public function findInLogReportTrigger($trigger_id = 0,$report_id=0,$report_status_id=0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_automation_log`
                                 WHERE `trigger_id`=$trigger_id AND `report_id`=$report_id AND `report_status_id`=$report_status_id  ORDER BY created_at DESC";
          return $this->_db->fetchAll($query_option_value);
      }
      public function findInRepoRemLog($reminder_id = 0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_report_reminder_log`
                                 WHERE `reminder_id`=$reminder_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getGeoCountries($app_id = 0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_geo_countries`
                                 WHERE migareference_geo_countries.`app_id`=$app_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getGeoCountryProvicnes($app_id = 0,$country_id=0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_geo_provinces`
                                 WHERE migareference_geo_provinces.`app_id`=$app_id AND migareference_geo_provinces.country_id=$country_id ORDER BY province";
          return $this->_db->fetchAll($query_option_value);
      }
      public function geoProvinceCountries($app_id = 0)
      {
          $query_option_value = "SELECT *
          FROM `migareference_geo_countries`
          JOIN migareference_geo_provinces ON migareference_geo_countries.migareference_geo_countries_id=migareference_geo_provinces.country_id
          WHERE migareference_geo_countries.`app_id`=$app_id GROUP BY migareference_geo_countries.migareference_geo_countries_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function getCustomstatusreports($app_id = 0)
      {
          $query_option_value = "SELECT *
                                 FROM `migareference_report`
                                 JOIN migareference_report_status ON migareference_report.currunt_report_status=migareference_report_status.migareference_report_status_id AND migareference_report_status.is_standard=0
                                 WHERE migareference_report.`app_id`=$app_id";
          return $this->_db->fetchAll($query_option_value);
      }
      public function importstatus($app_id = 0, $value_id = 0)
      {
          $my_test_return=[];
          $basePath = Core_Model_Directory::getBasePathTo("/var/tmp/migareferenceimport/");
          $data = json_decode(file_get_contents($basePath . "tatus_data.json"));
          $optional_type=1;
          $this->deletecustomstatusbyapp($app_id);
          foreach ($data as $key => $value) {
            $checkStatus=$this->checkStatus($app_id,$value->is_standard,$value->standard_type,$value->is_optional,$value->optional_type);            
            // Status table template
            $status_remplate=[];
            $status_remplate['app_id']              = $app_id;
            $status_remplate['status_title']        = $value->status_title;
            $status_remplate['status_icon']         = $value->status_icon;
            $status_remplate['status']              = $value->status;
            $status_remplate['is_standard']         = $value->is_standard;
            $status_remplate['is_optional']         = $value->is_optional;
            $status_remplate['is_comment']          = $value->is_comment;
            $status_remplate['is_acquired']         = $value->is_acquired;
            $status_remplate['standard_type']       = $value->standard_type;            
            $status_remplate['optional_type']       = $value->optional_type;            
            $status_remplate['order_id']            = $value->order_id;
            $status_remplate['is_declined']         = $value->is_declined;
            $status_remplate['declined_grace_days'] = $value->declined_grace_days;
            $status_remplate['declined_to']         = $value->declined_to;
            $status_remplate['is_reminder']         = $value->is_reminder;
            $status_remplate['reminder_grace_days'] = $value->reminder_grace_days;

            // PUSH
            $push_template_constant=[];
            $push_template_constant["app_id"]          = $app_id;
            $push_template_constant["value_id"]        = $value_id;
            $push_template_constant["is_push_ref"]     = $value->is_push_ref;
            $push_template_constant["is_push_agt"]     = $value->is_push_agt;
            $push_template_constant['event_id']        = $value->event_id;
            $push_template_constant["ref_push_title"]  = $value->ref_push_title;
            $push_template_constant["ref_push_text"]   = $value->ref_push_text;
            $push_template_constant["ref_open_feature"]= $value->ref_open_feature;
            $push_template_constant["ref_feature_id"]  = $value->ref_feature_id;
            $push_template_constant["ref_custom_url"]  = $value->ref_custom_url;
            $push_template_constant["ref_cover_image"] = $value->ref_cover_image;
            $push_template_constant["agt_push_title"]  = $value->agt_push_title;
            $push_template_constant["agt_push_text"]   = $value->agt_push_text;
            $push_template_constant["agt_open_feature"]= $value->agt_open_feature;
            $push_template_constant["agt_feature_id"]  = $value->agt_feature_id;
            $push_template_constant["agt_custom_url"]  = $value->agt_custom_url;
            $push_template_constant["agt_cover_image"] = $value->agt_cover_image;
            // Reminder PUSH
              // Reminder Refreela
            $push_template_constant["reminder_is_push_ref"]      = $value->reminder_is_push_ref;
            $push_template_constant["reminder_ref_push_title"]   = $value->reminder_ref_push_title;
            $push_template_constant["reminder_ref_push_text"]    = $value->reminder_ref_push_text;
            $push_template_constant["reminder_ref_open_feature"] = $value->reminder_ref_open_feature;
            $push_template_constant["reminder_ref_feature_id"]   = $value->reminder_ref_feature_id;
            $push_template_constant["reminder_ref_custom_url"]   = $value->reminder_ref_custom_url;
            $push_template_constant["reminder_ref_cover_image"]  = $value->reminder_ref_cover_image;
            // Reminder Agent
            $push_template_constant["reminder_is_push_agt"]      = $value->reminder_is_push_agt;
            $push_template_constant["reminder_agt_push_title"]   = $value->reminder_agt_push_title;
            $push_template_constant["reminder_agt_push_text"]    = $value->reminder_agt_push_text;
            $push_template_constant["reminder_agt_open_feature"] = $value->reminder_agt_open_feature;
            $push_template_constant["reminder_agt_feature_id"]   = $value->reminder_agt_feature_id;
            $push_template_constant["reminder_agt_custom_url"]   = $value->reminder_agt_custom_url;
            $push_template_constant["reminder_agt_cover_image"]  = $value->reminder_agt_cover_image;
            // EMAIL
            $email_template_constant=[];
            $email_template_constant['is_email_ref']    = $value->is_email_ref;
            $email_template_constant['is_email_agt']    = $value->is_email_agt;
            $email_template_constant['app_id']          = $app_id;
            $email_template_constant['event_id']        = $value->event_id;
            $email_template_constant['value_id']        = $value_id;
            $email_template_constant['ref_email_title'] = $value->ref_email_title;
            $email_template_constant['ref_email_text']  = $value->ref_email_text;
            $email_template_constant['agt_email_title'] = $value->agt_email_title;
            $email_template_constant['agt_email_text']  = $value->agt_email_text;
            // Reimnder
              // Reffreal
            $email_template_constant['reminder_is_email_ref']    = $value->reminder_is_email_ref;
            $email_template_constant['reminder_ref_email_title'] = $value->reminder_ref_email_title;
            $email_template_constant['reminder_ref_email_text']  = $value->reminder_ref_email_text;
              // Agent
            $email_template_constant['reminder_is_email_agt']    = $value->reminder_is_email_agt;
            $email_template_constant['reminder_agt_email_title'] = $value->reminder_agt_email_title;
            $email_template_constant['reminder_agt_email_text']  = $value->reminder_agt_email_text;

            if (!count($checkStatus)) {
              $status_remplate['optional_type']       = $optional_type++;
              $status_remplate['is_optional']=1;
              $status_id           = $this->saveStatus($status_remplate);
              $push_template_constant["event_id"]  = $status_id;
              $email_template_constant['event_id'] = $status_id;              
              $email_template_id   = $this->saveEmail( $email_template_constant);
              $push_template_id    = $this->savePushAuto($push_template_constant);
                                     $this->copyImages($app_id, $push_template_constant,$basePath);
                                     $this->copyImages($app_id, $status_remplate,$basePath);
                                     $noti_event_data=[];
              if ($email_template_id>0 && $push_template_id>0) {
                $days=0;
                $hours=0;
                $noti_event_data['app_id']            = $app_id;
                $noti_event_data['value_id']          = $value_id;
                $noti_event_data['event_id']          = $status_id;
                $noti_event_data['push_template_id']  = $push_template_id;
                $noti_event_data['email_template_id'] = $email_template_id;
                $noti_event_data['email_delay_days']  = $days;
                $noti_event_data['push_delay_days']   = $days;
                $noti_event_data['email_delay_hours'] = $hours;
                $noti_event_data['push_delay_hours']  = $hours;
                $push_template_id                     = $this->saveNotificationevent($noti_event_data);
              }
            }else{
              $status_id           = $this->updateStatusbyKey($status_remplate,$checkStatus[0]['migareference_report_status_id']);
              $push_template_constant["migareference_push_template_id"]  = $checkStatus[0]['migareference_push_template_id'];
              $push_template_constant["event_id"]  = $status_id;
              $email_template_constant['event_id'] = $status_id;
              $email_template_constant['migareference_email_template_id'] = $checkStatus[0]['migareference_email_template_id'];            
              $email_template_id   = $this->updateEmail( $email_template_constant);
              $push_template_id    = $this->updatePushconstant($app_id,$push_template_constant);              
                                     $this->copyImages($app_id, $push_template_constant,$basePath);
                                     $this->copyImages($app_id, $status_remplate,$basePath);

              if ($email_template_id>0 && $push_template_id>0) {
                $days=0;
                $hours=0;
                $noti_event_data['app_id']            = $app_id;
                $noti_event_data['value_id']          = $value_id;
                $noti_event_data['event_id']          = $status_id;
                $noti_event_data['push_template_id']  = $push_template_id;
                $noti_event_data['email_template_id'] = $email_template_id;
                $noti_event_data['email_delay_days']  = $days;
                $noti_event_data['push_delay_days']   = $days;
                $noti_event_data['email_delay_hours'] = $hours;
                $noti_event_data['push_delay_hours']  = $hours;
                $noti_event_data['migareference_notification_event_id']  = $checkStatus[0]['migareference_notification_event_id'];                
                $push_template_id                     = $this->updateNotificationevent( $noti_event_data);
              }

            }
          }
                  return $my_test_return;
      }
      public function get_app_content($app_id=0)
      {
        $query_option = "SELECT * 
        FROM `migarefrence_app_content` 
        LEFT JOIN migarefrence_app_content_two ON migarefrence_app_content_two.app_id=migarefrence_app_content.app_id
        WHERE migarefrence_app_content.app_id=$app_id ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_app_content_two($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_app_content_two` WHERE `app_id`=$app_id ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_gdpr_settings($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_gdpr_settings` WHERE `app_id`=$app_id ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getmanageredeemprize($app_id=0)
      {
        $query_option = "SELECT migarefrence_redeemed_prizes.*,migarefrence_prizes.*,customer.*,migarefrence_ledger.migarefrence_ledger_id as ledger_id FROM `migarefrence_prizes`
                        JOIN migarefrence_redeemed_prizes ON migarefrence_redeemed_prizes.prize_id=migarefrence_prizes.migarefrence_prizes_id
                        JOIN customer ON migarefrence_redeemed_prizes.redeemed_by=customer.customer_id
                        JOIN migarefrence_ledger ON migarefrence_ledger.redeem_id=migarefrence_redeemed_prizes.migarefrence_ledger_id
                        WHERE migarefrence_prizes.app_id=$app_id
                        ORDER BY migarefrence_redeemed_prizes.redeemed_at";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getprznotification($app_id=0,$type=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes_notification` WHERE `app_id` = $app_id AND `type` = $type";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getallprznotification($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes_notification` WHERE `app_id` = $app_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getCreditsApiNotification($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_credits_notification` WHERE `app_id` = $app_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function temp_upate_app_content($app_id=0)
      {
        
        //Move enroll image to new folder
        $default_icon[0]['icon']='enroll.png';
        $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
        if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
        if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
        if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
        $default = new Core_Model_Default();
        $base_url= $default->getBaseUrl();
        foreach ($default_icon as $key => $value) {
          $target_path="";
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$value['icon'];
          $default_path=$base_url."/app/local/modules/Migareference/resources/appicons/".$value['icon'];
          copy($default_path,$target_path);
        }
        $default_content['enroll_url_box_label']="ENROLL URL";
        $default_content['enroll_url_box_bg_color']="#f6edd4";
        $default_content['enroll_url_box_text_color']="#000000";
        $default_content['enroll_url_cover_file']="enroll.png";
        $this->_db->update("migarefrence_app_content_two", $default_content,['app_id = ?' => $app_id]);        

      }
      public function save_app_content($app_id=0)
      {
          // Default icons and lable array
          $default_icon[0]['icon']='how_it_works.png';
          $default_icon[1]['icon']='submit_reports.png';
          $default_icon[2]['icon']='report_status.png';
          $default_icon[3]['icon']='settings.png';
          $default_icon[4]['icon']='credits.png';
          $default_icon[5]['icon']='invite.png';
          $default_icon[6]['icon']='reminder.png';
          $default_icon[7]['icon']='landing_cover.jpg';
          $default_icon[8]['icon']='referrer_report.png';
          $default_icon[9]['icon']='phonebook.png';
          $default_icon[10]['icon']='statistics.png';
          $default_icon[11]['icon']='crm_header.png';
          $default_icon[12]['icon']='add_property_cover_file.gif';
          $default_icon[13]['icon']='enroll.png';

          $default_content['app_id']=$app_id;
          $default_content['top_home_header_bg']="#e34242";
          $default_content['top_home_header_text']="#ffffff";

          $default_content['enroll_url_box_label']="ENROLL URL";
          $default_content['enroll_url_box_bg_color']="#f6edd4";
          $default_content['enroll_url_box_text_color']="#000000";
          $default_content['enroll_url_cover_file']="enroll.png";

          $default_content['how_it_works_label']="COME FUNZIONA?";
          $default_content['how_it_works_bg_color']="#f6edd4";
          $default_content['how_it_works_text_color']="#000000";
          $default_content['how_it_works_file']="how_it_works.png";

          $default_content['referre_report_box_label']="SEGNALAZIONE MANUALE";
          $default_content['referre_report_bg_color']="#f6edd4";
          $default_content['referre_report_text_color']="#000000";
          $default_content['referre_report_file']="referrer_report.png";

          $default_content['add_property_box_label']="INVIA SEGNALAZIONE";
          $default_content['add_property_bg_color']="#f6edd4";
          $default_content['add_property_text_color']="#000000";
          $default_content['add_property_save_btn_color']="#d6d6d6";
          $default_content['add_property_cover_file']="add_property_cover_file.gif";
          $default_content['add_property_save_btn_text']="INVIA REFERENZA";
          $default_content['add_property_save_card_title']="Segnala Prospect";
          $default_content['add_property_invite_btn_color']="#2cec6b";
          $default_content['add_property_invite_btn_text']="INVITA PROSPECT";
          $default_content['add_property_invite_card_title']="Invita il Prospect";
          $default_content['add_property_save_btn_text_color']="#000000";
          $default_content['add_property_invite_btn_text_color']="#000000";
          $default_content['add_property_file']="submit_reports.png";

          $default_content['report_status_box_label']="STATO REPORT";
          $default_content['report_status_bg_color']="#f6edd4";
          $default_content['report_status_text_color']="#000000";
          $default_content['report_status_file']="report_status.png";

          $default_content['prizes_box_label']="RISCATTA PREMI";
          $default_content['prizes_box_bg_color']="#f6edd4";
          $default_content['prizes_box_text_color']="#000000";
          $default_content['prizes_file']="credits.png";

          $default_content['settings_box_label']="IL TUO PROFILO";
          $default_content['settings_bg_color']="#f6edd4";
          $default_content['settings_text_color']="#000000";
          $default_content['settings_file']="settings.png";

          $default_content['reminders_box_label']="PROMEMORIA";
          $default_content['reminders_box_bg_color']="#f6edd4";
          $default_content['reminders_box_text_color']="#000000";
          $default_content['reminders_file']="reminder.png";

          $default_content['phonebooks_box_label']="PhoneBook";
          $default_content['phonebooks_box_bg_color']="#f6edd4";
          $default_content['phonebooks_box_text_color']="#000000";
          $default_content['phonebooks_file']="phonebook.png";

          $default_content['statistics_box_label']="Statistics";
          $default_content['statistics_box_bg_color']="#f6edd4";
          $default_content['statistics_box_text_color']="#000000";
          $default_content['statistics_file']="statistics.png";

          $default_content['landing_page_title']="Richiesta Contatto";
          $default_content['landing_page_form_title']="Richiesta di Contatto";
          $default_content['landing_page_bg_color']="#ffffff";
          $default_content['landing_page_form_bg_color']="#ebebeb";
          $default_content['landing_page_text_color']="#000000";
          $default_content['landing_page_header_file']="landing_cover.jpg";
          $default_content['reportconfirm_page_title']="Complimenti! Richiesta ricevuta";
          $default_content['reportconfirm_page_message']="Provvederemo a contattarti nei prossimi giorni per conoscerci meglio.";
          
          $default_content['crmreport_page_title']="Gestione Report Segnalazione Vincente";
          $default_content['crmreport_page_form_title']="Gestione Report Referenza";
          $default_content['crmreport_page_bg_color']="#ffffff";
          $default_content['crmreport_page_form_bg_color']="#f6edd4";
          $default_content['crmreport_page_text_color']="#000000";
          $default_content['crmreport_nav_bg_color']="#e8e8e8";
          $default_content['crmreport_page_header_file']="crm_header.png";
          $default_content['crmreportconfirm_page_title']="Tutto ricevuto!";
          $default_content['crmreportconfirm_page_message']="Abbiamo correttamente aggiornato lo stato della segnalazione";

          $default_content['top_prize_header_bg']="#e34242";
          $default_content['top_prize_header_text']="#ffffff";
          $default_content['page_text_color']="#cccccc";
          $default_content['created_at']=date('Y-m-d H:i:s');
          // Copy default APP Icons to server image diracotry as Normal push save
          $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
          if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
          if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
          if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
          $default = new Core_Model_Default();
          $base_url= $default->getBaseUrl();
          foreach ($default_icon as $key => $value) {
            $target_path="";
            $target_path.=$dir_image;
            $target_path .= "/features/migareference/".$value['icon'];
            $default_path=$base_url."/app/local/modules/Migareference/resources/appicons/".$value['icon'];
            copy($default_path,$target_path);
          }
          $this->_db->insert("migarefrence_app_content", $default_content);
      }
      public function saveStatus($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_report_status", $data);
        return $this->_db->lastInsertId();
      }
      public function insertReportFields($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_report_fields", $data);
        return $this->_db->lastInsertId();
      }
      public function getBitlycredentails($app_id=0)
      {
        $sql="SELECT *  FROM `migareference_setting` WHERE 1";
        $res_option_value = $this->_db->fetchAll($sql);
        if (count($res_option_value)>0 && !empty($res_option_value[0]['bitly_login'])) {
          return $res_option_value;
        }else {
          $sql="SELECT *  FROM `migareference_pre_report_settings` WHERE `app_id`=$app_id";
          return $res_option_value = $this->_db->fetchAll($sql);
        }
      }
      public function migarefrenceApps()
      {
        $sql="SELECT app_id  FROM `migareference_pre_report_settings` WHERE 1 GROUP BY app_id
              ORDER BY `migareference_pre_report_settings`.`app_id`  ASC";
        $res_option_value = $this->_db->fetchAll($sql);
      }
      public function saveShortnercredentials($data=[])
      {
        $sql="SELECT *  FROM `migareference_setting` WHERE 1";
        $res_option_value = $this->_db->fetchAll($sql);
        if (count($res_option_value)>0) {
        $this->_db->update("migareference_setting", $data,['setting_id >= ?' => 1]);
        return 2;
        }else {
          $data['created_at']    = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_setting", $data);
          $this->_db->lastInsertId();
          return 2;
        }
      }
      public function saveComment($data=[])
      {
          $app_id=$data['app_id'];
          $status_id=$data['status_id'];
          $report_id=$data['report_id'];
        $sql="SELECT *  FROM `migareference_status_comment` WHERE `app_id` = $app_id AND `report_id` =$report_id   AND `status_id` =$status_id ";
        $res_option_value = $this->_db->fetchAll($sql);
        if (count($res_option_value)) {
          $id=$res_option_value[0]['migareference_status_comment_id'];
          $this->_db->update("migareference_status_comment", $data,['migareference_status_comment_id = ?' => $id]);
          return $id;
        }else {
          $data['created_at']    = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_status_comment", $data);
          return $this->_db->lastInsertId();
        }
      }
      public function loadpropertyaddresses($app_id=0)
      {
        $query_option_value = "SELECT address,longitude,latitude,report_no,count(report_no) as count
        FROM migareference_report
        WHERE migareference_report.app_id=$app_id
        GROUP By address,longitude,latitude";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function loadpropertyaphonenumbers($app_id=0)
      {
        $query_option_value = "SELECT owner_mobile,report_no,count(report_no) as count
                               FROM `migareference_report`
                               WHERE app_id=$app_id
                               GROUP By owner_mobile";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getreportfield($app_id=0)
      {
        $query_option_value = "SELECT *
                               FROM `migareference_report_fields`
                               WHERE app_id=$app_id
                               ORDER BY field_order";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function findReportField($key=0)
      {
        $query_option_value = "SELECT *
                               FROM `migareference_report_fields`
                               JOIN migareference_pre_report_settings ON migareference_pre_report_settings.app_id=migareference_report_fields.app_id
                               WHERE migareference_report_fields_id=$key";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getmobilereports($app_id=0,$mobile="")
      {
        $query_option_value = "SELECT report_no FROM `migareference_report` WHERE `app_id`=$app_id AND `owner_mobile`='$mobile'";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getaddressreports($app_id=0,$mobile="")
      {
        $query_option_value = "SELECT report_no FROM `migareference_report` WHERE `app_id`=$app_id AND `owner_mobile`='$mobile'";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_all_customers($app_id=0)
      {
        $query_option_value = "SELECT cr.customer_id,cr.firstname,cr.lastname,cr.email
                               FROM migareference_report as mr
                               JOIN customer as cr ON cr.customer_id=mr.user_id
                               WHERE mr.app_id=$app_id";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getAappadminagentdata($appadminid=0)
      {
        $query_option = "SELECT *
                         FROM admin
                         WHERE admin_id=$appadminid";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getnotificationsdata($report_id=0)
      {
        $query_option_value="SELECT *
              FROM migareference_report
              JOIN migareference_report_status ON migareference_report.currunt_report_status=migareference_report_status.migareference_report_status_id
              JOIN migareference_notification_event ON migareference_notification_event.app_id=migareference_report.app_id AND migareference_report_status.migareference_report_status_id=migareference_notification_event.event_id
              JOIN migareference_email_template ON migareference_email_template.app_id=migareference_report_status.app_id AND migareference_email_template.event_id=migareference_report_status.migareference_report_status_id
              JOIN migareference_push_template ON migareference_push_template.app_id=migareference_report_status.app_id AND migareference_push_template.event_id=migareference_report_status.migareference_report_status_id
              LEFT JOIN migareference_status_comment ON migareference_status_comment.app_id=migareference_report_status.app_id AND migareference_status_comment.status_id=migareference_report_status.migareference_report_status_id
              WHERE migareference_report.migareference_report_id=$report_id";
              $res_option_value = $this->_db->fetchAll($query_option_value);
              return $res_option_value;
      }
      public function getSponsorCustomer($app_id=0,$user_id=0)//deprecated 09-20-2023 as now we have multiple agents New Method is getSponsorList
      {
        $query_option = "SELECT * FROM
                         migareference_report
                         JOIN migareference_invoice_settings on migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN customer ON customer.customer_id=migareference_invoice_settings.sponsor_id
                         WHERE migareference_report.user_id=$user_id AND migareference_report.app_id=$app_id
                         GROUP BY migareference_invoice_settings.sponsor_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSponsorList($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM
        migareference_invoice_settings
        JOIN migareference_referrer_agents ON migareference_referrer_agents.referrer_id=migareference_invoice_settings.user_id
        JOIN migareference_app_agents ON migareference_app_agents.user_id=migareference_referrer_agents.agent_id
        JOIN customer ON customer.customer_id=migareference_referrer_agents.agent_id
        WHERE migareference_invoice_settings.user_id=$user_id AND migareference_invoice_settings.app_id=$app_id
        GROUP BY migareference_referrer_agents.agent_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportSponsor($app_id=0,$report_id=0)
      {
        $query_option = "SELECT customer.customer_id,customer.firstname,customer.lastname,customer.mobile,customer.email,migareference_referrer_agents.agent_id
                                FROM migareference_report
                                JOIN migareference_referrer_agents ON migareference_referrer_agents.referrer_id=migareference_report.user_id
                                JOIN customer ON customer.customer_id=migareference_referrer_agents.agent_id AND  customer.app_id=migareference_report.app_id
                                JOIN migareference_app_agents ON migareference_app_agents.user_id=migareference_referrer_agents.agent_id AND migareference_report.report_custom_type=migareference_app_agents.agent_type
                                WHERE migareference_report.migareference_report_id=$report_id AND migareference_report.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getRefReports($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM
                         migareference_report                         
                         WHERE migareference_report.user_id=$user_id AND migareference_report.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function sendNotification($app_id=0,$report_id=0,$comment='')
      {
        // // Send Notification (1:Refferral Email 2:Agent Email  2:Refferal Push  4: Reffrral PSUH)
        $migareference  = new Migareference_Model_Db_Table_Migareference();
         $eventtemplats  = $migareference->getnotificationsdata($report_id);
        if (count($eventtemplats)) {
        //     // Send Notification
        //       // START EMAIL Notification
              if ($eventtemplats[0]['email_delay_days']==0 && $eventtemplats[0]['email_delay_hours']==0) {
                // Send Immidiately Notification
                  // Find users to send notification (All Admins+1 Refferal Added Report)
                  $admin_customers    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agent_data         = $migareference->getAgentdata($eventtemplats[0]['last_modification_by']);//Who update the report
                  $referral_customers = $migareference->getRefferalCustomers($app_id,$eventtemplats[0]['user_id']);//Admin Users->Agents
                  //Send to Agents / Admins
                    // Subject
                      $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['agt_email_title']);
                      $email_data['email_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_title']);
                    //Message
                      $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['agt_email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@comment@@",$comment,$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$email_data['email_text']);
                      if ($eventtemplats[0]['is_email_agt']) {
                        foreach ($admin_customers as $key => $value) {
                          $mail_retur = $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                        }
                     }
                  //Send to Refferral / User who add Report
                    // Subject
                      $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_email_title']);
                      $email_data['email_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_title']);
                    //Message
                      $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['ref_email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@comment@@",$comment,$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$email_data['email_text']);
                      if ($eventtemplats[0]['is_email_ref']) {
                        $mail_retur = $migareference->sendMail($email_data,$app_id,$eventtemplats[0]['user_id']);
                      }
              }
              // START PUSH Notification
              if ($eventtemplats[0]['push_delay_days']==0 && $eventtemplats[0]['push_delay_hours']==0) {
                // Send Immidiately Notification
                  // Find users to send notification (All Admins+1 Refferal Added Report)
                  $push_agent_user_data    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agent_data              = $migareference->getAgentdata($eventtemplats[0]['last_modification_by']);//Who update the report
                  $push_reffreal_user_data = $migareference->getRefferalCustomers($app_id,$eventtemplats[0]['user_id']);//Admin Users->Agents
                  //Send to Agents / Admins
                    // Subject
                      $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_title']);
                      $push_data['push_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_title']);
                    //Message
                      $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['agt_push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@comment@@",$comment,$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$push_data['push_text']);
                      $push_data['open_feature'] = $eventtemplats[0]['agt_open_feature'];
                      $push_data['feature_id'] = $eventtemplats[0]['agt_feature_id'];
                      $push_data['custom_url'] = $eventtemplats[0]['agt_custom_url'];
                      $push_data['cover_image'] = $eventtemplats[0]['agt_cover_image'];
                      $push_data['app_id'] = $app_id;
                      if ($eventtemplats[0]['is_push_agt']) {
                        foreach ($push_agent_user_data as $key => $value) {
                          $mail_retur = $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                        }
                      }
                  //Send to Refferral / User who add Report
                    // Subject
                      $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_title']);
                      $push_data['push_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_title']);
                    //Message
                      $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['ref_push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@comment@@",$comment,$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$push_data['push_text']);
                      $push_data['open_feature'] = $eventtemplats[0]['ref_open_feature'];
                      $push_data['feature_id']   = $eventtemplats[0]['ref_feature_id'];
                      $push_data['custom_url']   = $eventtemplats[0]['ref_custom_url'];
                      $push_data['cover_image']  = $eventtemplats[0]['ref_cover_image'];
                      $push_data['app_id'] = $app_id;
                      if ($eventtemplats[0]['is_push_ref']) {
                       $mail_retur = $migareference->sendPush($push_data,$app_id,$eventtemplats[0]['user_id']);
                      }
              }
              if($eventtemplats[0]['push_delay_days']>0 || $eventtemplats[0]['push_delay_hours']>0 || $eventtemplats[0]['email_delay_days']>0 || $eventtemplats[0]['email_delay_hours']>0){
                $push_hours=0;
                $push_hours  = ($eventtemplats[0]['push_delay_days']>0) ? $eventtemplats[0]['push_delay_days']*24 : 0 ;
                $push_hours  =  $push_hours+$eventtemplats[0]['push_delay_hours'];
                $email_hours = 0;
                $email_hours = ($eventtemplats[0]['email_delay_days']>0) ? $eventtemplats[0]['email_delay_days']*24 : 0 ;
                $email_hours = $email_hours+$eventtemplats[0]['email_delay_hours'];
                $cron_notification['app_id']=$app_id;
                $cron_notification['report_id']=$eventtemplats[0]['migareference_report_id'];
                $cron_notification['notification_event_id']=$status_id;
                $cron_notification['trigger_start_time']=date('Y-m-d H:i:s');
                $cron_notification['push_delay_hours']=$push_hours;
                $cron_notification['email_delay_hours']=$email_hours;
                $migareference->saveCronnotification($cron_notification);
              }
        }
      }
      public function defaultGdprTemplates($app_id=0,$value_id=0)
      {
        $migarefrence_gdpr_settings = array(            
            'app_id' => $app_id,
            'value_id' => $value_id,
            'consent_info_active' => '1',
            'invite_consent_warning_active' => '1',
            'consent_info_popup_title' => 'Richiesta Consenso GDPR',
            'invite_consent_warning_title' => 'Segnalazione e Privacy',
            'consent_popup_doyou_text' => 'Fallo tu per me',
            'reportconfirm_page_bg' => 'rgba(255, 255, 255, 1)',
            'reportconfirm_page_font' => 'rgba(0, 0, 0, 1)',
            'consent_popup_proceed_text' => 'Si, procedo',
            'consent_popup_proceed_background' => 'rgba(0, 255, 34, 1)',
            'consent_col_page_title' => 'Raccolta Consenso Ricontatto',
            'consent_col_page_header' => 'Raccolta Consenso Ricontatto',
            'consent_popup_proceed_font' => 'rgba(36, 35, 35, 1)',
            'consent_col_agree_btn_background' => 'rgba(0, 255, 85, 1)',
            'consent_col_agree_btn_font' => 'rgba(0, 0, 0, 1)',
            'consent_col_page_font' => 'rgba(0, 0, 0, 1)',
            'consent_col_page_bg' => 'rgba(255, 255, 255, 1)',
            'consent_popup_doyou_font' => 'rgba(255, 255, 255, 1)',
            'invite_consent_btn_discard_text' => 'Scarta',
            'invite_consent_btn_discard_font' => 'rgba(0, 0, 0, 1)',
            'landing_page_title' => 'Richiesta ricontatto',
            'landing_page_form_title' => 'Richiesta ricontatto',
            'landing_page_header_file' => 'gdpr_header.jpg',
            'report_page_popup_file' => 'gdpr.png',
            'invite_report_popup_file' => 'gdpr.png',
            'landing_page_bg_color' => 'rgba(255, 255, 255, 1)',
            'landing_page_text_color' => 'rgba(0, 0, 0, 1)',
            'landing_page_form_bg_color' => 'rgba(224, 224, 224, 1)',
            'reportconfirm_page_title' => 'Consenso ricontatto raccolto!',
            'invite_consent_btn_submit_font' => 'rgba(0, 0, 0, 1)',
            'invite_consent_btn_submit_bg' => 'rgba(0, 250, 33, 1)',
            'invite_consent_btn_submit_text' => 'Ricontattatemi',
            'invite_consent_btn_discard_bg' => 'rgba(255, 187, 0, 1)',
            'invite_consent_btn_under_text' => 'Procedi',
            'invite_consent_btn_under_font' => 'rgba(0, 0, 0, 1)',
            'invite_consent_btn_under_bg' => 'rgba(0, 255, 64, 1)',
            'consent_popup_doyou_background' => 'rgba(255, 174, 0, 1)',
            'consent_thank_page_bg' => 'rgba(250, 250, 250, 1)',
            'consent_thank_page_font' => 'rgba(0, 0, 0, 1)',
            'consent_thank_page_title' => 'Conferma raccolta consenso',
            'consent_thank_page_header' => 'Conferma raccolta consenso',
            'consent_info_popup_body' => 'Se il tuo contatto è a portata di mano, inviagli la richiesta di consenso Privacy. Se non lo fai dovremo richiedere noi per te il consenso privacy prima di poter illustrare informazioni commerciali. Puoi ignorare questo passaggio.',
            'consent_col_page_body' => 'Gentile @@report_owner@@, il suo amico o conoscente @@agent_name@@ ci ha comunicato che vuole essere contattato per informazioni sui nostri prodotti o servizi. Come previsto dalla normativa GDPR abbiamo semplicemente necessità di raccogliere il suo consenso esplicito per procedere a contattarLa. Grazie per la collaborazione.',
            'consent_col_agree_btn_text' => 'Do il consenso!',
            'consent_invit_msg_body' => 'Ciao @@report_owner@@, sono @@referrer_name@@, clicca su questo link @@consent_link@@ per darci il consenso privacy per essere contattato, grazie. ',
            'consent_thank_page_body' => 'Grazie per averci dato il consenso per contattarti, ti chiameremo il prima possibile.',
            'invite_consent_warning_body' => 'Per segnalarci un potenziale cliente tuo amico o conoscente la procedura richiede che condividi con questa persona un semplice link via whatsapp o sms. Il destinatario dovrà compilare i suoi dati di contatto, accettare linformativa privacy e dare il consenso per essere contattato. Un operazione di 30 secondi! Se puoi cerca di aiutarlo. Terminata la compilazione troverai la segnalazione sotto il menu Stato Segnalazioni.',
            'invite_message' => 'Ciao sono @@referrer_name@@, ti invio il modulo richiesta contatto per lazienda con cui collaboro. Compilalo per essere richiamato senza impegno: @@landing_link@@',
            'reportconfirm_page_message' => 'La ringraziamo, abbiamo ricevuto la Sua richiesta di contatto e un nostro incaricato La contatterà nel più breve tempo possibile. Arrivederci!',
            'created_at' => date('Y-m-d H:i:s')
          );
          // upload Header file
          $header['gdpr_header']='gdpr_header.jpg';
          $report_icon['report_page_popup_file']='gdpr.png';
          $invite_icon['invite_report_popup_file']='gdpr.png';
          $this->copyImages($app_id, $header,'');
          $this->copyImages($app_id, $report_icon,'');
          $this->copyImages($app_id, $invite_icon,'');
          $this->_db->insert("migarefrence_gdpr_settings", $migarefrence_gdpr_settings);        
      }
      public function sendReminderNotification($app_id=0,$report_id=0,$comment='')
      {
        // // Send Notification (1:Refferral Email 2:Agent Email  2:Refferal Push  4: Reffrral PSUH)
        $migareference  = new Migareference_Model_Db_Table_Migareference();
        $eventtemplats  = $migareference->getnotificationsdata($report_id);
        if (count($eventtemplats)) {
        //     // Send Notification
        //       // START EMAIL Notification
              if ($eventtemplats[0]['email_delay_days']==0 && $eventtemplats[0]['email_delay_hours']==0) {
                // Send Immidiately Notification
                  // Find users to send notification (All Admins+1 Refferal Added Report)
                  $admin_customers    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agent_data               = $migareference->getAgentdata($eventtemplats[0]['last_modification_by']);//Who update the report
                  $referral_customers = $migareference->getRefferalCustomers($app_id,$eventtemplats[0]['user_id']);//Admin Users->Agents
                  //Send to Agents / Admins
                    // Subject
                      $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['reminder_agt_email_title']);
                      $email_data['email_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_title']);
                    //Message
                      $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['reminder_agt_email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@comment@@",$comment,$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$email_data['email_text']);
                      if ($eventtemplats[0]['reminder_is_email_agt']) {
                        foreach ($admin_customers as $key => $value) {
                          $mail_retur = $migareference->sendMail($email_data,$app_id,$value['customer_id']);
                        }
                     }
                  //Send to Refferral / User who add Report
                    // Subject
                      $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['reminder_ref_email_title']);
                      $email_data['email_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_title']);
                      $email_data['email_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_title']);
                    //Message
                      $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['invoice_name']." ".$referral_customers[0]['invoice_surname'],$eventtemplats[0]['reminder_ref_email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@comment@@",$comment,$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$email_data['email_text']);
                      $email_data['email_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$email_data['email_text']);
                      if ($eventtemplats[0]['reminder_is_email_ref']) {
                        $mail_retur = $migareference->sendMail($email_data,$app_id,$eventtemplats[0]['user_id']);
                      }
              }
              // START PUSH Notification
              if ($eventtemplats[0]['push_delay_days']==0 && $eventtemplats[0]['push_delay_hours']==0) {
                // Send Immidiately Notification
                  // Find users to send notification (All Admins+1 Refferal Added Report)
                  $push_agent_user_data    = $migareference->getAdminCustomers($app_id);//Admin Users->Agents
                  $agent_data              = $migareference->getAgentdata($eventtemplats[0]['last_modification_by']);//Who update the report
                  $push_reffreal_user_data = $migareference->getRefferalCustomers($app_id,$eventtemplats[0]['user_id']);//Admin Users->Agents
                  //Send to Agents / Admins
                    // Subject
                      $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['reminder_agt_push_title']);
                      $push_data['push_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_title']);
                    //Message
                      $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['reminder_agt_push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@comment@@",$comment,$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$push_data['push_text']);
                      $push_data['open_feature'] = $eventtemplats[0]['reminder_agt_open_feature'];
                      $push_data['feature_id'] = $eventtemplats[0]['reminder_agt_feature_id'];
                      $push_data['custom_url'] = $eventtemplats[0]['reminder_agt_custom_url'];
                      $push_data['cover_image'] = $eventtemplats[0]['reminder_agt_cover_image'];
                      $push_data['app_id'] = $app_id;
                      if ($eventtemplats[0]['reminder_is_push_agt']) {
                        foreach ($push_agent_user_data as $key => $value) {
                          $mail_retur = $migareference->sendPush($push_data,$app_id,$value['customer_id']);
                        }
                      }
                  //Send to Refferral / User who add Report
                    // Subject
                      $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['reminder_ref_push_title']);
                      $push_data['push_title']= str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_title']);
                      $push_data['push_title']= str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_title']);
                    //Message
                      $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['invoice_name']." ".$push_reffreal_user_data[0]['invoice_surname'],$eventtemplats[0]['reminder_ref_push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner@@",$eventtemplats[0]['owner_name']." ".$eventtemplats[0]['owner_surname'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@property_owner_phone@@",$eventtemplats[0]['owner_mobile'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@report_no@@",$eventtemplats[0]['report_no'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@comment@@",$comment,$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@commission@@",$eventtemplats[0]['commission_fee'],$push_data['push_text']);
                      $push_data['push_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$push_data['push_text']);
                      $push_data['open_feature'] = $eventtemplats[0]['reminder_ref_open_feature'];
                      $push_data['feature_id']   = $eventtemplats[0]['reminder_ref_feature_id'];
                      $push_data['custom_url']   = $eventtemplats[0]['reminder_ref_custom_url'];
                      $push_data['cover_image']  = $eventtemplats[0]['reminder_ref_cover_image'];
                      $push_data['app_id'] = $app_id;
                      if ($eventtemplats[0]['reminder_is_push_ref']) {
                       $mail_retur = $migareference->sendPush($push_data,$app_id,$eventtemplats[0]['user_id']);
                      }
              }
              if($eventtemplats[0]['push_delay_days']>0 || $eventtemplats[0]['push_delay_hours']>0 || $eventtemplats[0]['email_delay_days']>0 || $eventtemplats[0]['email_delay_hours']>0){
                $push_hours  = 0;
                $push_hours  = ($eventtemplats[0]['push_delay_days']>0) ? $eventtemplats[0]['push_delay_days']*24 : 0 ;
                $push_hours  =  $push_hours+$eventtemplats[0]['push_delay_hours'];
                $email_hours = 0;
                $email_hours = ($eventtemplats[0]['email_delay_days']>0) ? $eventtemplats[0]['email_delay_days']*24 : 0 ;
                $email_hours = $email_hours+$eventtemplats[0]['email_delay_hours'];
                $cron_notification['app_id']=$app_id;
                $cron_notification['report_id']=$eventtemplats[0]['migareference_report_id'];
                $cron_notification['notification_event_id']=$status_id;
                $cron_notification['trigger_start_time']=date('Y-m-d H:i:s');
                $cron_notification['push_delay_hours']=$push_hours;
                $cron_notification['email_delay_hours']=$email_hours;
                $migareference->saveCronnotification($cron_notification);
              }
              // Set is_reminder_sent=1
              $migareference->reminder_sent($eventtemplats[0]['migareference_report_id']);
        }
      }
      public function getDelaysnotification()
      {
         $query_option_value = "SELECT  *,migareference_cron_notifications.email_delay_hours as email_delay,migareference_cron_notifications.push_delay_hours as push_delay,migareference_cron_notifications.migareference_cron_notifications_id
                                FROM migareference_cron_notifications
                                LEFT JOIN migareference_status_comment ON migareference_status_comment.app_id=migareference_cron_notifications.app_id AND migareference_cron_notifications.notification_event_id=migareference_status_comment.status_id AND migareference_status_comment.report_id=migareference_cron_notifications.report_id
                                JOIN migareference_notification_event ON migareference_notification_event.event_id=migareference_cron_notifications.notification_event_id AND migareference_notification_event.app_id=migareference_cron_notifications.app_id
                                JOIN migareference_push_template ON migareference_push_template.event_id=migareference_cron_notifications.notification_event_id AND migareference_push_template.app_id=migareference_cron_notifications.app_id
                                JOIN migareference_email_template ON migareference_email_template.event_id=migareference_cron_notifications.notification_event_id AND migareference_email_template.app_id=migareference_cron_notifications.app_id
                                JOIN migareference_report ON migareference_report.migareference_report_id=migareference_cron_notifications.report_id
                                WHERE migareference_cron_notifications.is_push_deliverd=0 OR migareference_cron_notifications.is_email_deliverd=0";
        return $this->_db->fetchAll($query_option_value);
      }
      public function get_earnings($app_id=0,$user_id=0)
      {
        $query_option_value = "SELECT SUM(`earn_amount`) as total_earn FROM `migareference_user_earnings` WHERE `app_id`=$app_id AND `refferral_user_id`=$user_id";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_prize_entry_count($redeemed_id=0,$app_id=0,$prize_id=0,$user_id=0)
      {
        $query_option_value = "SELECT application.*,migarefrence_ledger.migarefrence_ledger_id,
                               Sum(Case When migarefrence_ledger.entry_type = 'C'  Then 1 Else 0 End) as credit,
                               Sum(Case When migarefrence_ledger.entry_type = 'D'  Then 1 Else 0 End) as debit
                               FROM migarefrence_ledger
                               jOIN application ON application.app_id=migarefrence_ledger.app_id
                               WHERE migarefrence_ledger.self_id=$redeemed_id AND migarefrence_ledger.app_id=$app_id and migarefrence_ledger.prize_id=$prize_id AND migarefrence_ledger.user_id=$user_id";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_referral_users($app_id=0)
      {
        $query_option_value = "SELECT mis.created_at,
        mis.invoice_name,       
        mis.invoice_surname,
        customer.email,
        mis.vat_id,
        mis.migareference_invoice_settings_id,
        mis.status,
        mis.address_province_id,
        mis.ref_consent_timestmp,
        SUM(ue.earn_amount) AS total_earn,
        mis.user_id,
        mis.leave_status,
        customer.mobile,
        customer.birthdate,
        geop.province,
        geop.province_code,
        geoc.country,
        geoc.country_code,
        ph.*,
        ph.created_at AS phone_creat_date,
        migareference_jobs.migareference_jobs_id,
        migareference_jobs.job_title,
        migareference_professions.migareference_professions_id,
        migareference_professions.profession_title,
        mis.terms_accepted,
        (SELECT SUM(CASE WHEN le.entry_type = 'C' THEN le.amount ELSE -le.amount END)
         FROM migarefrence_ledger AS le
         WHERE le.user_id = mis.user_id) AS credits,        
        mis.sponsor_id,
        ag.migareference_app_agents_id,
        COUNT(migareference_report.migareference_report_id) AS total_reports
         FROM migareference_invoice_settings AS mis
         LEFT JOIN migareference_app_admins AS ad ON ad.user_id = mis.user_id
         LEFT JOIN migareference_referrer_agents AS reag ON reag.referrer_id=mis.user_id         
         LEFT JOIN migareference_app_agents AS ag ON ag.user_id = reag.agent_id
         LEFT JOIN migareference_user_earnings AS ue ON mis.user_id = ue.refferral_user_id
         LEFT JOIN migarefrence_ledger AS le ON mis.user_id = le.user_id
         LEFT JOIN migareference_geo_provinces AS geop ON geop.migareference_geo_provinces_id = mis.address_province_id        
         LEFT JOIN migareference_geo_countries AS geoc ON geoc.migareference_geo_countries_id=mis.address_country_id
         LEFT JOIN migareference_report ON migareference_report.user_id = mis.user_id AND migareference_report.status = 1
         JOIN customer ON customer.customer_id = mis.user_id
         JOIN migarefrence_phonebook AS ph ON ph.invoice_id = mis.migareference_invoice_settings_id
         LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id = ph.job_id
         LEFT JOIN migareference_professions ON migareference_professions.migareference_professions_id = ph.profession_id
         WHERE mis.app_id =$app_id AND ad.user_id IS NULL
         GROUP BY mis.user_id
         ORDER BY mis.invoice_surname ASC, mis.created_at ASC";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_opt_referral_users($app_id=0,$join='')
      {
        $query_option_value = "SELECT mis.created_at,
        mis.invoice_name,       
        mis.invoice_surname,
        customer.email,
        mis.vat_id,
        mis.migareference_invoice_settings_id,
        mis.status,
        mis.address_province_id,
        mis.ref_consent_timestmp,
        SUM(ue.earn_amount) AS total_earn,
        mis.user_id,
        mis.leave_status,        
        customer.mobile,
        customer.birthdate,
        customer.firstname,
        customer.lastname,
        geop.province,
        geop.province_code,
        geoc.country,
        geoc.country_code,
        ph.*,        
        DATE_FORMAT(ph.created_at, '%d-%m-%Y') AS phone_creat_date,
        DATE_FORMAT(ph.last_contact_at, '%d-%m-%Y') AS last_contact_at,
        migareference_jobs.migareference_jobs_id,
        migareference_jobs.job_title,
        migareference_professions.migareference_professions_id,
        migareference_professions.profession_title,
        mis.terms_accepted,
        (SELECT SUM(CASE WHEN le.entry_type = 'C' THEN le.amount ELSE -le.amount END)
         FROM migarefrence_ledger AS le
         WHERE le.user_id = mis.user_id) AS credits,
         sponsor_one.customer_id AS sponsor_one_id,
        sponsor_two.customer_id AS sponsor_two_id,
        sponsor_one.firstname AS sponsor_one_firstname,
        sponsor_one.lastname AS sponsor_one_lastname,
        sponsor_two.firstname AS sponsor_two_firstname,
        sponsor_two.lastname AS sponsor_two_lastname,
        mis.sponsor_id,
        COUNT(DISTINCT migareference_report.migareference_report_id) AS total_reports,
        COUNT(DISTINCT CASE WHEN migareference_report_status.standard_type NOT IN (3, 4) THEN migareference_report.migareference_report_id END) AS active_reports
         FROM migareference_invoice_settings AS mis
         LEFT JOIN migareference_app_admins AS ad ON ad.user_id = mis.user_id        
         LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=mis.user_id
         LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=mis.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
         LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
         LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id       
         LEFT JOIN migareference_user_earnings AS ue ON mis.user_id = ue.refferral_user_id
         LEFT JOIN migarefrence_ledger AS le ON mis.user_id = le.user_id
         LEFT JOIN migareference_geo_provinces AS geop ON geop.migareference_geo_provinces_id = mis.address_province_id        
         LEFT JOIN migareference_geo_countries AS geoc ON geoc.migareference_geo_countries_id=mis.address_country_id
         LEFT JOIN migareference_report ON migareference_report.user_id = mis.user_id AND migareference_report.status = 1
         LEFT JOIN migareference_report_status ON migareference_report.currunt_report_status = migareference_report_status.migareference_report_status_id
         JOIN customer ON customer.customer_id = mis.user_id
         JOIN migarefrence_phonebook AS ph ON ph.invoice_id = mis.migareference_invoice_settings_id
         LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id = ph.job_id
         LEFT JOIN migareference_professions ON migareference_professions.migareference_professions_id = ph.profession_id
         WHERE mis.app_id =$app_id AND ad.user_id IS NULL AND ph.name NOT LIKE '%*%' ".$join;
$query_option_value.=" GROUP BY mis.user_id ORDER BY mis.invoice_surname ASC, mis.created_at ASC";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
  
      public function get_sponsor_agent($app_id=0,$agnet_key=0)
      {
        $query_option_value = "SELECT mis.created_at,mis.invoice_name,mis.invoice_surname,customer.email,mis.vat_id,mis.migareference_invoice_settings_id,mis.status,SUM(ue.earn_amount) AS total_earn,mis.user_id,mis.leave_status,customer.mobile,
                               Sum(Case When le.entry_type = 'C'  Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End) credits,ag.migareference_app_agents_id,mis.sponsor_id
                               FROM migareference_invoice_settings as mis
                               LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
                               LEFT JOIN migareference_app_agents as ag ON ag.user_id=mis.sponsor_id
                               LEFT JOIN migareference_user_earnings AS ue ON mis.user_id=ue.refferral_user_id
                               LEFT JOIN migarefrence_ledger AS le ON mis.user_id=le.user_id
                               JOIN customer ON customer.customer_id=mis.user_id
                               WHERE mis.app_id=$app_id AND ad.user_id IS NULL AND mis.sponsor_id=$agnet_key
                               GROUP BY mis.user_id
                               ORDER BY `mis`.`invoice_surname` ASC,`mis`.`invoice_name` ASC, customer.email ASC";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_referral_agent($agent_key=0)
      {
        $query_option_value = "SELECT customer.firstname,customer.lastname,customer.email,customer.customer_id
                               FROM migareference_app_agents as ag
                               JOIN customer ON customer.customer_id=ag.user_id
                               WHERE ag.migareference_app_agents_id=$agent_key";
                               $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_credit_balance($app_id=0,$user_id=0)
      {
        $query_option_value = "SELECT mis.created_at,mis.invoice_name,mis.invoice_surname,customer.email,mis.invoice_mobile,mis.vat_id,mis.migareference_invoice_settings_id,mis.status,SUM(ue.earn_amount) AS total_earn,mis.user_id,mis.leave_status,
                               Sum(Case When le.entry_type = 'C'  Then le.amount Else 0 End) -Sum(Case When le.entry_type = 'D'  Then le.amount Else 0 End) credits
                               FROM migareference_invoice_settings as mis
                               LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
                               LEFT JOIN migareference_user_earnings AS ue ON mis.user_id=ue.refferral_user_id
                               LEFT JOIN migarefrence_ledger AS le ON mis.user_id=le.user_id
                               JOIN customer ON customer.customer_id=mis.user_id
                               WHERE mis.app_id=$app_id AND mis.user_id=$user_id AND ad.user_id IS NULL
                               GROUP BY mis.user_id,mis.migareference_invoice_settings_id LIMIT 1";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_leadger($app_id=0,$user_id=0)
      {
        $query_option_value = "SELECT *  FROM `migarefrence_ledger` WHERE `app_id` = $app_id AND `user_id` = $user_id";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function get_siberianuser($app_id=0)
      {
        $query_option_value = "SELECT customer.customer_id,mis.migareference_invoice_settings_id,customer.firstname,customer.lastname,customer.email,mis.sponsor_id,refag.agent_id
                               FROM customer
                               LEFT JOIN migareference_invoice_settings as mis ON mis.user_id=customer.customer_id
                               LEFT JOIN migareference_referrer_agents AS refag ON refag.referrer_id=mis.user_id
                               LEFT JOIN migareference_app_admins as ad ON  ad.user_id=customer.customer_id
                               WHERE customer.app_id=$app_id AND ad.migareference_app_admins_id IS NULL
                               GROUP BY customer.customer_id
                               ORDER BY customer.lastname asc,customer.firstname asc,customer.email asc";
        $res_option_value   = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function useraccountSettings($app_id=0)
      {
        $query_option_value = "SELECT application_option_value.value_id,application_option_value.settings
                               FROM application_option, application_option_value
                               WHERE application_option.option_id = application_option_value.option_id
                               AND application_option_value.app_id = $app_id
                               AND application_option.code='tabbar_account' LIMIT 1";
        $res_option_value   =  $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function updateuseraccountSettings($value_id=0,$data=[])
      {
        $this->_db->update("application_option_value", $data,['value_id = ?' => $value_id]);
      }
        public function get_custom_status_reports($app_id=0)
        {
          $query_option_value = "SELECT *
                                 FROM `migareference_report` as rp
                                 JOIN migareference_report_status as rs ON rp.currunt_report_status=rs.migareference_report_status_id AND rs.is_standard=0
                                 WHERE rp.app_id=$app_id";
          $res_option_value = $this->_db->fetchAll($query_option_value);
          return $res_option_value;
        }
      public function get_leadger_customer($app_id=0,$referrer_id=0)
      {
        $query_option_value = "SELECT migarefrence_ledger.*,customer.email  FROM `migarefrence_ledger`
                               JOIN customer ON customer.customer_id=migarefrence_ledger.user_id
                               WHERE migarefrence_ledger.app_id = $app_id AND migarefrence_ledger.user_id=$referrer_id ORDER BY migarefrence_ledger.created_at ASC";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getAllReferralUsers($app_id=0)
      {
        $query_option_value = "SELECT
                               mis.app_id,
                               mis.user_id,
                               mis.migareference_invoice_settings_id,
                               mis.invoice_name,
                               mis.invoice_surname,
                               mis.invoice_mobile,
                               mis.token,
                               mis.referrer_source,
                               mis.created_at,
                               cs.birthdate,
                               cs.customer_id,
                               cs.email,
                               ph.rating,
                               ph.migarefrence_phonebook_id
                               FROM migareference_invoice_settings as mis
                               LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id
                               LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=mis.migareference_invoice_settings_id AND ph.type=1
                               JOIN customer as cs ON cs.customer_id=mis.user_id
                               WHERE ad.user_id IS NULL AND mis.app_id=$app_id
                               GROUP BY mis.user_id
                               ORDER BY mis.app_id,mis.user_id";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getReferrers($app_id=0)
      {
        $query_option_value = "SELECT
                               mis.app_id,
                               mis.user_id,
                               mis.migareference_invoice_settings_id,
                               mis.invoice_name,
                               mis.invoice_surname,
                               mis.invoice_mobile,
                               mis.token,
                               mis.referrer_source,
                               mis.created_at                               
                               FROM migareference_invoice_settings as mis
                               LEFT JOIN migareference_app_admins as ad ON ad.user_id=mis.user_id                               
                               JOIN customer ON customer.customer_id=mis.user_id                               
                               WHERE ad.user_id IS NULL AND mis.app_id=$app_id
                               AND mis.`invoice_name` NOT LIKE '%*%'
        						           AND mis.`invoice_name` NOT LIKE '*%'
                               GROUP BY mis.user_id
                               ORDER BY mis.invoice_surname";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getAllNewUsers($app_id=0,$min_hour=0,$max_hour=0)
      {
        $query_option_value = "SELECT *
        FROM migareference_invoice_settings                              
        JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
        LEFT JOIN migareference_automation_log ON migareference_automation_log.user_id=migareference_invoice_settings.user_id 
        AND migareference_automation_log.trigger_id=10
        WHERE TIMESTAMPDIFF(HOUR, migareference_invoice_settings.created_at, NOW()) >$min_hour
        AND migareference_invoice_settings.app_id=$app_id AND migareference_invoice_settings.terms_accepted!=1 
        AND DATE(migareference_invoice_settings.created_at)>='2024-02-01' 
        AND migareference_automation_log.migareference_automation_log_id IS NULL
        ORDER BY `migareference_invoice_settings`.`created_at` DESC";
        $res_option_value = $this->_db->fetchAll($query_option_value);
        return $res_option_value;
      }
      public function getReportStatus($app_id=0)
      {
        $query_option_value = "SELECT migareference_report_status.*,count(migareference_report.migareference_report_id) as joined_repoort
                               FROM migareference_report_status
                               LEFT JOIN migareference_report
                               ON migareference_report.currunt_report_status=migareference_report_status.migareference_report_status_id
                               WHERE migareference_report_status.app_id=$app_id AND migareference_report_status.status=1
                               GROUP BY migareference_report_status.migareference_report_status_id
                               ORDER BY migareference_report_status.order_id";
        return $res_option_value = $this->_db->fetchAll($query_option_value);
      }
      public function templateStatus($status=[],$type=0)
      {
        $StaticIons=$this->getStaticIons();
        foreach ($status as $key => $value) {
            $static_ions="";
          if ($value['is_standard']) {
            switch ($value['standard_type']) {
                case 1:
                    $static_ions=$StaticIons['new_report'];
                    break;
                case 2:
                    $static_ions=$StaticIons['mandate_required'];
                    break;
                case 3:
                    $static_ions=$StaticIons['paid'];
                    break;
                case 4:
                    $static_ions=$StaticIons['declined'];
                    break;
            }
          }else {
              $static_ions=$StaticIons['optional'];
          }
          if ($type==1) { //Admin Side
            $icno_image="<img width='35px' height='35px' src='".$static_ions."' />";
          }else { // App Side
            $icno_image="<img width='35px' height='35px' ng-src='".$static_ions."' />";
          }
          $value['status_title']=$icno_image." - ".$value['status_title'];
          $property_status[]=$value;
        }
        return $property_status;
      }
      public function saveLog($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_activity_logs", $data);
        return 1;
      }
      public function addAppcontenttwo($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_app_content_two", $data);
        return 1;
      }
      public function saveCronnotification($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_cron_notifications", $data);
        return 1;
      }
      public function saveEmail($datas=[])
      {
        $datas['created_at']  = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_email_template", $datas);
        $id = $this->_db->lastInsertId();
        return $id;
      }

      public function copyImages($app_id=0,$datas=[],$from_path='')
      {
        // Copy default images to server image diracotry as Normal push save
        $dir_image='';
        $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
        if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
        if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
        if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
        $default = new Core_Model_Default();
        $base_url= $default->getBaseUrl();
        $target_path='';
        $default_path='';
        if ($datas['gdpr_header']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['gdpr_header'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['gdpr_header'] : $from_path.$datas['gdpr_header'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['report_page_popup_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['report_page_popup_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['report_page_popup_file'] : $from_path.$datas['gdpr_header'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['invite_report_popup_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['invite_report_popup_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['invite_report_popup_file'] : $from_path.$datas['gdpr_header'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['ref_cover_image']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['ref_cover_image'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['ref_cover_image'] : $from_path.$datas['ref_cover_image'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['ref_credits_api_custom_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['ref_credits_api_custom_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['ref_credits_api_custom_file'] : $from_path.$datas['ref_credits_api_custom_file'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['agt_cover_image']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['agt_cover_image'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['agt_cover_image'] : $from_path.$datas['agt_cover_image'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['reminder_agt_cover_image']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['reminder_agt_cover_image'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['reminder_agt_cover_image'] : $from_path.$datas['reminder_agt_cover_image'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['agt_prz_custom_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['agt_prz_custom_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['agt_prz_custom_file'] : $from_path.$datas['agt_prz_custom_file'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['ref_prz_custom_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['ref_prz_custom_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['ref_prz_custom_file'] : $from_path.$datas['ref_prz_custom_file'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['status_icon']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['status_icon'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['status_icon'] : $from_path.$datas['status_icon'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['rep_rem_icon_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['rep_rem_icon_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['rep_rem_icon_file'] : $from_path.$datas['rep_rem_icon_file'] ;
          copy($default_path,$target_path);
        }
        $target_path='';
        $default_path='';
        if ($datas['rep_rem_custom_file']!="") {
          $target_path.=$dir_image;
          $target_path .= "/features/migareference/".$datas['rep_rem_custom_file'];
          $default_path = ($from_path=='') ? $base_url."/app/local/modules/Migareference/resources/appicons/".$datas['rep_rem_custom_file'] : $from_path.$datas['rep_rem_custom_file'] ;
          copy($default_path,$target_path);
        }
        return $datas;
      }
      public function updateEmail($datas=[])
      {
        $id=$datas['migareference_email_template_id'];
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migareference_email_template", $datas,['migareference_email_template_id = ?' => $id]);
        return $id;
      }
      public function updateGdprSetings($datas=[])
      {
        $this->uploadApplicationFile($datas['app_id'],$datas['landing_page_header_file'],0);
        $this->uploadApplicationFile($datas['app_id'],$datas['report_page_popup_file'],0);
        $this->uploadApplicationFile($datas['app_id'],$datas['invite_report_popup_file'],0);
        if (empty($datas['landing_page_header_file'])) {
          unset($datas['landing_page_header_file']);
        }
        if (empty($datas['report_page_popup_file'])) {
          unset($datas['report_page_popup_file']);
        }
        if (empty($datas['invite_report_popup_file'])) {
          unset($datas['invite_report_popup_file']);
        }
        $id=$datas['migarefrence_gdpr_settings_id'];
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migarefrence_gdpr_settings", $datas,['migarefrence_gdpr_settings_id = ?' => $id]);
        return $id;
      }
      public function prizestatus($datas=[])
      {
        $id=$datas['migarefrence_prizes_id'];
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migarefrence_prizes", $datas,['migarefrence_prizes_id = ?' => $id]);
        return $id;
      }
      public function deleteReferrerAgent($app_id=0,$agent_id=0)
      {
        return $this->_db->delete('migareference_referrer_agents',['agent_id = ?' => $agent_id,'app_id = ?' => $app_id]);
      }
      public function updateSponsoragent($app_id=0,$user_id=0,$sponsor_id=0)
      {
        $datas['sponsor_id']  = $sponsor_id;
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migareference_invoice_settings", $datas,['user_id = ?' => $user_id,'app_id = ?' => $app_id]);
        return $id;
      }
      public function referrerGdpr($app_id=0,$user_id=0,$datas=[])
      {
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migareference_invoice_settings", $datas,['user_id = ?' => $user_id,'app_id = ?' => $app_id]);
        return $id;
      }
      public function updateredeemstatus($id=0,$status=0)
      {
        $datas['redeemed_status']  = $status;
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migarefrence_redeemed_prizes", $datas,['migarefrence_ledger_id = ?' => $id]);
        return $id;
      }
      public function updateAppcontenttwo($data=[])
      {
        $id=$data['migarefrence_app_content_two_id'];
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migarefrence_app_content_two", $data,['migarefrence_app_content_two_id = ?' => $id]);
        return $id;
      }
      public function archiveReminder($id)
      {
        $datas['is_deleted']  = 1;
        $datas['current_reminder_status']  = 'cancele';
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migareference_automation_log", $datas,['migareference_automation_log_id = ?' => $id]);
        return $id;
      }
      public function updateAppcontent($data=[])
      {
        $app_id=$data['app_id'];
        if (!empty($data['how_it_works_file'])) {
            $icon = $data['how_it_works_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['how_it_works_file']);
        }
        if (!empty($data['crmreport_page_header_file'])) {
            $icon = $data['crmreport_page_header_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['crmreport_page_header_file']);
        }
        if (!empty($data['add_property_file'])) {
            $icon = $data['add_property_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['add_property_file']);
        }
        if (!empty($data['report_type_pop_cover'])) {
            $icon = $data['report_type_pop_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['report_type_pop_cover']);
        }
        if (!empty($data['report_type_pop_btn_one_icon'])) {
            $icon = $data['report_type_pop_btn_one_icon'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['report_type_pop_btn_one_icon']);
        }
        if (!empty($data['report_type_pop_btn_two_icon'])) {
            $icon = $data['report_type_pop_btn_two_icon'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['report_type_pop_btn_two_icon']);
        }
        if (!empty($data['add_property_cover_file'])) {
            $icon = $data['add_property_cover_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['add_property_cover_file']);
        }
        if (!empty($data['referre_report_file'])) {
            $icon = $data['referre_report_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['referre_report_file']);
        }
        if (!empty($data['report_status_file'])) {
            $icon = $data['report_status_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['report_status_file']);
        }
        if (!empty($data['prizes_file'])) {
            $icon = $data['prizes_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['prizes_file']);
        }
        if (!empty($data['reminders_file'])) {
            $icon = $data['reminders_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['reminders_file']);
        }
         if (!empty($data['enroll_url_cover_file'])) {
            $icon = $data['enroll_url_cover_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
            unset($data['enroll_url_cover_file']);
        }else {
          unset($data['enroll_url_cover_file']);
        }
        if (!empty($data['qlf_cover_file'])) {
            $icon = $data['qlf_cover_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
            unset($data['qlf_cover_file']);
        }else {
          unset($data['qlf_cover_file']);
        }
            if (!empty($data['qlf_level_two_cover'])) {
            $icon = $data['qlf_level_two_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
            unset($data['qlf_level_two_cover']);
        }else {
          unset($data['qlf_level_two_cover']);
        }
        

        if (!empty($data['qlf_level_one_cover'])) {
            $icon = $data['qlf_level_one_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
             unset($data['qlf_level_one_cover']);
        }else {
          unset($data['qlf_level_one_cover']);
        }
        if (!empty($data['qlf_level_one_btn_one_cover'])) {
            $icon = $data['qlf_level_one_btn_one_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
            unset($data['qlf_level_one_btn_one_cover']);
        }else {
          unset($data['qlf_level_one_btn_one_cover']);
        }
        if (!empty($data['qlf_level_two_btn_one_cover'])) {
            $icon = $data['qlf_level_two_btn_one_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
             unset($data['qlf_level_two_btn_one_cover']);
        }else {
          unset($data['qlf_level_two_btn_one_cover']);
        }
        if (!empty($data['qlf_level_one_btn_two_cover'])) {
            $icon = $data['qlf_level_one_btn_two_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
             unset($data['qlf_level_one_btn_two_cover']);
        }else {
          unset($data['qlf_level_one_btn_two_cover']);
        }
        if (!empty($data['qlf_level_one_btn_one_cover'])) {
            $icon = $data['qlf_level_one_btn_one_cover'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
             unset($data['qlf_level_one_btn_one_cover']);
        }else {
          unset($data['qlf_level_one_btn_one_cover']);
        }
        if (!empty($data['phonebooks_file'])) {
            $icon = $data['phonebooks_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['phonebooks_file']);
        }
        if (!empty($data['statistics_file'])) {
            $icon = $data['statistics_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['statistics_file']);
        }
        if (!empty($data['settings_file'])) {
            $icon = $data['settings_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['settings_file']);
        }
        unset($data['report_type_pop_btn_two_icon']);
        unset($data['report_type_pop_btn_one_icon']);
        unset($data['report_type_pop_cover']);
        $data['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migarefrence_app_content", $data,['app_id = ?' => $app_id]);
        return $id;
      }
      public function updateNotificationevent($datas=[])
      {
        $id=$datas['migareference_notification_event_id'];
        unset($datas['migareference_notification_event_id']);
        $datas['updated_at']  = date('Y-m-d H:i:s');
        $this->_db->update("migareference_notification_event", $datas,['migareference_notification_event_id = ?' => $id]);
        return $id;
      }
      public function updateStatus($datas=[])
      {
        $id=$datas['migareference_report_status_id'];
        $data['status_title'] = $datas['status_title'];
        $data['status_icon']  = $datas['c_migareference_status_icon_cover_file'];
        $data['is_comment']   = $datas['is_comment'];
        $data['is_acquired']  = $datas['is_acquired'];
        $data['is_acquired']  = $datas['is_acquired'];
        $data['is_declined']  = $datas['is_auto_declined'];
        $data['is_pause_sending']  = $datas['is_pause_sending'];
        $data['auto_fallabck_comment']= $datas['auto_fallabck_comment'];
        $data['declined_grace_days']  = $datas['declined_grace'];
        $data['declined_to']  = $datas['declined_to_status'];
        $data['is_reminder']  = $datas['is_auto_reminder'];
        $data['reminder_grace_days']  = $datas['reminder_grace'];
        $data['updated_at']   = date('Y-m-d H:i:s');
        $this->_db->update("migareference_report_status", $data,['migareference_report_status_id = ?' => $id]);
        return $id;
      }
      public function saveNotificationevent($datas=[])
      {
        $datas['created_at']  = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_notification_event", $datas);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function getLastvisit($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migareference_activity_logs` WHERE `app_id`=$app_id AND `user_id`=$user_id AND `log_type`='Login' ORDER By `created_at` DESC LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getLastCallActivity($phonebook_id=0)
      {
        $query_option = "SELECT migcom.migareference_communication_logs_id,migcom.log_type,migcom.created_at
                        FROM migareference_communication_logs AS migcom                        
                        WHERE migcom.phonebook_id=$phonebook_id AND (migcom.log_type='Enrollment' OR migcom.log_type='Manual')                        
                        ORDER BY migcom.migareference_communication_logs_id DESC LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getLastEngagemnetActivity($invoice_id=0)
      {
        $query_option = "SELECT migainv.migareference_invoice_settings_id,migph.migarefrence_phonebook_id,
                                migph.mobile,migcom.migareference_communication_logs_id,migcom.log_type,migcom.created_at
                        FROM migareference_invoice_settings as migainv
                        JOIN migarefrence_phonebook AS migph ON migph.invoice_id=migainv.migareference_invoice_settings_id
                        JOIN migareference_communication_logs AS migcom ON migcom.phonebook_id=migph.migarefrence_phonebook_id
                                                                 AND migcom.log_type='Engagement'
                        WHERE migainv.migareference_invoice_settings_id=$invoice_id
                        ORDER BY migcom.migareference_communication_logs_id DESC LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getLastReportActivity($report_id=0,$log_type='')
      {
        $query_option = "SELECT *  FROM `migareference_activity_logs`
        WHERE `report_id` = $report_id AND `log_type`='$log_type' ORDER BY `created_at` DESC LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      
      public function validateProspectMobile($app_id=0,$grace_date="",$owner_mobile="",$referrer_id=0) 
      {
        $query_option="SELECT migareference_report.owner_mobile,migareference_report.migareference_report_id
        FROM `migareference_report`
        JOIN migareference_report_status ON
        migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
        AND migareference_report_status.is_standard=1
        AND migareference_report_status.standard_type!=5
        AND migareference_report_status.standard_type!=4        
        WHERE migareference_report.`app_id` = 1 
        AND date(migareference_report.created_at)>='$grace_date' 
        AND migareference_report.owner_mobile='$owner_mobile'";
        $res_option   = $this->_db->fetchAll($query_option);
        // External CMS Mobile Verification is missing
        
        $response=false;
        if (COUNT($res_option)) {
          $response=true;
        }
        return $response;
      }
      public function isMobileunique($app_id=0,$date="",$mobile="") //Deprecated 10/11/2023 new is validateProspectMobile
      {
        $query_option = "SELECT *
        FROM `migareference_report`
        JOIN migarefrence_phonebook ON
        migarefrence_phonebook.mobile='$mobile'
        AND migarefrence_phonebook.type=2
        AND migarefrence_phonebook.report_id=migareference_report.migareference_report_id
        AND migarefrence_phonebook.is_exclude=1
        AND migarefrence_phonebook.app_id=$app_id
        AND migarefrence_phonebook.`created_at` >= '$date'
        JOIN migareference_report_status ON
        migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
        AND migareference_report_status.is_standard=1
        AND migareference_report_status.standard_type!=5
        AND migareference_report_status.standard_type!=4
        WHERE migareference_report.`app_id` = $app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isBlackList($app_id=0,$mobile="")
      {
        $query_option = "SELECT *
        FROM `migarefrence_phonebook`
        WHERE migarefrence_phonebook.mobile='$mobile'
        AND migarefrence_phonebook.type=2
        AND migarefrence_phonebook.is_blacklist=2
        AND migarefrence_phonebook.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isAddressunique($app_id=0,$data=[])
      {
        $address=$data['address'];
        $longitude=$data['longitude'];
        $latitude=$data['latitude'];
        $query_option = "SELECT * FROM `migareference_report` WHERE `app_id`=$app_id  AND `longitude`='$longitude' AND `latitude`='$latitude'";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isinternalAddressunique($app_id=0,$data=[],$date="")
      {
        $address=$data['address'];
        $longitude=$data['longitude'];
        $latitude=$data['latitude'];
        $query_option = "SELECT * FROM `migareference_report` WHERE `app_id`=$app_id   AND `longitude`='$longitude' AND `latitude`='$latitude' AND `created_at`>='$date'";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isexternalAddressunique($app_id=0,$data=[])
      {
        $address=$data['address'];
        $longitude=$data['longitude'];
        $latitude=$data['latitude'];
        $query_option = "SELECT * FROM `migarefrence_property_addresses` WHERE  `longitude`='$longitude' AND `latitude`='$latitude'";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getEventNotificationTemplats($app_id=0,$event_id=0)
      {
        $query_option = "SELECT *
                         FROM migareference_notification_event
                         JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_notification_event.event_id
                         JOIN migareference_email_template ON migareference_email_template.event_id=migareference_notification_event.event_id
                         LEFT JOIN migareference_pre_report_settings ON migareference_pre_report_settings.app_id=migareference_report_status.app_id
                         JOIN migareference_push_template ON migareference_push_template.event_id=migareference_notification_event.event_id
                         LEFT JOIN migareference_sms_template ON migareference_sms_template.event_id=migareference_notification_event.event_id
                         WHERE migareference_notification_event.app_id=$app_id AND migareference_notification_event.event_id=$event_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAdminCustomers($app_id=0)
      {
        $query_option = "SELECT customer.customer_id,customer.firstname,customer.lastname,customer.email
                         FROM migareference_app_admins
                         JOIN customer ON customer.customer_id=migareference_app_admins.user_id
                         WHERE migareference_app_admins.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAgentdata($agent_user_id=0)
      {
        $query_option = "SELECT *
                         FROM customer
                         WHERE customer_id=$agent_user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getrefreluserName($app_id=0,$refferal_user_id=0)
      {
        $query_option = "SELECT customer.customer_id,customer.firstname,customer.lastname,customer.email
                         FROM customer
                         WHERE customer.app_id=$app_id AND customer.customer_id=$refferal_user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getRefferalCustomers($app_id=0,$refferal_user_id=0)
      {
        $query_option = "SELECT application.*,customer.customer_id,customer.firstname,customer.lastname,customer.email,migareference_invoice_settings.invoice_name,migareference_invoice_settings.invoice_surname,migareference_invoice_settings.leave_date,migareference_invoice_settings.created_at as terms_accepted,customer.created_at,migareference_invoice_settings.birth_date
                         FROM customer
                         JOIN application ON application.app_id=$app_id
                         JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=$refferal_user_id AND migareference_invoice_settings.app_id=$app_id
                         WHERE customer.customer_id=migareference_invoice_settings.user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportlog($app_id=0,$report_id=0)
      {
        $query_option = "SELECT migareference_activity_logs.created_at,
                                migareference_activity_logs.app_id,
                                migareference_activity_logs.report_id,
                                migareference_activity_logs.log_type,
                                migareference_activity_logs.log_detail,
                                migareference_activity_logs.log_source,
                                migareference_activity_logs.user_type,
                                customer.firstname as cutomerfirstname,
                                customer.lastname as cutomerlastname,
                                admin.firstname adminfirstname,
                                admin.lastname adminlastname,
                                migareference_activity_logs.user_id,
                                migareference_activity_logs.migareference_activity_log_id
                         FROM `migareference_activity_logs`
                         LEFT JOIN customer ON (customer.customer_id=migareference_activity_logs.user_id AND migareference_activity_logs.user_type=1) OR (migareference_activity_logs.user_id=99999 AND migareference_activity_logs.user_type=1)
                         LEFT JOIN admin ON (admin.admin_id=migareference_activity_logs.user_id AND migareference_activity_logs.user_type=2) OR (migareference_activity_logs.user_id=99999 AND migareference_activity_logs.user_type=2)
                         WHERE migareference_activity_logs.`app_id`=$app_id AND migareference_activity_logs.`report_id`=$report_id 
                         GROUP BY migareference_activity_logs.migareference_activity_log_id
                         ORDER By migareference_activity_logs.`created_at` ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getPushlog($app_id=0)
      {        
        $data_set=[];
        $currentVersion = Siberian_Version::VERSION;          
        $minimumSupportedVersion = '5.0.0';
        if (version_compare($currentVersion, $minimumSupportedVersion, '<')) {                    
        $query_option = "SELECT mpl.push_message_id,pm.title,pm.text,cs.email,pm.status,pm.created_at
                         FROM migareference_push_log AS mpl
                         JOIN push_messages as pm ON pm.message_id=mpl.push_message_id
                         JOIN push_customer_message as pcm ON pcm.message_id=mpl.push_message_id
                         JOIN customer as cs ON cs.customer_id=pcm.customer_id
                         WHERE mpl.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);        
        foreach ($res_option as $key => $value) {
          $data_set[]=[
            'push_message_id'=>$value['push_message_id'],
            'title'=>__(trim(base64_decode($value['title']))),
            'text'=>__(base64_decode($value['text'])),
            'email'=>$value['email'] ,
            'status'=>__($value['status']) ,
            'date'=>date('Y-m-d', strtotime($value['created_at']))
          ];
        }
      }
        return $data_set;
      }
      public function getEmaillog($app_id=0)
      {
        $query_option = "SELECT mel.migareference_email_log_id,mel.email_title,mel.email_text,mel.created_at,cs.email
                         FROM migareference_email_log AS mel
                         JOIN customer as cs ON cs.customer_id=mel.email_customer_id
                         WHERE mel.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function is_admin($app_id,$user_id)
      {
        $query_option = "SELECT * FROM `migareference_app_admins` WHERE `app_id`=$app_id AND `user_id`=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function is_agent($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migareference_app_agents` WHERE `app_id`=$app_id AND `user_id`=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }

    public function findAgentByEmail($app_id = 0, $email = '')
    {
        $app_id = (int) $app_id;
        $email  = trim((string) $email);

        if (!$app_id || $email === '') {
            return []; // match is_agent() style
        }

        $query_option = "
            SELECT DISTINCT
                agents.user_id,
                agents.agent_type,
                customer.firstname,
                customer.lastname,
                customer.email
            FROM `migareference_app_agents` AS agents
            INNER JOIN `customer`
                ON `customer`.`customer_id` = `agents`.`user_id`
               AND `customer`.`app_id` = {$app_id}
            WHERE `agents`.`app_id` = {$app_id}
              AND `customer`.`email` = " . $this->_db->quote($email) . "
            ORDER BY customer.lastname, customer.firstname";

        $res_option   = $this->_db->fetchAll($query_option);

        return $res_option; // [] or [0 => row]
    }


      public function getagentProvinces($app_id=0,$country_id=0,$province_id_list=[])
      {
        $ids = join("','",$province_id_list);
        $query_option = "SELECT * FROM `migareference_geo_provinces` WHERE `app_id`=$app_id AND `country_id`=$country_id AND `migareference_geo_provinces_id` IN ($ids)";
        $res_option   = $this->_db->fetchAll($query_option);
        // $res[]=[
        //   $app_id,
        //   $country_id,
        //   $province_id_list
        // ];
        return $res_option;
      }
      public function get_migration_log($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_migartion_log` WHERE `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAdmins($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_app_admins`
                         JOIN customer ON customer.customer_id=migareference_app_admins.user_id
                         WHERE migareference_app_admins.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getContactsUsers($app_id=0)
      {
        $query_option = "SELECT cs.*,ad.migareference_app_admins_id,ag.migareference_app_agents_id,inv.migareference_invoice_settings_id
                        FROM customer AS cs
                        LEFT JOIN migareference_app_admins AS ad ON ad.user_id=cs.customer_id
                        LEFT JOIN migareference_app_agents AS ag ON ag.user_id=cs.customer_id
                        LEFT JOIN migareference_invoice_settings AS inv ON inv.user_id=cs.customer_id
                        WHERE cs.app_id=$app_id AND ad.migareference_app_admins_id IS NULL
                        AND ag.migareference_app_agents_id IS NULL AND inv.migareference_invoice_settings_id IS NULL";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }        
      public function get_all_agents($app_id=0) //deprecatd 01-23-2024 new method is in Utilities
      {
        $query_option = "SELECT *,COUNT(migareference_report.migareference_report_id) as total_reports,migareference_app_agents.user_id as user_id 
                         FROM `migareference_app_agents`
                         JOIN customer ON customer.customer_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.sponsor_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id 
                         AND migareference_report.status=1
                         WHERE migareference_app_agents.app_id=$app_id
                         GROUP BY customer.customer_id
                         ORDER BY customer.lastname";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_customer_agents($app_id=0)
      {
        $query_option = "SELECT *,COUNT(migareference_report.migareference_report_id) as total_reports,migareference_app_agents.user_id as user_id 
                         FROM `migareference_app_agents`
                         JOIN customer ON customer.customer_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.sponsor_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id 
                         AND migareference_report.status=1
                         WHERE migareference_app_agents.app_id=$app_id AND migareference_app_agents.agent_type=1
                         GROUP BY customer.customer_id
                         ORDER BY customer.lastname";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_partner_agents($app_id=0)
      {
        $query_option = "SELECT *,COUNT(migareference_report.migareference_report_id) as total_reports,migareference_app_agents.user_id as user_id 
                         FROM `migareference_app_agents`
                         JOIN customer ON customer.customer_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.sponsor_id=migareference_app_agents.user_id
                         LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id 
                         AND migareference_report.status=1
                         WHERE migareference_app_agents.app_id=$app_id AND migareference_app_agents.agent_type=2
                         GROUP BY customer.customer_id
                         ORDER BY customer.lastname";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function count_agent_reports($app_id=0,$agent_key=0)
      {
        $query_option = "SELECT *,COUNT(migareference_report.migareference_report_id) as total_reports 
                         FROM `migareference_invoice_settings`
                         
                         LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id 
                         AND migareference_report.status=1
                         WHERE migareference_invoice_settings.app_id=$app_id AND migareference_invoice_settings.sponsor_id=0";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }    
      public function get_socialshares($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_app_socialshares`
                         JOIN customer ON customer.customer_id=migareference_app_socialshares.user_id
                         WHERE migareference_app_socialshares.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSocialsharesUser($user_id=0)
      {
        $query_option = "SELECT * FROM `migareference_app_socialshares`
                         JOIN customer ON customer.customer_id=migareference_app_socialshares.user_id
                         WHERE migareference_app_socialshares.user_id=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function preReportsettigns($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_pre_report_settings` WHERE `app_id`=$app_id ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function savePreReport($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_pre_report_settings", $data);
        return 1;
      }
      public function updatePreReport($data=[])
      {
        $id=$data['migareference_pre_report_settings_id'];
        $data['updated_at']    = date('Y-m-d H:i:s');
        $this->_db->update("migareference_pre_report_settings", $data,['migareference_pre_report_settings_id = ?' => $id]);
        return $data;
      }
      public function saveGdprSetings($data=[])
      {
        $this->uploadApplicationFile($data['app_id'],$data['landing_page_header_file'],0);
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_gdpr_settings", $data);
      }
      public function updateStatusbyKey($data=[],$key=0)
      {
        $data['updated_at']    = date('Y-m-d H:i:s');
        $this->_db->update("migareference_report_status", $data,['migareference_report_status_id = ?' => $key]);
        return $key;
      }
      public function updateReportfieldbyKey($data=[],$key=0)
      {
        $data['updated_at']    = date('Y-m-d H:i:s');
        $this->_db->update("migareference_report_fields", $data,['migareference_report_fields_id = ?' => $key]);
        return $key;
      }
      public function saveEarning($data=[])
      {
        $app_id=$data['app_id'];
        $report_no=$data['report_id'];
        $query_option = "SELECT  *  FROM `migareference_user_earnings` WHERE `app_id` = $app_id AND `report_id`= $report_no";
        $res_option   = $this->_db->fetchAll($query_option);
        if (!count($res_option)) {
          $data['created_at']    = date('Y-m-d H:i:s');
          $this->_db->insert("migareference_user_earnings", $data);
        }
        return 1;
      }
      public function countNotifications($app_id=0,$datetime="",$user_id=0)
      {
        $query_option = "SELECT COUNT(app_id) as total  FROM `migareference_report` WHERE `app_id` = $app_id AND `last_modification_at` >= '$datetime' AND `user_id`=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function havereport($app_id=0,$user_id=0)
      {
        $query_option = "SELECT *  FROM `migareference_report` WHERE `app_id` = $app_id AND `user_id`=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function statusreports($app_id=0,$status_id=0)
      {
        $query_option = "SELECT *  FROM `migareference_report` WHERE `app_id` = $app_id AND `currunt_report_status`=$status_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      //START: HOW TO WORK
      public function addhowto($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_how_to", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function saveAdmin($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_app_admins", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function saveAgent($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_app_agents", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function updateAgent($data=[],$app_id=0,$customer_id=0)
      {
        return $this->_db->update("migareference_app_agents", $data,['app_id = ?' => $app_id,'user_id = ?'=>$customer_id]);
      }
      public function updateSponsor($data=[],$customer_id=0)
      {
        return $this->_db->update("migareference_referrer_agents", $data,['referrer_id = ?'=>$customer_id]);
      }
      public function saveSocialshare($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_app_socialshares", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function saveMigrationlog($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_migartion_log", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function deleteCustomer($user_id=0)
      {
        $this->_db->delete('customer',['customer_id = ?' => $user_id]);
      }
      public function deleteAdmin($app_id=0,$user_id=0)
      {
        $this->_db->delete('migareference_app_admins',['user_id = ?' => $user_id,'app_id = ?'=>$app_id]);
      }
      public function deleteAgnetProvince($app_id=0,$user_id=0,$country_id=0)
      {
        $this->_db->delete('migareference_agent_provinces',['user_id = ?' => $user_id,'app_id = ?'=>$app_id,'country_id = ?'=>$country_id]);
      }
      public function getAllocatedProvinces($app_id=0,$country_id=0,$province_id=0)
      {
        $query_option = "SELECT * 
        FROM migareference_agent_provinces AS pr
        JOIN migareference_app_agents AS ag ON ag.user_id=pr.user_id
        WHERE pr.app_id = $app_id AND pr.country_id=$country_id AND pr.province_id=$province_id
        GROUP BY pr.migareference_agent_provinces_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function agentProvinces($app_id=0,$user_id=0)
      {
        $query_option = "SELECT *,migareference_agent_provinces.province_id as province_id FROM migareference_agent_provinces
                        JOIN migareference_geo_provinces ON migareference_geo_provinces.migareference_geo_provinces_id=migareference_agent_provinces.province_id
                        WHERE migareference_agent_provinces.app_id = $app_id AND migareference_agent_provinces.user_id=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function agentAppProvinces($app_id=0)
      {
        $query_option = "SELECT * FROM migareference_agent_provinces
                        JOIN migareference_geo_provinces ON migareference_geo_provinces.migareference_geo_provinces_id=migareference_agent_provinces.province_id
                        WHERE migareference_agent_provinces.app_id = $app_id GROUP BY migareference_agent_provinces.province_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function referrerAppProvinces($app_id=0)
      {
        $query_option = "SELECT prov.migareference_geo_provinces_id,prov.province,prov.province_code
        FROM migareference_invoice_settings AS mis
        JOIN migareference_geo_provinces AS prov ON  prov.migareference_geo_provinces_id=mis.address_province_id
        WHERE mis.app_id=$app_id
        GROUP BY prov.migareference_geo_provinces_id 
        ORDER BY prov.province;";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadeProvinceReferrers($app_id=0,$province_id=0,$agent_id=0)
      {
        $query_option = "SELECT *
        FROM `migareference_invoice_settings` AS mis
        WHERE mis.app_id=$app_id AND mis.address_province_id=$province_id
        ORDER BY mis.invoice_surname";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function agentGeoProvince($app_id=0,$province_id=0)
      {
        $query_option = "SELECT * FROM migareference_agent_provinces
                        JOIN migareference_geo_provinces ON migareference_geo_provinces.migareference_geo_provinces_id=migareference_agent_provinces.province_id
                        WHERE migareference_agent_provinces.app_id = $app_id AND migareference_agent_provinces.province_id = $province_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function agentMultiGeoProvince($app_id=0,$province_id=0)
      {
        $query_option = "SELECT * FROM migareference_agent_provinces
        JOIN migareference_geo_provinces ON migareference_geo_provinces.migareference_geo_provinces_id=migareference_agent_provinces.province_id
        JOIN migareference_app_agents ON migareference_app_agents.user_id=migareference_agent_provinces.user_id
        WHERE migareference_agent_provinces.app_id = $app_id AND migareference_agent_provinces.province_id = $province_id 
        GROUP BY migareference_agent_provinces.migareference_agent_provinces_id ORDER BY migareference_app_agents.agent_type ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function deleteGeo($country_id,$province_id)
      {
        $this->_db->delete('migareference_geo_provinces',['country_id = ?' => $country_id,'migareference_geo_provinces_id = ?'=>$province_id]);
      }
      public function deleteSocialshare($app_id=0,$user_id=0)
      {
        $this->_db->delete('migareference_app_socialshares',['user_id = ?' => $user_id,'app_id = ?'=>$app_id]);
      }
      public function deleteReport($id=0)
      {
        $this->_db->delete('migareference_report',['migareference_report_id= ?' => $id]);
      }
      public function deleteMigrationlog($app_id=0)
      {
        $this->_db->delete('migareference_migartion_log',['app_id = ?'=>$app_id]);
      }
      public function deleteAgent($app_id=0,$user_id=0)
      {
        $this->_db->delete('migareference_app_agents',['user_id = ?' => $user_id,'app_id = ?'=>$app_id]);
      }
      public function deleteAgentProvinces($app_id=0,$user_id=0)
      {
        $this->_db->delete('migareference_agent_provinces',['user_id = ?' => $user_id,'app_id = ?'=>$app_id]);
      }
      public function gethowto($app_id=0)
      {
        $query_option = "SELECT * FROM migareference_how_to WHERE app_id = $app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function updatehowto($data=[])
      {
        $app_id             = $data['app_id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migareference_how_to", $data,['app_id = ?' => $app_id]);
        return $res;
      }
      public function updateMigrationlog($data=[])
      {
        $app_id             = $data['app_id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migareference_migartion_log", $data,['app_id = ?' => $app_id]);
        return $res;
      }
      public function checkDuplication($data=[],$id=0)
      {
        $app_id=$data['app_id'];
        $user_id=$data['user_id'];
        $query_option = "SELECT * FROM migareference_invoice_settings WHERE `app_id` = $app_id AND `user_id`=$user_id";
        return $this->_db->fetchAll($query_option);        
      }   
      //Temp to sync previous dob to Account feature
      public function syncDob($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_invoice_settings` WHERE `birth_date`!='' AND `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        foreach ($res_option as $key => $value) {
          if ($value['birth_date']!='') {
            $diff = strtotime($value['birth_date']);
            $id=$value['user_id'];
            $data['birthdate']=$diff;
            $this->_db->update("customer", $data,['customer_id = ?' => $id]);
          }
        }
        return $res_option;
      }
      public function savePropertysettings($data=[])
      {
        try {
        // Invoice or Referrer Entry
        $app_id=$data['app_id'];
        $data['created_at']    = date('Y-m-d H:i:s');        
        $job_id = $data['job_id'] ?? 0;
        $rating = $data['rating'] ?? 1;
        $note = $data['note'] ?? "";
        $reciprocity_notes = $data['reciprocity_notes'] ?? "";
        $profession_id = $data['profession_id'] ?? 0;
        $referrer_agent['app_id']=$app_id;
        $referrer_agent['referrer_id']=$data['user_id'];
        $referrer_agent['created_at']=date('Y-m-d H:i:s');        
        // Add Multi Agents
        $referrer_agent['agent_id']= (isset($data['sponsor_id'])) ? $data['sponsor_id'] : 0 ;
        $type_one=$referrer_agent['agent_id'];
        if ($referrer_agent['agent_id']!=0) {
          $this->addSponsor($referrer_agent);
        }                
        $referrer_agent['agent_id']= (isset($data['partner_sponsor_id'])) ? $data['partner_sponsor_id'] : 0 ;
        $type_two=$referrer_agent['agent_id'];
        if ($referrer_agent['agent_id']!=0) {
          $this->addSponsor($referrer_agent);
        }
        unset($data['job_id']);
        unset($data['note']);
        unset($data['rating']);
        unset($data['reciprocity_notes']);
        unset($data['profession_id']);
        unset($data['sponsor_id']);
        unset($data['partner_sponsor_id']);
        // End Multi Agnets
        if (!preg_match('/^00|\+/', $data['invoice_mobile'])) {              
              $data['invoice_mobile'] = '+39' . $data['invoice_mobile'];
        }        
        if (is_null($data['address_province_id']) || !isset($data['address_province_id'])) {
          $data['address_province_id'] = 0;                
        }
        if (is_null($data['address_country_id']) || !isset($data['address_country_id'])) {
          $data['address_country_id'] = 0;                
        }
        $customer_data=$this->getSingleuser($data['app_id'],$data['user_id']);
        $data['ref_consent_ip']       = '';
        $data['ref_consent_timestmp'] = '';
        if ($data['terms_accepted']) {
             $data['ref_consent_source'] = 'App User';
             $data['ref_consent_timestmp'] = $customer_data[0]['created_at'];
        }
        $this->_db->insert("migareference_invoice_settings", $data);
        $id   = $this->_db->lastInsertId();
        // PHONEBOOK Entry
        $exist=$this->isPhoneEmailExist($data['app_id'],'',$data['invoice_mobile'],1);        
        if (!count($exist) || $data['invoice_mobile']==NULL) {
          $phonebook['app_id']      = $data['app_id'];
          $phonebook['name']        = $data['invoice_name'];
          $phonebook['surname']     = $data['invoice_surname'];
          $phonebook['mobile']      = $data['invoice_mobile'];
          $phonebook['note']        = $note;
          $phonebook['rating']      = $rating;
          $phonebook['reciprocity_notes']= $reciprocity_notes;
          $phonebook['email']       = $customer_data[0]['email'];
          $phonebook['invoice_id']  = $id;
          $phonebook['job_id']      = $job_id;
          $phonebook['profession_id']= $profession_id;
          $phonebook['user_id']     = $data['user_id'];
          $phonebook['type']        = 1;
          $phonebook['created_at']  = date('Y-m-d H:i:s');
          $this->savePhoneBook($phonebook);
        }else{
          $phonebook['app_id']      = $data['app_id'];
          $phonebook['name']        = $data['invoice_name'];
          $phonebook['surname']     = $data['invoice_surname'];
          $phonebook['mobile']      = $data['invoice_mobile'];
          $phonebook['note']        = $note;
          $phonebook['rating']      = $rating;
          $phonebook['reciprocity_notes']= $reciprocity_notes;
          $phonebook['email']       = $customer_data[0]['email'];
          $phonebook['invoice_id']  = $id;
          $phonebook['job_id']      = $job_id;
          $phonebook['profession_id']= $profession_id;
          $phonebook['user_id']     = $data['user_id'];
          $this->updatePhoneBook($phonebook,$exist[0]['migarefrence_phonebook_id']);
        }
        // Trigger webhook at new referrer register if enabled
        $pre_settings   = $this->preReportsettigns($app_id);                        
        if ($pre_settings[0]['enable_new_ref_webhooks']==1 && $pre_settings[0]['enable_new_ref_webhooks_create']==1) {                                    
                  $webhook    = new Migareference_Model_Db_Table_Webhook();
                  $webhook_url = $webhook->referrerWebhookParamsTemplate($app_id,$data['user_id'],'create');
                  $webhook_log_params['app_id']          = $app_id;                  
                  $webhook_log_params['user_id']         = $data['user_id'];    
                  $webhook_log_params['type']            = 'referrer';              
                  $this->triggerWebhook($webhook_url,$webhook_log_params);//*New Referrer
        }
        } catch (\Throwable $th) {
          return $th->getMessage();
        }
        return $data;
      }
      public function updatePropertysettings($data=[],$id=0)
      {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->_db->update("migareference_invoice_settings", $data,['migareference_invoice_settings_id = ?' => $id]);        
      }
      public function updateCustomerdob($user_id=0,$data=[])
      {
        return $this->_db->update("customer", $data,['customer_id  = ?' => $user_id]);
      }
      public function getpropertysettings($app_id=0,$user_id=0)
      {
        $query_option = "SELECT 
        migareference_invoice_settings.*,
        migarefrence_phonebook.job_id,
        migarefrence_phonebook.profession_id,
        customer.birthdate,
        customer.mobile,
        migarefrence_phonebook.migarefrence_phonebook_id,
        migarefrence_phonebook.rating,
        customer.firstname,
        customer.lastname,
        customer.email,
        sponsor_one.customer_id AS sponsor_one_id,
        sponsor_two.customer_id AS sponsor_two_id,
        sponsor_one.firstname AS sponsor_one_firstname,
        sponsor_one.lastname AS sponsor_one_lastname,
        sponsor_one.email AS sponsor_one_email,
        sponsor_one.mobile AS sponsor_one_mobile,
        sponsor_two.firstname AS sponsor_two_firstname,
        sponsor_two.lastname AS sponsor_two_lastname,
        sponsor_two.email AS sponsor_two_email
        FROM migareference_invoice_settings
        JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
        LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=migareference_invoice_settings.user_id
        LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=migareference_invoice_settings.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
        LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
        LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
        LEFT JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
        WHERE migareference_invoice_settings.app_id = $app_id  AND migareference_invoice_settings.user_id=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_one_standard_status($app_id=0,$standard_index=0)
      {
        $query_option = "SELECT * FROM `migareference_report_status` WHERE `app_id` = $app_id AND `is_standard` = 1 AND `standard_type` = $standard_index";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function is_already_declined($app_id=0,$standard_index=0)
      {
        $query_option = "SELECT * FROM `migareference_report_status` WHERE `app_id` = $app_id AND `is_standard` = 1 AND `standard_type` = $standard_index";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function reportStatusByKey($pkid=0,$report_id=0)
      {
        $query_option = "SELECT *
                         FROM `migareference_report_status`
                         LEFT JOIN migareference_status_comment ON migareference_status_comment.status_id=migareference_report_status.migareference_report_status_id AND migareference_status_comment.report_id=$report_id
                         WHERE migareference_report_status.migareference_report_status_id=$pkid";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAllProspect($app_id=0)
      {
        $query_option = "SELECT *,migarefrence_prospect.created_at AS prospect_created_at
        FROM `migarefrence_prospect` 
        JOIN migareference_report on migarefrence_prospect.migarefrence_prospect_id=migareference_report.prospect_id
        JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=migareference_report.user_id
        LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migarefrence_prospect.job_id
        WHERE migarefrence_prospect.`app_id`=$app_id
        AND migarefrence_prospect.`name` NOT LIKE '%*%'
        AND migarefrence_prospect.`name` NOT LIKE '*%'
        GROUP BY migarefrence_prospect.migarefrence_prospect_id";
        return  $this->_db->fetchAll($query_option);        
      }
      public function getProspectItem($app_id=0,$prospect_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prospect`         
        WHERE `app_id`=$app_id AND migarefrence_prospect_id=$prospect_id";
        return  $this->_db->fetchAll($query_option);        
      }
      // END: Property Settings
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
      // START: Property Report
      public function savepropertyreport($data=[])
      {
        $prospect_found=$this->isProspectExist($data['app_id'],$data['owner_mobile']);

        if (COUNT($prospect_found)) {
            $prospect_id=$prospect_found[0]['migarefrence_prospect_id'];
        }else {
            // Add data to prospect table       
            $password             = $this->randomPassword(10);     

            $prospect['app_id']   = $data['app_id'];
            $prospect['name']     = $data['owner_name'];
            $prospect['surname']  = $data['owner_surname'];
            $prospect['mobile']   = $data['owner_mobile'];                
            $prospect['rating']   = 1;
            $prospect['password'] = $password;            

            $prospect_id=$this->addProspect($prospect);
        }                

        $log_item=[
              'app_id'       => $data['app_id'],
              'phonebook_id' => $prospect_id,
              'user_id'      => $data['user_id'],
              'log_type'     => "Enrollment"
          ];
        $this->saveCommunicationLog($log_item);        

        $data['prospect_id'] = $prospect_id;
        $data['created_at']  = date('Y-m-d H:i:s');
        $data['updated_at']  = date('Y-m-d H:i:s');

        $this->_db->insert("migareference_report", $data);
        $report_id = $this->_db->lastInsertId();
        // Call webhook if source is not API         
        $webhook    = new Migareference_Model_Db_Table_Webhook();
        if ($data['report_source']!=4) {          
          $webhook->triggerReportWebhook($data['app_id'],$report_id,'NewReport','create'); //*New Report
        }
        return $report_id;

        // Dpreated 05/01/2023
        // $phonebook['app_id']     = $data['app_id'];
        // $phonebook['name']       = $data['owner_name'];
        // $phonebook['surname']    = $data['owner_surname'];
        // $phonebook['mobile']     = $data['owner_mobile'];
        // $phonebook['type']       = 2;
        // $phonebook['report_id']  = $id;
        // $phonebook['created_at'] = date('Y-m-d H:i:s');
        // $exist=$this->isPhoneEmailExist($data['app_id'],'',$data['owner_mobile'],2);
        // if (count($exist)) {
        //   $phonebook['report_id']  = $id;
        //   $phonebook['created_at'] = date('Y-m-d H:i:s');
        //   $this->update_phonebook($phonebook,$exist[0]['migarefrence_phonebook_id'],9999,0);//Also save log if their is change in Rating,Job,Notes
        // }else {
        //   $phonebook['report_id']  = $data['user_id'];
        //   $this->savePhoneBook($phonebook);
        // }
      }
      public function savePushlog($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_push_log", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function addProspect($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_prospect", $data);                
        $prospect_id=$this->_db->lastInsertId();
        return $prospect_id;
      }
      public function saveAgnetProvince($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_agent_provinces", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function uploadApplicationFile($app_id=0,$file='',$remove=0)
      {
        if (!empty($file) && !intval($remove)) {
            $icon = $file;
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    return $icon;
                }
            } else {
                return $icon;
            }
        }
      }
      public function savePrizenotificationvePush($data=[])
      {
        $agt_file=$this->uploadApplicationFile($data['app_id'],$data['agt_prz_custom_file'],$data['remove_agt_prz_custom_cover_img_field']);
        $ref_file=$this->uploadApplicationFile($data['app_id'],$data['ref_prz_custom_file'],$data['remove_ref_prz_custom_cover_img_field']);
        unset($data['remove_ref_prz_custom_cover_img_field']);
        unset($data['remove_agt_prz_custom_cover_img_field']);
        unset($data['migarefrence_prizes_notification_id']);
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_prizes_notification", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function saveCreditsApiNotification($data=[])
      {
        $ref_file=$this->uploadApplicationFile($data['app_id'],$data['ref_credits_api_custom_file'],$data['remove_ref_credits_api_custom_cover_img_field']);
        unset($data['remove_ref_credits_api_custom_cover_img_field']);
        unset($data['migarefrence_credits_notification_id']);
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_credits_notification", $data);
        return $this->_db->lastInsertId();
      }
      public function updateCreditsApiNotification($data=[])
      {
        $ref_file=$this->uploadApplicationFile($data['app_id'],$data['ref_credits_api_custom_file'],$data['remove_ref_credits_api_custom_cover_img_field']);
        unset($data['remove_ref_credits_api_custom_cover_img_field']);
        if (empty($data['ref_credits_api_custom_file'])) {
          unset($data['ref_credits_api_custom_file']);
        }
        $id                 = $data['migarefrence_credits_notification_id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migarefrence_credits_notification", $data,['migarefrence_credits_notification_id = ?' => $id]);
        return $res;
      }
      public function updatePrizenotificationvePush($data=[])
      {
        $agt_file=$this->uploadApplicationFile($data['app_id'],$data['agt_prz_custom_file'],$data['remove_agt_prz_custom_cover_img_field']);
        $ref_file=$this->uploadApplicationFile($data['app_id'],$data['ref_prz_custom_file'],$data['remove_ref_prz_custom_cover_img_field']);
        unset($data['remove_ref_prz_custom_cover_img_field']);
        unset($data['remove_agt_prz_custom_cover_img_field']);
        $id                 = $data['migarefrence_prizes_notification_id'];
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migarefrence_prizes_notification", $data,['migarefrence_prizes_notification_id = ?' => $id]);
        return $res;
      }
      public function savePushAuto($datas=[])
      {
        $datas['created_at'] = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_push_template", $datas);
        return  $migareference_id = $this->_db->lastInsertId();
      }
      public function saveEmaillog($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_email_log", $data);
        $id = $this->_db->lastInsertId();
        return $id;
      }
      public function savePhoneBook($data=[])
      {
        $user_id = 0;
        if (isset($data['user_id'])) {
          $user_id=$data['user_id'];
        }
        unset($data['user_id']);
        if (!preg_match('/^00|\+/', $data['mobile'])) {
            // If not, add the prefix "+39"
            $data['mobile'] = '+39' . $data['mobile'];
        }
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_phonebook", $data);
        $id = $this->_db->lastInsertId();
        $log_item=[
              'app_id'       => $data['app_id'],
              'phonebook_id' => $id,
              'user_id'      => $user_id,
              'log_type'     => "Enrollment"
          ];
        $this->saveCommunicationLog($log_item);
        return $id;
      }
      public function saveCommunicationLog($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_communication_logs", $data);
        $phonebook_id=$data['phonebook_id'];
        $phonebook['last_contact_at']=date('Y-m-d');
        $this->_db->update("migarefrence_phonebook", $phonebook,['migarefrence_phonebook_id = ?' => $phonebook_id]);
      }
      public function getCommunicatioLog($app_id=0,$phonebook_id=0)
      {
        $query_option = "SELECT *
                         FROM migareference_communication_logs
                         WHERE phonebook_id=$phonebook_id AND app_id=$app_id AND (log_type='Automation' OR log_type='Enrollment' OR log_type='Manual' OR log_type='Rating')
                         ORDER BY created_at DESC";
        return  $this->_db->fetchAll($query_option);
      }
      public function declinedReport($app_id=0,$report_id=0,$status_title="",$status_id=0)
      {
        $migareference = new Migareference_Model_Db_Table_Migareference();
        $data['last_modification']     = $status_title;
        $data['last_modification_by']  = 99999;
        $data['last_modification_at']  = date('Y-m-d H:i:s');
        $data['currunt_report_status'] = $status_id;
        $data['updated_at']            = date('Y-m-d H:i:s');
        $data['is_reminder_sent']      = 0;
        $res                           = $this->_db->update("migareference_report", $data,['migareference_report_id = ?' => $report_id]);
        $log_data['app_id']=$app_id;
        $log_data['user_id']=99999;
        $log_data['user_type']=1;
        $log_data['report_id']=$report_id;
        $log_data['log_type']="System Fallback";
        $log_data['log_detail']="Update Status to ".$status_title;
        $migareference->saveLog($log_data);
      }
      public function reminder_sent($report_id=0)
      {
        $data['is_reminder_sent']      = 1;
        $res                           = $this->_db->update("migareference_report", $data,['migareference_report_id = ?' => $report_id]);
      }
      public function updateReportProspect($data=[],$prospect_id=0)
      {        
        $this->_db->update("migareference_report", $data,['prospect_id = ?' => $prospect_id]);
      }
      public function update_phonebook($data,$id=0,$chnage_by=0,$user_type=0)
      {        
        // Do not delete any RULE comment
        // *RULE 1  Setup logs if someone chnage job,note(Relationship) or Reciprocity Notes, or rating for stats (Ref. Processing Criteria for processed Referrers:)
        // *RULE 2  if their is any change ot Relational Note or Reciprocity Note we will set rating to 1 if previous is 0 (Ref. AI Matching Script)
        $query_option="SELECT * FROM migarefrence_phonebook WHERE migarefrence_phonebook_id=$id";
        $phone_item   = $this->_db->fetchAll($query_option);
        if (count($phone_item)) { 
          $log_item['app_id']=$phone_item[0]['app_id'];
          $log_item['phonebook_id']=$phone_item[0]['migarefrence_phonebook_id'];
          $log_item['reminder_id']=0;
          $log_item['chnage_by'] = ($chnage_by==null) ? 0 : $chnage_by;//9999 Mean automated or by system          
          $log_item['user_type']=($user_type==null) ? 0 : $user_type;;//0: for System or no user, 1 for app customer, 2 for owner side admin

		      $is_processing_prerequisites_met = false;
          if (isset($data['rating']) && $data['rating']!=$phone_item[0]['rating']) {
            $log_item['log_type']="Rating";
            $log_item['note']="Change Rating From: ".$phone_item[0]['rating']." to: ".$data['rating'];
            $this->saveCommunicationLog($log_item);
			      $is_processing_prerequisites_met = true;
          }
          
          if (isset($data['note']) && $data['note']!=$phone_item[0]['note']) {
            $log_item['log_type']="Note";
            $log_item['note']="Change note From: ".$phone_item[0]['note']." to: ".$data['note'];
            $this->saveCommunicationLog($log_item);
            // R2
            if (!$phone_item[0]['rating'] && (!isset($data['rating']) || (isset($data['rating']) || $data['rating']==0))) {
              $data['rating']=1;
              $log_item['log_type']="Rating";
              $log_item['note']="Change Rating From: ".$phone_item[0]['rating']." to: ".$data['rating']." due to change in Relationship Notes";
              $this->saveCommunicationLog($log_item);              
            }            
			      $is_processing_prerequisites_met = true;
          }
		      if (isset($data['reciprocity_notes']) && $data['reciprocity_notes']!=$phone_item[0]['reciprocity_notes']) {
            $log_item['log_type'] = "Reciprocity Notes";
            $log_item['note'] = "Change reciprocity notes From: " . $phone_item[0]['reciprocity_notes'] . " to: " . $data['reciprocity_notes'];
            $this->saveCommunicationLog($log_item);
            // R2
            if (!$phone_item[0]['rating'] && (!isset($data['rating']) || (isset($data['rating']) || $data['rating']==0))) {
              $data['rating']=1;
              $log_item['log_type']="Rating";
              $log_item['note']="Change Rating From: ".$phone_item[0]['rating']." to: ".$data['rating']." due to change in Reciprocity Notes";
              $this->saveCommunicationLog($log_item);              
            }
			      $is_processing_prerequisites_met = true;
          }
          if (isset($data['job_id']) && $data['job_id']!=$phone_item[0]['job_id']) {
            $log_item['log_type']="Job";
            $log_item['note']="Change Job From: ".$phone_item[0]['job_id']." to: ".$data['job_id'];
            $this->saveCommunicationLog($log_item);
          }
          if (isset($data['name']) && $data['name']!=$phone_item[0]['name']) {
            $log_item['log_type']="Name";
            $log_item['note']="Change Name From: ".$phone_item[0]['name']." to: ".$data['name'];
            $this->saveCommunicationLog($log_item);
          }
          if (isset($data['surname']) && $data['surname']!=$phone_item[0]['surname']) {
            $log_item['log_type']="surname";
            $log_item['note']="Change surname From: ".$phone_item[0]['surname']." to: ".$data['surname'];
            $this->saveCommunicationLog($log_item);
          }

		    $app_id = $log_item['app_id'];
			  $invoice_id = $phone_item[0]['invoice_id'];
			  $invoice_settings = $this->_db->fetchAll("SELECT user_id FROM migareference_invoice_settings WHERE migareference_invoice_settings_id = $invoice_id AND app_id = $app_id LIMIT 1");
			
			  $referrer_id = $invoice_settings[0]['user_id'];
			  $processed_referrer = (new Migareference_Model_ProcessedReferrer())->find([
				  'app_id' => $app_id,
				  'referrer_id' => $referrer_id,
			  ]);

			  $phonebook_id = $log_item['phonebook_id'];
		  	$communication_log = $this->_db->fetchAll("SELECT created_at FROM migareference_communication_logs WHERE phonebook_id = $phonebook_id AND app_id = $app_id AND (log_type = 'Rating' OR log_type = 'Note' OR log_type = 'Reciprocity Notes') ORDER BY created_at ASC LIMIT 1");

			if ($is_processing_prerequisites_met && $phone_item[0]['type'] == 1 && !$processed_referrer->getId() && count($communication_log)) { 
				$save_processed_referrer = (new Migareference_Model_ProcessedReferrer())
											->setData([
												'app_id' => $app_id,
												'referrer_id' => $referrer_id,
												'processed_date' => $communication_log[0]['created_at'],
											])
											->save();
			}
        }                         
        $this->_db->update("migarefrence_phonebook", $data,['migarefrence_phonebook_id = ?' => $id]);
        // Get Updated Phonebook
      
        //  AI Matchin All Rules will be checked in ReferrerMatching Function
        // $phone_item=$this->getSinglePhonebook($id);        
        // $phonebook->referrerMatching($phone_item[0]['app_id'],$phone_item[0]['migarefrence_phonebook_id'],'Phonebook_update');       
        return $data;
      }
      public function update_prospect($data=[],$id=0,$chnage_by=0,$user_type=0)//Also save log if their is change in Rating,Job,Notes
      {        
        $query_option="SELECT *
                         FROM migarefrence_prospect
                         WHERE migarefrence_prospect_id=$id";
                         $phone_item   = $this->_db->fetchAll($query_option);
        if (count($phone_item)) { //Setup logs if someone chnage job,note or rating for stats
          $log_item['app_id']=$phone_item[0]['app_id'];
          $log_item['phonebook_id']=$phone_item[0]['migarefrence_prospect_id'];
          $log_item['reminder_id']=0;
          $log_item['chnage_by'] = ($chnage_by==null) ? 0 : $chnage_by;//9999 Mean automated or by system          
          $log_item['user_type']=($user_type==null) ? 0 : $user_type;;//0: for System or no user, 1 for app customer, 2 for owner side admin
          if (isset($data['rating']) && $data['rating']!=$phone_item[0]['rating']) {
            $log_item['log_type']="Rating";
            $log_item['note']="Change Rating From: ".$phone_item[0]['rating']." to: ".$data['rating'];
            $this->saveCommunicationLog($log_item);
          }
          if (isset($data['note']) && $data['note']!=$phone_item[0]['note']) {
            $log_item['log_type']="Note";
            $log_item['note']="Change note From: ".$phone_item[0]['note']." to: ".$data['note'];
            $this->saveCommunicationLog($log_item);
          }
          if (isset($data['job_id']) && $data['job_id']!=$phone_item[0]['job_id']) {
            $log_item['log_type']="Job";
            $log_item['note']="Change Job From: ".$phone_item[0]['job_id']." to: ".$data['job_id'];
            $this->saveCommunicationLog($log_item);
          }
        }                         
        $this->_db->update("migarefrence_prospect", $data,['migarefrence_prospect_id = ?' => $id]);
        return $id;
      }
      public function reportGdpr($app_id=0,$user_id=0,$data=[])
      {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migareference_report", $data,['app_id = ?' => $app_id,'user_id = ?'=> $user_id]);
      }
      public function declinedNotification($id=0)
      {
        $datas['is_push_deliverd']   = 1;
        $datas['is_email_deliverd']  = 1;
        $datas['updated_at']         = date('Y-m-d H:i:s');
        $this->_db->update("migareference_cron_notifications", $datas,['migareference_cron_notifications_id = ?' => $id]);
      }
      public function updatePropertyReport($data = [])
      {
          if (!isset($data['migareference_report_id'])) {
              throw new InvalidArgumentException('Missing report ID.');
          }
      
          $id = $data['migareference_report_id'];
          $data['updated_at'] = date('Y-m-d H:i:s');
          $ignoreWebhook = isset($data['ignore_webhook']) && $data['ignore_webhook'];
          unset($data['ignore_webhook']);
      
          // Update the report in the database
          $this->_db->update("migareference_report", $data, ['migareference_report_id = ?' => $id]);
      
          // Retrieve the updated report
          $report = $this->get_report_by_key($id);
          if (empty($report) || !isset($report[0]['report_source'], $report[0]['app_id'])) {
              throw new UnexpectedValueException('Report not found or incomplete data.');
          }
      
          // Trigger webhook if conditions are met
          if ($report[0]['report_source'] != 4 && !$ignoreWebhook) {
              $webhook = new Migareference_Model_Db_Table_Webhook();
              $webhook->triggerReportWebhook($report[0]['app_id'], $id,'UpdateReport','update');//*Update Report
          }
      
          // Update reminders connected to this Report 
          $this->updateRemindersAndLog($id, $report[0]['currunt_report_status']);
      
          return $report;
      }
      
      private function updateRemindersAndLog($reportId, $reportStatusId)
      {
          $query_option = "SELECT *
                           FROM migareference_automation_log AS alg
                           JOIN migareference_invoice_settings AS inv ON inv.user_id = alg.user_id                
                           JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id = inv.migareference_invoice_settings_id
                           WHERE alg.report_id = $reportId 
                           AND alg.report_status_id != $reportStatusId 
                           AND (alg.current_reminder_status = 'Pending' OR alg.current_reminder_status = 'Postponed')";
          $reminder_list = $this->_db->fetchAll($query_option);
      
          foreach ($reminder_list as $reminder) {
              $update_data = [
                  'current_reminder_status' => 'Done',
                  'updated_at' => date('Y-m-d H:i:s')
              ];
              $this->_db->update('migareference_automation_log', $update_data, ['migareference_automation_log_id = ?' => $reminder['migareference_automation_log_id']]);
              
              $log_item = [
                  'app_id' => $reminder['app_id'],
                  'phonebook_id' => $reminder['migarefrence_phonebook_id'],
                  'reminder_id' => $reminder['migareference_automation_log_id'],
                  'log_type' => "Automation",
                  'note' => "Reminder Status Change on Report Status Change To: Done",
                  'user_id' => $reminder['user_id'],
                  'created_at' => date('Y-m-d H:i:s')
              ];
              $this->_db->insert('migareference_communication_logs', $log_item);
          }
      }
      

      
      public function get_all_reports($data=[]) //Too fetch all reports this method is deprecated 09-28-2023(Mutlti agent) new method is getReportList
      {
        $app_id=$data['app_id'];
        $query_option = "SELECT migareference_report.*,
                         migareference_report_status.*,
                         pros.*,
                         migareference_invoice_settings.invoice_name,
                         migareference_invoice_settings.invoice_surname,
                         migareference_invoice_settings.migareference_invoice_settings_id,
                         migareference_invoice_settings.sponsor_id,                         
                         DATE_FORMAT(migareference_report.created_at, '%d-%m-%Y') AS report_created_at,
                         DATE_FORMAT(migareference_report.last_modification_at, '%d-%m-%Y') AS report_modified_at,
                         DATE_FORMAT(migareference_report.last_modification_at, '%Y-%M-%D') AS report_modified_at_filter,
                         referrer.mobile,
                         agent.firstname AS sponsor_firstname,
                         agent.lastname AS sponsor_lastname,
                         rem.current_reminder_status
                         FROM migareference_report
                         JOIN migareference_invoice_settings ON migareference_invoice_settings.app_id=migareference_report.app_id AND migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status AND migareference_report_status.status=1".$data['status_string'].' ';
          $query_option.="LEFT JOIN customer AS referrer ON referrer.customer_id=migareference_invoice_settings.user_id
                         LEFT JOIN customer AS agent ON agent.customer_id=migareference_invoice_settings.sponsor_id
                         LEFT JOIN migarefrence_prospect AS pros ON pros.migarefrence_prospect_id=migareference_report.prospect_id
                         LEFT JOIN migareference_automation_log AS rem ON rem.report_id=migareference_report.migareference_report_id AND rem.current_reminder_status!='Done' AND rem.current_reminder_status!='cancele' AND rem.trigger_id!=1000000
                         WHERE migareference_report.app_id = $app_id AND migareference_report.status=1";
        $query_option.=" ".$data['filter_string'];
        $query_option.=" GROUP BY migareference_report.migareference_report_id  ORDER BY migareference_report.last_modification_at DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportList($data=[])
      {
        $app_id=$data['app_id'];
        $query_option = "SELECT migareference_report.*,
                         migareference_report_status.*,
                         pros.*,
                         migareference_invoice_settings.invoice_name,
                         migareference_invoice_settings.invoice_surname,
                         migareference_invoice_settings.migareference_invoice_settings_id,
                         migareference_invoice_settings.sponsor_id,                         
                         DATE_FORMAT(migareference_report.created_at, '%d-%m-%Y') AS report_created_at,
                         DATE_FORMAT(migareference_report.last_modification_at, '%d-%m-%Y') AS report_modified_at,
                         referrer.mobile,                         
                         rem.current_reminder_status,
                         sponsor_one.customer_id AS sponsor_one_id,
                         sponsor_two.customer_id AS sponsor_two_id,
                         sponsor_one.firstname AS sponsor_one_firstname,
                         sponsor_one.lastname AS sponsor_one_lastname,
                         sponsor_two.firstname AS sponsor_two_firstname,
                         sponsor_two.lastname AS sponsor_two_lastname,
                         COUNT(DISTINCT(migarefrence_notes.migarefrence_notes_id)) AS note_unread_count
                         FROM migareference_report
                         JOIN migareference_invoice_settings ON migareference_invoice_settings.app_id=migareference_report.app_id AND migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status AND migareference_report_status.status=1".$data['status_string'].' ';
          $query_option.="LEFT JOIN customer AS referrer ON referrer.customer_id=migareference_invoice_settings.user_id                         
                         LEFT JOIN migarefrence_prospect AS pros ON pros.migarefrence_prospect_id=migareference_report.prospect_id
                         LEFT JOIN migareference_automation_log AS rem ON rem.report_id=migareference_report.migareference_report_id AND rem.current_reminder_status!='Done' AND rem.current_reminder_status!='cancele' AND rem.trigger_id!=1000000
                         LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=migareference_report.user_id
                         LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=migareference_report.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id
                         LEFT JOIN migareference_app_agents AS apagt ON apagt.user_id=refag_one.agent_id AND apagt.agent_type=migareference_report.report_custom_type
                         LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
                         LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id
                         LEFT JOIN migarefrence_notes ON migarefrence_notes.report_id=migareference_report.migareference_report_id AND migarefrence_notes.is_read=0
                         WHERE migareference_report.app_id = $app_id AND migareference_report.status=1";
        $query_option.=" ".$data['filter_string'];
        $query_option.=" GROUP BY migareference_report.migareference_report_id  ORDER BY migareference_report.last_modification_at DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }     
      public function ref_get_all_reports($data=[])
      {
        $app_id=$data['app_id'];
        $user_id=$data['user_id'];
        $query_option = "SELECT migareference_report.*,migareference_report_status.*,
        migareference_invoice_settings.invoice_name,
        migareference_invoice_settings.invoice_surname,
        migareference_invoice_settings.invoice_mobile,
        migareference_report.created_at as report_created_at
                         FROM migareference_report
                         JOIN migareference_invoice_settings ON migareference_invoice_settings.app_id=migareference_report.app_id AND migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN migareference_report_status ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status AND migareference_report_status.status=1
                         WHERE migareference_report.app_id = $app_id AND migareference_report.status=1 AND migareference_report.user_id=$user_id";
        $query_option.=" ".$data['filter_string'];
        $query_option.=" ORDER BY migareference_report.last_modification_at DESC ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function updateCronnotifiaction($id=0,$push_id=0,$mail_id=0)
      {
        if ($push_id) {
          $datas['is_push_deliverd']   = $push_id;
        }else {
          $datas['is_email_deliverd']  = $mail_id;
        }
        $datas['updated_at']         = date('Y-m-d H:i:s');
        $datas['trigger_start_time'] = date('Y-m-d H:i:s');
        $res                = $this->_db->update("migareference_cron_notifications", $datas,['migareference_cron_notifications_id = ?' => $id]);
      }
      public function getStatus($app_id=0,$status_id=0)
      {
        $query_option = "SELECT *
                         FROM migareference_report_status
                         WHERE migareference_report_status_id=$status_id AND app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_reports($app_id=0)
      {
        $query_option = "SELECT *
                         FROM migareference_report
                         WHERE migareference_report.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_report_by_key($key=0)
      {
        $query_option = "SELECT *,migareference_report.app_id as app_id,migareference_report.created_at as report_created_at
                         FROM migareference_report
                         LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migareference_report.owner_job
                         WHERE migareference_report.migareference_report_id=$key";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerByKey($key=0)
      {
        $query_option = "SELECT *
                         FROM migareference_invoice_settings
                         JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
                         WHERE migareference_invoice_settings.migareference_invoice_settings_id=$key";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_last_report_no()
      {
        $query_option = "SELECT *
                         FROM migareference_report
                         WHERE 1 ORDER BY migareference_report_id DESC LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_standard($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_report_status` WHERE `app_id`=$app_id AND `is_standard`=1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReport($app_id=0,$report_id=0)//Deprected 09/20/2023 for multi agent new mthod is getReportItem
      {
        $query_option = "SELECT migareference_report.*,                                
                                application.*,                                
                                migareference_status_comment.*,
                                migareference_report_status.*,                                
                                migareference_invoice_settings.invoice_name,
                                migareference_invoice_settings.invoice_surname,
                                migareference_invoice_settings.migareference_invoice_settings_id,
                                migareference_invoice_settings.sponsor_id,
                                migareference_invoice_settings.invoice_mobile,
                                customer.firstname as sponsor_firstname,
                                customer.lastname as sponsor_lastname,
                                customer.email sponsor_email,
                                migareference_report.created_at as report_created_at,
                                migarefrence_prospect.migarefrence_prospect_id,
                                migarefrence_prospect.app_id,
                                migarefrence_prospect.name,
                                migarefrence_prospect.surname,
                                migarefrence_prospect.email,
                                migarefrence_prospect.mobile,
                                migarefrence_prospect.job_id,
                                migarefrence_prospect.rating,
                                migarefrence_prospect.note AS prospect_note,
                                migarefrence_prospect.is_blacklist,
                                migarefrence_prospect.password,
                                migarefrence_prospect.gdpr_consent_source,
                                migarefrence_prospect.gdpr_consent_ip,
                                migarefrence_prospect.gdpr_consent_timestamp,
                                migarefrence_prospect.created_at,
                                migarefrence_prospect.updated_at,
                                rem.current_reminder_status                                
                         FROM migareference_report
                         JOIN application
                              ON application.app_id=migareference_report.app_id
                         JOIN migareference_invoice_settings
                              ON migareference_invoice_settings.app_id=migareference_report.app_id
                              AND migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN migareference_report_status
                              ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                              AND migareference_report_status.status=1
                         LEFT JOIN migareference_status_comment
                              ON migareference_status_comment.status_id=migareference_report.currunt_report_status
                              AND migareference_report.migareference_report_id=migareference_status_comment.report_id
                         LEFT JOIN customer
                              ON customer.customer_id=migareference_invoice_settings.sponsor_id
                         LEFT JOIN migarefrence_prospect
                              ON migarefrence_prospect.migarefrence_prospect_id=migareference_report.prospect_id
                         LEFT JOIN migareference_automation_log AS rem
                              ON rem.report_id=migareference_report.migareference_report_id AND rem.current_reminder_status!='Done' AND rem.current_reminder_status!='cancele'
                         WHERE migareference_report.app_id = $app_id
                              AND migareference_report.migareference_report_id=$report_id
                              AND migareference_report.status=1
                         GROUP BY migareference_report.migareference_report_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportItem($app_id=0,$report_id=0)
      {
        // Sponsor Connection in query is deprecated 09-20-2023
        $query_option = "SELECT migareference_report.*,                                
                          application.*,                                
                          migareference_status_comment.*,
                          migareference_report_status.*,                                
                          migareference_invoice_settings.invoice_name,
                          migareference_invoice_settings.invoice_surname,
                          migareference_invoice_settings.migareference_invoice_settings_id,
                          migareference_invoice_settings.sponsor_id,
                          migareference_invoice_settings.invoice_mobile as invoice_mobile,
                          sponsor.firstname as sponsor_firstname,
                          sponsor.lastname as sponsor_lastname,
                          sponsor.email sponsor_email,
                          migareference_report.created_at as report_created_at,
                          migarefrence_prospect.migarefrence_prospect_id,
                          migarefrence_prospect.app_id,
                          migarefrence_prospect.name,
                          migarefrence_prospect.surname,
                          migarefrence_prospect.email,
                          migarefrence_prospect.mobile,
                          migarefrence_prospect.job_id,
                          migarefrence_prospect.rating,
                          migarefrence_prospect.note AS prospect_note,
                          migarefrence_prospect.is_blacklist,
                          migarefrence_prospect.password,
                          migarefrence_prospect.gdpr_consent_source,
                          migarefrence_prospect.gdpr_consent_ip,
                          migarefrence_prospect.gdpr_consent_timestamp,
                          migarefrence_prospect.created_at,
                          migarefrence_prospect.updated_at,
                          rem.current_reminder_status,
                          ph.migarefrence_phonebook_id                                
                  FROM migareference_report
                  JOIN application
                        ON application.app_id=migareference_report.app_id
                  JOIN migareference_invoice_settings
                        ON migareference_invoice_settings.app_id=migareference_report.app_id
                        AND migareference_invoice_settings.user_id=migareference_report.user_id
                  JOIN migareference_report_status
                        ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                        AND migareference_report_status.status=1
                  LEFT JOIN migareference_status_comment
                        ON migareference_status_comment.status_id=migareference_report.currunt_report_status
                        AND migareference_report.migareference_report_id=migareference_status_comment.report_id
                  LEFT JOIN migareference_referrer_agents AS refag 
                        ON refag.referrer_id=migareference_report.user_id
                  LEFT JOIN migareference_app_agents AS apagt 
                        ON apagt.user_id=refag.agent_id 
                        AND apagt.agent_type=migareference_report.report_custom_type
                  LEFT JOIN customer AS sponsor 
                        ON sponsor.customer_id=refag.agent_id
                  LEFT JOIN migarefrence_phonebook AS ph 
                        ON ph.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
                  LEFT JOIN migarefrence_prospect
                        ON migarefrence_prospect.migarefrence_prospect_id=migareference_report.prospect_id
                  LEFT JOIN migareference_automation_log AS rem
                        ON rem.report_id=migareference_report.migareference_report_id
                        AND rem.current_reminder_status!='Done'
                        AND rem.current_reminder_status!='cancele'
                  WHERE migareference_report.app_id = $app_id
                        AND migareference_report.migareference_report_id=$report_id
                        AND migareference_report.status=1
                  GROUP BY migareference_report.migareference_report_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getApiReport($app_id=0,$report_id=0)
      {
        $query_option = "SELECT migareference_report.*,
                                application.*,
                                migareference_status_comment.*,
                                migareference_report_status.*,
                                migareference_invoice_settings.invoice_name,
                                migareference_invoice_settings.invoice_surname,
                                migareference_invoice_settings.migareference_invoice_settings_id,
                                migareference_invoice_settings.sponsor_id,
                                customer.firstname as sponsor_firstname,
                                customer.lastname as sponsor_lastname,
                                customer.email sponsor_email,
                                migareference_invoice_settings.invoice_mobile as mobile,
                                migareference_report.created_at as report_created_at,
                                migarefrence_phonebook.migarefrence_phonebook_id
                         FROM migareference_report
                         JOIN application
                              ON application.app_id=migareference_report.app_id
                         JOIN migareference_invoice_settings
                              ON migareference_invoice_settings.app_id=migareference_report.app_id
                              AND migareference_invoice_settings.user_id=migareference_report.user_id
                         JOIN migareference_report_status
                              ON migareference_report_status.migareference_report_status_id=migareference_report.currunt_report_status
                              AND migareference_report_status.status=1
                         LEFT JOIN migareference_status_comment
                              ON migareference_status_comment.status_id=migareference_report.currunt_report_status
                              AND migareference_report.migareference_report_id=migareference_status_comment.report_id
                         LEFT JOIN customer
                              ON customer.customer_id=migareference_invoice_settings.sponsor_id
                         LEFT JOIN migarefrence_phonebook
                              ON migarefrence_phonebook.report_id=migareference_report.migareference_report_id
                         WHERE migareference_report.app_id = $app_id
                              AND migareference_report.report_no=$report_id
                              AND migareference_report.status=1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function sendPush($data=[],$app_id=0,$user_id=0)
      {
        try {
          //Get How it works details or Referral Directore details
          $howto_data=$this->gethowto($app_id);
          $howto_data=$howto_data[0];
          // Replace following tags in Email Text @@rd_name@@, @@rd_email@@, @@rd_phone@@, @@rd_calendar_url@@
          $data['push_text']=str_replace("@@rd_name@@", $howto_data['director_name'], $data['push_text']);
          $data['push_text']=str_replace("@@rd_email@@", $howto_data['director_email'], $data['push_text']);
          $data['push_text']=str_replace("@@rd_phone@@", $howto_data['director_phone'], $data['push_text']);
          $data['push_text']=str_replace("@@rd_calendar_url@@", $howto_data['director_calendar_url'], $data['push_text']);
          $currentVersion = Siberian_Version::VERSION;          
          $minimumSupportedVersion = '5.0.0';
          // 01-11-2024 check if Migabridge exist use old method
          if (version_compare($currentVersion, $minimumSupportedVersion, '<') || $this->isMigabridgeExist()) {   
                        
          $customers=$this->getSingleuser($app_id,$user_id);//10/11/2021 to avoid send notification to delted users
          if (count($customers)) {
            $default = new Core_Model_Default();
            $base_url= $default->getBaseUrl();
            if (intval($data['open_feature']) || !empty($data['open_feature'])) {
                if (!intval($data['feature_id']) && !empty($data['custom_url'])) {
                    $url = $data['custom_url'];
                    if (!preg_match('/^[0-9]*$/', $data['custom_url'])) {
                        $url = "http://" . $data['custom_url'];
                        if (stripos($data['custom_url'], "http://") !== false ||
                            stripos($data['custom_url'], "https://") !== false) {
                            $url = $data['custom_url'];
                        }
                    }
                    $action_value = file_get_contents("https://tinyurl.com/api-create.php?url=" . urlencode($url));;
                } else {
                    $action_value = $data['feature_id'];
                }
            } else {
                $action_value = null;
            }
            $message = new Push_Model_Message();
            $message->setData([
                'send_at'      => date('Y-m-d H:i:s'),
                'cover'        => empty($data['cover_image']) ? null : '/' . $app_id . '/features/migareference/' . $data['cover_image'],
                'action_value' => $action_value,
                'type_id'      => 1,
                'app_id'       => $data['app_id'],
                'send_to_all'  => 0,
                'send_to_specific_customer' => 1,
                'base_url'       => $base_url,
                'target_devices' => 'all',
            ]);
            $message->setTitle($data['push_title'])->setText($data['push_text']);
            $message->save();
            $message_id        = $message->getId();
            $push_log['app_id']=$app_id;
            $push_log['push_message_id']=$message_id;
            $push_log['deliver_method']=1;
            $push_log['notification_title']=$data['push_title'];
            $push_log['notification_text']=$data['push_text'];
            $push_log['calling_method']=$data['calling_method'];
            $push_log['user_id']=$user_id;
            $this->savePushlog($push_log);
            $customer_message = new Push_Model_Customer_Message();
            $customer_message_data = [
                "customer_id" => $user_id,
                "message_id" => $message_id
            ];
            $customer_message->setData($customer_message_data);
            $customer_message->save();
          }else {
            $push_log['app_id']=$app_id;
            $push_log['push_message_id']=0;
            $push_log['deliver_method']=1;
            $push_log['notification_title']=$data['push_title'];
            $push_log['calling_method']=$data['calling_method'];
            $push_log['notification_text']="User Not Found";
            $push_log['user_id']=$user_id;
            $this->savePushlog($push_log);
          }
        }else { //Send Native PUSH
              $push2_content['app_id']=$app_id;  
              $push2_content['push_title']=$data['push_title'];
              $push2_content['push_message']=$data['push_text'];
              $push2_content['customer_id']=$user_id;
              $response=$this->sendPush2($push2_content);            
              $push_log['app_id']=$app_id;
              $push_log['push_message_id']=0;
              $push_log['deliver_method']=1;
              $push_log['notification_title']=$data['push_title'];
              $push_log['calling_method']=$data['calling_method'];
              $push_log['notification_text']=$data['push_text'];
              $push_log['user_id']=$user_id;
            $this->savePushlog($push_log);
            return $response;
        }
        } catch (Exception $e) {
          throw new exception(__('Could not send Notifaction'.$e->getMessage()));
        }
        return true;
      }
      public function sendAutomationPush($data=[],$app_id=0,$user_id=0)
      {
        try {
          $customers=$this->getSingleuser($app_id,$user_id);//10/11/2021 to avoid send notification to delted users
          if (count($customers)) {
            $default = new Core_Model_Default();
            $base_url= $default->getBaseUrl();
            if (intval($data['rep_rem_open_feature']) || !empty($data['rep_rem_open_feature'])) {
                if (!intval($data['rep_rem_feature_id']) && !empty($data['rep_rem_custom_url'])) {
                    $url = $data['rep_rem_custom_url'];
                    if (!preg_match('/^[0-9]*$/', $data['rep_rem_custom_url'])) {
                        $url = "http://" . $data['rep_rem_custom_url'];
                        if (stripos($data['rep_rem_custom_url'], "http://") !== false ||
                            stripos($data['rep_rem_custom_url'], "https://") !== false) {
                            $url = $data['rep_rem_custom_url'];
                        }
                    }
                    $action_value = file_get_contents("https://tinyurl.com/api-create.php?url=" . urlencode($url));;
                } else {
                    $action_value = $data['rep_rem_feature_id'];
                }
            } else {
                $action_value = null;
            }

            $message = new Push_Model_Message();
            $message->setData([
                'send_at'      => date('Y-m-d H:i:s'),
                'cover'        => empty($data['rep_rem_cover_image']) ? null : '/' . $app_id . '/features/migareference/' . $data['rep_rem_cover_image'],
                'action_value' => $action_value,
                'type_id'      => 1,
                'app_id'       => $app_id,
                'send_to_all'  => 0,
                'send_to_specific_customer' => 1,
                'base_url'       => $base_url,
                'target_devices' => 'all',
            ]);
            $message->setTitle($data['rep_rem_push_title'])->setText($data['rep_rem_push_text']);
            $message->save();
            $message_id        = $message->getId();
            $push_log['app_id']=$app_id;
            $push_log['push_message_id']=$message_id;
            $push_log['deliver_method']=1;
            $push_log['type']=1;
            $push_log['notification_title']=$data['rep_rem_push_title'];
            $push_log['notification_text']=$data['rep_rem_push_text'];
            $push_log['user_id']=$user_id;
            $customer_message = new Push_Model_Customer_Message();
            $customer_message_data = [
              "customer_id" => $user_id,
              "message_id" => $message_id
            ];
            $customer_message->setData($customer_message_data);
            $customer_message->save();
            return $this->savePushlog($push_log);
          }else {
            $push_log['app_id']=$app_id;
            $push_log['push_message_id']=0;
            $push_log['deliver_method']=1;
            $push_log['type']=1;
            $push_log['notification_title']=$data['rep_rem_push_title'];
            $push_log['notification_text']="User Not Found";
            $push_log['user_id']=$user_id;
            return $this->savePushlog($push_log);
          }
        } catch (Exception $e) {
          throw new exception(__('Could not send Notifaction'.$e->getMessage()));
        }
        return 1;
      }
      public function sendAutomationMail($data=[],$app_id = 0, $customer = [],$footer="")
      {    
        //Get How it works details or Referral Directore details
        $howto_data=$this->gethowto($app_id);
        $howto_data=$howto_data[0];
        // Replace following tags in Email Text @@rd_name@@, @@rd_email@@, @@rd_phone@@, @@rd_calendar_url@@
        $data['rep_rem_email_text']=str_replace("@@rd_name@@", $howto_data['director_name'], $data['rep_rem_email_text']);
        $data['rep_rem_email_text']=str_replace("@@rd_email@@", $howto_data['director_email'], $data['rep_rem_email_text']);
        $data['rep_rem_email_text']=str_replace("@@rd_phone@@", $howto_data['director_phone'], $data['rep_rem_email_text']);
        $data['rep_rem_email_text']=str_replace("@@rd_calendar_url@@", $howto_data['director_calendar_url'], $data['rep_rem_email_text']);    
        $user_id   = $customer['customer_id'];
        $e_mail    = $customer['email'];
        $e_title   = $data['rep_rem_email_title'];
        $e_message = $data['rep_rem_email_text'];        
        $e_message.=$footer;
        // Send Mail
        $mail = new Siberian_Mail();
        $mail->setBodyHtml($e_message);
        $mail->addTo($e_mail, "Migareference");
        $mail->setSubject($e_title);
        $mail->send();
        // Save Log
        $email_log['app_id']            = $app_id;
        $email_log['email_customer_id'] = ($user_id!=NULL) ? $user_id : 0 ;
        $email_log['deliver_method']    = 1;
        $email_log['type']              = 1;
        $email_log['email_title']       = $e_title;
        $email_log['email_text']        = $e_message;
        $this->saveEmaillog($email_log);

        return $email_log;

      }
      public function sendMail($data=[],$app_id = 0, $user_id = 0)
      {
        //Get How it works details or Referral Directore details
        $howto_data=$this->gethowto($app_id);
        $howto_data=$howto_data[0];
        $default = new Core_Model_Default();
        $base_url= $default->getBaseUrl();
        $application=$this->application($app_id);
        $app_name=$application[0]['name'];
        // Replace following tags in Email Text @@rd_name@@, @@rd_email@@, @@rd_phone@@, @@rd_calendar_url@@
        $data['email_text']=str_replace("@@rd_name@@", $howto_data['director_name'], $data['email_text']);
        $data['email_text']=str_replace("@@rd_email@@", $howto_data['director_email'], $data['email_text']);
        $data['email_text']=str_replace("@@rd_phone@@", $howto_data['director_phone'], $data['email_text']);
        $data['email_text']=str_replace("@@app_name@@", $app_name, $data['email_text']);
        $data['email_text']=str_replace("@@rd_calendar_url@@", $howto_data['director_calendar_url'], $data['email_text']);
        // Get Customer Emial
        $customer_email = $this->_db->fetchAll("SELECT `email` FROM `customer` WHERE `customer_id`=$user_id");        
        // Contact Referral Link if the user type is referrer
        $admin_data    = $this->is_admin($app_id,$user_id);
        $agent_data    = $this->is_agent($app_id,$user_id);
        if (empty($admin_data) && empty($agent_data)) {
          $sponsor_customers  = $this->getSponsorList($app_id,$user_id); 
          $invitation_link=$this->getRefInvitationLink($app_id,$user_id,$sponsor_customers[0]['agent_id']);                              
          $data['email_text']=str_replace("@@referrer_link@@", $invitation_link['email_formate'], $data['email_text']);
          // $e_message.="<br><br>".$invitation_link['email_formate']."<br>";
        }
        $e_mail=$customer_email[0]['email'];
        $e_title=$data['email_title'];
        $e_message=$data['email_text'];
        // Mail Footer
        $host_name=  gethostname();
        $host_ip=shell_exec('nslookup ' . $host_name);
        $footer="<br><br><small><small><small>Sender: App ID ".$app_id." APP Name: ".$app_name." DOMAIN: ".$base_url." IP: ".$_SERVER['SERVER_ADDR']."</small></small></small>";
        $e_message.=$footer;
        try {          
          $mail = new Siberian_Mail();        
          $mail->setBodyHtml($e_message);
          $mail->addTo($e_mail, "Migareference");
          $mail->setSubject($e_title);
          $mail->clearReplyTo();
          if (isset($data['reply_to_email']) && !$data['reply_to_email'] == "") {
                $mail->setReplyTo($data['reply_to_email']);
            }
            if (isset($data['bcc_to_email']) && $data['bcc_to_email'] !== "") {
                $mail->addBcc($data['bcc_to_email']);
            }
            $mail->send();
        } catch (\Throwable $th) {         
          return $th->getMessage();
        }        
        // Save EmailLog
        $type = (isset($data['type'])) ? $data['type'] : 0 ;
        $email_log['app_id']=$app_id;
        $email_log['email_customer_id']=($user_id!=NULL) ? $user_id : 0 ;
        $email_log['deliver_method']=$type;
        $email_log['email_title']=$data['email_title'];
        $email_log['email_text']=$data['email_text'];
        $email_log['calling_method']=$data['calling_method'];
        return $this->saveEmaillog($email_log);
      }
      public function sendSms($data=[],$app_id = 0, $user_id = 0)
      {
        // Get Customer Emial
        $customer   = $this->getSingleuser($app_id,$user_id);        
        $pre_settings = $this->preReportsettigns($app_id);
        $customer=$customer[0];
        $customer_id=$customer['customer_id'];
        $to=$customer['mobile'];                    
        $messagebody=$data['sms_text'];
        $sid=$pre_settings[0]['twillio_sid'];
        $sim_id=$pre_settings[0]['twillio_sim_id'];
        $token=$pre_settings[0]['twillio_token'];
           try {
             if (class_exists('Twilio\Rest\Client'))
              {
                $client = new Twilio\Rest\Client($sid,$token);
                $balance=$client->balance->fetch()->balance;
                if ($balance>1) {
                    if ($to!='' && $to!=NULL) {
                      if (substr($to, 0, 1)!='+'&& substr($to, 0, 2)!='00') {
                        $to=$pre_settings[0]['default_country_code'].$to;
                      }
                  $message = $client->messages->create(
                    $to,
                    array( "from" => $sim_id,
                    "body" =>$messagebody)
                    );
                      $response="OK";
                  }else {
                    $response="Invalid Phone ".$to;
                  } 
                }else{
                  // Send OUt of balance alert Email
                  $admin_customers    = $this->getAdminCustomers($app_id);//Admin Users->Agents
                  foreach ($admin_customers as $key => $value) {
                    $email_data['email_title']= "SMS Gateway senza credito";
                    $email_data['email_text'] = "Gentile Admin, sembra che l'account Twillio non ha credito e non sia possibile inviare SMS";
                    $this->sendMail($email_data,$app_id,$value['customer_id']);
                  }
                  $response="Your account is out of balance";
                }             
              }else {
                $response="Unable to Connect API";
              }
           } catch (\Throwable $th) {
             $response=$th->getMessage();
           }
          $sms_log['app_id']=$app_id;          
          $sms_log['user_id']=$customer_id;                    
          $sms_log['sms_text']=$messagebody;
          $sms_log['api_response']=$response;
          $sms_log['created_at']= date('Y-m-d H:i:s');
          $this->_db->insert("migareference_twillio_log", $sms_log);
          return $sms_log;
      }
      public function sendTestSms($messagebody="",$to="",$app_id=0)
      {        
        $pre_settings = $this->preReportsettigns($app_id);                
        $sid=$pre_settings[0]['twillio_sid'];
        $sim_id=$pre_settings[0]['twillio_sim_id'];
        $token=$pre_settings[0]['twillio_token'];
           try {
             if (class_exists('Twilio\Rest\Client'))
              {
                $client = new Twilio\Rest\Client($sid,$token);
                $balance=$client->balance->fetch()->balance;
                if ($balance>1) {
                    if ($to!='' && $to!=NULL) {
                      if (substr($to, 0, 1)!='+'&& substr($to, 0, 2)!='00') {
                        $to=$pre_settings[0]['default_country_code'].$to;
                      }
                  $message = $client->messages->create(
                    $to,
                    array( "from" => $sim_id,
                    "body" =>$messagebody)
                    );
                      $response="OK";
                  }else {
                    $response=__("Invalid Phone").$to;
                  } 
                }else{                  
                  $response=__("Your account is out of balance");
                }             
              }else {
                $response=__("Unable to Connect API");
              }
           } catch (\Throwable $th) {
             $response=$th->getMessage();
           }          
          return $response;
      }
      public function getPush($app_id = 0, $value_id = 0)
      {
          return $this->_db->fetchAll("SELECT * FROM migareference_push_template WHERE value_id = $value_id AND app_id = $app_id");
      }
      public function isMigabridgeExist()
      {
          $result=$this->_db->fetchAll("SELECT * FROM `module` WHERE `name` LIKE 'Migabridge'");
          if (COUNT($result)) {
            return true;
          } else {
            return false;
          }
      }
      public function getEmail($app_id = 0, $value_id = 0)
      {
          return $this->_db->fetchAll("SELECT * FROM migareference_email_template WHERE value_id = $value_id AND app_id = $app_id");
      }
      public function loadPush($app_id = 0)
      {
          return $this->_db->fetchAll("SELECT * FROM migareference_push_template WHERE app_id = $app_id");
      }
      public function loadEmail($app_id = 0)
      {
          return $this->_db->fetchAll("SELECT * FROM migareference_email_template WHERE app_id = $app_id");
      }
      public function savePush($app_id = 0, $data = [])
      {
          $value_id               = $data['value_id'];
          $migareference_id       = 0;
          $is_push_ref            = 0;
          $is_push_agt            = 0;
          $reminder_is_push_ref   = 0;
          $reminder_is_push_agt   = 0;
          // Status icon
          if (!empty($data['status_icon_file']) ) {
              $icon = $data['status_icon_file'];
              $ext = pathinfo($icon, PATHINFO_EXTENSION);
              $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
              $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image .= "/features/migareference/";
              $image_name = $icon;
              if (file_exists($file)) {
                  if (!copy($file, $dir_image . $image_name)) {
                      throw new exception(__('An error occurred while saving. Please try again later.'));
                  } else {
                      $cover = $icon;
                  }
              } else {
                $cover = $icon;
              }
          }
          // Normal Status
          if ($data['push_notification_to_user']==1 || $data['push_notification_to_user']==2) {
            $is_push_ref=1;
            $cover = intval($data['migareference_remove_cover']) ? '' : '';
            if(!empty($data['c_migareference_cover_file']) && !intval($data['migareference_remove_cover'])) {
                $cover = $data['c_migareference_cover_file'];
            }
            if (!empty($data['file']) && !intval($data['migareference_remove_cover'])) {
                $icon = $data['file'];
                $ext = pathinfo($icon, PATHINFO_EXTENSION);
                $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                $dir_image .= "/features/migareference/";
                $image_name = $icon;
                if (file_exists($file)) {
                    if (!copy($file, $dir_image . $image_name)) {
                        throw new exception(__('An error occurred while saving. Please try again later.'));
                    } else {
                        $cover = $icon;
                    }
                } else {
                    $cover = $icon;
                }
            }
          }
          if ($data['push_notification_to_user']==1 || $data['push_notification_to_user']==3) {
            $is_push_agt=1;
            $cover_agt = intval($data['migareference_remove_cover_agt']) ? '' : '';
            if(!empty($data['c_migareference_cover_file_agt']) && !intval($data['migareference_remove_cover_agt'])) {
                $cover_agt = $data['c_migareference_cover_file_agt'];
            }
            if (!empty($data['file_agt']) && !intval($data['migareference_remove_cover_agt'])) {
                $icon = $data['file_agt'];
                $ext = pathinfo($icon, PATHINFO_EXTENSION);
                $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                $dir_image .= "/features/migareference/";
                $image_name = $icon;
                if (file_exists($file)) {
                    if (!copy($file, $dir_image . $image_name)) {
                        throw new exception(__('An error occurred while saving. Please try again later.'));
                    } else {
                        $cover_agt = $icon;
                    }
                } else {
                    $cover_agt = $icon;
                }
            }
          }
          // Reminder Status
          if ($data['is_auto_reminder']) {
            if ($data['reminder_push_notification_to_user']==1 || $data['reminder_push_notification_to_user']==2) {
              $reminder_is_push_ref=1;
              $cover = intval($data['reminder_migareference_remove_cover']) ? '' : '';
              if(!empty($data['reminder_c_migareference_cover_file']) && !intval($data['reminder_migareference_remove_cover'])) {
                  $cover = $data['reminder_c_migareference_cover_file'];
              }
              if (!empty($data['reminder_file']) && !intval($data['reminder_migareference_remove_cover'])) {
                  $icon = $data['reminder_file'];
                  $ext = pathinfo($icon, PATHINFO_EXTENSION);
                  $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                  $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                  if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                  if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                  if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                  $dir_image .= "/features/migareference/";
                  $image_name = $icon;
                  if (file_exists($file)) {
                      if (!copy($file, $dir_image . $image_name)) {
                          throw new exception(__('An error occurred while saving. Please try again later.'));
                      } else {
                        // throw new exception(__('An error occurred while saving. Please try again later.'.$dir_image . $image_name));
                          $reminder_cover = $icon;
                      }
                  } else {
                      $reminder_cover = $icon;
                  }
              }
            }
            if ($data['reminder_push_notification_to_user']==1 || $data['reminder_push_notification_to_user']==3) {
              $reminder_is_push_agt=1;
              $cover_agt = intval($data['reminder_migareference_remove_cover_agt']) ? '' : '';
              if(!empty($data['reminder_c_migareference_cover_file_agt']) && !intval($data['reminder_migareference_remove_cover_agt'])) {
                  $cover_agt = $data['reminder_c_migareference_cover_file_agt'];
              }
              if (!empty($data['reminder_file_agt']) && !intval($data['reminder_migareference_remove_cover_agt'])) {
                  $icon = $data['reminder_file_agt'];
                  $ext = pathinfo($icon, PATHINFO_EXTENSION);
                  $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                  $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                  if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                  if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                  if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                  $dir_image .= "/features/migareference/";
                  $image_name = $icon;
                  if (file_exists($file)) {
                      if (!copy($file, $dir_image . $image_name)) {
                          throw new exception(__('An error occurred while saving. Please try again later.'));
                      } else {
                          $reminder_cover_agt = $icon;
                      }
                  } else {
                      $reminder_cover_agt = $icon;
                  }
              }
            }
          }
          $datas = [
              "app_id"          => $app_id,
              "value_id"        => $value_id,
              "event_id"        => $data['event_id'],
              "is_push_ref"     => $is_push_ref,
              "is_push_agt"     => $is_push_agt,
              "ref_push_title"  => $data['ref_push_title'],
              "ref_push_text"   => $data['ref_push_text'],
              "ref_open_feature"=> isset($data['ref_is_feature']) ? $data['ref_is_feature'] : 0,
              "ref_feature_id"  => $data['ref_feature_id'],
              "ref_custom_url"  => $data['ref_custom_url'],
              "ref_cover_image" => $cover,
              "agt_push_title"  => $data['agt_push_title'],
              "agt_push_text"   => $data['agt_push_text'],
              "agt_open_feature"=> isset($data['agt_is_feature']) ? $data['agt_is_feature'] : 0,
              "agt_feature_id"  => $data['agt_feature_id'],
              "agt_custom_url"  => $data['agt_custom_url'],
              "agt_cover_image" => $cover_agt,
              "reminder_is_push_ref"     => $reminder_is_push_ref,
              "reminder_is_push_agt"     => $reminder_is_push_agt,
              "reminder_ref_push_title"  => $data['reminder_ref_push_title'],
              "reminder_ref_push_text"   => $data['reminder_ref_push_text'],
              "reminder_ref_open_feature"=> isset($data['reminder_ref_is_feature']) ? $data['reminder_ref_is_feature'] : 0,
              "reminder_ref_feature_id"  => $data['reminder_ref_feature_id'],
              "reminder_ref_custom_url"  => $data['reminder_ref_custom_url'],
              "reminder_ref_cover_image" => $reminder_cover,
              "reminder_agt_push_title"  => $data['reminder_agt_push_title'],
              "reminder_agt_push_text"   => $data['reminder_agt_push_text'],
              "reminder_agt_open_feature"=> isset($data['reminder_agt_is_feature']) ? $data['reminder_agt_is_feature'] : 0,
              "reminder_agt_feature_id"  => $data['reminder_agt_feature_id'],
              "reminder_agt_custom_url"  => $data['reminder_agt_custom_url'],
              "reminder_agt_cover_image" => $reminder_cover_agt,
          ];
              $datas['created_at'] = date('Y-m-d H:i:s');
              $this->_db->insert("migareference_push_template", $datas);
            return  $migareference_id = $this->_db->lastInsertId();
      }
    public function updatePush($app_id = 0, $data = [])
    {
        $value_id        = $data['value_id'];
        $migareference_id = 0;
        $is_push_ref=0;
        $is_push_agt=0;
        $reminder_is_push_ref=0;
        $reminder_is_push_agt=0;
        // Statusicon
        if (!empty($data['status_icon_file']) && !intval($data['migareference_remove_cover'])) {
            $icon = $data['status_icon_file'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }
        // Normanl
        if ($data['push_notification_to_user']==1 || $data['push_notification_to_user']==2) {
          $is_push_ref=1;
          if(!empty($data['c_migareference_cover_file']) && !intval($data['migareference_remove_cover'])) {
              $cover = $data['c_migareference_cover_file'];
          }
          if (!empty($data['file']) && !intval($data['migareference_remove_cover'])) {
              $icon = $data['file'];
              $ext = pathinfo($icon, PATHINFO_EXTENSION);
              $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
              $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image .= "/features/migareference/";
              $image_name = $icon;
              if (file_exists($file)) {
                  if (!copy($file, $dir_image . $image_name)) {
                      throw new exception(__('An error occurred while saving. Please try again later.'));
                  } else {
                      $cover = $icon;
                  }
              } else {
                  $cover = $icon;
              }
          }
        }
        if ($data['push_notification_to_user']==1 || $data['push_notification_to_user']==3) {
          $is_push_agt=1;
          if(!empty($data['c_migareference_cover_file_agt']) && !intval($data['migareference_remove_cover_agt'])) {
              $cover_agt = $data['c_migareference_cover_file_agt'];
          }
          if (!empty($data['file_agt']) && !intval($data['migareference_remove_cover_agt'])) {
              $icon = $data['file_agt'];
              $ext = pathinfo($icon, PATHINFO_EXTENSION);
              $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
              $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
              if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
              if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
              if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
              $dir_image .= "/features/migareference/";
              $image_name = $icon;
              if (file_exists($file)) {
                  if (!copy($file, $dir_image . $image_name)) {
                      throw new exception(__('An error occurred while saving. Please try again later.'));
                  } else {
                      $cover_agt = $data['file_agt'];
                  }
              } else {
                  $cover_agt = $data['file_agt'];
              }
          }
        }
        //Reminder
        if ($data['is_auto_reminder']) {
          if ($data['reminder_push_notification_to_user']==1 || $data['reminder_push_notification_to_user']==2) {
            $reminder_is_push_ref=1;
            $cover = intval($data['reminder_migareference_remove_cover']) ? '' : '';
            if(!empty($data['reminder_c_migareference_cover_file']) && !intval($data['reminder_migareference_remove_cover'])) {
                $cover = $data['reminder_c_migareference_cover_file'];
            }
            if (!empty($data['reminder_file']) && !intval($data['reminder_migareference_remove_cover'])) {
                $icon = $data['reminder_file'];
                $ext = pathinfo($icon, PATHINFO_EXTENSION);
                $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                $dir_image .= "/features/migareference/";
                $image_name = $icon;
                if (file_exists($file)) {
                    if (!copy($file, $dir_image . $image_name)) {
                        throw new exception(__('An error occurred while saving. Please try again later.'));
                    } else {
                        $reminder_cover = $icon;
                    }
                } else {
                    $reminder_cover = $icon;
                }
            }
          }
          if ($data['reminder_push_notification_to_user']==1 || $data['reminder_push_notification_to_user']==3) {
            $reminder_is_push_agt=1;
            $reminder_cover_agt = intval($data['reminder_migareference_remove_cover_agt']) ? '' : '';
            if(!empty($data['reminder_c_migareference_cover_file_agt']) && !intval($data['reminder_migareference_remove_cover_agt'])) {
                $reminder_cover_agt = $data['reminder_c_migareference_cover_file_agt'];
            }
            if (!empty($data['reminder_file_agt']) && !intval($data['reminder_migareference_remove_cover_agt'])) {
                $icon = $data['reminder_file_agt'];
                $ext = pathinfo($icon, PATHINFO_EXTENSION);
                $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
                $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
                if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
                if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
                $dir_image .= "/features/migareference/";
                $image_name = $icon;
                if (file_exists($file)) {
                    if (!copy($file, $dir_image . $image_name)) {
                        throw new exception(__('An error occurred while saving. Please try again later.'));
                    } else {
                        $reminder_cover_agt = $icon;
                    }
                } else {
                    $reminder_cover_agt = $icon;
                }
            }
          }
        }
        $datas = [
            "app_id" => $app_id,
            "value_id" => $value_id,
            "is_push_ref" => $is_push_ref,
            "is_push_agt" => $is_push_agt,
            "ref_push_title" => $data['ref_push_title'],
            "ref_push_text" => $data['ref_push_text'],
            "ref_open_feature" => isset($data['ref_is_feature']) ? $data['ref_is_feature'] : 0,
            "ref_feature_id" => $data['ref_feature_id'],
            "ref_custom_url" => $data['migareference_custom_url_agt'],
            "agt_push_title" => $data['agt_push_title'],
            "agt_push_text" => $data['agt_push_text'],
            "agt_open_feature" => isset($data['agt_is_feature']) ? $data['agt_is_feature'] : 0,
            "agt_feature_id" => $data['agt_feature_id'],
            "agt_custom_url" => $data['agt_custom_url'],
            "reminder_is_push_ref"     => $reminder_is_push_ref,
            "reminder_is_push_agt"     => $reminder_is_push_agt,
            "reminder_ref_push_title"  => $data['reminder_ref_push_title'],
            "reminder_ref_push_text"   => $data['reminder_ref_push_text'],
            "reminder_ref_open_feature"=> isset($data['reminder_ref_is_feature']) ? $data['reminder_ref_is_feature'] : 0,
            "reminder_ref_feature_id"  => $data['reminder_ref_feature_id'],
            "reminder_ref_custom_url"  => $data['reminder_ref_custom_url'],
            "reminder_agt_push_title"  => $data['reminder_agt_push_title'],
            "reminder_agt_push_text"   => $data['reminder_agt_push_text'],
            "reminder_agt_open_feature"=> isset($data['reminder_agt_is_feature']) ? $data['reminder_agt_is_feature'] : 0,
            "reminder_agt_feature_id"  => $data['reminder_agt_feature_id'],
            "reminder_agt_custom_url"  => $data['reminder_agt_custom_url'],
        ];
        if (!empty($data['reminder_file'])) {
          $datas['reminder_ref_cover_image']=$reminder_cover;
        }
        if (!empty($data['reminder_file_agt'])) {
          $datas['reminder_agt_cover_image']=$reminder_cover_agt;
        }
        if (!empty($data['file'])) {
          $datas['ref_cover_image']=$cover;
        }
        if (!empty($data['file_agt'])) {
          $datas['agt_cover_image']=$cover_agt;
        }
        if ($data['migareference_remove_cover']==1) {
          $datas['ref_cover_image']="";
        }
        if ($data['migareference_remove_cover_agt']==1) {
          $datas['agt_cover_image']="";
        }
            $id=$data['migareference_push_template_id'];
            $datas['updated_at']  = date('Y-m-d H:i:s');
            $this->_db->update("migareference_push_template", $datas,['migareference_push_template_id = ?' => $id]);
            return $datas;
    }
      public function updatePushconstant($app_id = 0, $data = [])
      {
          $datas = [
              "app_id" => $app_id,
              "value_id" => $data['value_id'],
              "is_push_ref" => $data['is_push_ref'],
              "is_push_agt" => $data['is_push_agt'],
              "ref_push_title" => $data['ref_push_title'],
              "ref_push_text" => $data['ref_push_text'],
              "ref_open_feature" => isset($data['ref_is_feature']) ? $data['ref_is_feature'] : 0,
              "ref_feature_id" => $data['ref_feature_id'],
              "ref_custom_url" => $data['migareference_custom_url_agt'],
              "agt_push_title" => $data['agt_push_title'],
              "agt_push_text" => $data['agt_push_text'],
              "agt_open_feature" => isset($data['agt_is_feature']) ? $data['agt_is_feature'] : 0,
              "agt_feature_id" => $data['agt_feature_id'],
              "agt_custom_url" => $data['agt_custom_url'],
              "ref_cover_image" => $data['ref_cover_image'],
              "agt_cover_image" => $data['agt_cover_image'],
              "reminder_is_push_ref"     => $data['reminder_is_push_ref'],
              "reminder_is_push_agt"     => $data['reminder_is_push_agt'],
              "reminder_ref_push_title"  => $data['reminder_ref_push_title'],
              "reminder_ref_push_text"   => $data['reminder_ref_push_text'],
              "reminder_ref_open_feature"=> isset($data['reminder_ref_is_feature']) ? $data['reminder_ref_is_feature'] : 0,
              "reminder_ref_feature_id"  => $data['reminder_ref_feature_id'],
              "reminder_ref_custom_url"  => $data['reminder_ref_custom_url'],
              "reminder_ref_cover_image" => $data['reminder_ref_cover_image'],
              "reminder_agt_push_title"  => $data['reminder_agt_push_title'],
              "reminder_agt_push_text"   => $data['reminder_agt_push_text'],
              "reminder_agt_open_feature"=> isset($data['reminder_agt_is_feature']) ? $data['reminder_agt_is_feature'] : 0,
              "reminder_agt_feature_id"  => $data['reminder_agt_feature_id'],
              "reminder_agt_custom_url"  => $data['reminder_agt_custom_url'],
              "reminder_agt_cover_image" => $data['reminder_agt_cover_image'],
          ];

              $id=$data['migareference_push_template_id'];
              $datas['updated_at']  = date('Y-m-d H:i:s');
              $this->_db->update("migareference_push_template", $datas,['migareference_push_template_id = ?' => $id]);
              return $id;
      }
      public function defaultPreSettings($app_id=0,$value_id=0,$type="")
      {
        $data=[
            "app_id" => $app_id,
            "value_id" => $value_id,
            "reward_type" => 1,
            "commission_type" => 2,
            "fix_commission_amount" => 500,
            "payable_limit" => 5000,
            "read_only" =>2,
            "is_unique_mobile" => 1,
            "grace_days" => 90,
            "notification_type" =>1,
            "privacy" =>"<p>&ldquo;L&rsquo;Utente Segnalatore dichiara di essere stato autorizzato dal Soggetto Segnalato a trasmettere i Dati Personali di quest&rsquo;ultimo a il Titolare e manleva il Titolare da qualsiasi pretesa se del caso avanzata dal Soggetto Segnalato nei confronti del Titolare a titolo di danno. In conformit&agrave; al d. lgs. 30 giugno 2004, 196 e del Regolamento UE 679/2016 l&rsquo;Utente Segnalatore acconsente al trattamento dei propri e dei Dati Personali del Soggetto Segnalato nei limiti dell&rsquo;Informativa sul trattamento dei Dati Personali (l&rsquo;&rdquo;<strong>Informativa</strong>&rdquo;), nonch&eacute; alla comunicazione degli stessi ai soggetti indicati nell&rsquo;Informativa, che dichiara di aver ricevuto, nonch&eacute; al loro trattamento da parte dei soggetti destinatari della comunicazione.&rdquo;</p>",
            "terms" =>"<p><strong>Condizioni generali applicabili al contratto fra Utente Segnalatore e il Titolare</strong></p><p>&nbsp;</p><ol><li><ol>
		<li>Le presenti condizioni generali (di seguito &ldquo;CG&rdquo;) disciplinano il rapporto fra Immobiliare Scandiano (di seguito &ldquo;il Titolare&rdquo;) e l&rsquo;utente (di seguito l&rsquo;&ldquo;Utente Segnalatore&rdquo;) che ha scaricato l&rsquo;applicazione &ldquo;Immobiliare Scandiano&rdquo; (di seguito &ldquo;App&rdquo;) al fine di effettuare segnalazioni di potenziali venditori di immobili (di seguito, le &ldquo;Segnalazioni&rdquo;), in modo occasionale e saltuario, con organizzazione, gestione e rischio a carico dell&rsquo;Utente Segnalatore, essendo questi soggetto economico indipendente, al di fuori di qualsiasi vincolo di stabilit&agrave;, di subordinazione o di coordinamento con, e senza assumere alcun rapporto di subordinazione, parasubordinazione, coordinamento o di agenzia, nonch&eacute; in maniera occasionale e senza alcun vincolo di continuit&agrave; e costanza.&nbsp;</li>
		<li>Il contratto si conclude esclusivamente attraverso la rete internet, mediante la registrazione dell&rsquo;Utente Segnalatore dell&rsquo;App e la compilazione delle sezioni secondo la procedura prevista dall&rsquo;App stessa e la sua accettazione da parte del Titolare. La Segnalazione trasmessa dall&rsquo;Utente Segnalatore mediante l&rsquo;App ha valore di proposta contrattuale ed &egrave; regolata dalle presenti CG che, l&rsquo;Utente Segnalatore &egrave; tenuto ad accettare integralmente e senza riserva alcuna. A tal fine, l&rsquo;Utente Segnalatore, prima di procedere alla Segnalazione, s&rsquo;impegna a prendere visione delle presenti CG e delle informazioni fornite nell&rsquo;App. Il contratto si perfeziona con l&#39;invio da parte del Titolare al Cliente di un&#39;e-mail di conferma dell&#39;ordine. In particolare, l&rsquo;Utente Segnalatore dovr&agrave; preliminarmente: a) compilare il modulo contenuto nella sezione &ldquo;IMPOSTAZIONI&rdquo;, esplicitando il tipo di notifiche che intende riceve fra &ldquo;notifiche push&rdquo;, &ldquo;notifiche e-mail&rdquo; o entrambe; b) individuare una password alfanumerica per consentire la funzionalit&agrave; blockchain che garantisce all&rsquo;Utente Segnalatore la possibilit&agrave; di registrare la data e l&rsquo;ora in cui saranno effettuate le sue Segnalazioni, anche ai fini di cui all&rsquo;art. 13 delle presenti CG; c) compilare la sezione anagrafica; d) prendere visione dell&rsquo;Informativa sulla Privacy e delle CG e prestare i consensi richiesti. In seguito, ogni Segnalazione verr&agrave; effettuata accedendo alla sezione &ldquo;INVIA SEGNALAZIONE&rdquo; e completando i campi obbligatori relativi ai dati del Soggetto Segnalato e allo specifico immobile che vuole vendere, identificato attraverso tipologia e indirizzo (di seguito, l&rsquo; &ldquo;Immobile&rdquo;). L&rsquo;Utente Segnalatore s&rsquo;impegna a verificare la correttezza dei dati personali inseriti nell&rsquo;App e a comunicare tempestivamente a il Titolare eventuali correzioni. Il contratto si perfeziona con l&#39;invio da parte del Titolare all&rsquo;Utente Segnalatore di una notifica a mezzo e-mail o push di conferma della ricezione della Segnalazione con indicazione della relativa fase (di seguito, &ldquo;Fase&rdquo;) conformemente a quanto previsto nel punto 3 che segue.&nbsp;</li>
		<li>L&rsquo;Utente Segnalatore prende atto, accettandone il funzionamento, che successivamente a ogni Segnalazione da parte dell&rsquo;Utente Segnalatore, nella sezione &ldquo;STATO SEGNALAZIONE&rdquo; l&rsquo;App dar&agrave; evidenza delle Fasi, successive ed eventuali: Fase I, denominata &ldquo;<strong>Attesa Contatto</strong>&rdquo;, evidenzia l&rsquo;arco temporale fra il ricevimento della Segnalazione da parte del Titolare e il contatto diretto fra quest&rsquo;ultimo e il potenziale venditore dell&rsquo;Immobile (di seguito &ldquo;Soggetto Segnalato&rdquo;); Fase II, denominata <em>alternativamente</em> &ldquo;<strong>Appuntamento</strong>&rdquo;, ove il Soggetto Segnalato abbia confermato la propria intenzione di vendere un immobile e abbia preso appuntamento per un incontro e/o sopralluogo con il Titolare, oppure &ldquo;<strong>Rinuncia</strong>&rdquo;, laddove il Soggetto Segnalato abbia declinato per qualsiasi ragione e senza necessit&agrave; di giusta causa l&rsquo;intenzione di procedere nel servizio; Fase III: denominata <em>alternativamente </em>&ldquo;<strong>Mandato Acquisito</strong>&rdquo;, ove il Soggetto Segnalato abbia conferito a il Titolare mandato a vendere l&rsquo;immobile di propriet&agrave;, oppure &ldquo;<strong><em>Rinuncia</em></strong>&rdquo;, laddove il Soggetto Segnalato abbia declinato per qualsiasi ragione e senza necessit&agrave; di giusta causa l&rsquo;intenzione di conferire a il Titolare mandato a vendere l&rsquo;immobile di propriet&agrave;.</li>
		<li>Le presenti CG non attribuiscono all&rsquo;Utente Segnalatore alcun potere di rappresentanza del Titolare. L&rsquo;Utente Segnalatore non dovr&agrave; qualificarsi, e non potr&agrave; essere considerato, come un agente e/o un rappresentante del Titolare e non potr&agrave; in alcun modo assumere impegni in nome e per conto del Titolare. Resta espressamente ferma la circostanza secondo la quale tutte le eventuali trattative e la successiva conclusione dei contratti sono esclusivamente riservate a il Titolare.</li>
		<li>All&rsquo;Utente Segnalatore non &egrave; assegnato alcun territorio di operativit&agrave; per l&rsquo;esercizio dell&rsquo;attivit&agrave; che intender&agrave; svolgere; la stessa potr&agrave; quindi estendersi a tutte le zone e/o territori in cui l&rsquo;Utente Segnalatore riterr&agrave; opportuno espletare liberamente ed autonomamente la propria opera.</li>
		<li>L&rsquo;Utente Segnalatore non potr&agrave; in nessun caso incassare somme per conto del Titolare.</li>
		<li>L&rsquo;Utente Segnalatore si impegna a non effettuare Segnalazioni di identico contenuto a soggetti in rapporto di concorrenza con il Titolare (di seguito &ldquo;Segnalazioni Multiple&rdquo;).&nbsp;</li>
		<li>Con il presente accordo non viene riconosciuta alcuna esclusiva all&rsquo;Utente Segnalatore che &egrave; libero di condurre i propri affari in modo autonomo ed indipendente.</li>
		<li>Il corrispettivo spettante all&rsquo;Utente Segnalatore (di seguito, il &ldquo;Corrispettivo&rdquo;) per ogni Segnalazione andate a buon fine e conclusasi con la vendita dell&rsquo;Immobile &egrave; fissato in una somma pari a &euro; 500,00 (cinquecento) lordi, oltre Iva nella misura di legge, se dovuta e soggetta a ritenuta alla fonte se dovuta.</li>
		<li>10. Nel Corrispettivo saranno compresi tutti gli oneri e le spese derivanti dall&rsquo;attivit&agrave; di Segnalazione, nessuno escluso.&nbsp;</li>
		<li>I pagamenti dell&rsquo;eventuale Corrispettivo sono <strong>subordinati alla regolare esecuzione del contratto fra il Soggetto Segnalato e il Titolare, </strong>essendo espressamente pattuito che solo successivamente alla vendita dell&rsquo;Immobile da parte del Soggetto Segnalato, esplicitato nell&rsquo;App con la dicitura &ldquo;<strong>Immobile Venduto</strong>&rdquo; cui abbia fatto seguito il pagamento della provvigione dovuta da parte del Soggetto Segnalato e il Titolare, esplicitato nell&rsquo;App con la dicitura &ldquo;<strong>Pagabile</strong>&rdquo; (le &ldquo;Condizioni&rdquo;), l&rsquo;Utente Segnalatore avr&agrave; diritto al Corrispettivo. In caso di mancato avveramento delle Condizioni, l&rsquo;Utente Segnalatore non avr&agrave; alcun diritto verso il Titolare per provvigioni, indennizzi di sorta o risarcimenti.</li>
		<li>I Compensi saranno corrisposti all&rsquo;Utente Segnalatore al netto di eventuali ritenute alla fonte o di acconti ed eventualmente con emissione di regolari fatture ove richiesto dall&rsquo;Utente Segnalatore, da emettersi entro 30giorni dalla sottoscrizione del contratto di compravendita innanzi al Notaio rogante.</li>
		<li>In caso di vendita di un Immobile da parte di un soggetto segnalato che sia stato oggetto di pi&ugrave; Segnalazioni da parte di pi&ugrave; Utenti Segnalatori diversi, il Compenso sar&agrave; riconosciuto all&rsquo;Utente Segnalatore che per primo ha effettuato la Segnalazione. A tal fine l&rsquo;App &egrave; integrata con una funzionalit&agrave; blockchain, al fine di validare e notarizzare il dato su blockchain Ethereum, garantendo certezza in ordine alla priorit&agrave; di una data Segnalazione rispetto ad un&rsquo;eventuale altra di identico contenuto. In particolare, per la notarizzazione &egrave; utilizzato il sistema <a href='http://www.migachain.com'>www.migachain.com</a>, che consente di ricevere pi&ugrave; volte al giorno copia del registro delle Segnalazioni e di salvarla nella rete distribuita IPFS. L&rsquo;algoritmo HASH256 consente quindi di calcolare una firma univoca del file, che viene a sua volta salvata in modo indellebile in un blocco della blockchain Ethereum.<br />Ogni transazione nell&rsquo;App riporta un certificato che identifica il codice HASH256, l&rsquo;indirizzo ETHEREUM dove questo &egrave; salvato e il link IPFS dover poter reperire il file online. L&rsquo;Utente Segnalatore potr&agrave; autonomamente scaricare il file di backup del libro mastro, verificare che la sua firma corrisponda all&rsquo;HASH256 presente nel blocco Ehtereum (verificabile tramite etherscan.io) e ottenere prova certa dell&rsquo;esistenza della Segnalazione.</li>
		<li>Qualora il Titolare decida in piena autonomia e discrezionalit&agrave; di non addivenire all&rsquo;acquisizione di una o pi&ugrave; Segnalazioni, nulla sar&agrave; dovuto all&rsquo;Utente Segnalatore, nemmeno a titolo di rimborso delle spese eventualmente sostenute.&nbsp;</li>
		<li>il Titolare si riserva la facolt&agrave; di risolvere di diritto ogni rapporto con l&rsquo;Utente Segnalatore, con conseguente immediata cancellazione del relativo account sull&rsquo;App e venir meno del diritto al Compenso, anche per Segnalazioni gi&agrave; effettuate e conclusesi con la vendita dell&rsquo;Immobile nei seguenti casi: a) qualsiasi comportamento contrario alla normativa applicabile o non conforme a correttezza e buona fede sia nei rapporti con i Soggetti Segnalati sia nei rapporti con il Titolare (a mero titolo di esempio, Segnalazioni aventi ad oggetto persone sconosciute rintracciate sul web); b) Segnalazioni Multiple; e c) qualora l&rsquo;Utente Segnalatore intraprenda una qualsiasi iniziativa e/o attivit&agrave; idonea a recare, anche soltanto potenzialmente, danno economico e/o di immagine a il Titolare.</li>
		<li>E&rsquo; comunque in facolt&agrave; del Titolare recedere in qualunque momento dal presente accordo, a proprio insindacabile giudizio corrispondendo solo ed esclusivamente i Compensi pattuiti e maturati sino a quel momento. Tale decisione non produrr&agrave; alcun diritto dell&rsquo;Utente Segnalatore al risarcimento del danno, il quale rinuncia fin da ora ai relativi diritti e azioni.</li>
		<li>il Titolare non assume alcuna responsabilit&agrave; per disservizi imputabili a causa di forza maggiore o caso fortuito, anche ove dipendenti da malfunzionamenti e disservizi della rete internet, che impediscano il completamento della procedura di Segnalazione.</li>
		<li>L&rsquo;Utente Segnalatore dichiara di essere stato autorizzato dal Soggetto Segnalato a trasmettere i Dati Personali di quest&rsquo;ultimo a il Titolare e manleva il Titolare da qualsiasi pretesa se del caso avanzata dal Soggetto Segnalato nei confronti del Titolare a titolo di danno. In conformit&agrave; al d. lgs. 30 giugno 2004, 196 e del Regolamento UE 679/2016 l&rsquo;Utente Segnalatore acconsente al trattamento dei propri e dei Dati Personali del Soggetto Segnalato nei limiti dell&rsquo;Informativa sul trattamento dei Dati Personali (l&rsquo;&rdquo;<strong>Informativa</strong>&rdquo;), nonch&eacute; alla comunicazione degli stessi ai soggetti indicati nell&rsquo;Informativa, che dichiara di aver ricevuto, nonch&eacute; al loro trattamento da parte dei soggetti destinatari della comunicazione.</li>
		<li>Le presenti CG sono disciplinate, nella loro interezza, dalla Legge Italiana.</li>
		<li>Qualsiasi controversia dovesse insorgere il Titolare e l&rsquo;Utente Segnalatore in ordine alla interpretazione, esecuzione del rapporto e che non trovino un bonario componimento saranno di competenza esclusiva del Foro di Bologna, rinunciando le Parti a qualsiasi altro foro concorrente. &nbsp;</li></ol></li></ol>",
            "term_label_text" => "Approvazione espressa",
            "special_terms" =>"<p>Ai sensi e per gli effetti di cui all&#39;art. 1341 Cod. Civ. l&rsquo;Utente Segnalatore dichiara di approvare espressamente le previsioni contrattuali di seguito indicate: <strong>1, 7, 9, 10, 11, 12, 14, 15, 16, 17, 18 e 19.</strong></p>",
            "enable_company" =>2,
            "mandatory_company" =>2,
            "enable_legal_address" =>2,
            "mandatory_legal_address" =>2,
            "extra_one" =>2,
            "mandatory_extra_one" =>2,
            "extra_two" =>2,
            "mandatory_extra_two" =>2,
            "is_visible_invite_prospectus" => 1,
            "is_visible_submit_report" => 2,
            "referrer_wellcome_email_title" => 'Complimenti a te @@user_name@@ sei un nostro segnalatore!',
            "referrer_wellcome_email_body" => 'Ciao @@user_name@@,Registrandoti nella nostra APP come Segnalatore sei ora parte del nostro Club di Partners e ti aspettano importanti provvigioni per ogni segnalazione andata a buon fine.I dati di accesso allAPP sono questi:user: @@user_email@@Pass: @@user_firstpassword@@Link APP: @@app_link@@Non esitare a metterti in contatto con noi per qualsiasi dubbio o anche per un semplice aiuto. Sentiamo decine di persone ogni giorno e abbiamo una grande cerchia di influenza.Al tuo successoCustomer Service',
            "invite_message" => "Ciao sono @@referrer_name@@, ecco il link dove puoi richiedere il contatto @@landing_lin@@",
            "confirm_report_privacy_label" => "Please read and accept the accept the privacy statement.",
            "authorized_call_back_label" => "I authorized be call back for commercial purpose.",
            "owner_hot_label" =>"How hot is the contact",
            "landing_pg_header_txt" => "Add Report",
            "created_at" =>date('Y-m-d H:i:s')
        ];
        if ($type=="create") {
          $this->_db->insert("migareference_pre_report_settings", $data);
        }else{
          $datas['updated_at']  = date('Y-m-d H:i:s');
          $this->_db->update("migareference_pre_report_settings", $data,['app_id = ?' => $app_id]);
        }
      }
      public function getMissingPhonebookEntries($app_id){
        $query="SELECT i.*,customer.email
        FROM migareference_invoice_settings i
        JOIN customer on customer.customer_id=i.user_id
        LEFT JOIN migarefrence_phonebook p ON i.migareference_invoice_settings_id = p.invoice_id
        WHERE p.invoice_id IS NULL AND i.app_id = $app_id";
        return $this->_db->fetchAll($query);
      }
      public function mergePhonebookWithInvoice() {
        // This function merges the phonebook with the invoice table
        //Fetach all presettings data to indentify list of apps
        //Fetch all missing invoice entries missing in phonebook per app
        //Save all new entries in phonebook
        $migareference = new Migareference_Model_Db_Table_Migareference();
        $query_option = "SELECT * FROM `migareference_pre_report_settings` WHERE  1";
        $preSettings   = $this->_db->fetchAll($query_option);
        if (count($preSettings)) {
          foreach ($preSettings as $setting) {
            $app_id = $setting['app_id'];
            $missingEntries = $migareference->getMissingPhonebookEntries($app_id);
            if (count($missingEntries)) {
              foreach ($missingEntries as $entry) {
                $migareference->findPhonebookItem($entry['migareference_invoice_settings_id'], $entry);
              }
            }
          }
        }
      }
      public static function cronNotification()
      {
         $migareference = new Migareference_Model_Db_Table_Migareference();
        // START: External method to manage WEBHOOK Error Notification etc.
         $currentMinute = intval(date('i'));
        if ($currentMinute % 5 == 0) {
          // $migareference->retryFailedWebhooks();
        }
        // END: External method to manage WEBHOOK Error Notification etc.
         $default          = new Core_Model_Default();
         $base_url         = $default->getBaseUrl();
         $host_name        =  gethostname();
         $host_ip          = shell_exec('nslookup ' . $host_name);
         /*Merge Invoice table with phonebook
         *for old there are some entries are not in phonebook that casuse the issue in stats
          *so we need to merge the phonebook with invoice table
          */
          $migareference->mergePhonebookWithInvoice(); 
         // Reminder Notifactions
         $report_remiders = $migareference->getcronReminders();
         $notificationTags= [
                              "@@report_owner@@",
                              "@@report_owner_phone@@",
                              "@@report_no@@",
                              "@@time_reminder@@",
                              "@@referral_name@@",
                              "@@referral_phone@@",
                              "@@referral_email@@",
                              "@@commission@@",
                              "@@agent_name@@",
                              "@@comment@@",
                            ];
         foreach ($report_remiders as $remider_item) {
           $reminder_date_time = $remider_item['reminder_date_time'];
           $auto_rem_type      = $remider_item['event_type'];
           $app_id             = $remider_item['app_id'];
           $reminder_id        = $remider_item['migarefrence_reminders_id'];
           $report_id          = $remider_item['report_id'];
           $sponsor_id         = $remider_item['sponsor_id'];
           $now_date_time      = date('Y-m-d H:i:s');
           $autom_log['app_id']          = $app_id;
           $autom_log['reminder_id']     = $reminder_id;
           $autom_log['report_id']       = $report_id;
           $application    = $migareference->application($app_id);
            $app_name       = $application[0]['name'];                                        
            // Mail Footer                
            $footer="<br><br><small><small><small>Sender: App ID ".$app_id." APP Name: ".$app_name." DOMAIN: ".$base_url." IP: ".$_SERVER['SERVER_ADDR']."</small></small></small>";      
           if ($now_date_time>=$reminder_date_time) {
             $reminder_type= $migareference->getSingleReminderType($auto_rem_type);
             $admin_users  = $migareference->getAdminCustomers($app_id);
             $agent_user   = $migareference->is_agent($app_id,$sponsor_id);
             $find_in_log  = $migareference->findInRepoRemLog($reminder_id);
            if (count($reminder_type) && count($admin_users) && !count($find_in_log)) {
              $notificationStrings = [
                $remider_item['owner_name']." ".$remider_item['owner_surname'],
                $remider_item['owner_mobile'],
                $remider_item['report_no'],
                $remider_item['event_date_time'],
                $remider_item['invoice_name']." ".$remider_item['invoice_surname'],
                $remider_item['mobile'],
                $remider_item['invoice_mobile'],
                $remider_item['commission_fee'],
                "",
                ""
              ];
              if (count($agent_user)) {
                $user_id                   = $agent_user[0]['user_id'];
                $autom_log['user_id']      = $user_id;
                $autom_log['receipent']    = "Agent";

                $reminder_type[0]['rep_rem_email_title'] = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_email_title']);
                $reminder_type[0]['rep_rem_email_text']  = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_email_text']);
                $reminder_type[0]['rep_rem_push_title']  = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_push_title']);
                $reminder_type[0]['rep_rem_push_text']   = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_push_text']);

                $autom_log['email_log_id'] = $migareference->sendAutomationMail($reminder_type[0],$app_id,$agent_user[0],$footer);
                $autom_log['push_log_id']  = $migareference->sendAutomationPush($reminder_type[0],$app_id,$user_id);

                $migareference->saveRepoRemLog($autom_log);
                $updaetReminder['reminder_current_status']="Done";//For Done
                $migareference->update_reminder($reminder_id,$updaetReminder);
              }else {
                  foreach ($admin_users as $admin) {
                  $user_id                   = $admin['customer_id'];
                  $autom_log['user_id']      = $user_id;
                  $autom_log['receipent']    = "Admin";
                  $reminder_type[0]['rep_rem_email_title'] = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_email_title']);
                  $reminder_type[0]['rep_rem_email_text']  = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_email_text']);
                  $reminder_type[0]['rep_rem_push_title']  = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_push_title']);
                  $reminder_type[0]['rep_rem_push_text']   = str_replace($notificationTags, $notificationStrings,$reminder_type[0]['rep_rem_push_text']);
                  //*
                  $autom_log['email_log_id'] = $migareference->sendAutomationMail($reminder_type[0],$app_id,$admin,$footer);
                  $autom_log['push_log_id']  = $migareference->sendAutomationPush($reminder_type[0],$app_id,$user_id);

                  $migareference->saveRepoRemLog($autom_log);
                  $updaetReminder['reminder_current_status']="Done";//For Done
                  $migareference->update_reminder($reminder_id,$updaetReminder);
                }
              }
            }
           }
         }
         // END REMINDER Notifaction
         $data          = $migareference->getDelaysnotification();
         foreach ($data as $key => $value) {
           // Set log fields
          $agent_user_id               = $value['last_modification_by'];//The user who last time update the report as Agent
          $push_log_data['user_id']    = 99999;
          $push_log_data['log_type']   = "Push Notification sent";
          $push_log_data['log_detail'] = "Status change Notification";
          $email_log_data['user_id']   = 99999;
          $email_log_data['log_type']  = "Email Notification sent";
          $email_log_data['log_detail']= "Status change Notification";
          // Count Hours
          $date1 = new DateTime($value['trigger_start_time']);
          $date2 = new DateTime(date('Y/m/d H:i:s'));
          $diff  = $date2->diff($date1);
          $hours = $diff->h;
          $hours = $hours + ($diff->days*24);
          // Send Email Notifications
          if ($value['email_delay']>0 && $hours>=$value['email_delay']) {
             $admin_customers    = $migareference->getAdminCustomers($value['app_id']);//Admin Users->Agents
             $agent_data         = $migareference->getAgentdata($value['user_id']);//Who update the report
             $referral_customers = $migareference->getrefreluserName($value['app_id'],$value['user_id']);//Admin Users->Agents
             //Send to Refferal / User who add Report
               // Subject
                 $email_data['email_title']= str_replace("@@referral_name@@",$referral_customers[0]['firstname'],$value['agt_email_title']);
                 $email_data['email_title']= str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_title']);
                 $email_data['email_title']= str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_title']);
                 $email_data['email_title']= str_replace("@@report_owner_phone@@",$value['owner_mobile'],$email_data['email_title']);
                 $email_data['email_title']= str_replace("@@property_owner_phone@@",$value['owner_mobile'],$email_data['email_title']);
                 $email_data['email_title']= str_replace("@@report_no@@",$data['report_no'],$email_data['email_title']);
               //Message
                 $email_data['email_text'] = str_replace("@@referral_name@@",$referral_customers[0]['firstname'],$value['agt_email_text']);
                 $email_data['email_text'] = str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@report_owner_phone@@",$value['owner_mobile'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@property_owner_phone@@",$value['owner_mobile'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@report_no@@",$value['report_no'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@comment@@",$value['comment'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@commission@@",$value['commission_fee'],$email_data['email_text']);
                 $email_data['email_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$email_data['email_text']);
                 if ($value['is_email_ref']) {
                   $mail_retur = $migareference->sendMail($email_data,$value['app_id'],$value['user_id']);
                   $migareference->saveLog($push_log_data);
                   $migareference->updateCronnotifiaction($value['migareference_cron_notifications_id'],0,1);
                 }
            //Send to Agents / User who set as Afmin
              // Subject
                $email_data['email_title']= str_replace("@@referral_name@@",$value['firstname'],$value['agt_email_title']);
                $email_data['email_title']= str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_title']);
                $email_data['email_title']= str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_title']);
                $email_data['email_title']= str_replace("@@report_owner_phone@@",$value['owner_mobile'],$email_data['email_title']);
                $email_data['email_title']= str_replace("@@property_owner_phone@@",$value['owner_mobile'],$email_data['email_title']);
                $email_data['email_title']= str_replace("@@report_no@@",$data['report_no'],$email_data['email_title']);
              //Message
                $email_data['email_text'] = str_replace("@@referral_name@@",$value['firstname'],$value['agt_email_text']);
                $email_data['email_text'] = str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@report_owner_phone@@",$value['owner_mobile'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@property_owner_phone@@",$value['owner_mobile'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@report_no@@",$value['report_no'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@comment@@",$value['comment'],$email_data['email_text']);
                $email_data['email_text'] = str_replace("@@commission@@",$value['commission_fee'],$email_data['email_text']);
                if ($value['is_email_agt']) {
                  foreach ($admin_customers as $keyy => $valuee) {
                    $email_data['email_title'] = str_replace("@@agent_name@@",$valuee['firstname'],$email_data['email_title']);
                    $email_data['email_text']  = str_replace("@@agent_name@@",$valuee['firstname'],$email_data['email_text']);
                    $mail_retur                = $migareference->sendMail($email_data,$value['app_id'],$valuee['customer_id']);
                    $migareference->saveLog($email_log_data);
                    $migareference->updateCronnotifiaction($value['migareference_cron_notifications_id'],0,1);
                  }
                }
            }
          // Send PUSH Notification
          if ($value['push_delay']>0 && $hours>=$value['push_delay']) {
                   $admin_customers=$migareference->getAdminCustomers($value['app_id']);//Admin Users->Agents
                   $referral_customers=$migareference->getrefreluserName($value['app_id'],$value['user_id']);//Admin Users->Agents
                      //Send to Agents / Admins
                        // Subject
                          $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['firstname'],$value['agt_push_title']);
                          $push_data['push_title']= str_replace("@@report_owner@@",$value['owner_name'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@property_owner@@",$value['owner_name'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@report_owner_phone@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@property_owner_phone@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@report_no@@",$value['report_no'],$push_data['push_title']);
                        //Message
                          $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['firstname'],$value['agt_push_text']);
                          $push_data['push_text'] = str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@report_owner_phone@@",$value['owner_mobile'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@property_owner_phone@@",$value['owner_mobile'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@report_no@@",$value['report_no'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@comment@@",$value['comment'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@commission@@",$value['commission_fee'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@agent_name@@",$agent_data[0]['lastname'],$push_data['push_text']);
                          $push_data['open_feature'] = $value['agt_open_feature'];
                          $push_data['feature_id'] = $value['agt_feature_id'];
                          $push_data['custom_url'] = $value['agt_custom_url'];
                          $push_data['cover_image'] = $value['agt_cover_image'];
                          $push_data['app_id'] = $value['app_id'];
                          if ($value['is_push_agt']) {
                            foreach ($admin_customers as $keyy => $valuee) {
                              $mail_retur = $migareference->sendPush($push_data,$value['app_id'],$valuee['customer_id']);
                              $migareference->saveLog($push_log_data);
                              $migareference->updateCronnotifiaction($value['migareference_cron_notifications_id'],1,0);
                            }
                          }
                      //Send to Refferral / User who add Report
                        // Subject
                          $push_data['push_title']= str_replace("@@referral_name@@",$push_reffreal_user_data[0]['firstname'],$value['ref_push_title']);
                          $push_data['push_title']= str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@report_owner_phone@@",$value['owner_mobile'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@property_owner_phone@@",$value['owner_mobile'],$push_data['push_title']);
                          $push_data['push_title']= str_replace("@@report_no@@",$value['report_no'],$push_data['push_title']);
                        //Message
                          $push_data['push_text'] = str_replace("@@referral_name@@",$push_reffreal_user_data[0]['firstname'],$value['ref_push_text']);
                          $push_data['push_text'] = str_replace("@@report_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@property_owner@@",$value['owner_name']." ".$value['owner_surname'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@report_owner_phone@@",$value['owner_mobile'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@property_owner_phone@@",$value['owner_mobile'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@report_no@@",$value['report_no'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@comment@@",$value['comment'],$push_data['push_text']);
                          $push_data['push_text'] = str_replace("@@commission@@",$value['commission_fee'],$push_data['push_text']);
                          $push_data['open_feature'] = $value['ref_open_feature'];
                          $push_data['feature_id'] = $value['ref_feature_id'];
                          $push_data['custom_url'] = $value['ref_custom_url'];
                          $push_data['cover_image'] = $value['ref_cover_image'];
                          $push_data['app_id'] = $value['app_id'];
                          if ($value['is_push_ref']) {
                           $mail_retur = $migareference->sendPush($push_data,$value['app_id'],$value['user_id']);
                           $migareference->saveLog($push_log_data);
                           $migareference->updateCronnotifiaction($value['migareference_cron_notifications_id'],1,0);
                          }
                      }
        }
      }
      public function checkGcm($customer_id=0,$app_id=0)
      {
        return $this->_db->fetchAll("SELECT * FROM `push_gcm_devices` WHERE `customer_id`=$customer_id AND `app_id`=$app_id");
      }
      public function get_prize_compatible($app_id=0)
      {
        return $this->_db->fetchAll("SELECT migarefrence_ledger.migarefrence_ledger_id as ledger_id,migarefrence_redeemed_prizes.migarefrence_ledger_id as redeem_id
                                     FROM migarefrence_ledger
                                     JOIN migarefrence_redeemed_prizes ON
                                          migarefrence_redeemed_prizes.app_id=migarefrence_ledger.app_id
                                          AND migarefrence_redeemed_prizes.prize_id=migarefrence_ledger.prize_id
                                          AND migarefrence_redeemed_prizes.redeemed_by=migarefrence_ledger.user_id
                                     WHERE migarefrence_ledger.prize_id!=0 AND migarefrence_ledger.app_id=$app_id  AND migarefrence_ledger.self_id IS NULL");
      }
      public function checkApns($customer_id=0,$app_id=0)
      {
        return $this->_db->fetchAll("SELECT * FROM `push_apns_devices` WHERE `customer_id`=$customer_id AND `app_id`=$app_id");
      }
      public function checkGcmtoken($customer_id=0,$app_id=0,$token="")
      {
        return $this->_db->fetchAll("SELECT * FROM `push_gcm_devices` WHERE `customer_id`=$customer_id AND `app_id`=$app_id AND `registration_id`='$token'");
      }
      public function checkApnstoken($customer_id=0,$app_id=0,$token="")
      {
        return $this->_db->fetchAll("SELECT * FROM `push_apns_devices` WHERE `customer_id`=$customer_id AND `app_id`=$app_id AND `device_token`='$token'");
      }
      public function getMaxorder($app_id=0)
      {
        return $this->_db->fetchAll("SELECT MAX(order_id) as order_id FROM `migareference_report_status` WHERE `app_id`=$app_id");
      }
      public function getLatCommunication($phonbok_id=0)
      {
        $res_option=$this->_db->fetchAll("SELECT migareference_communication_logs_id as id,DATE_FORMAT(created_at, '%d-%m-%Y') AS created_at 
                                    FROM `migareference_communication_logs`
                                    WHERE `phonebook_id`=$phonbok_id AND (log_type='Enrollment' OR log_type='Manual' OR log_type='Automation') ORDER BY id DESC LIMIT 1");
      
      if (COUNT($res_option)) {
        $created_at = $res_option[0]['created_at'];
        $formatted_date = date('Y-m-d', strtotime($created_at));        
        $phonebook['last_contact_at'] = $formatted_date;
        $this->_db->update("migarefrence_phonebook", $phonebook, ['migarefrence_phonebook_id = ?' => $phonbok_id]);
      }      
        return $res_option;
      }
      public function getTranslations()
      {
        return $this->_db->fetchAll("SELECT *  FROM `migareference_translations` WHERE 1");
      }
      public function writeDbTranslations($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_translations", $data);
      }
      public function saveLedger($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_ledger", $data);
        if (isset($data['redeem_id'])) {
          $id=$this->_db->lastInsertId();
          $datas['self_id']=$id;
          $this->_db->update("migarefrence_ledger", $datas,['migarefrence_ledger_id = ?' => $id]);
        }
      }
      public function temp_upate_app_redeem_prizes($data=[])
      {
          foreach ($data as $key => $value) {
            $id=$value['ledger_id'];
            $datas['self_id']=$value['ledger_id'];
            $datas['redeem_id']=$value['redeem_id'];
            $this->_db->update("migarefrence_ledger", $datas,['migarefrence_ledger_id = ?' => $id]);
          }
      }
      public function temp_upate_blacklist_phonenumbers($id=0,$data=[])
      {          
            
            $this->_db->update("migarefrence_phonebook", $data,['migarefrence_phonebook_id = ?' => $id ]);                    
      }
      public function deleteSponsor($ref_id=0)
      {          
          $this->_db->delete('migareference_referrer_agents',['referrer_id = ?' => $ref_id]);
          //For the dissable the sponsor of old methods
          $data['sponsor_id']  = 0;        
          $data['sponsor_one_id']  = 0;        
          $data['sponsor_two_id']  = 0;        
          $this->_db->update("migareference_invoice_settings", $data,['user_id = ?' => $ref_id]);
      }
      public function saveRedeemed($data=[])
      {
        $data['redeemed_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_redeemed_prizes", $data);
        return $this->_db->lastInsertId();
      }
      public function readTranslationsFile()
      {
        $dir_file_csv = Core_Model_Directory::getBasePathTo("/app/local/modules/Migareference/resources/translations/default/migareference.csv");
        $file = fopen($dir_file_csv, 'r');
        $index=0;
        while (($line = fgetcsv($file)) !== FALSE) {
          $data['app_id']    = 1000;
          $data['text_field']    = $line[0];
          $data['created_at']    = date('Y-m-d H:i:s');
          if ($data['text_field']==NULL) {
            $index++;
          }
          $this->_db->insert("migareference_translations", $data);
          if ($index==30) {
            break;
            exit;
          }
        }
        fclose($file);
        return $data;
      }
      public function getAllreportsforDecline()
      {
        return $this->_db->fetchAll("SELECT *
                                    FROM migareference_report as rep
                                    JOIN migareference_report_status as sta ON sta.migareference_report_status_id=rep.currunt_report_status
                                    WHERE 1
                                    ORDER BY rep.migareference_report_id");
      }
      // public function tempo()
      // {
      //   return $this->_db->fetchAll("SELECT `migareference_report_id`,`report_no`,`user_id`,`owner_name`,`owner_surname`,`owner_mobile`,`created_at` FROM `migareference_report` WHERE `last_modification_by` = 99999");
      // }
      // public function tempoo($rep=[],$report_id=0)
      // {
      //   $this->_db->update("migareference_report", $rep,['migareference_report_id = ?' => $report_id]);
      // }
      public function getAllReports($app_id)
      {
        return $this->_db->fetchAll("SELECT migrep.migareference_report_id,migrep.app_id,migrep.user_id,
                                            migrep.last_modification,migrep.last_modification_at,migrep.report_no,
                                            migrep.currunt_report_status,migrstat.status_title,
                                            migrep.created_at as report_created_at,
                                            migrep.owner_name,migrep.owner_surname,migrep.owner_mobile,
                                            migrstat.is_standard,migrstat.standard_type,
                                            ph.rating,
                               			        ph.migarefrence_phonebook_id,
                                            migainv.*,
                                            customer.*
                                     FROM migareference_report as migrep
                                     JOIN migareference_report_status AS migrstat ON migrstat.migareference_report_status_id=migrep.currunt_report_status
                                     JOIN migareference_invoice_settings AS migainv ON migainv.user_id=migrep.user_id
                                     JOIN customer ON customer.customer_id=migainv.user_id
                                     LEFT JOIN migarefrence_phonebook as ph ON ph.invoice_id=migainv.migareference_invoice_settings_id AND ph.type=1
                                     WHERE migrep.app_id=$app_id
                                     GROUP BY migrep.migareference_report_id
                                     ORDER BY migrep.app_id,migrep.user_id");
      }
      public function getDeclinedtoReports($app_id=0,$date="")
      {
        return $this->_db->fetchAll("SELECT *  FROM `migareference_report` WHERE `app_id` = $app_id AND `last_modification_at` <= '$date'");
      }
      public function checkStatus($app_id=0,$is_standard=0,$standard_type=0,$is_optional=0,$optional_type=0)
      {
        return $this->_db->fetchAll("SELECT *  FROM `migareference_report_status`
        JOIN migareference_email_template ON migareference_email_template.app_id=migareference_report_status.app_id AND migareference_email_template.event_id=migareference_report_status.migareference_report_status_id
        JOIN migareference_push_template ON migareference_push_template.app_id=migareference_report_status.app_id AND migareference_push_template.event_id=migareference_report_status.migareference_report_status_id
        JOIN migareference_notification_event ON migareference_notification_event.app_id=migareference_report_status.app_id AND migareference_notification_event.event_id=migareference_report_status.migareference_report_status_id
        WHERE migareference_report_status.app_id = $app_id
        AND migareference_report_status.is_standard = $is_standard
        AND migareference_report_status.standard_type = $standard_type
        AND migareference_report_status.is_optional = $is_optional
        AND migareference_report_status.optional_type = $optional_type");
      }
      public function grtsllkStatus($app_id=0)
      {
        return $this->_db->fetchAll("SELECT *  FROM `migareference_report_status`
        JOIN migareference_email_template ON migareference_email_template.app_id=migareference_report_status.app_id AND migareference_email_template.event_id=migareference_report_status.migareference_report_status_id
        JOIN migareference_push_template ON migareference_push_template.app_id=migareference_report_status.app_id AND migareference_push_template.event_id=migareference_report_status.migareference_report_status_id
        JOIN migareference_notification_event ON migareference_notification_event.app_id=migareference_report_status.app_id AND migareference_notification_event.event_id=migareference_report_status.migareference_report_status_id
        WHERE migareference_report_status.app_id = $app_id AND migareference_report_status.status=1");
      }
      public function checkDeletedreferrer($app_id=0)
      {
        return $this->_db->fetchAll("SELECT migareference_invoice_settings.user_id,customer.customer_id
        FROM `migareference_invoice_settings`
        LEFT JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id        
        WHERE migareference_invoice_settings.app_id=$app_id AND customer.customer_id IS NULL
        GROUP BY migareference_invoice_settings.migareference_invoice_settings_id
        ORDER BY `migareference_invoice_settings`.`user_id` ASC;");
      }
      public function assigntRepToAdmin($referrer_user_id=0,$admin_id=0)
      {
        $data['user_id']  = $admin_id;
        $data['user_id']  = $admin_id;
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migareference_report", $data,['user_id = ?' => $referrer_user_id]);
      }
      public static function reminderFallback(){ //If Reminder is not done in 7 days
        // Find Reminderds of type Pending or Postponed
        $migareference = new Migareference_Model_Db_Table_Migareference();
        $reminderCollection  = $migareference->getReminders();
        foreach ($reminderCollection as $key => $value) {
          //update reminder status to cancelled          
          $data['current_reminder_status'] = "cancele";
          $data['updated_at'] = date('Y-m-d H:i:s');
          $migareference->updateAutoReminder($value['migareference_automation_log_id'],$data);
          $log_item=[
            'app_id'       => $value['app_id'],
            'phonebook_id' => $value['migarefrence_phonebook_id'],
            'reminder_id' => $value['migareference_automation_log_id'],
            'log_type'     => "Automation",  
            'note' => "Reminder Status Change To (7 Days Fallback):".'cancele',            
            'user_id'      => $value['user_id'],
            'created_at'   => date('Y-m-d H:i:s')
        ];  
        $migareference->saveCommunicationLog($log_item);
        }
      }
      public function getReminders(){
        $query_option = "SELECT alg.*,migarefrence_phonebook.migarefrence_phonebook_id
        FROM `migareference_automation_log` AS alg
        JOIN migareference_invoice_settings AS inv ON inv.user_id = alg.user_id
        LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id = inv.sponsor_id
        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id = inv.migareference_invoice_settings_id
        JOIN migarefrence_report_reminder_types AS rt ON rt.migarefrence_report_reminder_types_id = alg.trigger_type_id
        JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id = alg.report_reminder_auto_id
        JOIN customer ON customer.customer_id = inv.user_id
        WHERE (alg.current_reminder_status = 'pending' OR alg.current_reminder_status = 'postpone')
          AND alg.is_deleted = 0
          AND DATE(alg.created_at) <= (CURDATE() - INTERVAL 7 DAY)  
        ORDER BY `alg`.`migareference_automation_log_id` DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrersRating(){
        $query_option = "SELECT 
        migareference_report.report_no,
        migareference_report_status.status_title,
        migareference_report.created_at,
        migarefrence_phonebook.rating,
        migareference_report.user_id
        FROM 
            migareference_report 
        JOIN 
            migareference_report_status 
            ON migareference_report_status.migareference_report_status_id = migareference_report.currunt_report_status  
            AND migareference_report_status.standard_type != 4
        JOIN 
            migareference_invoice_settings 
            ON migareference_invoice_settings.user_id = migareference_report.user_id
        JOIN 
          migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id AND migarefrence_phonebook.rating<3
        WHERE       
            DATE(migareference_report.created_at) < (NOW() - INTERVAL 2 DAY)
        GROUP BY migareference_report.user_id;";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public static function refferalUserElimination(){
        $migareference = new Migareference_Model_Db_Table_Migareference();
        //04-21-2025 Get all newly created reports (Older than 2 days) whos status is not declined and set their rating to 3 if its less than 3
        $reports=$migareference->getReferrersRating();
        foreach ($reports as $key => $value) {
          $phonebook['rating'] = 3;
          $phonebook['updated_at'] = date('Y-m-d H:i:s');          
          // $this->update_phonebook($phonebook,$value['migarefrence_phonebook_id'],9999,0);         
        }
        // Logic for https://trello.com/c/nDXxUW39/133-report-status-notification-when-referrer-user-is-deleted
        // $checkDeletedreferrer=$migareference->checkDeletedreferrer();
        // if (count($checkDeletedreferrer)) {
        //   foreach ($checkDeletedreferrer as $key => $value) {
        //     if ($value['customer_id']==NULL && $value['migareference_report_id']!=NULL) {
        //       // NULL mean user is deleted and assigne it to first admin
        //       $admins=$migareference->getAdminCustomers($value['app_id']);
        //       if (count($admins)) {
        //         $referrer_id=$value['user_id'];
        //         $admin_id=$admins[0]['user_id'];
        //         $migareference->assigntRepToAdmin($referrer_id,$admin_id);
        //       }
        //     }
        //   }
        // }
        // End logic to handle deleted referrer user to assign first Admin
         $insert        = $migareference->insert_stats();
         $data          = $migareference->getAllReferralUsers();
        foreach ($data as $key => $value) {
          $gcm_token  = $migareference->checkGcmtoken($value['user_id'],$value['app_id'],$value['token']);
          $apns_token = $migareference->checkApnstoken($value['user_id'],$value['app_id'],$value['token']);
          $a[]=$gcm_token;
            if (count($gcm_token) || count($apns_token)) {
              $user=true;
            }else{
                  $data_update['leave_status']= 2;
                  $data_update['leave_date']  = date('Y-m-d H:i:s');
                  $migareference->updatePropertysettings($data_update,$value['migareference_invoice_settings_id']);
            }
        }
        // Logic for Auto Declined Reports AND Reminders
        $reports=$migareference->getAllreportsforDecline();//Get all reports whose grace_Days>0
        foreach ($reports as $key => $value) {
          // Fallback Logic
          if ($value['is_declined']==1) {
            $grace_days = $value['declined_grace_days'];
            // Count days for no Activity
            $now = date('Y-m-d H:i:s'); // or your date as well
            $last_modification_date = $value['last_modification_at'];
            $now=strtotime($now);
            $last_modification_date=strtotime($last_modification_date);
            $datediff = $now-$last_modification_date;
            $noActivityDays=round($datediff / (60 * 60 * 24));
            if ($noActivityDays>$grace_days) { //True ready for decline
                $fallback_status=$migareference->reportStatusByKey($value['declined_to'],$value['migareference_report_id']);//GET declined status
                $migareference->declinedReport($value['app_id'],$value['migareference_report_id'],$fallback_status[0]['status_title'],$fallback_status[0]['migareference_report_status_id']);
                $migareference->sendNotification($value['app_id'],$value['migareference_report_id'],$value['auto_fallabck_comment']);
                  $comment_tb['app_id']       = $value['app_id'];
                  $comment_tb['report_id']    = $value['migareference_report_id'];
                  $comment_tb['status_id']    = $fallback_status[0]['migareference_report_status_id'];
                  $comment_tb['comment']      = $value['auto_fallabck_comment'];
                  $commnent_id                = $migareference->saveComment($comment_tb);
            }
          }

          
          // $rev_reports=$migareference->tempo();
          // foreach ($rev_reports as $key => $value) {
          //   $report_id=$value['migareference_report_id'];
          //   $rep['last_modification']     = 'Segnalazione ricevuta';
          //   $rep['last_modification_by']  = $value['user_id'];
          //   $rep['last_modification_at']  = $value['created_at'];
          //   $rep['currunt_report_status'] = 1;
          //   $rep['updated_at']            = $value['created_at'];
          //   $migareference->tempoo($rep,$report_id);
          // }
          //Logic to update status of Daily Email Reminder to Declined if its still pending after 3 days
          $day_book_logs=$migareference->getDayBookLogs();
          foreach ($day_book_logs as $key => $value) {
            $migareference->markasDon($value['migareference_reminder_daybook'],'decliend');
          }
          // Reminder Logic
          if ($value['is_reminder']==1 && $value['is_reminder_sent']==0) {
            $grace_days = $value['reminder_grace_days'];
            // Count days for no Activity
            $now = date('Y-m-d H:i:s'); // or your date as well
            $last_modification_date = $value['last_modification_at'];
            $now=strtotime($now);
            $last_modification_date=strtotime($last_modification_date);
            $datediff = $now-$last_modification_date;
            $noActivityDays=round($datediff / (60 * 60 * 24));
            // if ($noActivityDays>$grace_days) { //True ready for decline
            //     $fallback_status=$migareference->reportStatusByKey($value['currunt_report_status'],$value['migareference_report_id']);//GET declined status
            //      $migareference->sendReminderNotification($value['app_id'],$value['migareference_report_id'],$value['auto_fallabck_comment']);
            // }
          }
        }
        return $rev_reports;
      }
      public function defaultTriggers($app_id=0,$value_id=0,$type="")
      {
        $migareference = new Migareference_Model_Db_Table_Migareference();
        // Default Email Footer for all emails 3/26/2021
        $email_footer="<p>---</p><p>Se ancora non hai installato la nostra APP @@app_name@@ fallo subito a questo link @@app_link@@ registrandoti con la stessa mail con cui hai ricevuto questo messaggio.Potrai seguire le segnalazioni a portata di dito, vedere tutti i premi che ti aspettano e conoscere di più sulle opportunità di guadagno con noi</p>";
        // Event 1 Waiting for Contact
          // Satus
          $status_remplate[1]['app_id']=$app_id;
          $status_remplate[1]['status_title']=__("Nuova Segnalazione");
          $status_remplate[1]['status_icon']="newreport_icon.png";
          $status_remplate[1]['status']=1;
          $status_remplate[1]['is_standard']=1;
          $status_remplate[1]['is_optional']=0;
          $status_remplate[1]['is_comment']=0;
          $status_remplate[1]['is_acquired']=0;
          $status_remplate[1]['standard_type']=1;
          $status_remplate[1]['optional_type']=0;
          $status_remplate[1]['order_id']=1;
          $status_remplate[1]['is_declined']=0;
          $status_remplate[1]['declined_grace_days']=0;
          $status_remplate[1]['declined_to']=0;
          $status_remplate[1]['is_reminder']=1;
          $status_remplate[1]['reminder_grace_days']=7;
          // EMAIL
          $email_template_constant[1]['is_email_ref']    = 1;
          $email_template_constant[1]['is_email_agt']    = 1;
          $email_template_constant[1]['app_id']          = $app_id;
          $email_template_constant[1]['event_id']        = 1;//will set in loop
          $email_template_constant[1]['value_id']        = $value_id;
          $email_template_constant[1]['ref_email_title'] = "Abbiamo ricevuto una segnalazione per @@report_owner@@";
          $email_template_constant[1]['ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>abbiamo ricevuto la tua segnalazione nr.# @@report_no@@.</p><p>I dettagli della segnalazione sono:</p><p>Proprietario immobile @@report_owner@@<br />Telefono @@report_owner_phone@@ .</p><p>Attendi ora miei aggiornamenti nei prossimi giorni.</p>".$email_footer;
          $email_template_constant[1]['agt_email_title'] = "Nuova segnalazione ricevuta: @@referral_name@@";
          $email_template_constant[1]['agt_email_text']  = "<p>Ciao Admin,</p><p>abbiamo ricevuto una nuova segnalazione:</p><p>Segnalatore: @@referral_name@@&nbsp;<br />Proprietario immobile:&nbsp;@@report_owner@@<br />Telefono: @@report_owner_phone@@<br />Segnalazione nr.# @@report_no@@ .<br /><br />Procedi a contattarlo nel pi&ugrave; breve tempo possibile ed ad aggiornare lo status.</p>".$email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[1]['reminder_is_email_ref']    = 0;
          $email_template_constant[1]['reminder_ref_email_title'] = "Abbiamo ricevuto una segnalazione per @@report_owner@@";
          $email_template_constant[1]['reminder_ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>abbiamo ricevuto la tua segnalazione nr.# @@report_no@@.</p><p>I dettagli della segnalazione sono:</p><p>Proprietario immobile @@report_owner@@<br />Telefono @@report_owner_phone@@ .</p><p>Attendi ora miei aggiornamenti nei prossimi giorni.</p>".$email_footer;
            // Agent
          $email_template_constant[1]['reminder_is_email_agt']    = 1;
          $email_template_constant[1]['reminder_agt_email_title'] = "Nuova segnalazione ricevuta: @@referral_name@@";
          $email_template_constant[1]['reminder_agt_email_text']  = "<p>Ciao Admin,</p><p>abbiamo ricevuto una nuova segnalazione:</p><p>Segnalatore: @@referral_name@@&nbsp;<br />Proprietario immobile:&nbsp;@@report_owner@@<br />Telefono: @@report_owner_phone@@<br />Segnalazione nr.# @@report_no@@ .<br /><br />Procedi a contattarlo nel pi&ugrave; breve tempo possibile ed ad aggiornare lo status.</p>".$email_footer;
          // PUSH
          $push_template_constant[1]["app_id" ]         = $app_id;
          $push_template_constant[1]["value_id" ]       = $value_id;
          $push_template_constant[1]["is_push_ref" ]    = 0;
          $push_template_constant[1]["is_push_agt" ]    = 1;
          $push_template_constant[1]['event_id']        = 1;
          $push_template_constant[1]["ref_push_title" ] = "";
          $push_template_constant[1]["ref_push_text" ]  = "";
          $push_template_constant[1]["ref_open_feature"]= 0;
          $push_template_constant[1]["ref_feature_id" ] = 0;
          $push_template_constant[1]["ref_custom_url" ] = 0;
          $push_template_constant[1]["ref_cover_image" ]= "";
          $push_template_constant[1]["agt_push_title" ] = "Abbiamo ricevuto una nuova segnalazione";
          $push_template_constant[1]["agt_push_text" ]  = "Ciao @@referral_name@@, il segnalatore @@referral_name@@ ha appena inviato una segnalazione relativa al sig. @@report_owner@@ .";
          $push_template_constant[1]["agt_open_feature"]= 1;
          $push_template_constant[1]["agt_feature_id" ] = $value_id;
          $push_template_constant[1]["agt_custom_url" ] = "";
          $push_template_constant[1]["agt_cover_image" ]= "event_101_agent.jpg";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[1]["reminder_is_push_ref"]      = 0;
          $push_template_constant[1]["reminder_ref_push_title"]   = 'Mancato aggiornamento segnalazione nr. @@report_no@@';
          $push_template_constant[1]["reminder_ref_push_text"]    = 'Hai mancato di aggiornare la segnalazione inviata dal sig. @@referral_name@@ nr. @@report_no@@ relativa al proprietario immobile @@report_owner@@ . Procedi subito ad aggiornare lo status nella tua APP.';
          $push_template_constant[1]["reminder_ref_open_feature"] = 1;
          $push_template_constant[1]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[1]["reminder_ref_custom_url"]   = "";
          $push_template_constant[1]["reminder_ref_cover_image"]  = "";
          // Reminder Agent
          $push_template_constant[1]["reminder_is_push_agt"]      = 1;
          $push_template_constant[1]["reminder_agt_push_title"]   = 'Mancato aggiornamento segnalazione nr. @@report_no@@';
          $push_template_constant[1]["reminder_agt_push_text"]    = 'Hai mancato di aggiornare la segnalazione inviata dal sig. @@referral_name@@ nr. @@report_no@@ relativa al potenziale cliente @@report_owner@@ . Procedi subito ad aggiornare lo status nella tua APP.';
          $push_template_constant[1]["reminder_agt_open_feature"] = 1;
          $push_template_constant[1]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[1]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[1]["reminder_agt_cover_image"]  = "event_101_reminder_agent.jpg";
          // Event 2 Optional
          // Satus
          $status_remplate[2]['app_id']=$app_id;
          $status_remplate[2]['status_title']=__("Appuntamento Fissato");
          $status_remplate[2]['status_icon']="optional_one.png";
          $status_remplate[2]['status']=1;
          $status_remplate[2]['is_standard']=0;
          $status_remplate[2]['is_optional']=1;
          $status_remplate[2]['is_comment']=0;
          $status_remplate[2]['is_acquired']=0;
          $status_remplate[2]['standard_type']=0;
          $status_remplate[2]['optional_type']=1;
          $status_remplate[2]['order_id']=2;
          $status_remplate[2]['is_declined']=0;
          $status_remplate[2]['declined_grace_days']=0;
          $status_remplate[2]['declined_to']=0;
          $status_remplate[2]['is_reminder']=0;
          $status_remplate[2]['reminder_grace_days']=0;
          // EMAIL
          $email_template_constant[2]['is_email_ref']    = 1;
          $email_template_constant[2]['is_email_agt']    = 0;
          $email_template_constant[2]['app_id']          = $app_id;
          $email_template_constant[2]['event_id']        = 1;//will set in loop
          $email_template_constant[2]['value_id']        = $value_id;
          $email_template_constant[2]['ref_email_title'] = "Appuntamento Fissato con @@report_owner @@";
          $email_template_constant[2]['ref_email_text']  = "<p>Ciao&nbsp;@@referral_name@@,</p><p>&egrave; con piacere che ti comunichiamo di aver&nbsp;fissato un appuntamento con il potenziale cliente&nbsp;&quot;@@report_owner@@&quot;&nbsp; relativo alla segnalazione nr.&nbsp;@@report_no@@.&nbsp;<br /><br />Qui eventuali note del nostro operatore:<br />@@comment@@</p>          
          <p>Ti terremo aggiornato sul suo esito nei prossimi giorni.<br />
          <br />
          Come vedi stiamo tenendo a cuore la tua segnalazione, facci altre referenze e sapremo come sdebitarci ben oltre le semplici commissioni. Ricorda, noi vogliamo fare squadra con i migliori!&nbsp;</p>
          <p>Al tuo successo&nbsp;<br />
          @@agent_name@@<br />
          <br />
          --- Non hai ancora installato la nostra APP? Scopri i premi incredibili che ti aspettano, controlla lo stato delle tue segnalazioni e scopri tantissime informazioni sul mondo di MIGASTONE.<br />
          Vai a questo link qui: @@app_name@@<br />
          (se sei gi&agrave; registrato e non ricordi la password fai &quot;recupera password&quot;</p>
          ".$email_footer;
          $email_template_constant[2]['agt_email_title'] = "";
          $email_template_constant[2]['agt_email_text']  = $email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[2]['reminder_is_email_ref']    = 0;
          $email_template_constant[2]['reminder_ref_email_title'] = "";
          $email_template_constant[2]['reminder_ref_email_text']  = $email_footer;
            // Agent
          $email_template_constant[2]['reminder_is_email_agt']    = 0;
          $email_template_constant[2]['reminder_agt_email_title'] = "";
          $email_template_constant[2]['reminder_agt_email_text']  = $email_footer;
          // PUSH
          $push_template_constant[2]["app_id" ]         = $app_id;
          $push_template_constant[2]["value_id" ]       = $value_id;
          $push_template_constant[2]["is_push_ref" ]    = 1;
          $push_template_constant[2]["is_push_agt" ]    = 0;
          $push_template_constant[2]['event_id']        = 0;//Will set in loop
          $push_template_constant[2]["ref_push_title" ] = "Appuntamento Fissato";
          $push_template_constant[2]["ref_push_text" ]  = "E' con piacere che ti comunichiamo di aver fissato l'appuntamento con il tuo contatto @@report_owner@@";
          $push_template_constant[2]["ref_open_feature"]= 1;
          $push_template_constant[2]["ref_feature_id" ] = $value_id;
          $push_template_constant[2]["ref_custom_url" ] = "";
          $push_template_constant[2]["ref_cover_image" ]= "event_2_referral.jpg";
          $push_template_constant[2]["agt_push_title" ] = "";
          $push_template_constant[2]["agt_push_text" ]  = "";
          $push_template_constant[2]["agt_open_feature"]= 1;
          $push_template_constant[2]["agt_feature_id" ] = $value_id;
          $push_template_constant[2]["agt_custom_url" ] = "";
          $push_template_constant[2]["agt_cover_image" ]= "";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[2]["reminder_is_push_ref"]      = 0;
          $push_template_constant[2]["reminder_is_push_agt"]      = 0;
          $push_template_constant[2]["reminder_ref_push_title"]   = '';
          $push_template_constant[2]["reminder_ref_push_text"]    = '';
          $push_template_constant[2]["reminder_ref_open_feature"] = 1;
          $push_template_constant[2]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[2]["reminder_ref_custom_url"]   = NULL;
          $push_template_constant[2]["reminder_ref_cover_image"]  = NULL;
            // Reminder Agent
          $push_template_constant[2]["reminder_agt_push_title"]   = '';
          $push_template_constant[2]["reminder_agt_push_text"]    = '';
          $push_template_constant[2]["reminder_agt_open_feature"] = 1;
          $push_template_constant[2]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[2]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[2]["reminder_agt_cover_image"]  = "";
          // Event 3 Mandate Acquired
          // Satus
          $status_remplate[3]['app_id']=$app_id;
          $status_remplate[3]['status_title']=__("Contratto Firmato");
          $status_remplate[3]['status_icon']="mandate_icon.png";
          $status_remplate[3]['status']=1;
          $status_remplate[3]['is_standard']=1;
          $status_remplate[3]['is_optional']=0;
          $status_remplate[3]['is_comment']=0;
          $status_remplate[3]['is_acquired']=1;
          $status_remplate[3]['standard_type']=2;
          $status_remplate[3]['optional_type']=0;
          $status_remplate[3]['order_id']=3;
          $status_remplate[3]['is_declined']=0;
          $status_remplate[3]['declined_grace_days']=0;
          $status_remplate[3]['declined_to']=0;
          $status_remplate[3]['is_reminder']=0;
          $status_remplate[3]['reminder_grace_days']=0;
          // EMAIL
          $email_template_constant[3]['is_email_ref']    = 1;
          $email_template_constant[3]['is_email_agt']    = 0;
          $email_template_constant[3]['app_id']          = $app_id;
          $email_template_constant[3]['event_id']        = 2;//will set in Loop
          $email_template_constant[3]['value_id']        = $value_id;
          $email_template_constant[3]['ref_email_title'] = "Aggiornamento: Contratto Firmato";
          $email_template_constant[3]['ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>sono felice di aggiornarti che ho appena acquisito il mandato di vendita dell&#39;immobile del sig. @@report_owner@@ relativo alla segnalazione nr. @@report_no@@.</p><p>Quando vender&ograve; l&#39;immobile La tua commissione sar&agrave; di @@commission@@ euro.</p><p>Ti aggiorner&ograve; quando ci sono sviluppi.</p><p>Un Abbraccio @@agent_name@@</p>".$email_footer;
          $email_template_constant[3]['agt_email_title'] = "";
          $email_template_constant[3]['agt_email_text']  = $email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[3]['reminder_is_email_ref']    = 0;
          $email_template_constant[3]['reminder_ref_email_title'] = "";
          $email_template_constant[3]['reminder_ref_email_text']  = $email_footer;
            // Agent
          $email_template_constant[3]['reminder_is_email_agt']    = 0;
          $email_template_constant[3]['reminder_agt_email_title'] = "";
          $email_template_constant[3]['reminder_agt_email_text']  = $email_footer;
          // PUSH
          $push_template_constant[3]["app_id" ]= $app_id;
          $push_template_constant[3]["value_id" ]= $value_id;
          $push_template_constant[3]["event_id" ]= 2;//Will set in loop
          $push_template_constant[3]["is_push_ref" ]= 1;
          $push_template_constant[3]["is_push_agt" ]= 0;
          $push_template_constant[3]["ref_push_title" ]= "Aggiornamento: Contratto Firmato";
          $push_template_constant[3]["ref_push_text" ]= "Ciao @@referral_name@@, sono felice di aggiornarti che ho appena acquisito il mandato di vendita dell'immobile del sig. @@report_owner@@  segnalazione nr. @@report_no@@. Quando venderò l'immobile La tua commissione sarà di @@commission@@ euro. Ti aggiornerò quando ci sono sviluppi. Un Abbraccio @@agent_name@@";
          $push_template_constant[3]["ref_open_feature" ]= 1;
          $push_template_constant[3]["ref_feature_id" ]= $value_id;
          $push_template_constant[3]["ref_custom_url" ]= 0;
          $push_template_constant[3]["ref_cover_image" ]= "event_3_referral.jpg";
          $push_template_constant[3]["agt_push_title" ]= "";
          $push_template_constant[3]["agt_push_text" ]= "";
          $push_template_constant[3]["agt_open_feature" ]= 0;
          $push_template_constant[3]["agt_feature_id" ]= 0;
          $push_template_constant[3]["agt_custom_url" ]= "";
          $push_template_constant[3]["agt_cover_image" ]= "";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[3]["reminder_is_push_ref"]      = 0;
          $push_template_constant[3]["reminder_is_push_agt"]      = 0;
          $push_template_constant[3]["reminder_ref_push_title"]   = '';
          $push_template_constant[3]["reminder_ref_push_text"]    = '';
          $push_template_constant[3]["reminder_ref_open_feature"] = 1;
          $push_template_constant[3]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[3]["reminder_ref_custom_url"]   = NULL;
          $push_template_constant[3]["reminder_ref_cover_image"]  = NULL;
            // Reminder Agent
          $push_template_constant[3]["reminder_agt_push_title"]   = '';
          $push_template_constant[3]["reminder_agt_push_text"]    = '';
          $push_template_constant[3]["reminder_agt_open_feature"] = 1;
          $push_template_constant[3]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[3]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[3]["reminder_agt_cover_image"]  = "";
          // Event 4 Optional
          // Satus
          $status_remplate[4]['app_id']=$app_id;
          $status_remplate[4]['status_title']=__("Posticipa Trattativa");
          $status_remplate[4]['status_icon']="optional_two.png";
          $status_remplate[4]['status']=1;
          $status_remplate[4]['is_standard']=0;
          $status_remplate[4]['is_optional']=1;
          $status_remplate[4]['is_comment']=0;
          $status_remplate[4]['is_acquired']=0;
          $status_remplate[4]['standard_type']=0;
          $status_remplate[4]['optional_type']=2;
          $status_remplate[4]['order_id']=4;
          $status_remplate[4]['is_declined']=0;
          $status_remplate[4]['declined_grace_days']=0;
          $status_remplate[4]['declined_to']=0;
          $status_remplate[4]['is_reminder']=0;
          $status_remplate[4]['reminder_grace_days']=0;
          // EMAIL
          $email_template_constant[4]['is_email_ref']    = 1;
          $email_template_constant[4]['is_email_agt']    = 0;
          $email_template_constant[4]['app_id']          = $app_id;
          $email_template_constant[4]['event_id']        = 1;//will set in loop
          $email_template_constant[4]['value_id']        = $value_id;
          $email_template_constant[4]['ref_email_title'] = "Aggiornamento: Trattativa posticipata";
          $email_template_constant[4]['ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>Abbiamo effettuato la trattativa con il contatto&nbsp;@@report_owner@@ relativo alla segnalazione nr. @@report_no@@, ma ha preso tempo e per ora non ha deciso se procedere all&#39;acquisto.&nbsp;</p><p>Cercheremo di sentirlo nei prossimi giorni e portarlo alla conclusione del contratto. Ti consiglio di sentirlo anche tu per chiedere un parere e comunicarmi eventuali obiezioni che ti condivider&agrave;, questo potrebbe aiutarmi a capire il vero problema che ha impedito la firma del contratto in prima battuta.<br /><br />Se serve ci sentiamo cosi ci coordiniamo meglio, scrivimi pure anche via whatsapp.</p><p>Un abbraccio @@agent_name@@</p>".$email_footer;
          $email_template_constant[4]['agt_email_title'] = "";
          $email_template_constant[4]['agt_email_text']  = $email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[4]['reminder_is_email_ref']    = 0;
          $email_template_constant[4]['reminder_ref_email_title'] = "";
          $email_template_constant[4]['reminder_ref_email_text']  = $email_footer;
            // Agent
          $email_template_constant[4]['reminder_is_email_agt']    = 0;
          $email_template_constant[4]['reminder_agt_email_title'] = "";
          $email_template_constant[4]['reminder_agt_email_text']  = $email_footer;
          // PUSH
          $push_template_constant[4]["app_id" ]         = $app_id;
          $push_template_constant[4]["value_id" ]       = $value_id;
          $push_template_constant[4]["is_push_ref" ]    = 1;
          $push_template_constant[4]["is_push_agt" ]    = 0;
          $push_template_constant[4]['event_id']        = 0;//Will set in loop
          $push_template_constant[4]["ref_push_title" ] = "Evvaii!! Immobile venduto";
          $push_template_constant[4]["ref_push_text" ]  = "Ciao @@referral_name@@, sono veramente eccitato di comunicarti che ho appena venduto l'immobile del sig. @@report_owner@@  segnalazione nr. @@report_no@@. Ora devo solo incassare la mia commissione e appena concluso questo ultimo passaggio potrai passare a ritirare i tuoi @@commission@@ euro. Un abbraccio. @@agent_name@@";
          $push_template_constant[4]["ref_open_feature"]= 1;
          $push_template_constant[4]["ref_feature_id" ] = $value_id;
          $push_template_constant[4]["ref_custom_url" ] = "";
          $push_template_constant[4]["ref_cover_image" ]= "optional_two_reffreal.jpg";
          $push_template_constant[4]["agt_push_title" ] = "";
          $push_template_constant[4]["agt_push_text" ]  = "";
          $push_template_constant[4]["agt_open_feature"]= 1;
          $push_template_constant[4]["agt_feature_id" ] = $value_id;
          $push_template_constant[4]["agt_custom_url" ] = "";
          $push_template_constant[4]["agt_cover_image" ]= "";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[4]["reminder_is_push_ref"]      = 0;
          $push_template_constant[4]["reminder_is_push_agt"]      = 0;
          $push_template_constant[4]["reminder_ref_push_title"]   = '';
          $push_template_constant[4]["reminder_ref_push_text"]    = '';
          $push_template_constant[4]["reminder_ref_open_feature"] = 1;
          $push_template_constant[4]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[4]["reminder_ref_custom_url"]   = NULL;
          $push_template_constant[4]["reminder_ref_cover_image"]  = NULL;
            // Reminder Agent
          $push_template_constant[4]["reminder_agt_push_title"]   = '';
          $push_template_constant[4]["reminder_agt_push_text"]    = '';
          $push_template_constant[4]["reminder_agt_open_feature"] = 1;
          $push_template_constant[4]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[4]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[4]["reminder_agt_cover_image"]  = "";
          // // Event 5 Optional
          // // Satus
          // $status_remplate[5]['app_id']=$app_id;
          // $status_remplate[5]['status_title']=__("Pagabile");
          // $status_remplate[5]['status_icon']="optional_three.png";
          // $status_remplate[5]['status']=1;
          // $status_remplate[5]['is_standard']=0;
          // $status_remplate[5]['is_optional']=1;
          // $status_remplate[5]['is_comment']=0;
          // $status_remplate[5]['is_acquired']=0;
          // $status_remplate[5]['standard_type']=0;
          // $status_remplate[5]['optional_type']=3;
          // $status_remplate[5]['order_id']=5;
          // $status_remplate[5]['is_declined']=0;
          // $status_remplate[5]['declined_grace_days']=0;
          // $status_remplate[5]['declined_to']=0;
          // $status_remplate[5]['is_reminder']=0;
          // $status_remplate[5]['reminder_grace_days']=0;
          // // EMAIL
          // $email_template_constant[5]['is_email_ref']    = 1;
          // $email_template_constant[5]['is_email_agt']    = 0;
          // $email_template_constant[5]['app_id']          = $app_id;
          // $email_template_constant[5]['event_id']        = 1;//will set in loop
          // $email_template_constant[5]['value_id']        = $value_id;
          // $email_template_constant[5]['ref_email_title'] = "Passa a ritirare il cash!";
          // $email_template_constant[5]['ref_email_text']  = "Ciao @@referral_name@@, ho appena incassato la commissione di vendita da @@report_owner@@ segnalazione nr. @@report_no@@. Contattami subito per passare a ritirare la tua commissione di @@commission@@ euro. Brinderemo assieme,Un Abbraccio @@agent_name@@".$email_footer;
          // $email_template_constant[5]['agt_email_title'] = "";
          // $email_template_constant[5]['agt_email_text']  = $email_footer;
          // // Reimnder
          //   // Reffreal
          // $email_template_constant[5]['reminder_is_email_ref']    = 0;
          // $email_template_constant[5]['reminder_ref_email_title'] = "";
          // $email_template_constant[5]['reminder_ref_email_text']  = $email_footer;
          //   // Agent
          // $email_template_constant[5]['reminder_is_email_agt']    = 0;
          // $email_template_constant[5]['reminder_agt_email_title'] = "";
          // $email_template_constant[5]['reminder_agt_email_text']  = $email_footer;
          // // PUSH
          // $push_template_constant[5]["app_id" ]         = $app_id;
          // $push_template_constant[5]["value_id" ]       = $value_id;
          // $push_template_constant[5]["is_push_ref" ]    = 1;
          // $push_template_constant[5]["is_push_agt" ]    = 0;
          // $push_template_constant[5]['event_id']        = 0;//Will set in loop
          // $push_template_constant[5]["ref_push_title" ] = "Passa a ritirare il cash!";
          // $push_template_constant[5]["ref_push_text" ]  = "<p>Ciao @@referral_name@@,</p><p>ho appena incassato la commissione di vendita da @@report_owner@@ segnalazione nr. @@report_no@@.</p><p>Contattami subito per passare a ritirare la tua commissione di @@commission@@ euro.</p><p>Brinderemo assieme,</p><p>Un abbraccio @@agent_name@@</p>";
          // $push_template_constant[5]["ref_open_feature"]= 1;
          // $push_template_constant[5]["ref_feature_id" ] = $value_id;
          // $push_template_constant[5]["ref_custom_url" ] = "";
          // $push_template_constant[5]["ref_cover_image" ]= "event_5_referral.jpg";
          // $push_template_constant[5]["agt_push_title" ] = "";
          // $push_template_constant[5]["agt_push_text" ]  = "";
          // $push_template_constant[5]["agt_open_feature"]= 1;
          // $push_template_constant[5]["agt_feature_id" ] = $value_id;
          // $push_template_constant[5]["agt_custom_url" ] = "";
          // $push_template_constant[5]["agt_cover_image" ]= "";
          // // Reminder PUSH
          //   // Reminder Refreela
          // $push_template_constant[5]["reminder_is_push_ref"]      = 0;
          // $push_template_constant[5]["reminder_is_push_agt"]      = 0;
          // $push_template_constant[5]["reminder_ref_push_title"]   = '';
          // $push_template_constant[5]["reminder_ref_push_text"]    = '';
          // $push_template_constant[5]["reminder_ref_open_feature"] = 1;
          // $push_template_constant[5]["reminder_ref_feature_id"]   = $value_id;
          // $push_template_constant[5]["reminder_ref_custom_url"]   = NULL;
          // $push_template_constant[5]["reminder_ref_cover_image"]  = NULL;
          //   // Reminder Agent
          // $push_template_constant[5]["reminder_agt_push_title"]   = '';
          // $push_template_constant[5]["reminder_agt_push_text"]    = '';
          // $push_template_constant[5]["reminder_agt_open_feature"] = 1;
          // $push_template_constant[5]["reminder_agt_feature_id"]   = $value_id;
          // $push_template_constant[5]["reminder_agt_custom_url"]   = NULL;
          // $push_template_constant[5]["reminder_agt_cover_image"]  = "";
          // Event 4 Paid Status or Success Baskit
          // Satus
          $status_remplate[6]['app_id']=$app_id;
          $status_remplate[6]['status_title']=__("Pagato");
          $status_remplate[6]['status_icon']="paid_icon.png";
          $status_remplate[6]['status']=1;
          $status_remplate[6]['is_standard']=1;
          $status_remplate[6]['is_optional']=0;
          $status_remplate[6]['is_comment']=0;
          $status_remplate[6]['is_acquired']=1;
          $status_remplate[6]['standard_type']=3;
          $status_remplate[6]['optional_type']=0;
          $status_remplate[6]['order_id']=6;
          $status_remplate[6]['is_declined']=0;
          $status_remplate[6]['declined_grace_days']=0;
          $status_remplate[6]['declined_to']=0;
          $status_remplate[6]['is_reminder']=0;
          $status_remplate[6]['reminder_grace_days']=0;
          // EMAIL
          $email_template_constant[6]['is_email_ref']    = 1;
          $email_template_constant[6]['is_email_agt']    = 0;
          $email_template_constant[6]['event_id']        = 3;//will set in loop
          $email_template_constant[6]['app_id']          = $app_id;
          $email_template_constant[6]['value_id']        = $value_id;
          $email_template_constant[6]['ref_email_title'] = "Segnalazione Pagata";
          $email_template_constant[6]['ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>Ti confermiamo che ti abbiamo accreditato la somma della tua provvigione per la segnalazione di&nbsp;@@report_owner@@ relative alla segnalazione nr. @@report_no@@ .<br /><br />Complimenti e ora continua con una nuova segnalazione per brindare nuovamente assieme</p><p>Un abbraccio @@agent_name@@</p>".$email_footer;
          $email_template_constant[6]['agt_email_title'] = "";
          $email_template_constant[6]['agt_email_text']  = $email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[6]['reminder_is_email_ref']    = 0;
          $email_template_constant[6]['reminder_ref_email_title'] = "";
          $email_template_constant[6]['reminder_ref_email_text']  = $email_footer;
            // Agent
          $email_template_constant[6]['reminder_is_email_agt']    = 0;
          $email_template_constant[6]['reminder_agt_email_title'] = "";
          $email_template_constant[6]['reminder_agt_email_text']  = $email_footer;
          // PUSH
          $push_template_constant[6]["app_id" ]= $app_id;
          $push_template_constant[6]["value_id" ]= $value_id;
          $push_template_constant[6]["event_id" ]= 3;//will set in loop
          $push_template_constant[6]["is_push_ref" ]= 1;
          $push_template_constant[6]["is_push_agt" ]= 0;
          $push_template_constant[6]["ref_push_title" ]= "Segnalazione Pagata";
          $push_template_constant[6]["ref_push_text" ]= "Ti confermiamo che ti abbiamo accreditato la somma della tua provvigione per la segnalazione di @@report_owner@@ relative alla segnalazione nr. @@report_no@@ .Complimenti e ora continua con una nuova segnalazione per brindare nuovamente assieme";
          $push_template_constant[6]["ref_open_feature" ]= 1;
          $push_template_constant[6]["ref_feature_id" ]= $value_id;
          $push_template_constant[6]["ref_custom_url" ]= 0;
          $push_template_constant[6]["ref_cover_image" ]= "event_3_referral.jpg";
          $push_template_constant[6]["agt_push_title" ]= "";
          $push_template_constant[6]["agt_push_text" ]= "";
          $push_template_constant[6]["agt_open_feature" ]= 0;
          $push_template_constant[6]["agt_feature_id" ]= 0;
          $push_template_constant[6]["agt_custom_url" ]= "";
          $push_template_constant[6]["agt_cover_image" ]= "";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[6]["reminder_is_push_ref"]      = 0;
          $push_template_constant[6]["reminder_is_push_agt"]      = 0;
          $push_template_constant[6]["reminder_ref_push_title"]   = '';
          $push_template_constant[6]["reminder_ref_push_text"]    = '';
          $push_template_constant[6]["reminder_ref_open_feature"] = 1;
          $push_template_constant[6]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[6]["reminder_ref_custom_url"]   = NULL;
          $push_template_constant[6]["reminder_ref_cover_image"]  = NULL;
            // Reminder Agent
          $push_template_constant[6]["reminder_agt_push_title"]   = '';
          $push_template_constant[6]["reminder_agt_push_text"]    = '';
          $push_template_constant[6]["reminder_agt_open_feature"] = 1;
          $push_template_constant[6]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[6]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[6]["reminder_agt_cover_image"]  = "";
          // Event 5 Decline Status
          // Satus
          $status_remplate[7]['app_id']=$app_id;
          $status_remplate[7]['status_title']=__("Declinato/Non Venduto");
          $status_remplate[7]['status_icon']="declined_icon.png";
          $status_remplate[7]['status']=1;
          $status_remplate[7]['is_standard']=1;
          $status_remplate[7]['is_optional']=0;
          $status_remplate[7]['is_comment']=1;
          $status_remplate[7]['is_acquired']=1;
          $status_remplate[7]['standard_type']=4;
          $status_remplate[7]['optional_type']=0;
          $status_remplate[7]['order_id']=7;
          $status_remplate[7]['is_declined']=0;
          $status_remplate[7]['declined_grace_days']=0;
          $status_remplate[7]['declined_to']=0;
          $status_remplate[7]['is_reminder']=0;
          $status_remplate[7]['reminder_grace_days']=0;
          // EMAIL
          $email_template_constant[7]['is_email_ref']    = 1;
          $email_template_constant[7]['is_email_agt']    = 0;
          $email_template_constant[7]['app_id']          = $app_id;
          $email_template_constant[7]['event_id']        = 4;//Wills set in loop
          $email_template_constant[7]['value_id']        = $value_id;
          $email_template_constant[7]['ref_email_title'] = "Segnalazione Declinata";
          $email_template_constant[7]['ref_email_text']  = "<p>Ciao @@referral_name@@,</p><p>purtroppo questa volta non &egrave; andata come pensavamo, il sig.&nbsp;@@report_owner@@ relative alla segnalazione nr. @@report_no@@ non ha portato ad una vendita dell&#39;immobile<br /><br />Il motivo &egrave; stato questo:<br />@@comment@@<br /><br />Non disperare, abbiamo semplicemente tolto di mezzo uno delle mancante vendite che dobbiamo ovviamente prenderci.&nbsp;<br /><br />Riproviamo con il prossimo e sicuramente sar&agrave; la volta vince</p><p>Brinderemo assieme,</p><p>Un abbraccio @@agent_name@@</p>".$email_footer;
          $email_template_constant[7]['agt_email_title'] = "";
          $email_template_constant[7]['agt_email_text']  = $email_footer;
          // Reimnder
            // Reffreal
          $email_template_constant[7]['reminder_is_email_ref']    = 0;
          $email_template_constant[7]['reminder_ref_email_title'] = "";
          $email_template_constant[7]['reminder_ref_email_text']  = $email_footer;
            // Agent
          $email_template_constant[7]['reminder_is_email_agt']    = 0;
          $email_template_constant[7]['reminder_agt_email_title'] = "";
          $email_template_constant[7]['reminder_agt_email_text']  = $email_footer;
          // PUSH
          $push_template_constant[7]["app_id"]= $app_id;
          $push_template_constant[7]["value_id"]= $value_id;
          $push_template_constant[7]["event_id"]= 4;//Will set in loop
          $push_template_constant[7]["is_push_ref"]= 1;
          $push_template_constant[7]["is_push_agt"]= 0;
          $push_template_constant[7]["ref_push_title" ]= "Segnalazione Declinata o Non Venduto";
          $push_template_constant[7]["ref_push_text" ]= "Purtroppo questa volta non è andata come pensavamo, il sig. @@report_owner@@ relative alla segnalazione nr. @@report_no@@ non ha portato ad una vendita dell'immobile.Il motivo è stato questo: @@comment@@";
          $push_template_constant[7]["ref_open_feature"]= 1;
          $push_template_constant[7]["ref_feature_id"]= $value_id;
          $push_template_constant[7]["ref_custom_url"]= 0;
          $push_template_constant[7]["ref_cover_image"]= "event_4_referral.jpg";
          $push_template_constant[7]["agt_push_title"]= "";
          $push_template_constant[7]["agt_push_text"]= "";
          $push_template_constant[7]["agt_open_feature"]= 0;
          $push_template_constant[7]["agt_feature_id"]= 0;
          $push_template_constant[7]["agt_custom_url"]= "";
          $push_template_constant[7]["agt_cover_image"]= "";
          // Reminder PUSH
            // Reminder Refreela
          $push_template_constant[7]["reminder_is_push_ref"]      = 0;
          $push_template_constant[7]["reminder_is_push_agt"]      = 0;
          $push_template_constant[7]["reminder_ref_push_title"]   = '';
          $push_template_constant[7]["reminder_ref_push_text"]    = '';
          $push_template_constant[7]["reminder_ref_open_feature"] = 1;
          $push_template_constant[7]["reminder_ref_feature_id"]   = $value_id;
          $push_template_constant[7]["reminder_ref_custom_url"]   = NULL;
          $push_template_constant[7]["reminder_ref_cover_image"]  = NULL;
            // Reminder Agent
          $push_template_constant[7]["reminder_agt_push_title"]   = '';
          $push_template_constant[7]["reminder_agt_push_text"]    = '';
          $push_template_constant[7]["reminder_agt_open_feature"] = 1;
          $push_template_constant[7]["reminder_agt_feature_id"]   = $value_id;
          $push_template_constant[7]["reminder_agt_custom_url"]   = NULL;
          $push_template_constant[7]["reminder_agt_cover_image"]  = "";
          if ($type=="update") {
            // Delete all custom status when reset opration called
            $custom_status=$this->getCustomestatus($app_id);
            foreach ($custom_status as $key => $value) {
              $this->deletecustomstatus($value['migareference_report_status_id']);              
            }
          }
          $test=[];
            foreach ($email_template_constant as $key => $value) {
              $checkStatus=$this->checkStatus($app_id,$status_remplate[$key]['is_standard'],$status_remplate[$key]['standard_type'],$status_remplate[$key]['is_optional'],$status_remplate[$key]['optional_type']);              
              $test['key'][$key]=$app_id."@".$status_remplate[$key]['is_standard']."@".$status_remplate[$key]['standard_type']."@".$status_remplate[$key]['is_optional']."@".$status_remplate[$key]['optional_type'];
              $test['status'][$key]=$checkStatus;
              if (!count($checkStatus)) {
                $test['IF'][$key]=$checkStatus;
                $status_id           = $this->saveStatus($status_remplate[$key]);
                $push_template_constant[$key]["event_id"]  = $status_id;
                $email_template_constant[$key]['event_id'] = $status_id;
                $email_template_id   = $this->saveEmail( $email_template_constant[$key]);
                $push_template_id    = $this->savePushAuto($push_template_constant[$key]);
                                       $this->copyImages($app_id, $push_template_constant[$key],'');
                                       $this->copyImages($app_id, $status_remplate[$key],'');
                                       $noti_event_data=[];
                if ($email_template_id>0 && $push_template_id>0) {
                  $days=0;
                  $hours=0;
                  $noti_event_data['app_id']            = $app_id;
                  $noti_event_data['value_id']          = $value_id;
                  $noti_event_data['event_id']          = $status_id;
                  $noti_event_data['push_template_id']  = $push_template_id;
                  $noti_event_data['email_template_id'] = $email_template_id;
                  $noti_event_data['email_delay_days']  = $days;
                  $noti_event_data['push_delay_days']   = $days;
                  $noti_event_data['email_delay_hours'] = $hours;
                  $noti_event_data['push_delay_hours']  = $hours;
                  $push_template_id                     = $this->saveNotificationevent($noti_event_data);
                }
              }else{
                $test['ELSE'][$key]=$checkStatus;
                $status_id           = $this->updateStatusbyKey($status_remplate[$key],$checkStatus[0]['migareference_report_status_id']);
                $push_template_constant[$key]["migareference_push_template_id"]  = $checkStatus[0]['migareference_push_template_id'];
                $push_template_constant[$key]["event_id"]  = $status_id;
                $email_template_constant[$key]['event_id'] = $status_id;
                $email_template_constant[$key]['migareference_email_template_id'] = $checkStatus[0]['migareference_email_template_id'];;
                $email_template_id   = $this->updateEmail( $email_template_constant[$key]);
                $push_template_id    = $this->updatePushconstant($app_id,$push_template_constant[$key]);
                $this->copyImages($app_id, $push_template_constant[$key],'');
                $this->copyImages($app_id, $status_remplate[$key],'');
                if ($email_template_id>0 && $push_template_id>0) {
                  $days=0;
                  $hours=0;
                  $noti_event_data['app_id']            = $app_id;
                  $noti_event_data['value_id']          = $value_id;
                  $noti_event_data['event_id']          = $status_id;
                  $noti_event_data['push_template_id']  = $push_template_id;
                  $noti_event_data['email_template_id'] = $email_template_id;
                  $noti_event_data['email_delay_days']  = $days;
                  $noti_event_data['push_delay_days']   = $days;
                  $noti_event_data['email_delay_hours'] = $hours;
                  $noti_event_data['push_delay_hours']  = $hours;
                  $noti_event_data['migareference_notification_event_id']  = $checkStatus[0]['migareference_notification_event_id'];
                  $push_template_id                     = $this->updateNotificationevent( $noti_event_data);
                }
              }
            }
            return $test;
      }
  public static function compressDirectory($rootPath, $fileName)
  {

      // Initialize archive object

      $zip_file = $rootPath . "/" . $fileName;

      $zip = new ZipArchive();

      $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

      // Create recursive directory iterator

      /** @var SplFileInfo[] $files */

      $files = new RecursiveIteratorIterator(

          new RecursiveDirectoryIterator($rootPath),

          RecursiveIteratorIterator::LEAVES_ONLY

      );

      foreach ($files as $name => $file) {

          // Skip directories (they would be added automatically)

          if (!$file->isDir()) {

              // Get real and relative path for current file

              $filePath = $file->getRealPath();

              $relativePath = substr($filePath, strlen($rootPath) + 1);

              // Add current file to archive

              $zip->addFile($filePath, $relativePath);

          }

      }

      // Zip archive will be created only after closing object

      $zip->close();

      return;

  }
  public static function deleteDir($dirPath)
  {
      try {
          if (!is_dir($dirPath)) {
              if (is_file($dirPath)) {
                  chmod($dirPath, 0755);
                  unlink($dirPath);
              }
              return true;
              exit;
          }
          if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
              $dirPath .= '/';
          }
          $files = glob($dirPath . '*', GLOB_MARK);
          foreach ($files as $file) {
              chmod($file, 0755);
              if (is_dir($file)) {
                  Migareference_Model_Db_Table_Migareference::deleteDir($file);
              } elseif (is_file($file)) {
                  unlink($file);
              } else {
                  break;
              }
          }
          ;
          if (rmdir($dirPath)) {
              return "true";
              exit;
          }
      } catch (Exception $e) {
          return $e->getMessage();
      }
  }
      public function exportStatus($app_id=0)
      {
        $baseUrl = Core_Model_Directory::getBasePathTo("");
        if (!file_exists($baseUrl . "/var/tmp")) {
            mkdir($baseUrl . "/var/tmp");
        }
        if (!file_exists($baseUrl . "/var/tmp/migarefrenceexport")) {
            mkdir($baseUrl . "/var/tmp/migarefrenceexport");
        }
        if (file_exists($baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id)) {
          @  Migareference_Model_Db_Table_Migareference::deleteDir($baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id);
        }
        mkdir($baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id);
        $data = $this->grtsllkStatus($app_id);
        foreach ($data as $key => $value) {
            if ($value['status_icon'] != "") {
                copy("$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['status_icon'], $baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/t" . $value['status_icon']);
                $data[$key]['picture_path'] = "$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['status_icon'];
            }
            if ($value['ref_cover_image'] != "") {
                copy("$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['ref_cover_image'], $baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/t" . $value['ref_cover_image']);
                $data[$key]['picture_path'] = "$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['ref_cover_image'];
            }
            if ($value['agt_cover_image'] != "") {
                copy("$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['agt_cover_image'], $baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/t" . $value['agt_cover_image']);
                $data[$key]['picture_path'] = "$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['agt_cover_image'];
            }
            if ($value['reminder_ref_cover_image'] != "") {
                copy("$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['reminder_ref_cover_image'], $baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/t" . $value['reminder_ref_cover_image']);
                $data[$key]['picture_path'] = "$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['reminder_ref_cover_image'];
            }
            if ($value['reminder_agt_cover_image'] != "") {
                copy("$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['reminder_agt_cover_image'], $baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/t" . $value['reminder_agt_cover_image']);
                $data[$key]['picture_path'] = "$baseUrl/images/application/" . $value['app_id'] . "/features/migareference/" . $value['reminder_agt_cover_image'];
            }
            if ($value['ref_open_feature'] == 1 && $value['ref_custom_url'] == "") {
                $feature_record = $this->_db->fetchAll("SELECT application_option.code
                                                            FROM `application_option_value`
                                                            JOIN application_option on application_option_value.option_id=application_option.option_id
                                                            where value_id=" . $value['ref_feature_id']);
                if (!empty($feature_record)) {
                    $data[$key]['feature_name'] = $feature_record['0']['tabbar_name'];
                }
            }
            if ($value['agt_open_feature'] == 1 && $value['agt_custom_url'] == "") {
                $feature_record = $this->_db->fetchAll("SELECT application_option.code
                                                            FROM `application_option_value`
                                                            JOIN application_option on application_option_value.option_id=application_option.option_id
                                                            where value_id=" . $value['agt_feature_id']);
                if (!empty($feature_record)) {
                    $data[$key]['feature_name'] = $feature_record['0']['tabbar_name'];
                }
            }
            if ($value['reminder_agt_open_feature'] == 1 && $value['reminder_agt_custom_url'] == "") {
                $feature_record = $this->_db->fetchAll("SELECT application_option.code
                                                            FROM `application_option_value`
                                                            JOIN application_option on application_option_value.option_id=application_option.option_id
                                                            where value_id=" . $value['reminder_agt_feature_id']);
                if (!empty($feature_record)) {
                    $data[$key]['feature_name'] = $feature_record['0']['tabbar_name'];
                }
            }
            if ($value['reminder_ref_open_feature'] == 1 && $value['reminder_ref_custom_url'] == "") {
                $feature_record = $this->_db->fetchAll("SELECT application_option.code
                                                            FROM `application_option_value`
                                                            JOIN application_option on application_option_value.option_id=application_option.option_id
                                                            where value_id=" . $value['reminder_ref_feature_id']);
                if (!empty($feature_record)) {
                    $data[$key]['feature_name'] = $feature_record['0']['tabbar_name'];
                }
            }
        }
        $myfile = fopen($baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id . "/status_data.json", "w");
        fwrite($myfile, json_encode($data));
        fclose($myfile);
        Migareference_Model_Db_Table_Migareference::compressDirectory($baseUrl . "/var/tmp/migarefrenceexport/status" . $app_id, "status" . $app_id . ".zip");
        $default = new Core_Model_Default();
        $url = $default->getBaseUrl();
        $responce = [
            "file_path" => $url . "/var/tmp/migarefrenceexport/status" . $app_id . "/status" . $app_id . ".zip",
        ];
        return $responce;
      }
      public function getCustomestatus($app_id = 0)
      {
        $query_option = "SELECT *
        FROM migareference_report_status as st
        WHERE  st.is_optional=1 AND st.app_id=$app_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getCustomer($app_id = 0,$email="")
      {
        $query_option = "SELECT * FROM `customer` WHERE `app_id` = $app_id AND `email`='$email'";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getDayBookLogs()
      {
        $query_option = "SELECT *
        FROM migareference_reminder_daybook
        WHERE DATE(created_at) < DATE(NOW() - INTERVAL 3 DAY)
          AND status='pending';";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getCustomerMobile($app_id = 0,$mobile="")
      {
        $query_option = "SELECT *  
        FROM `migareference_invoice_settings` 
        JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
        WHERE migareference_invoice_settings.app_id=$app_id AND  migareference_invoice_settings.`invoice_mobile`=$mobile";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getSingleuser($app_id = 0,$user_id=0)
      {
        $query_option = "SELECT * FROM `customer` WHERE `app_id` = $app_id AND customer_id=$user_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getSingleuserByEmail($app_id = 0,$email='')
      {
        $query_option = "SELECT * FROM `customer` WHERE `app_id` = $app_id AND email='$email'";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function agmingAgentGroup($app_id = 0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migareference_app_agents` WHERE `app_id` = $app_id AND admin_user_id=$user_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getsingejob($id=0)
      {
        $query_option = "SELECT * FROM `migareference_jobs` WHERE migareference_jobs_id=$id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getsingeprofession($id=0)
      {
        $query_option = "SELECT * FROM `migareference_professions` WHERE migareference_professions_id=$id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getprize($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes` WHERE `app_id`=$app_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getprizewithredeem($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes`
                        LEFT JOIN migarefrence_redeemed_prizes ON migarefrence_redeemed_prizes.prize_id=migarefrence_prizes.migarefrence_prizes_id
                                                               AND migarefrence_redeemed_prizes.redeemed_by=$user_id
                        WHERE migarefrence_prizes.app_id=$app_id AND migarefrence_prizes.prize_status=1 GROUP BY migarefrence_prizes.migarefrence_prizes_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getredeemprizelist($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes`
                        JOIN migarefrence_redeemed_prizes ON migarefrence_redeemed_prizes.prize_id=migarefrence_prizes.migarefrence_prizes_id
                                                               AND migarefrence_redeemed_prizes.redeemed_by=$user_id
                        WHERE migarefrence_prizes.app_id=$app_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getSingleRedeemPrize($app_id=0,$user_id=0,$prize_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_prizes`
                        JOIN migarefrence_redeemed_prizes ON migarefrence_redeemed_prizes.prize_id=migarefrence_prizes.migarefrence_prizes_id
                                                               AND migarefrence_redeemed_prizes.redeemed_by=$user_id
                        WHERE migarefrence_prizes.app_id=$app_id AND migarefrence_prizes.migarefrence_prizes_id=$prize_id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function getSinglePrize($id=0)
      {
        $query_option = "SELECT *
                         FROM `migarefrence_prizes`
                         JOIN application ON application.app_id=migarefrence_prizes.app_id
                         WHERE migarefrence_prizes.`migarefrence_prizes_id`=$id";
        return $res_option   = $this->_db->fetchAll($query_option);
      }
      public function saveprize($data=[])
      {
        $app_id=$data['app_id'];
        if (!empty($data['prize_icon'])) {
            $icon = $data['prize_icon'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_prizes", $data);
        return $this->_db->lastInsertId();
      }
      public function updateprize($data=[])
      {
        $app_id=$data['app_id'];
        if (!empty($data['prize_icon'])) {
            $icon = $data['prize_icon'];
            $ext = pathinfo($icon, PATHINFO_EXTENSION);
            $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
            $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
            if (!is_dir($dir_image)) mkdir($dir_image, 0775, true);
            if (!is_dir($dir_image . "/features")) mkdir($dir_image . "/features", 0775, true);
            if (!is_dir($dir_image . "/features/migareference")) mkdir($dir_image . "/features/migareference", 0775, true);
            $dir_image .= "/features/migareference/";
            $image_name = $icon;
            if (file_exists($file)) {
                if (!copy($file, $dir_image . $image_name)) {
                    throw new exception(__('An error occurred while saving. Please try again later.'));
                } else {
                    $cover = $icon;
                }
            } else {
                $cover = $icon;
            }
        }else {
          unset($data['prize_icon']);
        }
        $id=$data['migarefrence_prizes_id'];
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_prizes", $data,['migarefrence_prizes_id = ?' => $id]);
      }
      public function insert_notes($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_notes", $data);
        return $this->_db->lastInsertId();
      }
      public function insertaddresses($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_property_addresses", $data);
        return $this->_db->lastInsertId();
      }
      public function addSponsor($data=[])
      {

        $this->_db->insert("migareference_referrer_agents", $data);
        $this->_db->lastInsertId();
        //Add agent to Invoice table as well
        $this->updateInvoiceSponsor($data);
      }
      // a temp method to to copy previous agents data from migareference_referrer_agents to migareference_invoice_settings
      public function copySponsor()
      {
        // $query_option = "SELECT * FROM `migareference_referrer_agents` WHERE 1";
        // $res_option   = $this->_db->fetchAll($query_option);
        // foreach ($res_option as $key => $value) {
        //   $this->updateInvoiceSponsor($value);
        // }
      }
      public function tempUpdates()//Will be called on Install: this method will be used for any particular one time updates
      {
        //Update migareference_geo_countries table where countery is Swit and set countery_code=CH 
        $update['country_code'] = 'CH';        
        $this->_db->update("migareference_geo_countries", $update,['country = ?' => 'Switzerland']);
        // Fetch all records where the country is Switzerland
        $switzerland_countries = $this->_db->fetchAll("SELECT * FROM migareference_geo_countries WHERE country = 'Switzerland'");
        $switzerland_provinces = array(
          "Aargau"=>"AG",	
          "Appenzell Ausserrhoden"=>"AR",	
          "Appenzell Innerrhoden"=>"AI",	
          "Basel-Landschaft"=>"BL",	
          "Basel-Stadt"=>"BS",	
          "Bern"=>"BE",	
          "Berne"=>"BE",	
          "Freiburg"=>"FR",	
          "Fribourg"=>"FR",	
          "Genève"=>"GE",	
          "Glarus"=>"GL",	
          "Graubünden"=>"GR",	
          "Grigioni"=>"GR",	
          "Grischun"=>"GR",	
          "Jura"=>"JU",	
          "Luzern"=>"LU",	
          "Neuchâtel"=>"NE",	
          "Nidwalden"=>"NW",	
          "Obwalden"=>"OW",	
          "Sankt Gallen"=>"SG",	
          "Schaffhausen"=>"SH",	
          "Schwyz"=>"SZ",	
          "Solothurn"=>"SO",	
          "Thurgau"=>"TG",	
          "Ticino"=>"TI",	
          "Uri"=>"UR",	
          "Valais"=>"VS",	
          "Vaud"=>"VD",	
          "Wallis"=>"VS",	
          "Zug"=>"ZG",	
          "Zürich"=>"ZH"
        );
           // Add provinces for Switzerland for all existing apps
          foreach ($switzerland_countries as $country) {
            $country_id = $country['migareference_geo_countries_id']; // Assuming the table has an 'id' column for the country
            $app_id = $country['app_id']; // Assuming the table stores the app_id

            foreach ($switzerland_provinces as $key => $value) {
                // Check if the province already exists
                $existing_province = $this->_db->fetchAll("SELECT migareference_geo_provinces_id FROM migareference_geo_provinces WHERE country_id = $country_id AND province ='$key'  AND app_id =$app_id");   
                if (!COUNT($existing_province)) {
                    // Insert province if it does not already exist
                    $province_data['app_id'] = $app_id;
                    $province_data['country_id'] = $country_id;
                    $province_data['province'] = $key;
                    $province_data['province_code'] = $value;
                    $this->addProvince($province_data);
                }
            }
        }     
        return $switzerland_countries;
      }
      public function updateInvoiceSponsor($data=[])
      {
        //get agent type either one or two and assign to relevant column
        $agent_key=$data['agent_id'];
        $query_option_value = "SELECT * FROM migareference_app_agents  WHERE user_id=$agent_key";
        $agent_item = $this->_db->fetchAll($query_option_value);
        if ($agent_item[0]['agent_type']==1) {
          $update['sponsor_one_id'] = $data['agent_id'];
        }else {
          $update['sponsor_two_id'] = $data['agent_id'];
        }        
        $this->_db->update("migareference_invoice_settings", $update,['user_id = ?' => $data['referrer_id']]);
      }
      public function insertphonenumber($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_blacklist_phone_numbers", $data);
        return $this->_db->lastInsertId();
      }
      public function insertjob($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_jobs", $data);
        return $this->_db->lastInsertId();
      }
      public function insertProfession($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_professions", $data);
        return $this->_db->lastInsertId();
      }
      public function createUser($data=[])
      {
        $customer_exist=$this->getCustomer($data['app_id'],$data['email']);
        if (!count($customer_exist)) {          
            $data['created_at']    = date('Y-m-d H:i:s');
            $this->_db->insert("customer", $data);
            return $this->_db->lastInsertId();
        }else{
          return $customer_exist[0]['customer_id'];
        }
      }
      public function savekey($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_usercreation", $data);
        return $this->_db->lastInsertId();
      }
      public function getJobs($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_jobs`
                         WHERE `app_id`=$app_id ORDER  BY `migareference_jobs`.`job_title`  ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getProfessions($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_professions`
                         WHERE `app_id`=$app_id ORDER  BY `migareference_professions`.`profession_title`  ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSponsor($app_id=0,$user_id=0)
      {
        $query_option = "SELECT * FROM `migareference_invoice_settings`
                         WHERE `app_id`=$app_id AND user_id=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getProspectJobs($app_id=0,$type=0)
      {
        $query_option = "SELECT *,migarefrence_phonebook.created_at as phone_creat_date
                         FROM `migarefrence_phonebook`
                         LEFT JOIN migareference_report on migarefrence_phonebook.report_id=migareference_report.migareference_report_id
                         LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.user_id=migareference_report.user_id OR migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
                         LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id=migareference_invoice_settings.sponsor_id
                         LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
                         WHERE migarefrence_phonebook.`app_id`=$app_id AND migarefrence_phonebook.type=$type AND (migarefrence_phonebook.report_id!=0 OR migarefrence_phonebook.invoice_id!=0)
                         ORDER BY migarefrence_phonebook.name ASC, migarefrence_phonebook.surname ASC, migarefrence_phonebook.created_at ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAgentReferrerPhonebook($app_id=0,$user_id=0)
      {
        $query_option = "SELECT *,
        migarefrence_phonebook.created_at as phone_creat_date,
        COUNT(migareference_report.migareference_report_id) as total_reports,
        migareference_invoice_settings.user_id as user_id,
        migareference_referrer_agents.agent_id AS sponsor_one_id,
        migareference_referrer_agents.agent_id AS sponsor_two_id        
        FROM migareference_invoice_settings
        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=migareference_invoice_settings.migareference_invoice_settings_id
        LEFT JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id AND migareference_report.status=1
        JOIN migareference_referrer_agents ON migareference_referrer_agents.referrer_id=migareference_invoice_settings.user_id AND migareference_referrer_agents.agent_id=$user_id
        JOIN customer ON customer.customer_id=migareference_invoice_settings.user_id
        WHERE migareference_invoice_settings.app_id=$app_id                        
        GROUP BY migareference_invoice_settings.migareference_invoice_settings_id
        ORDER BY migareference_invoice_settings.invoice_surname ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAgentProspectPhonebook($app_id=0,$user_id=0)
      {
        $query_option = "SELECT *,migarefrence_phonebook.created_at as phone_creat_date
                        FROM migareference_invoice_settings
                        JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id
                        JOIN migarefrence_phonebook ON migarefrence_phonebook.report_id=migareference_report.migareference_report_id
                        WHERE migareference_invoice_settings.app_id=$app_id AND migareference_invoice_settings.sponsor_id=$user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerJobs($app_id=0,$type=0)
      {
        $query_option = "SELECT *,migarefrence_phonebook.created_at as phone_creat_date
                         FROM `migarefrence_phonebook`
                         LEFT JOIN migareference_jobs ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
                         LEFT JOIN migareference_invoice_settings ON migareference_invoice_settings.migareference_invoice_settings_id=migarefrence_phonebook.invoice_id
                         WHERE migarefrence_phonebook.`app_id`=$app_id AND migarefrence_phonebook.type=$type AND (migarefrence_phonebook.report_id!=0 OR migarefrence_phonebook.invoice_id!=0)
                         ORDER BY migarefrence_phonebook.name ASC, migarefrence_phonebook.surname ASC, migarefrence_phonebook.created_at ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getAllPhoneBook($app_id=0)
      {
        $query_option = "SELECT *
                         FROM `migarefrence_phonebook`
                        WHERE migarefrence_phonebook.`app_id`=$app_id AND (migarefrence_phonebook.report_id!=0 OR migarefrence_phonebook.invoice_id!=0)";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isPhoneEmailExist($app_id=0,$email='',$mobile='',$type=0)
      {
        $query_option = "SELECT *
                         FROM `migarefrence_phonebook`
                         WHERE migarefrence_phonebook.`app_id`=$app_id
                         AND migarefrence_phonebook.type=$type AND migarefrence_phonebook.mobile='$mobile' ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function isProspectExist($app_id=0,$mobile='')
      {
        $query_option="SELECT * FROM `migarefrence_prospect` WHERE migarefrence_prospect.mobile='$mobile' AND migarefrence_prospect.app_id=$app_id";            
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function referrer_to_prospect($invoice_id=0)
      {
        $query_option = "SELECT migarefrence_phonebook.*
                         FROM `migareference_invoice_settings`
                         JOIN migareference_report ON migareference_report.user_id=migareference_invoice_settings.user_id
                         JOIN migarefrence_phonebook ON migarefrence_phonebook.report_id=migareference_report.migareference_report_id
                         WHERE migareference_invoice_settings.migareference_invoice_settings_id=$invoice_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getInvoiceItem($invoice_id=0)
      {
        $query_option = "SELECT *
                         FROM `migareference_invoice_settings`
                         WHERE migareference_invoice_settings.migareference_invoice_settings_id=$invoice_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }

      public function editnote($public_key=0)
      {
        $query_option = "SELECT * FROM `migarefrence_notes`
                         WHERE `migarefrence_notes_id`=$public_key";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getaddresses($app_id=0)
      {
        $query_option = "SELECT adr.address,adr.longitude,adr.latitude,migareference_report.report_no,count(migareference_report.report_no) as count FROM `migarefrence_property_addresses` as adr
                         LEFT JOIN migareference_report ON migareference_report.address=adr.address
                         WHERE adr.app_id=$app_id
                         GROUP BY adr.address,adr.longitude,adr.latitude";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getphonenumbers($app_id=0)
      {
        $query_option = "SELECT migarefrence_blacklist_phone_numbers.phone_number,migareference_report.report_no,count(migareference_report.report_no) as count FROM `migarefrence_blacklist_phone_numbers`
                         LEFT JOIN migareference_report ON migareference_report.owner_mobile=migarefrence_blacklist_phone_numbers.phone_number
                         WHERE migarefrence_blacklist_phone_numbers.app_id=$app_id
                         GROUP BY migarefrence_blacklist_phone_numbers.phone_number";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function informatToFormatePhoneNumbers($app_id=0)
      {
        // phonebook
        $query_option = "SELECT *
        FROM migarefrence_phonebook
        WHERE migarefrence_phonebook.mobile NOT LIKE '+%' AND mobile NOT LIKE '00%' AND mobile NOT LIKE '%*%' AND app_id=$app_id";        
        $res_option   = $this->_db->fetchAll($query_option);
        foreach ($res_option as $row) {
            $migarefrence_phonebook_id = $row['migarefrence_phonebook_id'];
            $mobile_number = $row['mobile'];
            $updated_mobile = '+39' . $mobile_number;
            $data = ['mobile' => $updated_mobile];
            $result = $this->_db->update("migarefrence_phonebook", $data, ['migarefrence_phonebook_id = ?' => $migarefrence_phonebook_id]);
        }
        // Invoice table
        $query_option = "SELECT migareference_invoice_settings_id,invoice_mobile
          FROM migareference_invoice_settings
          WHERE invoice_mobile NOT LIKE '+%' AND invoice_mobile NOT LIKE '00%' AND invoice_mobile NOT LIKE '%*%' AND app_id=$app_id";        
        $res_option   = $this->_db->fetchAll($query_option);
        foreach ($res_option as $row) {
            $migareference_invoice_settings_id = $row['migareference_invoice_settings_id'];
            $mobile_number = $row['invoice_mobile'];
            $updated_mobile = '+39' . $mobile_number;
            $data = ['invoice_mobile' => $updated_mobile];
            $result = $this->_db->update("migareference_invoice_settings", $data, ['migareference_invoice_settings_id = ?' => $migareference_invoice_settings_id]);
        }
        return $res_option;
      }
      public function getBlackListPhoneNumers($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_blacklist_phone_numbers` WHERE `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function deletecustomstatus($public_key=0)
      {
        $this->_db->delete('migareference_report_status',['migareference_report_status_id = ?' => $public_key]);
      }
      public function deletecustomstatusbyapp($app_id=0)
      {
        $query_option = "SELECT *
                        FROM migareference_report_status
                        LEFT JOIN migareference_push_template ON
                        migareference_report_status.migareference_report_status_id=migareference_push_template.event_id
                        LEFT JOIN migareference_email_template ON
                        migareference_report_status.migareference_report_status_id=migareference_email_template.event_id
                        LEFT JOIN migareference_notification_event ON
                        migareference_report_status.migareference_report_status_id=migareference_notification_event.event_id
                        WHERE migareference_report_status.app_id=$app_id AND migareference_report_status.is_standard=0";
        $res_option   = $this->_db->fetchAll($query_option);
        foreach ($res_option as $key => $value) {
          $event_id=$value['migareference_report_status_id'];
          $this->_db->delete('migareference_report_status',['app_id = ?' => $app_id,'migareference_report_status_id = ?'=> $event_id]);
          $this->_db->delete('migareference_push_template',['app_id = ?' => $app_id,'event_id = ?'=> $event_id]);
          $this->_db->delete('migareference_email_template',['app_id = ?' => $app_id,'event_id = ?'=> $event_id]);
          $this->_db->delete('migareference_notification_event',['app_id = ?' => $app_id,'event_id = ?'=> $event_id]);
        }
      }
      public function deleteexternaladdress($key=0)
      {
        $this->_db->delete('migarefrence_property_addresses',['migarefrence_property_addresses_id = ?' => $key]);
      }
      public function deleteCommunicationLog($key=0)
      {
        $this->_db->delete('migareference_communication_logs',['migareference_communication_logs_id = ?' => $key]);
      }
      public function deleteexternalphonenumber($key=0)
      {
        $this->_db->delete('migarefrence_blacklist_phone_numbers',['migarefrence_blacklist_phone_numbers_id = ?' => $key]);
      }
      public function deleteaddresses($app_id=0)
      {
        $this->_db->delete('migarefrence_property_addresses',['app_id = ?' => $app_id]);
      }
      public function deleteemailtemplate($public_key=0)
      {
        $this->_db->delete('migareference_email_template',['event_id = ?' => $public_key]);
      }
      public function deletepushtemplate($public_key=0)
      {
        $this->_db->delete('migareference_push_template',['event_id = ?' => $public_key]);
      }
      public function deleteeventnotfication($public_key=0)
      {
        $this->_db->delete('migareference_notification_event',['event_id = ?' => $public_key]);
      }
      public function deletenote($public_key=0)
      {
        $this->_db->delete('migarefrence_notes',['migarefrence_notes_id = ?' => $public_key]);
      }
      public function update_notes($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_notes", $data,['migarefrence_notes_id = ?' => $id]);
      }
      public function updatejob($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migareference_jobs", $data,['migareference_jobs_id = ?' => $id]);
      }
      public function updateProfession($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migareference_professions", $data,['migareference_professions_id = ?' => $id]);
      }
      public function update_reminder($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_reminders", $data,['migarefrence_reminders_id = ?' => $id]);
      }
      public function updateAutoReminder($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migareference_automation_log", $data,['migareference_automation_log_id = ?' => $id]);
      }
      public function insert_reminder($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_reminders", $data);
        return $this->_db->lastInsertId();
      }
      public function get_reminder($app_id=0,$report_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_reminders`
                         JOIN `migarefrence_reminder_type` ON migarefrence_reminder_type.migarefrence_reminder_type_id=migarefrence_reminders.event_type
                         WHERE migarefrence_reminders.app_id=$app_id AND `report_id`=$report_id ORDER  BY `migarefrence_reminders`.`created_at`  DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSingleReportReminder($app_id=0,$report_id=0)
      {
        $query_option = "SELECT *
                         FROM `migarefrence_reminders` as rm
                         JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=rm.event_type
                         WHERE rm.app_id=$app_id AND rm.report_id=$report_id AND rm.is_deleted=0";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function get_all_reminder($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_reminders`
                         JOIN `migarefrence_reminder_type` ON migarefrence_reminder_type.migarefrence_reminder_type_id=migarefrence_reminders.event_type
                         WHERE migarefrence_reminders.app_id=$app_id ORDER  BY `migarefrence_reminders`.`created_at`  DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportReminders($app_id=0)
      {
        $query_option = "SELECT *
                         FROM `migarefrence_reminders` as rm
                         JOIN migareference_report as mr ON mr.migareference_report_id=rm.report_id
                         JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=rm.event_type
                         JOIN migareference_invoice_settings AS migainv ON migainv.user_id=mr.user_id
                         LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id=migainv.sponsor_id
                         WHERE  rm.app_id=$app_id  GROUP BY rm.migarefrence_reminders_id ORDER BY rm.event_date_time ASC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerReminders($app_id=0)
      {
        $query_option = "SELECT *,
                        alg.created_at AS automation_trigger_stamp,
                        alg.report_id AS automation_trigger_report_id,
                        alg.created_at AS reminder_created_at,
                        sponsor_one.customer_id AS sponsor_one_id,
                        sponsor_two.customer_id AS sponsor_two_id,
                        sponsor_one.firstname AS sponsor_one_firstname,
                        sponsor_one.lastname AS sponsor_one_lastname,
                        sponsor_two.firstname AS sponsor_two_firstname,
                        sponsor_two.lastname AS sponsor_two_lastname,
                        inv.sponsor_id,
                        inv.user_id AS referrer_id
                        FROM `migareference_automation_log` AS alg
                        JOIN migareference_invoice_settings AS inv ON inv.user_id=alg.user_id                        
                        LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=inv.user_id
                        LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=inv.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
                        LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
                        LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
                        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
                        JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
                        JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
                        LEFT JOIN migareference_report AS rp ON rp.migareference_report_id=alg.report_id
                        JOIN customer ON customer.customer_id=inv.user_id
                        WHERE alg.app_id=$app_id AND alg.is_deleted=0                        
                        ORDER BY alg.created_at DESC;";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerRemindersNonCanceled($app_id=0)
      {
        $query_option = "SELECT *,
                        alg.created_at AS automation_trigger_stamp,
                        alg.report_id AS automation_trigger_report_id,
                        alg.created_at AS reminder_created_at,
                        sponsor_one.customer_id AS sponsor_one_id,
                        sponsor_two.customer_id AS sponsor_two_id,
                        sponsor_one.firstname AS sponsor_one_firstname,
                        sponsor_one.lastname AS sponsor_one_lastname,
                        sponsor_two.firstname AS sponsor_two_firstname,
                        sponsor_two.lastname AS sponsor_two_lastname,
                        inv.sponsor_id,
                        inv.user_id AS referrer_id
                        FROM `migareference_automation_log` AS alg
                        JOIN migareference_invoice_settings AS inv ON inv.user_id=alg.user_id                        
                        LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=inv.user_id
                        LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=inv.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
                        LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
                        LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
                        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
                        JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
                        JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
                        LEFT JOIN migareference_report AS rp ON rp.migareference_report_id=alg.report_id
                        JOIN customer ON customer.customer_id=inv.user_id
                        WHERE alg.app_id=$app_id AND alg.is_deleted=0 AND alg.current_reminder_status!='cancele'                       
                        ORDER BY alg.created_at DESC;";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerRemindersCanceled($app_id=0,$limit=0)
      {
        $query_option = "SELECT *,
                        alg.created_at AS automation_trigger_stamp,
                        alg.report_id AS automation_trigger_report_id,
                        alg.created_at AS reminder_created_at,
                        sponsor_one.customer_id AS sponsor_one_id,
                        sponsor_two.customer_id AS sponsor_two_id,
                        sponsor_one.firstname AS sponsor_one_firstname,
                        sponsor_one.lastname AS sponsor_one_lastname,
                        sponsor_two.firstname AS sponsor_two_firstname,
                        sponsor_two.lastname AS sponsor_two_lastname,
                        inv.sponsor_id,
                        inv.user_id AS referrer_id
                        FROM `migareference_automation_log` AS alg
                        JOIN migareference_invoice_settings AS inv ON inv.user_id=alg.user_id                        
                        LEFT JOIN migareference_referrer_agents AS refag_one ON refag_one.referrer_id=inv.user_id
                        LEFT JOIN migareference_referrer_agents AS refag_two ON refag_two.referrer_id=inv.user_id && refag_two.migareference_referrer_agents_id!=refag_one.migareference_referrer_agents_id        
                        LEFT JOIN customer AS sponsor_one ON sponsor_one.customer_id=refag_one.agent_id
                        LEFT JOIN customer AS sponsor_two ON sponsor_two.customer_id=refag_two.agent_id  
                        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
                        JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
                        JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
                        LEFT JOIN migareference_report AS rp ON rp.migareference_report_id=alg.report_id
                        JOIN customer ON customer.customer_id=inv.user_id
                        WHERE alg.app_id=$app_id AND alg.is_deleted=0 AND alg.current_reminder_status='cancele'                       
                        ORDER BY alg.created_at DESC
                        LIMIT $limit";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getFilteredReferrerReminders($app_id=0,$trigger_id=0,$assigned_to=0,$current_reminder_status='')
      {
        $query_option = "SELECT *,alg.created_at AS automation_trigger_stamp,alg.report_id AS automation_trigger_report_id
                        FROM `migareference_automation_log` AS alg
                        JOIN migareference_invoice_settings AS inv ON inv.user_id=alg.user_id
                        LEFT JOIN migareference_app_agents ON migareference_app_agents.user_id=inv.sponsor_id
                        JOIN migarefrence_phonebook ON migarefrence_phonebook.invoice_id=inv.migareference_invoice_settings_id
                        JOIN migarefrence_report_reminder_types as rt ON rt.migarefrence_report_reminder_types_id=alg.trigger_type_id
                        JOIN migarefrence_report_reminder_auto AS rmat ON rmat.migarefrence_report_reminder_auto_id=alg.report_reminder_auto_id
                        LEFT JOIN migareference_report AS rp ON rp.migareference_report_id=alg.report_id
                        JOIN customer ON customer.customer_id=inv.user_id
                        WHERE alg.app_id=$app_id AND alg.is_deleted=0";
        // Reminder Staus
        if ($current_reminder_status!='all') {
          $query_option.=' AND alg.current_reminder_status='."'$current_reminder_status'";
        }
        // Reminder User Either Agent or Admins: 1000000 indicate its admin and 0 for both
        if ($assigned_to) {
          $query_option.=' AND alg.reminder_to='.$assigned_to;
        }
        // Filter by report reminders
        if ($trigger_id) {
          $query_option.=' AND alg.report_reminder_auto_id='.$trigger_id;
        }
        $query_option.=' ORDER BY alg.created_at DESC';
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function editreminder($public_key=0)
      {
        $query_option = "SELECT * FROM `migarefrence_reminders`
                         WHERE `migarefrence_reminders_id`=$public_key";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getResetLogs($app_id=0)
      {
        $query_option = "SELECT * 
        FROM `migareference_reminder_reset_logs`
        JOIN admin ON admin.admin_id=migareference_reminder_reset_logs.admin_id
        WHERE migareference_reminder_reset_logs.app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function autoReminderItem($public_key=0)
      {
        $query_option = "SELECT *
        FROM `migareference_automation_log`
        JOIN migarefrence_report_reminder_auto ON migarefrence_report_reminder_auto.migarefrence_report_reminder_auto_id=migareference_automation_log.report_reminder_auto_id
        WHERE `migareference_automation_log_id`=$public_key";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function deleteReminderType($public_key=0)
      {
        $this->_db->delete('migarefrence_report_reminder_types',['migarefrence_report_reminder_types_id = ?' => $public_key]);
      }
      public function deletejob($public_key=0)
      {
        $this->_db->delete('migareference_jobs',['migareference_jobs_id = ?' => $public_key]);
      }
      public function deleteProfession($public_key=0)
      {
        $this->_db->delete('migareference_professions',['migareference_professions_id = ?' => $public_key]);
      }
      public function insert_reminder_type($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_reminder_type", $data);
        return $this->_db->lastInsertId();
      }
      public function saveReportReminderAuto($data=[])
      {
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_report_reminder_auto", $data);
        return $this->_db->lastInsertId();
      }
      public function getReportReminderAuto($app_id=0)
      {
        $query_option = "SELECT migarefrence_report_reminder_auto.*,COUNT(migareference_automation_log.migareference_automation_log_id) AS total_trigger
                        FROM `migarefrence_report_reminder_auto`
                        LEFT JOIN migareference_automation_log ON migareference_automation_log.report_reminder_auto_id=migarefrence_report_reminder_auto.migarefrence_report_reminder_auto_id  AND migareference_automation_log.app_id=migarefrence_report_reminder_auto.app_id AND migareference_automation_log.is_deleted=0
                        WHERE migarefrence_report_reminder_auto.app_id=$app_id 
                        GROUP BY migarefrence_report_reminder_auto.migarefrence_report_reminder_auto_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getDeclinedFixRating($app_id=0)
      {
        $query_option = "SELECT *  FROM `migarefrence_report_reminder_auto` WHERE `app_id` = $app_id AND `auto_rem_trigger` = 6 ORDER BY `migarefrence_report_reminder_auto_id`  DESC";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function deleteReportReminderAuto($id=0)
      {
        $this->_db->delete('migarefrence_report_reminder_auto',['migarefrence_report_reminder_auto_id = ?' => $id]);
      }
      public function updateReportReminderAuto($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_report_reminder_auto", $data,['migarefrence_report_reminder_auto_id = ?' => $id]);
      }
      public function getReportReminder($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_report_reminder_types`
                         WHERE `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReportReminderType($app_id=0,$type)
      {
        $query_option = "SELECT * FROM `migarefrence_report_reminder_types`
                         WHERE `app_id`=$app_id AND rep_rem_type=$type";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSingleReminderType($id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_report_reminder_types`
                         WHERE `migarefrence_report_reminder_types_id`=$id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getReferrerAgents($app_id=0,$referrer_id=0)
      {
        $query_option = "SELECT * 
        FROM `migareference_referrer_agents` 
        JOIN migareference_app_agents ON migareference_app_agents.user_id=migareference_referrer_agents.agent_id
        JOIN customer ON customer.customer_id=migareference_app_agents.user_id AND customer.app_id=migareference_referrer_agents.app_id
        WHERE migareference_referrer_agents.app_id=$app_id
        AND migareference_referrer_agents.referrer_id=$referrer_id
        GROUP BY migareference_app_agents.migareference_app_agents_id
        ORDER BY migareference_app_agents.agent_type;";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSingleReminderAuto($id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_report_reminder_auto`
                         WHERE `migarefrence_report_reminder_auto_id`=$id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getSinglePhonebook($id=0)
      {
        $query_option = "SELECT migarefrence_phonebook.*,
                        migarefrence_phonebook.created_at as phone_creat_date,
                        migarefrence_phonebook.note as phone_note,
                        migareference_invoice_settings.*,
                        migareference_report.*,
                        migareference_jobs.*,
                        migareference_professions.*,
                        migareference_geo_provinces.*,
                        customer.customer_id,
                        customer.birthdate,
                        customer.privacy_policy,
                        customer.created_at as customer_consent_date
                         FROM `migarefrence_phonebook`
                         LEFT JOIN migareference_jobs
                         ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
                         LEFT JOIN migareference_professions
                         ON migareference_professions.migareference_professions_id=migarefrence_phonebook.profession_id
                         LEFT JOIN migareference_invoice_settings
                         ON migareference_invoice_settings.migareference_invoice_settings_id=migarefrence_phonebook.invoice_id
                         LEFT JOIN customer
                         ON customer.customer_id=migareference_invoice_settings.user_id
                         LEFT JOIN migareference_report
                         ON migareference_report.migareference_report_id=migarefrence_phonebook.report_id
                         LEFT JOIN migareference_geo_provinces 
                         ON migareference_geo_provinces.migareference_geo_provinces_id=migareference_invoice_settings.address_province_id
                         WHERE migarefrence_phonebook.migarefrence_phonebook_id=$id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function prospectRefPhnDetail($id=0)
      {
        $query_option = "SELECT migarefrence_phonebook.*,
                        migarefrence_phonebook.created_at as phone_creat_date,
                        migareference_invoice_settings.*,
                        migareference_report.*,
                        migareference_jobs.*,
                        customer.customer_id,
                        customer.birthdate,
                        customer.privacy_policy,
                        customer.created_at as customer_consent_date
                         FROM `migarefrence_phonebook`
                         LEFT JOIN migareference_jobs
                         ON migareference_jobs.migareference_jobs_id=migarefrence_phonebook.job_id
                         LEFT JOIN migareference_invoice_settings
                         ON migareference_invoice_settings.migareference_invoice_settings_id=migarefrence_phonebook.invoice_id
                         LEFT JOIN customer
                         ON customer.customer_id=migareference_invoice_settings.user_id
                         LEFT JOIN migareference_report
                         ON migareference_report.migareference_report_id=migarefrence_phonebook.report_id
                         WHERE migarefrence_phonebook.invoice_id=$id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getInvoicePhonebook($id=0)
      {
        $query_option = "SELECT *  FROM `migarefrence_phonebook` WHERE `invoice_id` = $id AND `type` = 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
        //DELETE STATUS: set Status=1 Active 2 Disable 3 Delete
      public function update_reminder_type($id=0,$data=[])
      {
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_reminder_type", $data,['migarefrence_reminder_type_id = ?' => $id]);
      }
      public function saveReportReminder($data=[])
      {
        // Updload PUSH FILE
        $this->uploadApplicationFile($data['app_id'],$data['rep_rem_custom_file'],0);
        $this->uploadApplicationFile($data['app_id'],$data['rep_rem_icon_file'],0);
        $data['rep_rem_custom_file']=$data['rep_rem_c_migareference_cover_file'];
        unset($data['rep_rem_c_migareference_cover_file']);
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migarefrence_report_reminder_types", $data);
        return $this->_db->lastInsertId();
      }
      public function updateReportReminder($id=0,$data=[])
      {
        $this->uploadApplicationFile($data['app_id'],$data['rep_rem_custom_file'],0);
        $this->uploadApplicationFile($data['app_id'],$data['rep_rem_icon_file'],0);
        if (empty($data['rep_rem_custom_file'])) {
          $data['rep_rem_custom_file']=$data['rep_rem_c_migareference_cover_file'];
        }
        if (empty($data['rep_rem_icon_file'])) {
          $data['rep_rem_icon_file']=$data['c_rep_rem_icon_file'];
        }
        unset($data['c_rep_rem_icon_file']);
        unset($data['rep_rem_c_migareference_cover_file']);
        $data['updated_at']  = date('Y-m-d H:i:s');
        return $this->_db->update("migarefrence_report_reminder_types", $data,['migarefrence_report_reminder_types_id = ?' => $id]);
      }
      public function get_custom_reminder_settings($app_id=0)
      {
        $query_option = "SELECT * FROM `migarefrence_reminder_email_push`
                         WHERE `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function getcronReminders()
      {
        $query_option = "SELECT *
                         FROM `migarefrence_reminders` as rm
                         JOIN migareference_report as mr ON mr.migareference_report_id=rm.report_id
                         JOIN migareference_invoice_settings as inv ON inv.user_id=mr.user_id
                         JOIN customer as cs ON cs.customer_id=inv.user_id
                         WHERE rm.reminder_current_status!='Done' AND rm.reminder_current_status!='Cancele' ";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadgraphstats($data=[])
      {
        $date_from=$data['from_date'];
        $date_to  =$data['to_date'];
        $query_option = "SELECT * FROM `migarefrence_stats` WHERE `created_at`>='$date_from' AND `created_at`<='$date_to' GROUP BY DATE(created_at)";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadallgraphstats($data=[])
      {
        $query_option = "SELECT * FROM `migarefrence_stats` WHERE 1 LIMIT 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function application($app_id=0)
      {
        $query_option = "SELECT * FROM `application` WHERE app_id=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function phonebookReportStats($app_id=0,$invoice_id=0)
      {
        $query_option = "SELECT
        inv.terms_accepted,
        COUNT(DISTINCT migrp.migareference_report_id) as total_reports,
        COUNT(DISTINCT CASE WHEN migrp.currunt_report_status=mrst.migareference_report_status_id AND mrst.standard_type!=3 AND mrst.standard_type!=4   THEN migrp.migareference_report_id END) active_reports
        FROM `migareference_report` AS migrp
        JOIN migareference_report_status as mrst ON   mrst.app_id=$app_id
        JOIN migareference_invoice_settings as inv ON inv.migareference_invoice_settings_id=$invoice_id
        WHERE migrp.user_id=inv.user_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadtablestats($data = [])
      {
        $date_from = $data['from_date'];
        $date_to   = $data['to_date'];
          // $query_option = "SELECT
          //                  preset.app_id,
          //                  ap.name,
          //                  COUNT(DISTINCT invst.migareference_invoice_settings_id) refrel_users,
          //                  COUNT(DISTINCT adm.migareference_app_admins_id) as admins,
          //                  COUNT(DISTINCT invst.migareference_invoice_settings_id)-COUNT(DISTINCT adm.migareference_app_admins_id) as net_refreal,
          //                  COUNT(DISTINCT migrp.migareference_report_id) as total_reports,
          //                  COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id AND mrst.standard_type!=3 AND mrst.standard_type!=4   THEN migrp.migareference_report_id END) active_reports,
          //                  COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id  AND mrst.standard_type=4 THEN migrp.migareference_report_id END) declined_reports,
          //                  COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id  AND mrst.standard_type=3 THEN migrp.migareference_report_id END) paid_reports,
          //                  COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id AND migrp.commission_fee>0 AND mrst.standard_type!=1  AND mrst.standard_type!=3 AND mrst.standard_type!=4   THEN migrp.migareference_report_id END) payable_reports
          //                  FROM migareference_pre_report_settings as preset
          //                  JOIN application as ap ON  preset.app_id=ap.app_id
          //                  LEFT JOIN migareference_invoice_settings as invst ON preset.app_id=invst.app_id AND invst.created_at>='$date_from' AND invst.created_at<='$date_to'
          //                  LEFT JOIN migareference_app_admins as adm ON adm.app_id=preset.app_id AND invst.user_id=adm.user_id AND adm.created_at>='$date_from' AND adm.created_at<='$date_to'
          //                  JOIN migareference_report_status as mrst ON   mrst.app_id=preset.app_id
          //                  LEFT JOIN migareference_report as migrp ON migrp.app_id=preset.app_id AND migrp.created_at>='$date_from' AND migrp.created_at<='$date_to'
          //                  WHERE 1
          //                  GROUP BY preset.app_id";
          $query_option="SELECT
          preset.app_id,
          ap.name,
          COALESCE(invst_data.refrel_users, 0) AS refrel_users,
          COALESCE(adm_data.admins, 0) AS admins,
          COALESCE(invst_data.refrel_users, 0)  AS net_refreal,
          COALESCE(reports.total_reports, 0) AS total_reports,
          COALESCE(reports.active_reports, 0) AS active_reports,
          COALESCE(reports.declined_reports, 0) AS declined_reports,
          COALESCE(reports.paid_reports, 0) AS paid_reports,
          COALESCE(reports.payable_reports, 0) AS payable_reports
      FROM
          migareference_pre_report_settings AS preset
          JOIN application AS ap ON preset.app_id = ap.app_id
          LEFT JOIN (
              SELECT
                  invst.app_id,
                  COUNT(DISTINCT invst.migareference_invoice_settings_id) AS refrel_users
              FROM
                  migareference_invoice_settings AS invst
              WHERE
                  DATE(invst.created_at) >= '$date_from'
                  AND DATE(invst.created_at) <= '$date_to'
              GROUP BY
                  invst.app_id
          ) AS invst_data ON preset.app_id = invst_data.app_id
          LEFT JOIN (
              SELECT
                  adm.app_id,
                  COUNT(DISTINCT adm.migareference_app_admins_id) AS admins
              FROM
                  migareference_app_admins AS adm
              WHERE
                  DATE(adm.created_at) >= '$date_from'
                  AND DATE(adm.created_at) <= '$date_to'
              GROUP BY
                  adm.app_id
          ) AS adm_data ON preset.app_id = adm_data.app_id
          LEFT JOIN (
              SELECT
                  migrp.app_id,
                  COUNT(DISTINCT migrp.migareference_report_id) AS total_reports,
                  COUNT(DISTINCT CASE WHEN mrst.standard_type NOT IN (3, 4) THEN migrp.migareference_report_id END) AS active_reports,
                  COUNT(DISTINCT CASE WHEN mrst.standard_type = 4 THEN migrp.migareference_report_id END) AS declined_reports,
                  COUNT(DISTINCT CASE WHEN mrst.standard_type = 3 THEN migrp.migareference_report_id END) AS paid_reports,
                  COUNT(DISTINCT CASE WHEN migrp.commission_fee > 0 AND mrst.standard_type NOT IN (1, 3, 4) THEN migrp.migareference_report_id END) AS payable_reports
              FROM
                  migareference_report AS migrp
                  JOIN migareference_report_status AS mrst ON migrp.currunt_report_status = mrst.migareference_report_status_id
              WHERE
                  DATE(migrp.created_at) >= '$date_from'
                  AND DATE(migrp.created_at) <= '$date_to'
              GROUP BY
                  migrp.app_id
          ) AS reports ON preset.app_id = reports.app_id
      GROUP BY
          preset.app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadtablestatsUsers($data = [])
      {
        $date_from = $data['from_date'];
        $date_to   = $data['to_date'];
        $app_id    = $data['app_id'];
          $query_option = "SELECT
                           COUNT(DISTINCT gcm.device_id) as total_gcm,
                           COUNT(DISTINCT apns.device_id) as total_apns,
                           COUNT(DISTINCT gcm.device_id)+COUNT(DISTINCT apns.device_id) as total_tokens,
                           COUNT(DISTINCT cs.customer_id) as total_users
                           FROM migareference_pre_report_settings as preset
                           LEFT JOIN push_gcm_devices as gcm ON gcm.app_id=preset.app_id AND gcm.created_at>='$date_from' AND gcm.created_at<='$date_to'
                           LEFT JOIN push_apns_devices as apns ON apns.app_id=preset.app_id AND apns.created_at>='$date_from' AND apns.created_at<='$date_to'
                           LEFT JOIN customer as cs ON cs.app_id=preset.app_id AND cs.created_at>='$date_from' AND cs.created_at<='$date_to'
                           WHERE preset.app_id=$app_id
                           GROUP BY preset.app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function loadtotalstats($data = [])
      {
        $date_from=$data['from_date'];
        $date_to  =$data['to_date'];
        $query_option = "SELECT
                 COUNT(DISTINCT preset.app_id) as total_apps,
                 COUNT(DISTINCT gcm.device_id) as total_gcm,
                 COUNT(DISTINCT apns.device_id) as total_apns,
                 COUNT(DISTINCT gcm.device_id)+COUNT(DISTINCT apns.device_id) as total_tokens,
                 COUNT(DISTINCT cs.customer_id) as total_users ,
                 COUNT(DISTINCT invst.migareference_invoice_settings_id) refrel_users,
                 COUNT(DISTINCT adm.migareference_app_admins_id) as admins,
                 COUNT(DISTINCT invst.migareference_invoice_settings_id)-COUNT(DISTINCT adm.migareference_app_admins_id) as net_refreal,
                 COUNT(DISTINCT migrp.migareference_report_id) as total_reports,
                 COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id AND mrst.standard_type!=3 AND mrst.standard_type!=4   THEN migrp.migareference_report_id END) active_reports,
                 COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id  AND mrst.standard_type=4 THEN migrp.migareference_report_id END) declined_reports,
                 COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id  AND mrst.standard_type=3 THEN migrp.migareference_report_id END) paid_reports,
                 COUNT(DISTINCT CASE WHEN migrp.app_id = preset.app_id AND migrp.currunt_report_status=mrst.migareference_report_status_id AND migrp.commission_fee>0 AND mrst.standard_type!=1  AND mrst.standard_type!=3 AND mrst.standard_type!=4   THEN migrp.migareference_report_id END) payable_reports
                 FROM migareference_pre_report_settings as preset
                 JOIN application as ap ON  preset.app_id=ap.app_id
                 LEFT JOIN push_gcm_devices as gcm ON gcm.app_id=preset.app_id AND gcm.created_at>='$date_from' AND gcm.created_at<='$date_to'
                 LEFT JOIN push_apns_devices as apns ON apns.app_id=preset.app_id AND apns.created_at>='$date_from' AND apns.created_at<='$date_to'
                 LEFT JOIN customer as cs ON cs.app_id=preset.app_id AND cs.created_at>='$date_from' AND cs.created_at<='$date_to'
                 LEFT JOIN migareference_invoice_settings as invst ON preset.app_id=invst.app_id AND invst.created_at>='$date_from' AND invst.created_at<='$date_to'
                 LEFT JOIN migareference_app_admins as adm ON adm.app_id=preset.app_id AND invst.user_id=adm.user_id AND adm.created_at>='$date_from' AND adm.created_at<='$date_to'
                 JOIN migareference_report_status as mrst ON   mrst.app_id=preset.app_id
                 LEFT JOIN migareference_report as migrp ON migrp.app_id=preset.app_id AND migrp.created_at>='$date_from' AND migrp.created_at<='$date_to'
                 WHERE 1";
        $res_option   = $this->_db->fetchAll($query_option);
        return $res_option;
      }
      public function insert_stats()
      {
         $data['from_date']=date('Y-m-d H:i:s', strtotime('-24 hours', strtotime(date('Y-m-d H:i:s'))));
         $data['to_date']  =$date=date('Y-m-d H:i:s');
         $migareference  = new Migareference_Model_Db_Table_Migareference();
         //To make it compatibale with previous version if stats table is empty then it will add stats sterted from 1 March to currunt date
         $grapghdata=$migareference->loadallgraphstats();
         if (!count($grapghdata)) {
            $interval = DateInterval::createFromDateString('1 day');
            $start = new DateTime('2020-3-1 0:0:0');
            $end = new DateTime($data['to_date']);
            $period   = new DatePeriod( $start, $interval,$end);
            foreach ($period as $dt) {
              $data['to_date']=$dt->format("Y-m-d H:i:s");
              $to_date=$dt->format("Y-m-d H:i:s");
              $data['from_date']=date('Y-m-d H:i:s', strtotime('-24 hours', strtotime($to_date)));
              $loadTableStats = $migareference->loadtablestats($data);
              $loadTotalStats = $migareference->loadtotalstats($data);
              $loadTotalStats[0]['per_report_dump']=serialize($loadTableStats[0]);
              $loadTotalStats[0]['created_at']    = $to_date;
              $this->_db->insert("migarefrence_stats", $loadTotalStats[0]);
            }
         }
         $loadTableStats = $migareference->loadtablestats($data);
         $loadTotalStats = $migareference->loadtotalstats($data);
         $loadTotalStats[0]['per_report_dump']=serialize($loadTableStats[0]);
         $loadTotalStats[0]['created_at']    = date('Y-m-d H:i:s');
         $this->_db->insert("migarefrence_stats", $loadTotalStats[0]);
      }

      public function reportpage($app_id)
      {
        $standard_fields=7;
        $optional_fields=10;
        $count=1;
        $total=$standard_fields+$optional_fields;

        $pre_report=$this->preReportsettigns($app_id);
        // @field_type: 1 Text,2 Number,3 Options,4 Address
        // Standard Fields
        // *Report Type
        $data[0]['field_type']   = 3;
        $data[0]['label']        = "Report Type";
        $data[0]['field_option'] = "Flat@Villa";
        $data[0]['is_visible']   =  (count($pre_report) && $pre_report[0]['enable_property_type']==1) ? 1 : 2 ;
        // *Sales Expectations
        $data[1]['field_type']   = 2;
        $data[1]['label']        = "Sales Expectations";
        $data[1]['is_visible']   =  (count($pre_report) && $pre_report[0]['enable_property_sales_expectaion']==1) ? 1 : 2 ;
        // *Address
        $data[2]['field_type']   = 4;
        $data[2]['label']        = "Address";
        $data[2]['is_visible']   =  (count($pre_report) && $pre_report[0]['enable_property_address']==1) ? 1 : 2 ;
        // *Owner Name
        $data[3]['label']        = "Name";
        // *Owner Sur Name
        $data[4]['label']        = "Sur Name";
        // *Owner Mobile
        $data[5]['label']        = "Mobile";
        // *Note
        $data[6]['field_type']   = 5;
        $data[6]['label']        = "Note";
        $data[6]['is_required']  = 2;
        $data[6]['is_visible']   = 1;
        $static_field_count=1;
        $dynamic_field_count=1;
        for ($i=0; $i < $total ; $i++) {
          $order=$i+1;
          if ($standard_fields>$i) {
            $data[$i]['field_order']      = $order;
            $data[$i]['app_id']           = $app_id;
            $data[$i]['field_type_count'] = $static_field_count++;
            $this->insertReportFields($data[$i]);
          }else {
            $data_set_dynamic['is_visible']   = 2;
            $data_set_dynamic['label']        = "Custom Field ".$count++;
            $data_set_dynamic['field_order']  = $order;
            $data_set_dynamic['type']         = 2;
            $data_set_dynamic['app_id']       = $app_id;
            $data_set_dynamic['field_type_count']= $dynamic_field_count++;
            $this->insertReportFields($data_set_dynamic);
          }
        }

      }
      public function prz_default_notifications($app_id=0,$value_id=0)
      {
          // Type=1 Redeemed
          $data[0]['app_id'] =$app_id;
          $data[0]['type'] =1;
          $data[0]['prz_notification_to_user'] =1;//1 FOR ALL ADMIN REFRERR AND AGENT
          $data[0]['ref_prz_notification_type'] =2;
          $data[0]['ref_prz_email_title'] ="Complimenti! Abbiamo ricevuto la richiesta del premio @@prize_title@@";
          $data[0]['ref_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ciao @@referral_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>&egrave; con piacere che ti confermiamo la ricezione della richiesta del seguente premio:<br /><br />Premio:&nbsp;@@prize_title@@<br />Nr. Crediti:&nbsp;@@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Un nostro operatore si occuper&agrave; ora di approvare la richiesta e ti aggiorneremo in merito sui prossimi passaggi.</span></span></p> <p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Complimenti per il risultato raggiunto<br />Il team di&nbsp;@@app_name@@</span></span></p>";
          // $data[0]['ref_prz_push_title'] =
          // $data[0]['ref_prz_push_text'] =
          // $data[0]['ref_prz_open_feature'] =
          // $data[0]['ref_prz_feature_id'] =
          // $data[0]['ref_prz_custom_url'] =
          // $data[0]['ref_prz_custom_file'] =
          $data[0]['agt_prz_notification_type'] =1;
          $data[0]['agt_prz_email_title'] ="Abbiamo una richiesta premio dal segnalatore @@referral_name@@";
          $data[0]['agt_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ciao&nbsp;@@agent_name@@ @@admin_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore&nbsp;<strong>@@referral_name@@.</strong></span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Premio:&nbsp;@@prize_title@@<br />Nr. Crediti:&nbsp;@@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ti preghiamo di entrare nel gestionale e confermare o rifiutare la richiesta. Se la richiesta &egrave; confermata devi provvedere a contattare il segnalatore per concordare la consegna del premio.</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Buon lavoro<br />La tua APP&nbsp;@@app_name@@</span></span></p>";
          $data[0]['agt_prz_push_title'] ="Premio Riscattato da @@referral_name@@";
          $data[0]['agt_prz_push_text'] ="Abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore @@referral_name@@. Premio: @@prize_title@@ Nr. Crediti: @@prize_credits@@. Entra nel tuo backoffice per autorizzarla o rifiutarla.";
          $data[0]['agt_prz_open_feature'] =1;
          $data[0]['agt_prz_feature_id'] =$value_id;
          $data[0]['agt_prz_custom_url'] ="";
          $data[0]['agt_prz_custom_file'] ="prz_redeem.jpg";
          // Type=2 Redeemed
          $data[1]['app_id'] =$app_id;
          $data[1]['type'] =2;
          $data[1]['prz_notification_to_user'] =2;//2 for only refrerr
          $data[1]['ref_prz_notification_type'] =1;
          $data[1]['ref_prz_email_title'] ="Ci dispiace, la tua richiesta premio è stata rifiutata";
          $data[1]['ref_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Gentile&nbsp;@@referral_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>la tua richiesta del premio &egrave; stata rifiutata:<br />Premio: @@prize_title@@<br />Nr. Crediti: @@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ti preghiamo di contattarci per maggiori informazioni in merito</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Grazie<br />Team App&nbsp;@@app_name@@</span></span></p>";
          $data[1]['ref_prz_push_title'] ="Ci dispiace, la tua richiesta premio è stata rifiutata";
          $data[1]['ref_prz_push_text'] ="La tua richiesta del premio @@prize_title@@ è stata rifiutata. Ti preghiamo di contattarci.";
          $data[1]['ref_prz_open_feature'] =1;
          $data[1]['ref_prz_feature_id'] =$value_id;
          $data[1]['ref_prz_custom_url'] ="";
          $data[1]['ref_prz_custom_file'] ="prz_refused.jpg";
          // $data[1]['agt_prz_notification_type'] =1;
          //
          // $data[1]['agt_prz_email_title'] ="Abbiamo una richiesta premio dal segnalatore @@referral_name@@";
          // $data[1]['agt_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ciao&nbsp;@@agent_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore&nbsp;<strong>@@referral_name@@.</strong></span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Premio:&nbsp;@@prize_title@@<br />Nr. Crediti:&nbsp;@@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ti preghiamo di entrare nel gestionale e confermare o rifiutare la richiesta. Se la richiesta &egrave; confermata devi provvedere a contattare il segnalatore per concordare la consegna del premio.</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Buon lavoro<br />La tua APP&nbsp;@@app_name@@</span></span></p>";
          //
          // $data[1]['agt_prz_push_title'] ="Premio Riscattato da @@referral_name@@";
          // $data[1]['agt_prz_push_text'] ="Abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore @@referral_name@@. Premio: @@prize_title@@ Nr. Crediti: @@prize_credits@@. Entra nel tuo backoffice per autorizzarla o rifiutarla.";
          // $data[1]['agt_prz_open_feature'] =1;
          // $data[1]['agt_prz_feature_id'] =$value_id;
          // $data[1]['agt_prz_custom_url'] ="";
          // $data[1]['agt_prz_custom_file'] ="agt_redeemed.jpg";
          // Type=2 Delivered
          $data[2]['app_id'] =$app_id;
          $data[2]['type'] =3;
          $data[2]['prz_notification_to_user'] =2;//2 for only refrerr
          $data[2]['ref_prz_notification_type'] =1;//1 for both email and push
          $data[2]['ref_prz_email_title'] ="Complimenti! Abbiamo consegnato il tuo premio @@prize_title@@";
          $data[2]['ref_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Gentile&nbsp;@@referral_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>il tuo premio &egrave; stato consegnato/pagato!<br /><br />Premio: @@prize_title@@<br />Nr. Crediti: @@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Il tuo saldo crediti ora &egrave; pari a : @@user_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Per qualsiasi domanda non esitare a contattarci.</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Grazie<br />Team App&nbsp;@@app_name@@</span></span></p>";
          $data[2]['ref_prz_push_title'] ="Il tuo premio è stato consegnato!";
          $data[2]['ref_prz_push_text'] ="Ti confermiamo che abbiamo consegnato/pagato il premio da te richiesto @@prize_title@@. Il tuo attuale saldo crediti è @@user_credits@@";
          $data[2]['ref_prz_open_feature'] =1;
          $data[2]['ref_prz_feature_id'] =$value_id;
          $data[2]['ref_prz_custom_url'] ="";
          $data[2]['ref_prz_custom_file'] ="prz_redeem.jpg";
          // $data[2]['agt_prz_notification_type'] =1;
          //
          // $data[2]['agt_prz_email_title'] ="Abbiamo una richiesta premio dal segnalatore @@referral_name@@";
          // $data[2]['agt_prz_email_text'] ="<p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ciao&nbsp;@@agent_name@@,</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore&nbsp;<strong>@@referral_name@@.</strong></span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Premio:&nbsp;@@prize_title@@<br />Nr. Crediti:&nbsp;@@prize_credits@@</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Ti preghiamo di entrare nel gestionale e confermare o rifiutare la richiesta. Se la richiesta &egrave; confermata devi provvedere a contattare il segnalatore per concordare la consegna del premio.</span></span></p><p><span style='font-size:12pt'><span style='font-family:&quot;Times New Roman&quot;,serif'>Buon lavoro<br />La tua APP&nbsp;@@app_name@@</span></span></p>";
          //
          // $data[2]['agt_prz_push_title'] ="Premio Riscattato da @@referral_name@@";
          // $data[2]['agt_prz_push_text'] ="Abbiamo appena ricevuto una richiesta di riscatto per un premio da parte del segnalatore @@referral_name@@. Premio: @@prize_title@@ Nr. Crediti: @@prize_credits@@. Entra nel tuo backoffice per autorizzarla o rifiutarla.";
          // $data[2]['agt_prz_open_feature'] =1;
          // $data[2]['agt_prz_feature_id'] =$value_id;
          // $data[2]['agt_prz_custom_url'] ="";
          // $data[2]['agt_prz_custom_file'] ="agt_redeemed.jpg";
          foreach ($data as $key => $value) {
            $value['created_at']    = date('Y-m-d H:i:s');
            $this->_db->insert("migarefrence_prizes_notification", $value);
            $this->copyImages($value['app_id'],$value,'');
          }
      }
      // Compatible New Mandatory Note Filed
      public function compatibleNewNoteFiled($app_id=0)
      {
        $query_option = "SELECT * FROM `migareference_report_fields` WHERE `field_type_count` = 7 AND `type` = 1 AND `app_id`=$app_id";
        $res_option   = $this->_db->fetchAll($query_option);
        if (!count($res_option)) { //No Mandatory Note Filed FOund
          $data['field_type']   = 5;
          $data['label']        = "Note";
          $data['is_required']  = 2;
          $data['is_visible']   = 1;
          $data['field_order']      = 17;
          $data['app_id']           = $app_id;
          $data['field_type_count'] = 7;
          $this->insertReportFields($data);
          $query_option = "SELECT * FROM `migareference_report_fields` WHERE `label` = 'Note' AND `type` = 2 AND `app_id`=$app_id";
          $res_option   = $this->_db->fetchAll($query_option);
          if (count($res_option)) { //Previously Optional Note filed Found (Disable it)
            $diable['is_required']  = 2;
            $diable['is_visible']     = 2;
            $id=$res_option[0]['migareference_report_fields_id'];
            $this->_db->update("migareference_report_fields", $diable,['migareference_report_fields_id = ?' => $id, 'app_id = ?' => $app_id]);
          }
        }

      }
      // GDPR
      public function gdprsave($data=[])
      {
        $migareference  = new Migareference_Model_Db_Table_Migareference();
        $app_id=$data['app_id'];
        foreach ($data['gdpr_to_delete'] as $key => $value) {
            $user_id  = $value;
            $invoice_data = $migareference->getpropertysettings($app_id,$user_id);
            $phonebook_id = $invoice_data[0]['migarefrence_phonebook_id'];
            $rep['app_id']=$app_id;
            $rep['user_id']=$user_id;
            $reports=$this->ref_get_all_reports($rep);
            $this->deleteCustomer($user_id);
            $this->deleteAdmin($user_id);
            $this->deleteAgent($user_id);
            $invoice_gdpr_data['invoice_name']="****";
            $invoice_gdpr_data['invoice_surname']="****";
            $invoice_gdpr_data['invoice_mobile']="****";
            $invoice_gdpr_data['company']="****";
            $invoice_gdpr_data['leagal_address']="****";
            $invoice_gdpr_data['tax_id']="****";
            $invoice_gdpr_data['vat_id']="****";
            $this->referrerGdpr($app_id,$user_id,$invoice_gdpr_data);
            $reportGdpr_data['address']="****";
            $reportGdpr_data['longitude']="****";
            $reportGdpr_data['latitude']="****";
            $reportGdpr_data['owner_name']="****";
            $reportGdpr_data['owner_surname']="****";
            $reportGdpr_data['owner_mobile']="****";
            $this->reportGdpr($app_id,$user_id,$reportGdpr_data);
            $phonebookGdpr_data['name']="****";
            $phonebookGdpr_data['surname']="****";
            $phonebookGdpr_data['email']="****";
            $phonebookGdpr_data['mobile']="****";
            $phonebookGdpr_data['job_id']="****";
            $phonebookGdpr_data['rating']="****";            
            $phonebookGdpr_data['first_password']="****";            
            $this->_db->update("migarefrence_phonebook", $phonebookGdpr_data,['migarefrence_phonebook_id = ?' => $phonebook_id]);
            if (count($reports)) {
              $prospect_id=$reports[0]['prospect_id'];              
              $prospect['app_id']= $app_id;          
              $prospect['name']="****";
              $prospect['surname']="****";
              $prospect['email']="****";
              $prospect['mobile']="****";
              $prospect['job_id']="****";
              $prospect['rating']="****";            
              $prospect['password']="****";
              $migareference->update_prospect($prospect,$prospect_id,0,0);//Also save log if their is change in Rating,Job,Notes                                  
            }
        }
      }
      // Delte Referrer
      public function deltereferrer($app_id=0,$data=[])
      {     $user_id=$data['user_id'];
            $this->deleteCustomer($user_id);
            $invoice_gdpr_data['invoice_name']="******";
            $invoice_gdpr_data['invoice_surname']="******";
            $invoice_gdpr_data['invoice_mobile']="******";
            $invoice_gdpr_data['company']="******";
            $invoice_gdpr_data['leagal_address']="******";
            $invoice_gdpr_data['tax_id']="******";
            $invoice_gdpr_data['vat_id']="******";
            $this->referrerGdpr($app_id,$user_id,$invoice_gdpr_data);
            $reportGdpr_data['address']="******";
            $reportGdpr_data['longitude']="******";
            $reportGdpr_data['latitude']="******";
            $reportGdpr_data['owner_name']="******";
            $reportGdpr_data['owner_surname']="******";
            $reportGdpr_data['owner_mobile']="******";
            $this->reportGdpr($app_id,$user_id,$reportGdpr_data);
            $phonebookGdpr_data['name']="******";
            $phonebookGdpr_data['surname']="******";
            $phonebookGdpr_data['email']="******";
            $phonebookGdpr_data['mobile']="******";
            $phonebookGdpr_data['job_id']=0;
            $phonebookGdpr_data['first_password']="******";
            $this->_db->update("migarefrence_phonebook", $phonebookGdpr_data,['migarefrence_phonebook_id = ?' => $phonebook_id]);
      }    
    
    public function default_jobs($app_id=0)
    {
      $default_jobs[]="Abbigliamento";
      $default_jobs[]="Agente Call Center";
      $default_jobs[]="Agente Commercio";
      $default_jobs[]="Agente di Cambio";
      $default_jobs[]="Agente Immobiliare";
      $default_jobs[]="Agente Viaggi";
      $default_jobs[]="Agente Telefonia Mobile";
      $default_jobs[]="Agenzia Collocamento";
      $default_jobs[]="Altro non classificabile";
      $default_jobs[]="Amministratore";
      $default_jobs[]="Antincendio";
      $default_jobs[]="Arredamento";
      $default_jobs[]="Assicuratore";
      $default_jobs[]="Assistente Anziani";
      $default_jobs[]="Astrologo";
      $default_jobs[]="Automotive";
      $default_jobs[]="Avvocato";
      $default_jobs[]="Bancario";
      $default_jobs[]="Benzinaio";
      $default_jobs[]="Cameriere";
      $default_jobs[]="Catering";
      $default_jobs[]="Chiropratico";
      $default_jobs[]="Centro Estetico";
      $default_jobs[]="Commercialista";
      $default_jobs[]="Commerciante";
      $default_jobs[]="Concessionaria";
      $default_jobs[]="Consulente Aziendale";
      $default_jobs[]="Consulente Informatico";
      $default_jobs[]="Consulente Privacy";
      $default_jobs[]="Consulente Serv. Pagamento";
      $default_jobs[]="Contabile";
      $default_jobs[]="Corriere";
      $default_jobs[]="Cuoco";
      $default_jobs[]="Dentista";
      $default_jobs[]="Dipendente";
      $default_jobs[]="Dipendente Pubblico";
      $default_jobs[]="Doppiatore";
      $default_jobs[]="Elettrauto";
      $default_jobs[]="Elettricista";
      $default_jobs[]="Enoteca";
      $default_jobs[]="Event Manager";
      $default_jobs[]="Fabbro";
      $default_jobs[]="Falegname";
      $default_jobs[]="Farmacista";
      $default_jobs[]="Fisioterapista";
      $default_jobs[]="Gestione Fiduciarie";
      $default_jobs[]="Gioielliere";
      $default_jobs[]="Guida Turistica";
      $default_jobs[]="Grafico";
      $default_jobs[]="Home Stagging";
      $default_jobs[]="Illuminazione";
      $default_jobs[]="Insegnante";
      $default_jobs[]="Investigatore Privato";
      $default_jobs[]="Istruttore arti marziali";
      $default_jobs[]="Marketer";
      $default_jobs[]="Marketing e Comunicazione";
      $default_jobs[]="Meccanico";
      $default_jobs[]="Massaggiatore";
      $default_jobs[]="Mediatore";
      $default_jobs[]="Medico";
      $default_jobs[]="Musicista";
      $default_jobs[]="Naturopata";
      $default_jobs[]="Noleggio Attrezzature eventi";
      $default_jobs[]="Noleggio lungo termine";
      $default_jobs[]="Nutrizionista";
      $default_jobs[]="Notaio";
      $default_jobs[]="Oculista";
      $default_jobs[]="Onlus";
      $default_jobs[]="Organizzatore Eventi";
      $default_jobs[]="Operaio";
      $default_jobs[]="Osteopata";
      $default_jobs[]="Panettiere";
      $default_jobs[]="Panettiere";
      $default_jobs[]="Parrucchiere";
      $default_jobs[]="Pensionato";
      $default_jobs[]="Perito Assicurativo";
      $default_jobs[]="Personal Trainer Fitness";
      $default_jobs[]="Pizzaiolo";
      $default_jobs[]="Pompe Funebri";
      $default_jobs[]="Procacciatore";
      $default_jobs[]="Prodotti Salute & Benessere";
      $default_jobs[]="Psicologo";
      $default_jobs[]="Pulizie Uffici";
      $default_jobs[]="Recruiter";
      $default_jobs[]="Responsabile Acquisti";
      $default_jobs[]="Risorse Umane";
      $default_jobs[]="Ristoratore";
      $default_jobs[]="Segretario Società";
      $default_jobs[]="Servizi Catering";
      $default_jobs[]="Servizi per uffici";
      $default_jobs[]="Smaltimento Rifiuti";
      $default_jobs[]="Social Media Manager";
      $default_jobs[]="Tabaccaio";
      $default_jobs[]="Taxista";
      $default_jobs[]="Tecnico Audio Video";
      $default_jobs[]="Tecnico Riparatore";
      $default_jobs[]="Tipografia";
      $default_jobs[]="Titolare Hotel";
      $default_jobs[]="Titolare Location Eventi";
      $default_jobs[]="Traduttore";
      $default_jobs[]="Vendita Gomma & Plastica";
      $default_jobs[]="Vendita imballaggi";
      $default_jobs[]="Wedding Planner";
      foreach ($default_jobs as $key => $value) {
        $data['app_id']    = $app_id;
        $data['job_title']    = $value;
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_jobs", $data);
      }
    }
    public function deleteProfessionNA()
    {
      return $this->_db->delete('migareference_professions',['profession_title = ?' => 'N/A']);
    }
    public function default_professions($app_id=0)
    {

    $default_professions = [
        'Tecnologia e Informatica',
        'Finanza e Banking',
        'Salute e Assistenza Sanitaria',
        'Educazione e Ricerca',
        'Marketing e Comunicazione',
        'Ingegneria e Produzione',
        'Energia e Risorse Naturali',
        'Turismo e Ospitalità',
        'Retail e Commercio',
        'Immobiliare e Costruzioni',
        'Trasporti e Logistica',
        'Arte e Intrattenimento',
        'Legale e Consulenza',
        'Agricoltura e Alimentazione',
        'Non Profit e Servizi Sociali',
        'Moda e Design',
        'Sport e Fitness',
        'Editoria e Giornalismo',
        'Settore Pubblico e Governo',
        'Ricerca e Sviluppo',
    ];

     
      foreach ($default_professions as $key => $value) {
        $data['app_id']    = $app_id;
        $data['profession_title']    = $value;
        $data['created_at']    = date('Y-m-d H:i:s');
        $this->_db->insert("migareference_professions", $data);
      }
    }
    public function defaultGeoCountrieProvinces($app_id=0)
    {
      // Countries List

      $countries = array(
        'Afghanistan' => 'AF',
        'Albania' => 'AL',
        'Algeria' => 'DZ',
        'Andorra' => 'AD',
        'Angola' => 'AO',
        'Antigua and Barbuda' => 'AG',
        'Argentina' => 'AR',
        'Armenia' => 'AM',
        'Australia' => 'AU',
        'Austria' => 'AT',
        'Azerbaijan' => 'AZ',
        'Bahamas' => 'BS',
        'Bahrain' => 'BH',
        'Bangladesh' => 'BD',
        'Barbados' => 'BB',
        'Belarus' => 'BY',
        'Belgium' => 'BE',
        'Belize' => 'BZ',
        'Benin' => 'BJ',
        'Bhutan' => 'BT',
        'Bolivia' => 'BO',
        'Bosnia and Herzegovina' => 'BA',
        'Botswana' => 'BW',
        'Brazil' => 'BR',
        'Brunei' => 'BN',
        'Bulgaria' => 'BG',
        'Burkina Faso' => 'BF',
        'Burundi' => 'BI',
        'Côte dIvoire' => 'CI',
        'Cabo Verde' => 'CV',
        'Cambodia' => 'KH',
        'Cameroon' => 'CM',
        'Canada' => 'CA',
        'Central African Republic' => 'CF',
        'Chad' => 'TD',
        'Chile' => 'CL',
        'China' => 'CN',
        'Colombia' => 'CO',
        'Comoros' => 'KM',
        'Congo-Brazzaville' => 'CG',
        'Costa Rica' => 'CR',
        'Croatia' => 'HR',
        'Cuba' => 'CU',
        'Cyprus' => 'CY',
        'Czech Republic' => 'CZ',
        'Democratic Republic of the Congo' => 'CD',
        'Denmark' => 'DK',
        'Djibouti' => 'DJ',
        'Dominica' => 'DM',
        'Dominican Republic' => 'DO',
        'Ecuador' => 'EC',
        'Egypt' => 'EG',
        'El Salvador' => 'SV',
        'Equatorial Guinea' => 'GQ',
        'Eritrea' => 'ER',
        'Estonia' => 'EE',
        'Swaziland' => 'SW',
        'Ethiopia' => 'ET',
        'Fiji' => 'FJ',
        'Finland' => 'FI',
        'France' => 'FR',
        'Gabon' => 'GA',
        'Gambia' => 'GM',
        'Georgia' => 'GE',
        'Germany' => 'DE',
        'Ghana' => 'GH',
        'Greece' => 'GR',
        'Grenada' => 'GD',
        'Guatemala' => 'GT',
        'Guinea' => 'GN',
        'Guinea-Bissau' => 'GW',
        'Guyana' => 'GY',
        'Haiti' => 'VA',
        'Holy See' => 'HT',
        'Honduras' => 'HN',
        'Hungary' => 'HU',
        'Iceland' => 'IS',
        'India' => 'IN',
        'Indonesia' => 'ID',
        'Iran' => 'IR',
        'Iraq' => 'IQ',
        'Ireland' => 'IE',
        'Israel' => 'IL',
        'Italy' => 'IT',
        'Jamaica' => 'JM',
        'Japan' => 'JP',
        'Jordan' => 'JO',
        'Kazakhstan' => 'KZ',
        'Kenya' => 'KE',
        'Kiribati' => 'KI',
        'Kuwait' => 'KW',
        'Kyrgyzstan' => 'KG',
        'Laos' => 'LA',
        'Latvia' => 'LV',
        'Lebanon' => 'LB',
        'Lesotho' => 'LS',
        'Liberia' => 'LR',
        'Libya' => 'LY',
        'Liechtenstein' => 'LI',
        'Lithuania' => 'LT',
        'Luxembourg' => 'LU',
        'Madagascar' => 'MG',
        'Malawi' => 'MW',
        'Malaysia' => 'MY',
        'Maldives' => 'MV',
        'Mali' => 'ML',
        'Malta' => 'MT',
        'Marshall Islands' => 'MH',
        'Mauritania' => 'MR',
        'Mauritius' => 'MU',
        'Mexico' => 'MX',
        'Micronesia' => 'FM',
        'Moldova' => 'MD',
        'Monaco' => 'MC',
        'Mongolia' => 'MN',
        'Montenegro' => 'ME',
        'Morocco' => 'MA',
        'Mozambique' => 'MZ',
        'Myanma' => 'MM',
        'Namibia' => 'NA',
        'Nauru' => 'NR',
        'Nepal' => 'NP',
        'Netherlands' => 'NL',
        'New Zealand' => 'NZ',
        'Nicaragua' => 'NI',
        'Niger' => 'NE',
        'Nigeria' => 'NG',
        'North Korea' => 'KP',
        'North Macedonia' => 'MK',
        'Norway' => 'NO',
        'Oman' => 'OM',
        'Pakistan' => 'PK',
        'Palau' => 'PW',
        'Palestine State' => 'PS',
        'Panama' => 'PA',
        'Papua New Guinea' => 'PG',
        'Paraguay' => 'PY',
        'Peru' => 'PE',
        'Philippines' => 'PH',
        'Poland' => 'PL',
        'Portugal' => 'PT',
        'Qatar' => 'QA',
        'Romania' => 'RO',
        'Russia' => 'RU',
        'Rwanda' => 'RW',
        'Saint Kitts and Nevis' => 'KN',
        'Saint Lucia' => 'LC',
        'Saint Vincent and the Grenadines' => 'VC',
        'Samoa' => 'WS',
        'San Marino' => 'SM',
        'Sao Tome and Principe' => 'ST',
        'Saudi Arabia' => 'SA',
        'Senegal' => 'SN',
        'Serbia' => 'RS',
        'Seychelles' => 'SC',
        'Sierra Leone' => 'SL',
        'Singapore' => 'SG',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Solomon Islands' => 'SB',
        'Somalia' => 'SO',
        'South Africa' => 'ZA',
        'South Korea' => 'KR',
        'South Sudan' => 'SS',
        'Spain' => 'ES',
        'Sri Lanka' => 'LK',
        'Sudan' => 'SD',
        'Suriname' => 'SR',
        'Sweden' => 'SE',
        'Switzerland' => 'CH',
        'Syria' => 'SY',
        'Tajikistan' => 'TJ',
        'Tanzania' => 'TZ',
        'Thailand' => 'TH',
        'Timor-Leste' => 'TL',
        'Togo' => 'TG',
        'Tonga' => 'TO',
        'Trinidad and Tobago' => 'TT',
        'Tunisia' => 'TN',
        'Turkey' => 'TR',
        'Turkmenistan' => 'TM',
        'Tuvalu' => 'TV',
        'Uganda' => 'UG',
        'Ukraine' => 'UA',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States of America' => 'US',
        'Uruguay' => 'UY',
        'Uzbekistan' => 'UZ',
        'Vanuatu' => 'VU',
        'Venezuela' => 'VE',
        'Vietnam' => 'VN',
        'Yemen' => 'YE',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW'
      );
      // Provinces
      $italy_provinces = array(
        "Padova" => "PD",
        "Lodi" => "LO",
        "Lecco" => "LC",
        "Siena" => "SI",
        "Oristano" => "OR",
        "Pescara" => "PE",
        "Milano" => "MI",
        "Pistoia" => "PT",
        "Potenza" => "PZ",
        "Ragusa" => "RG",
        "Foggia" => "FG",
        "Cuneo" => "CN",
        "Matera" => "MT",
        "L Aquila" => "AQ",
        "Rieti" => "RI",
        "Salerno" => "SA",
        "Napoli" => "NA",
        "Catania" => "CT",
        "Frosinone" => "FR",
        "Cosenza" => "CS",
        "Brescia" => "BS",
        "Pesaro e Urbino" => "PU",
        "Cremona" => "CR",
        "Mantova" => "MN",
        "Viterbo" => "VT",
        "Lecce" => "LE",
        "Vibo Valentia" => "VV",
        "Ascoli Piceno" => "AP",
        "Terni" => "TR",
        "Campobasso" => "CB",
        "Bari" => "BA",
        "Isernia" => "IS",
        "Caltanissetta" => "CL",
        "Messina" => "ME",
        "Alessandria" => "AL",
        "Bergamo" => "BG",
        "Rovigo" => "RO",
        "Verona" => "VR",
        "Roma Capitale" => "RM",
        "Reggio Calabria" => "RC",
        "Piacenza" => "PC",
        "Sassari" => "SS",
        "Enna" => "EN",
        "Asti" => "AT",
        "Torino" => "TO",
        "Belluno" => "BL",
        "Varese" => "VA",
        "Monza e Brianza" => "MB",
        "Novara" => "NO",
        "Agrigento" => "AG",
        "Ancona" => "AN",
        "Vicenza" => "VI",
        "Udine" => "UD",
        "Avellino" => "AV",
        "Caserta" => "CE",
        "Biella" => "BI",
        "Benevento" => "BN",
        "Imperia" => "IM",
        "Trento" => "TN",
        "Pavia" => "PV",
        "Vercelli" => "VC",
        "Savona" => "SV",
        "Teramo" => "TE",
        "Sondrio" => "SO",
        "Parma" => "PR",
        "Como" => "CO",
        "Catanzaro" => "CZ",
        "Reggio Emilia" => "RE",
        "Trapani" => "TP",
        "Bolzano Bozen" => "BZ",
        "Ravenna" => "RA",
        "Palermo" => "PA",
        "Valle d Aosta" => "AO",
        "Fermo" => "FM",
        "Chieti" => "CH",
        "Treviso" => "TV",
        "Bologna" => "BO",
        "Lucca" => "LU",
        "La Spezia" => "SP",
        "Pordenone" => "PN",
        "Barletta-Andria-Trani" => "BT",
        "Arezzo" => "AR",
        "Venezia" => "VE",
        "Verbano Cusio Ossola" => "VB",
        "Macerata" => "MC",
        "Latina" => "LT",
        "Sud Sardegna" => "SU",
        "Grosseto" => "GR",
        "Genova" => "GE",
        "Ferrara" => "FE",
        "Nuoro" => "NU",
        "Cagliari" => "CA",
        "Perugia" => "PG",
        "Siracusa" => "SR",
        "Massa e Carrara" => "MS",
        "Taranto" => "TA",
        "Firenze" => "FI",
        "Forlì Cesena" => "FC",
        "Modena" => "MO",
        "Rimini" => "RN",
        "Crotone" => "KR",
        "Livorno" => "LI",
        "Pisa" => "PI",
        "Brindisi" => "BR",
        "Prato" => "PO",
        "Gorizia" => "GO",
        "Trieste" => "TS"
      );
      $switzerland_provinces = array(
        "Aargau"=>"AG",	
        "Appenzell Ausserrhoden"=>"AR",	
        "Appenzell Innerrhoden"=>"AI",	
        "Basel-Landschaft"=>"BL",	
        "Basel-Stadt"=>"BS",	
        "Bern"=>"BE",	
        "Berne"=>"BE",	
        "Freiburg"=>"FR",	
        "Fribourg"=>"FR",	
        "Genève"=>"GE",	
        "Glarus"=>"GL",	
        "Graubünden"=>"GR",	
        "Grigioni"=>"GR",	
        "Grischun"=>"GR",	
        "Jura"=>"JU",	
        "Luzern"=>"LU",	
        "Neuchâtel"=>"NE",	
        "Nidwalden"=>"NW",	
        "Obwalden"=>"OW",	
        "Sankt Gallen"=>"SG",	
        "Schaffhausen"=>"SH",	
        "Schwyz"=>"SZ",	
        "Solothurn"=>"SO",	
        "Thurgau"=>"TG",	
        "Ticino"=>"TI",	
        "Uri"=>"UR",	
        "Valais"=>"VS",	
        "Vaud"=>"VD",	
        "Wallis"=>"VS",	
        "Zug"=>"ZG",	
        "Zürich"=>"ZH"
      );
        $country_ids = [];
        foreach ($countries as $key => $value) {
            $data['app_id'] = $app_id;
            $data['country'] = $key;
            $data['country_code'] = $value;
    
            // Save the country and track IDs for Italy and Switzerland
            if ($key == "Italy" || $key == "Switzerland") {
                $country_ids[$key] = $this->addCountry($data);
            } else {
                $this->addCountry($data);
            }
        }
    
        // Add provinces for Italy
        if (isset($country_ids['Italy'])) {
            foreach ($italy_provinces as $key => $value) {
                $province_data['app_id'] = $app_id;
                $province_data['country_id'] = $country_ids['Italy'];
                $province_data['province'] = $key;
                $province_data['province_code'] = $value;
                $this->addProvince($province_data);
            }
        }
    
        // Add provinces for Switzerland
        if (isset($country_ids['Switzerland'])) {
            foreach ($switzerland_provinces as $key => $value) {
                $province_data['app_id'] = $app_id;
                $province_data['country_id'] = $country_ids['Switzerland'];
                $province_data['province'] = $key;
                $province_data['province_code'] = $value;
                $this->addProvince($province_data);
            }
        }
      
    }
    public function addCountry($data=[])
    {
      $data['created_at']    = date('Y-m-d H:i:s');
      $this->_db->insert("migareference_geo_countries", $data);
      return $this->_db->lastInsertId();
    }
    public function addProvince($data=[])
    {
      $data['created_at']    = date('Y-m-d H:i:s');
      $this->_db->insert("migareference_geo_provinces", $data);
    }
    public function updateCountry($data=[])
    {
      $id=$data['geo_country'];
      $app_id=$data['app_id'];
      $country['country']=$data['country'];
      $country['country_code']=$data['country_code'];
      $this->_db->update("migareference_geo_countries", $country,['migareference_geo_countries_id = ?' => $id, 'app_id = ?' => $app_id]);
    }
    function compatibleCountries($app_id=0) {
            $countries = array(
              'Afghanistan' => 'AF',
              'Albania' => 'AL',
              'Algeria' => 'DZ',
              'Andorra' => 'AD',
              'Angola' => 'AO',
              'Antigua and Barbuda' => 'AG',
              'Argentina' => 'AR',
              'Armenia' => 'AM',
              'Australia' => 'AU',
              'Austria' => 'AT',
              'Azerbaijan' => 'AZ',
              'Bahamas' => 'BS',
              'Bahrain' => 'BH',
              'Bangladesh' => 'BD',
              'Barbados' => 'BB',
              'Belarus' => 'BY',
              'Belgium' => 'BE',
              'Belize' => 'BZ',
              'Benin' => 'BJ',
              'Bhutan' => 'BT',
              'Bolivia' => 'BO',
              'Bosnia and Herzegovina' => 'BA',
              'Botswana' => 'BW',
              'Brazil' => 'BR',
              'Brunei' => 'BN',
              'Bulgaria' => 'BG',
              'Burkina Faso' => 'BF',
              'Burundi' => 'BI',
              'Côte dIvoire' => 'CI',
              'Cabo Verde' => 'CV',
              'Cambodia' => 'KH',
              'Cameroon' => 'CM',
              'Canada' => 'CA',
              'Central African Republic' => 'CF',
              'Chad' => 'TD',
              'Chile' => 'CL',
              'China' => 'CN',
              'Colombia' => 'CO',
              'Comoros' => 'KM',
              'Congo-Brazzaville' => 'CG',
              'Costa Rica' => 'CR',
              'Croatia' => 'HR',
              'Cuba' => 'CU',
              'Cyprus' => 'CY',
              'Czech Republic' => 'CZ',
              'Democratic Republic of the Congo' => 'CD',
              'Denmark' => 'DK',
              'Djibouti' => 'DJ',
              'Dominica' => 'DM',
              'Dominican Republic' => 'DO',
              'Ecuador' => 'EC',
              'Egypt' => 'EG',
              'El Salvador' => 'SV',
              'Equatorial Guinea' => 'GQ',
              'Eritrea' => 'ER',
              'Estonia' => 'EE',
              'Swaziland' => 'SW',
              'Ethiopia' => 'ET',
              'Fiji' => 'FJ',
              'Finland' => 'FI',
              'France' => 'FR',
              'Gabon' => 'GA',
              'Gambia' => 'GM',
              'Georgia' => 'GE',
              'Germany' => 'DE',
              'Ghana' => 'GH',
              'Greece' => 'GR',
              'Grenada' => 'GD',
              'Guatemala' => 'GT',
              'Guinea' => 'GN',
              'Guinea-Bissau' => 'GW',
              'Guyana' => 'GY',
              'Haiti' => 'VA',
              'Holy See' => 'HT',
              'Honduras' => 'HN',
              'Hungary' => 'HU',
              'Iceland' => 'IS',
              'India' => 'IN',
              'Indonesia' => 'ID',
              'Iran' => 'IR',
              'Iraq' => 'IQ',
              'Ireland' => 'IE',
              'Israel' => 'IL',
              'Italy' => 'IT',
              'Jamaica' => 'JM',
              'Japan' => 'JP',
              'Jordan' => 'JO',
              'Kazakhstan' => 'KZ',
              'Kenya' => 'KE',
              'Kiribati' => 'KI',
              'Kuwait' => 'KW',
              'Kyrgyzstan' => 'KG',
              'Laos' => 'LA',
              'Latvia' => 'LV',
              'Lebanon' => 'LB',
              'Lesotho' => 'LS',
              'Liberia' => 'LR',
              'Libya' => 'LY',
              'Liechtenstein' => 'LI',
              'Lithuania' => 'LT',
              'Luxembourg' => 'LU',
              'Madagascar' => 'MG',
              'Malawi' => 'MW',
              'Malaysia' => 'MY',
              'Maldives' => 'MV',
              'Mali' => 'ML',
              'Malta' => 'MT',
              'Marshall Islands' => 'MH',
              'Mauritania' => 'MR',
              'Mauritius' => 'MU',
              'Mexico' => 'MX',
              'Micronesia' => 'FM',
              'Moldova' => 'MD',
              'Monaco' => 'MC',
              'Mongolia' => 'MN',
              'Montenegro' => 'ME',
              'Morocco' => 'MA',
              'Mozambique' => 'MZ',
              'Myanma' => 'MM',
              'Namibia' => 'NA',
              'Nauru' => 'NR',
              'Nepal' => 'NP',
              'Netherlands' => 'NL',
              'New Zealand' => 'NZ',
              'Nicaragua' => 'NI',
              'Niger' => 'NE',
              'Nigeria' => 'NG',
              'North Korea' => 'KP',
              'North Macedonia' => 'MK',
              'Norway' => 'NO',
              'Oman' => 'OM',
              'Pakistan' => 'PK',
              'Palau' => 'PW',
              'Palestine State' => 'PS',
              'Panama' => 'PA',
              'Papua New Guinea' => 'PG',
              'Paraguay' => 'PY',
              'Peru' => 'PE',
              'Philippines' => 'PH',
              'Poland' => 'PL',
              'Portugal' => 'PT',
              'Qatar' => 'QA',
              'Romania' => 'RO',
              'Russia' => 'RU',
              'Rwanda' => 'RW',
              'Saint Kitts and Nevis' => 'KN',
              'Saint Lucia' => 'LC',
              'Saint Vincent and the Grenadines' => 'VC',
              'Samoa' => 'WS',
              'San Marino' => 'SM',
              'Sao Tome and Principe' => 'ST',
              'Saudi Arabia' => 'SA',
              'Senegal' => 'SN',
              'Serbia' => 'RS',
              'Seychelles' => 'SC',
              'Sierra Leone' => 'SL',
              'Singapore' => 'SG',
              'Slovakia' => 'SK',
              'Slovenia' => 'SI',
              'Solomon Islands' => 'SB',
              'Somalia' => 'SO',
              'South Africa' => 'ZA',
              'South Korea' => 'KR',
              'South Sudan' => 'SS',
              'Spain' => 'ES',
              'Sri Lanka' => 'LK',
              'Sudan' => 'SD',
              'Suriname' => 'SR',
              'Sweden' => 'SE',
              'Switzerland' => 'CH',
              'Syria' => 'SY',
              'Tajikistan' => 'TJ',
              'Tanzania' => 'TZ',
              'Thailand' => 'TH',
              'Timor-Leste' => 'TL',
              'Togo' => 'TG',
              'Tonga' => 'TO',
              'Trinidad and Tobago' => 'TT',
              'Tunisia' => 'TN',
              'Turkey' => 'TR',
              'Turkmenistan' => 'TM',
              'Tuvalu' => 'TV',
              'Uganda' => 'UG',
              'Ukraine' => 'UA',
              'United Arab Emirates' => 'AE',
              'United Kingdom' => 'GB',
              'United States of America' => 'US',
              'Uruguay' => 'UY',
              'Uzbekistan' => 'UZ',
              'Vanuatu' => 'VU',
              'Venezuela' => 'VE',
              'Vietnam' => 'VN',
              'Yemen' => 'YE',
              'Zambia' => 'ZM',
              'Zimbabwe' => 'ZW'
          );
          $provinces = array(
            "Padova" => "PD",
            "Lodi" => "LO",
            "Lecco" => "LC",
            "Siena" => "SI",
            "Oristano" => "OR",
            "Pescara" => "PE",
            "Milano" => "MI",
            "Pistoia" => "PT",
            "Potenza" => "PZ",
            "Ragusa" => "RG",
            "Foggia" => "FG",
            "Cuneo" => "CN",
            "Matera" => "MT",
            "L Aquila" => "AQ",
            "Rieti" => "RI",
            "Salerno" => "SA",
            "Napoli" => "NA",
            "Catania" => "CT",
            "Frosinone" => "FR",
            "Cosenza" => "CS",
            "Brescia" => "BS",
            "Pesaro e Urbino" => "PU",
            "Cremona" => "CR",
            "Mantova" => "MN",
            "Viterbo" => "VT",
            "Lecce" => "LE",
            "Vibo Valentia" => "VV",
            "Ascoli Piceno" => "AP",
            "Terni" => "TR",
            "Campobasso" => "CB",
            "Bari" => "BA",
            "Isernia" => "IS",
            "Caltanissetta" => "CL",
            "Messina" => "ME",
            "Alessandria" => "AL",
            "Bergamo" => "BG",
            "Rovigo" => "RO",
            "Verona" => "VR",
            "Roma Capitale" => "RM",
            "Reggio Calabria" => "RC",
            "Piacenza" => "PC",
            "Sassari" => "SS",
            "Enna" => "EN",
            "Asti" => "AT",
            "Torino" => "TO",
            "Belluno" => "BL",
            "Varese" => "VA",
            "Monza e Brianza" => "MB",
            "Novara" => "NO",
            "Agrigento" => "AG",
            "Ancona" => "AN",
            "Vicenza" => "VI",
            "Udine" => "UD",
            "Avellino" => "AV",
            "Caserta" => "CE",
            "Biella" => "BI",
            "Benevento" => "BN",
            "Imperia" => "IM",
            "Trento" => "TN",
            "Pavia" => "PV",
            "Vercelli" => "VC",
            "Savona" => "SV",
            "Teramo" => "TE",
            "Sondrio" => "SO",
            "Parma" => "PR",
            "Como" => "CO",
            "Catanzaro" => "CZ",
            "Reggio Emilia" => "RE",
            "Trapani" => "TP",
            "Bolzano Bozen" => "BZ",
            "Ravenna" => "RA",
            "Palermo" => "PA",
            "Valle d Aosta" => "AO",
            "Fermo" => "FM",
            "Chieti" => "CH",
            "Treviso" => "TV",
            "Bologna" => "BO",
            "Lucca" => "LU",
            "La Spezia" => "SP",
            "Pordenone" => "PN",
            "Barletta-Andria-Trani" => "BT",
            "Arezzo" => "AR",
            "Venezia" => "VE",
            "Verbano Cusio Ossola" => "VB",
            "Macerata" => "MC",
            "Latina" => "LT",
            "Sud Sardegna" => "SU",
            "Grosseto" => "GR",
            "Genova" => "GE",
            "Ferrara" => "FE",
            "Nuoro" => "NU",
            "Cagliari" => "CA",
            "Perugia" => "PG",
            "Siracusa" => "SR",
            "Massa e Carrara" => "MS",
            "Taranto" => "TA",
            "Firenze" => "FI",
            "Forlì Cesena" => "FC",
            "Modena" => "MO",
            "Rimini" => "RN",
            "Crotone" => "KR",
            "Livorno" => "LI",
            "Pisa" => "PI",
            "Brindisi" => "BR",
            "Prato" => "PO",
            "Gorizia" => "GO",
            "Trieste" => "TS"
        );
        
          $query_option_value = "SELECT * FROM `migareference_geo_countries` WHERE  `app_id` = $app_id AND country_code IS NULL";
          $country_exist=$this->_db->fetchAll($query_option_value);
          foreach ($country_exist as $key => $value) {
            $id=$value['migareference_geo_countries_id'];
            $cou=$value['country'];
            $code['country_code']=$countries[$cou];
            $this->_db->update("migareference_geo_countries", $code,['migareference_geo_countries_id = ?' => $id, 'app_id = ?' => $app_id]);
          }
          $query_option_value = "SELECT * FROM `migareference_geo_provinces` WHERE  `app_id` = $app_id AND province_code IS NULL";
          $province_exist=$this->_db->fetchAll($query_option_value);
          foreach ($province_exist as $key => $value) {
            $id=$value['migareference_geo_provinces_id'];
            $cou=$value['province'];
            $code['province_code']=$provinces[$cou];
            $this->_db->update("migareference_geo_provinces", $code,['migareference_geo_provinces_id = ?' => $id, 'app_id = ?' => $app_id]);
          }
          return $code;
    }
    public function fixRemindeerdefault()
    {
        $query_option = "SELECT *
                        FROM `migarefrence_report_reminder_auto`                        
                        WHERE 1
                        ORDER BY app_id";
        $auto_rem   = $this->_db->fetchAll($query_option);        
        foreach ($auto_rem as $key => $value) {
            $id=$value['migarefrence_report_reminder_auto_id'];
            $app_id=$value['app_id'];
            $auto_rem_type=$value['auto_rem_type'];
            $q2="SELECT * FROM `migarefrence_report_reminder_types` WHERE app_id=$app_id AND `migarefrence_report_reminder_types_id`=$auto_rem_type";
            $exist   = $this->_db->fetchAll($q2);//it mean correct data exist
            if (!count($exist)) {
                if ($auto_rem_type==4) {                    
                    $q3="SELECT * FROM `migarefrence_report_reminder_types` WHERE app_id=$app_id AND `rep_rem_title`='Buon Compleanno'";
                }elseif ($auto_rem_type==3) {
                    $q3="SELECT * FROM `migarefrence_report_reminder_types` WHERE app_id=$app_id AND `rep_rem_title`='Benvenuto'";
                }elseif ($auto_rem_type==2) {
                    $q3="SELECT * FROM `migarefrence_report_reminder_types` WHERE app_id=$app_id AND `rep_rem_title`='Ricontatto'";
                }
                $rem_type   = $this->_db->fetchAll($q3);
                $data['auto_rem_type']  = $rem_type[0]['migarefrence_report_reminder_types_id'];
                $data['updated_at']  = date('Y-m-d H:i:s');                                  
                $this->_db->update("migarefrence_report_reminder_auto", $data,['migarefrence_report_reminder_auto_id = ?' => $id]);                
            }
        }
    }
    public function updateProvince($data=[])
    {
      $id=$data['geo_province'];
      $app_id=$data['app_id'];
      $province['province']=$data['province'];
      $province['province_code']=$data['province_code'];
      $this->_db->update("migareference_geo_provinces", $province,['migareference_geo_provinces_id = ?' => $id, 'app_id = ?' => $app_id]);
      return true;
    }
    // 12/06/2021 Temporary Method to make it compatible (All the Landing Pgae Rports will have consent by default)
    public function landingReportconsent($app_id=0)
    {
      $query_option = "SELECT * FROM `migareference_report` WHERE `app_id`=$app_id AND `report_source`=2";
      $landing_reports   = $this->_db->fetchAll($query_option);
      foreach ($landing_reports as $key => $value) {
        $data['app_id']									= $app_id;
        $data['migareference_report_id']= $value['migareference_report_id'];
        $data['consent_ip']							= '';
        $data['consent_timestmp']       = $value['created_at'];
        $data['consent_source']					= 'Landing Page';
        // $this->updatepropertyreport($data);
      }
      return true;
    }
}
