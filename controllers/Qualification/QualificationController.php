<?php
class Migareference_Qualification_QualificationController extends Application_Controller_Default
{
    public function loadqualificationviewAction()
    {
        try {
            // Pass the value_id to the layout
            $value_id = $this->getRequest()->getParam('option_value_id');
            $resp = $this->getLayout()
                ->setBaseRender('emailform', 'migareference/application/qualification/qualification.phtml', 'admin_view_default')
                ->setValueid($value_id);
            $data = [
                'success' => true,
                'resp' => $resp,
                'form' => $this->getLayout()->render(),
                'message_timeout' => 0,
                'message_button' => 0,
                'message_loader' => 0,
            ];
        } catch (Exception $e) {
            $data = [
                'error' => true,
                'message' => $e->getMessage(),
                'message_button' => 1,
                'message_loader' => 1,
            ];
        }
        $this->_sendJson($data);
    }
    public function savequalificationAction()
    {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $app_id = $datas['app_id'];
                if (!$app_id) {
                    throw new Exception('An error occurred while saving. Please try again later.');
                }

                $errors = "";

                // Validation
                if (empty($datas['qlf_name'])) {
                    $errors .= __('Name cannot be empty.') . "<br/>";
                }

                if (!isset($datas['qlf_credits']) || $datas['qlf_credits'] === "") {
                    $errors .= __('Credits cannot be empty.') . "<br/>";
                } elseif (!is_numeric($datas['qlf_credits'])) {
                    $errors .= __('Credits must be a number.') . "<br/>";
                }

                if (!isset($datas['qlf_status'])) {
                    $errors .= __('Status is required.') . "<br/>";
                }

                if (!isset($datas['qlf_file'])) {
                    $errors .= __('Logo/Icon is required.') . "<br/>";
                }

                if (!empty($errors)) {
                    throw new Exception($errors);
                } else {
                    $migareference = new Migareference_Model_Qualification();
                    $operation = $datas['operation'];
                    unset($datas['operation']);

                    if ($operation == 'create') {
                        $migareference->saveQualification($datas);
                    } else {
                        $migareference->updateQualification($datas);
                    }
                }

                $html = [
                    'success'         => true,
                    'message'         => __('Qualification saved successfully.'),
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
    public function getqualificationsAction()
{
    try {
        $appId = $this->getRequest()->getParam('app_id');
        if (!$appId) {
            throw new Exception("App ID missing");
        }

        $rows = (new Migareference_Model_Qualification())->findAll(['app_id' => $appId]);

        $default = new Core_Model_Default();
        $base_url = $default->getBaseUrl();

        $data = [];
        foreach ($rows as $row) {
       
            $qlfFile = $row->getQlfFile();
            $imageUrl = "";
            if (!empty($qlfFile)) {
                
                $imageUrl = $base_url . '/images/application/' . $appId . '/features/migareference/' . $qlfFile;
            }

            $data[] = [
                'id'     => $row->getId(),
                'name'   => $row->getQlfName(),
                'credits' => $row->getQlfCredits(),
               'list' => "
                            <a href='javascript:void(0);' class='font-weight-bold' onclick='ViewList(".$row->getId().")'>
                                <i class='fa fa-eye fa-lg text-primary'></i>
                            </a>
                        ",
                'content_type' => "
                            <a href='javascript:void(0);' class='font-weight-bold' onclick='initContentTypeOptions(" . (int)$row->getId() . "); return false;'>
                                <i class='fa fa-eye fa-lg text-success'></i>
                            </a>
                        ",
                'status' => $row->getQlfStatus() ? 'Active' : 'Inactive',
                'created_at' => $row->getCreatedAt(),

                
                'qlf_file' => $qlfFile,    
                'image_url' => $imageUrl,  

                'action' => "
                    <button class='btn btn-info btn-sm' onclick='editQualification(" . $row->getId() . ")'>Edit</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteQualification(" . $row->getId() . ")'>Delete</button>
                "
            ];
        }

        $payload = ['data' => $data];
    } catch (Exception $e) {
        $payload = ['data' => [], 'error' => $e->getMessage()];
    }

    $this->_sendJson($payload);
}

    public function deletequalificationAction()
    {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $id = $datas['migarefrence_qualifications_id'] ?? null;
                if (!$id) throw new Exception("Qualification ID is missing");

                $model = new Migareference_Model_Qualification();
                $model->deleteQualification($id);

                $this->_sendJson(['success' => true, 'message' => "Qualification deleted successfully"]);
            } catch (Exception $e) {
                $this->_sendJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
 public function editqualificationAction()
{
    if ($request = $this->getRequest()->getQuery()) {
        try {
            $qualificationModel = new Migareference_Model_Qualification();

            $id = $request['id'] ?? null;
            if (!$id) {
                throw new Exception("Qualification ID missing");
            }

            $qualification = $qualificationModel->findAll([
                'migareference_qualifications_id' => $id
            ])->toArray();

            if (!$qualification || !isset($qualification[0])) {
                throw new Exception("Qualification not found");
            }

          
            $app_id  = $this->getApplication()->getId();
            $base_url = $this->getUrl(); // ya tumhare system ka base url function
            $applicationBase = $base_url."/images/application/".$app_id."/features/migareference/";

         
            if (!empty($qualification[0]['qlf_file'])) {
                $qualification[0]['qlf_file'] = $applicationBase.$qualification[0]['qlf_file'];
            }

            $payload = [
                "data" => $qualification[0]
            ];
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    } else {
        $payload = [
            'error' => true,
            'message' => __('Invalid request.')
        ];
    }

    $this->_sendJson($payload);
}

    public function viewlistAction()
    {
        try {
            $qualification_id = (int)$this->getRequest()->getParam('id');

            if (!$qualification_id) {
                return $this->_sendJson([
                    'error' => 1,
                    'message' => 'Qualification ID is required'
                ]);
            }

             
            $model = new Migareference_Model_QualificationReferrer();
            $customers = $model->getCustomersByQualification($qualification_id);

            if (empty($customers)) {
                return $this->_sendJson([
                    'error' => 1,
                    'message' => 'No customers found for this qualification'
                ]);
            }

            return $this->_sendJson([
                'error' => 0,
                'data' => [
                    'qualification_id' => $qualification_id,
                    'customers' => $customers
                ]
            ]);
        } catch (Exception $e) {
            return $this->_sendJson([
                'error' => 1,
                'message' => 'Error fetching customer list: ' . $e->getMessage()
            ]);
        }
    }
 
public function savecontentsettingAction()
{
    if ($datas = $this->getRequest()->getPost()) {
        try {
            $app_id = isset($datas['app_id']) ? (int)$datas['app_id'] : 0;
            $value_id = isset($datas['value_id']) ? (int)$datas['value_id'] : 0;
            $qualification_id = isset($datas['qualification_id'])
                ? (int)$datas['qualification_id']
                : (int)($datas['qualification_id'] ?? 0);

            if (!$app_id || !$qualification_id) {
                throw new Exception(__('App ID and Qualification ID are required.'));
            }

            $errors = "";
 
            if (isset($datas['customer_content_type']) && $datas['customer_content_type'] !== "") {
                if (!isset($datas['customer_list_id']) || $datas['customer_list_id'] === "") {
                    $errors .= __('Customer list is required.') . "<br/>";
                }
            }
            if (isset($datas['non_customer_content_type']) && $datas['non_customer_content_type'] !== "") {
                if (!isset($datas['non_customer_list_id']) || $datas['non_customer_list_id'] === "") {
                    $errors .= __('Non-customer list is required.') . "<br/>";
                }
            }

            if (!empty($errors)) {
                throw new Exception($errors);
            }

            $model = new Migareference_Model_QualificationContentSetting();

            // Prepare save data
            $save = [
                'migareference_qualifications_content_setting_id' => $datas['migareference_qualification_content_setting_id'] ?? null,
                'app_id' => $app_id,
                'value_id' => $value_id,
                'qualification_id' => $qualification_id,
                'non_customer_content_type' => $datas['non_customer_content_type'] ?? null,
                'non_customer_list_id' => $datas['non_customer_list_id'] ?? null,
                'customer_content_type' => $datas['customer_content_type'] ?? null,
                'customer_list_id' => $datas['customer_list_id'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Decide update vs create
            if (!empty($save['migareference_qualifications_content_setting_id'])) {
                // update by id
                $model->find((int)$save['migareference_qualifications_content_setting_id']);
            } else {
                // upsert by composite keys
                $model->find([
                    'app_id' => $app_id,
                    'value_id' => $value_id,
                    'qualification_id' => $qualification_id,
                ]);
            }

            if (!$model->getId()) {
                $save['created_at'] = date('Y-m-d H:i:s');
            }

            $model->setData($save)->save();

            $html = [
                'success'         => true,
                'message'         => __('Content setting saved successfully.'),
                'data'            => ['id' => $model->getId()],
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

        return $this->_sendJson($html);
    }
}
public function getcontentsettingAction()
{
    $request = $this->getRequest();
    if (!$request->isPost()) {
        return $this->_sendJson([
            'success' => false,
            'message' => __('Invalid request method.')
        ]);
    }

    
    $qualificationId = $request->getPost('migareference_qualifications_id')
        ?: $request->getPost('qualification_id');
    $appId   = $request->getPost('app_id');
    $valueId = $request->getPost('value_id');

    if (!$qualificationId || !$appId || !$valueId) {
        return $this->_sendJson([
            'success' => false,
            'message' => __('Missing required parameters.')
        ]);
    }

    try {
        $model = new Migareference_Model_QualificationContentSetting();
        $setting = $model->getByAppValueQualification((int)$appId, (int)$valueId, (int)$qualificationId);

        if ($setting) {  
            return $this->_sendJson([
                'success' => true,
                'data' => $setting
            ]);
        }

        return $this->_sendJson([
            'success' => true,
            'data' => null
        ]);
    } catch (Exception $e) {
        return $this->_sendJson([
            'success' => false,
            'message' => 'Error fetching data: ' . $e->getMessage()
        ]);
    }
}
public function loadfeaturesAction()
{
    $request = $this->getRequest();

    if (!$request->isPost()) {
        return $this->_sendJson([
            'success' => false,
            'message' => __('Invalid request method.')
        ]);
    }

    $app_id = (int) $this->getApplication()->getId();

    $type = $request->getPost('type', 'feature');
    $key  = ($type === '2' || $type === 2 || $type === 'folder') ? 'folder' : 'feature';

    try {
        $model   = new Migareference_Model_QualificationContentSetting();
        $options = [];

        if ($key === 'feature') {
            $rows = $model->fetchVisibleFeaturesForApp($app_id);
            $rows = is_array($rows) ? $rows : ($rows ? $rows->toArray() : []);
            foreach ($rows as $r) {
                $label = $r['tabbar_name'] ?: ($r['name'] ?? '');
                if ($label === '' || $label === null) {
                    $label = $r['code'] ?? ('Feature #' . $r['value_id']);
                }
                $options[] = [
                    'value' => (string) $r['value_id'],
                    'label' => (string) $label,
                ];
            }
        } else {
            $rows = $model->fetchFoldersForApp($app_id);
            $rows = is_array($rows) ? $rows : ($rows ? $rows->toArray() : []);
            foreach ($rows as $r) {
                $label = $r['tabbar_name'] ?: ($r['name'] ?? '');
                if ($label === '' || $label === null) {
                    $label = $r['code'] ?? ('Folder #' . ($r['value'] ?? ''));
                }
                $options[] = [
                    'value' => (string) ($r['value'] ?? ''),
                    'label' => (string) $label,
                ];
            }
        }

        return $this->_sendJson([
            'success' => true,
            'options' => $options
        ]);

    } catch (Exception $e) {
        return $this->_sendJson([
            'success' => false,
            'message' => 'Error loading options: ' . $e->getMessage(),
            'options' => []
        ]);
    }
}






}
