<?php
class Migareference_Qualification_NotificationController extends Application_Controller_Default
{
    public function savenotificationAction()
    {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $app_id = $datas['app_id'];
                if (!$app_id) {
                    throw new Exception('An error occurred while saving. Please try again later.');
                }

                $errors = "";

                // Validation
                if (empty($datas['webhook'])) {
                    $errors .= __('Webhook URL cannot be empty.') . "<br/>";
                }

                if (empty($datas['email_subject'])) {
                    $errors .= __('Email Subject cannot be empty.') . "<br/>";
                }

                if (empty($datas['email_text'])) {
                    $errors .= __('Email Body cannot be empty.') . "<br/>";
                }

                if (empty($datas['push_title'])) {
                    $errors .= __('Push Notification Title is required.') . "<br/>";
                }

                if (empty($datas['push_text'])) {
                    $errors .= __('Push Notification Message is required.') . "<br/>";
                }

                if (
                    !empty($datas['ref-credits-api-open-features'])
                    && $datas['ref-credits-api-open-features'] == 1
                    && empty($datas['ref-credits-api-feature-ids'])
                ) {
                    $errors .= __('You must select a feature or custom URL.') . "<br/>";
                }

                if (
                    !empty($datas['ref-credits-api-feature-ids'])
                    && $datas['ref_credits_api_feature_id'] == 0
                    && empty($datas['ref_credits_api_custom_url'])
                ) {
                    $errors .= __('Custom URL cannot be empty.') . "<br/>";
                }

                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $notification = new Migareference_Model_Notification();
                    $operation = $datas['operation'];
                    unset($datas['operation']);

                    if ($operation == 'create') {
                        $notification->saveNotification($datas);
                    } else {
                        $notification->updateNotification($datas);
                    }
                }

                $html = [
                    'success'         => true,
                    'message'         => __('Notification saved successfully.'),
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
   public function getnotificationAction()
{
    if ($datas = $this->getRequest()->getPost()) {
        try {
            $app_id = $datas['app_id'];
            if (!$app_id) {
                throw new Exception('App ID missing.');
            }

            $notification = new Migareference_Model_Notification();
            $data = $notification->getNotificationByAppId($app_id);

            if ($data) {
                $imageUrl = '';
                if (!empty($data['logo_url'])) { // Column name is logo_url
                    $default = new Core_Model_Default();
                    $base_url = $default->getBaseUrl();

                   
                    $imageUrl = $base_url . '/images/application/' . $app_id . '/features/migareference/' . $data['logo_url'];
                }
                $data['image_url'] = $imageUrl;

                $html = [
                    'success' => true,
                    'data'    => $data
                ];
            } else {
                $html = [
                    'success' => false,
                    'data'    => null
                ];
            }
        } catch (Exception $e) {
            $html = [
                'error'   => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($html);
    }
}


}