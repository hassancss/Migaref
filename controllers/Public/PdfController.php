<?php
use \Chirp\Cryptor;
use \Migaref\mPDF;
/**
 * Class Migareference_Public_PdfController
 */
class Migareference_Public_PdfController extends Migareference_Controller_Default {
	//Solana
	public function downloadPdfAction() {
		if ($report_id = $this->getRequest()->getParam('report_id')) {
			$ledger = new Migareference_Model_Ledger();
			$report = $ledger->getReportData($report_id);
			if (count($report)) {
				$report = $report[0];
				$cryptor_report_hash = new Cryptor($report['app_id'] ."_". $report['app_name'] ."_". $report['report_no']);
				$report_hash = $cryptor_report_hash->encrypt($report['app_id'] ."_". $report['app_name'] ."_". $report['report_no'] ."_". $report['referral_name'] ."_". $report['referral_surname'] ."_". $report['owner_name'] ."_". $report['owner_surname'] ."_". $report['report_created_at']);
				$platform_url = explode("/", Core_Model_Directory::getBasePathTo());//index 4 have platform url
				$certificate_bg = "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/certificate_bg.png";
				$pdf_data = '<h3 style="position: absolute; top: 310px; left: 620px; width: 500px;">'.$report['report_no'].'</h3>';
				$pdf_data .= '<h3 style="position: absolute; top: 360px; left: 620px; width: 500px;">'.$report['app_name'].'</h3>';
				$pdf_data .= '<h5 style="position: absolute; top: 410px; left: 620px; width: 500px;">'.$report['xml_file_name'].'</h5>';
				$pdf_data .= '<h6 style="position: absolute; top: 440px; left: 620px; width: 340px;font-size: 10px;">'.$report_hash.'</h6>';
				$pdf_data .= '<p style="position: absolute; top: 505px; left: 620px; width: 340px;"><a href="'.$report['ipfs_address'].'" target="_blank" style="text-decoration: none;">'.$report['ipfs_address'].'</a></p>';
				if ($report['notarization_platform'] == 'Solana') {
					$pdf_data .= '<p style="position: absolute; top: 550px; left: 620px; width: 340px;font-size: 12px;"><a href="https://explorer.solana.com/tx/'.$report['eth_address'].'" target="_blank" style="text-decoration: none;">https://explorer.solana.com/tx/'.$report['eth_address'].'</a></p>';
				} else {
				    $certificate_bg = "https://".$platform_url[4]."/app/local/modules/Migareference/resources/appicons/certificate_bg_ETH.png";
					$pdf_data .= '<p style="position: absolute; top: 555px; left: 620px; width: 340px;"><a href="'.$report['eth_address_url'].'" target="_blank" style="text-decoration: none;">'.$report['eth_address_url'].'</a></p>';
				}
				$title = "Digital Notarization Certificate";
				$name = "digital_notarization_certificate_".time().".pdf";
				$mpdf = new mPDF('en-GB-x','A4-L','','',0,0,0,0,0,0);
				$mpdf->SetTitle($title);
				$mpdf->SetDefaultBodyCSS('background', "url('".$certificate_bg."')");
				$mpdf->SetDefaultBodyCSS('background-image-resize', 6);
				$mpdf->WriteHTML($pdf_data);
				$mpdf->Output($name, 'I');
			} else {
				$title = "No Report Found.";
				$name = "no_report_found_".time().".pdf";
				$mpdf = new mPDF('en-GB-x','A4-L','','',0,0,0,0,0,0);
				$mpdf->SetTitle($title);
				$mpdf->WriteHTML('<p style="text-align:center;color: red; position: absolute; top: 45%; left: 45%;"><strong>'.__('No Report Found.').'</strong></p>');
				$mpdf->Output($name, 'I');
			}
		}
		exit;
	  }
}
