<?php
/**
 * Class Migareference_Public_CronController
 */
class Migareference_Public_CronController extends Migareference_Controller_Default {
    public function runAction() {
		$status = Migareference_Model_Db_Table_Cron::xmlCron();
        dd($status);
        $message = "Cron does not executed successfully.";
        if ($status) {
            $message = "Cron executed successfully.";
		}
        echo __($message);
		exit;
    }
    public function runcronAction() {
		$status = Migareference_Model_Db_Table_Migareference::automationTriggerscron();
        dd($status);
        $message = "Cron does not executed successfully.";
        if ($status) {
            $message = "Cron executed successfully.";
		}
        echo __($message);
		exit;
    }
}