<?php
use \Chirp\Cryptor;
class Migareference_DecryptorController extends Migareference_Controller_Default {
	public static $platform_url = '';
	
	public function indexAction() {
		$default = new Core_Model_Default();
		$errors = '';
		$success = '';
		$xml = '';
		if ($datas = $this->getRequest()->getPost()) {
			if(empty($_FILES)) {
				$errors .= __('No file uploaded.').'<br/>';
			} else if (!in_array(pathinfo($_FILES['xml_file']['name'], PATHINFO_EXTENSION), ['xml', 'XML'])) {
				$errors .= __('File must be an XML file.').'<br/>';
			}
			if(empty($datas['encryption_key'])) {
				$errors .= __('Encryption key cannot be empty.').'<br/>';
			}
			if (empty($errors)) {
				$temp = explode(".", $_FILES["xml_file"]["name"]);
				$filename = round(microtime(true)) . '.' . end($temp);
				if (move_uploaded_file($_FILES["xml_file"]["tmp_name"], Core_Model_Directory::getTmpDirectory(true).'/'.$filename)) {
					if (file_exists(Core_Model_Directory::getTmpDirectory(true).'/'.$filename)) { 
						$xml_array = (array) readDatabase($default->getBaseUrl().'/var/tmp/'.$filename);
						if (is_array($xml_array) && count($xml_array) && array_keys_exists(['app_id', 'app_name', 'report_no', 'referral_name', 'referral_surname', 'prospect_name', 'prospect_surname', 'report_created_at', 'report_hash'], (array) $xml_array[0])) {
							$user_data = '';
							foreach ($xml_array as $key => $report) {
								$current_user_report = '';
								$report = (array) $report;
								$cryptor_report = new Cryptor($report['app_id'] ."_". $report['app_name'] ."_". $report['report_no']);
								$report_hash = $cryptor_report->decrypt($report['report_hash']);
								$report_data = [];
								$report_data[] = $report['app_id'];
								$report_data[] = $report['app_name'];
								$report_data[] = $report['report_no'];
								foreach ($report as $tag => $value) {
									if (in_array($tag, ['referral_name', 'referral_surname', 'prospect_name', 'prospect_surname', 'report_created_at'])) {
										$cryptor = new Cryptor($datas['encryption_key']);
										$token = $cryptor->decrypt($value); 
										if ($token != FALSE) {
											$current_user_report .= "			<" . $tag . ">" . $token . "</" . $tag . ">\n";
											$report_data[] = $token;
										}
									} 
								}
								$report_data[] = $report['report_created_at'];
								if (!empty($current_user_report) && $report_hash == implode("_", $report_data)) {
									$user_data .= "		<report>\n";
									$user_data .= "			<app_id>".$report['app_id']."</app_id>\n";
									$user_data .= "			<app_name>".$report['app_name']."</app_name>\n";
									$user_data .= "			<report_no>".$report['report_no']."</report_no>\n";
									$user_data .= $current_user_report;
									$user_data .= "			<report_created_at>".$report['report_created_at']."</report_created_at>\n";
									$user_data .= "		</report>\n";
								}
							}
							if (empty($user_data)) {
								$errors .= __('No matching data found.').'<br/>';
							} else {
								$success = __('Your XML has been decrypted successfully and we have found the data.').'<br/>';
								$xml = "<xml>\n";
								$xml .= "	<platform_url>".Migareference_DecryptorController::$platform_url."</platform_url>\n";
								$xml .= "	<reports>\n";
								$xml .= $user_data;
								$xml .= "	</reports>\n";
								$xml .= "</xml>";
							}
						} else {
							$errors .= __('Invalid XML file.').'<br/>';
						}
						@unlink($default->getBaseUrl().'/var/tmp/'.$filename);
					}
				} else {
					$errors .= __('Sorry, there was an error uploading your file.').'<br/>';
				}
			}
		}
		$layout = new Siberian_Layout();
		$layout->setBaseRender('base', 'migareference/application/decryptor.phtml', 'core_view_default');
		$layout
			->getBaseRender()
			->setTitle(__('XML Decryptor'))
			->setDescription(__('This XML decryption tool can be use decrypt the XML that is notarized and downloaded from block chain. You can use your key to decrypt only your data and rest will remain encrypted.'))
			->setVersion('v1.0.0')
			->setVendor(__('Powered By Migastone International Ltd.'))
			->setSampleXmlTitle(__('Sample XML'))
			->setPanelTitle(__('Upload XML with Key'))
			->setLabelFile(__('XML File'))
			->setLabelFileHelp(__('Upload the XML file downloaded from IPFS.'))
			->setLabelKey(__('Encryption Key'))
			->setLabelKeyHelp(__('Enter the encryption key you set for your reports.'))
			->setLabelDecrypt(__('Upload & Decrypt'))
			->setMainUrl($default->getBaseUrl())
			->setErrors($errors)
			->setSuccess($success)
			->setUserXmlLabel('Your XML')
			->setUserXml($xml)
			->setSampleXmlFile($default->getBaseUrl() . '/app/local/modules/Migareference/resources/appicons/sample_xml.png');
		echo $layout->render();
        die;
	}
}

class Reports {
	var $app_id;
	var $app_name;
	var $report_no;
	var $referral_name;
	var $referral_surname;
	var $prospect_name;
	var $prospect_surname;
	var $report_created_at;
    
    function __construct ($aa) 
    {
        foreach ($aa as $k=>$v)
            $this->$k = $aa[$k];
    }
}

function readDatabase($filename) 
{
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
	xml_parser_free($parser);
	
    foreach ($tags as $key => $val) {
		if ($key == "platform_url") {
			Migareference_DecryptorController::$platform_url = $values[$val[0]]['value'];
		}
        if ($key == "report") {
            $reports = $val;
            for ($i=0; $i < count($reports); $i+=2) {
                $offset = $reports[$i] + 1;
                $len = $reports[$i + 1] - $offset;
                $tdb[] = parseReport(array_slice($values, $offset, $len));
            }
        } else {
            continue;
        }
    }
    return $tdb;
}

function parseReport($report_values) {
    for ($i=0; $i < count($report_values); $i++) {
        $report[$report_values[$i]["tag"]] = $report_values[$i]["value"];
    }
    return new Reports($report);
}

function array_keys_exists(array $keys, array $arr) {
	return !array_diff_key(array_flip($keys), $arr);
 }
