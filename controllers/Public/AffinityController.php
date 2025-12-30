<?php
/**
 * Public API endpoints for affinity pairing runs.
 *
 * Endpoints:
 * - POST /migareference/public_affinity/start
 *   Validates token, finds app_id, gathers eligible referrers, creates a run,
 *   and initializes cursor_i/cursor_j for deterministic pair iteration.
 * - POST /migareference/public_affinity/nextpairs
 *   Validates token, checks run ownership, re-evaluates eligibility, and
 *   returns the next batch of (i,j) pairs while persisting cursors/progress.
 *
 * Notes:
 * - This controller does not call OpenAI; it only exposes pair generation.
 * - All responses are JSON via $this->_sendJson().
 */
class Migareference_Public_AffinityController extends Migareference_Controller_Default {
    public function startAction() {
        try {
            $reportapi = new Migareference_Model_Reportapi();
            $affinity = new Migareference_Model_Affinity();
            $data = $this->getRequest()->getPost();

            $token = isset($data['token']) ? trim($data['token']) : '';
            if (empty($token) || strlen($token) != 35) {
                throw new Exception(__("Token Mismatchd"));
            }
            $pre_report_settings = $reportapi->validateToken($token);
            if (!count($pre_report_settings)) {
                throw new Exception(__("Token Mismatched"));
            }
            $app_id = (int) $pre_report_settings[0]['app_id'];

            $eligible_ids = $affinity->getEligibleReferrerIds($app_id);
            $total_referrers = count($eligible_ids);
            $total_pairs_estimate = ($total_referrers * ($total_referrers - 1)) / 2;

            $status = 'running';
            $cursor_i = 0;
            $cursor_j = 1;
            $message = "Affinity run started.";
            if ($total_referrers < 2) {
                $status = 'completed';
                $cursor_i = 0;
                $cursor_j = 0;
                $message = "Not enough referrers";
            }

            $run_id = $affinity->createRun($app_id, [
                'status' => $status,
                'total_referrers' => $total_referrers,
                'total_pairs_estimate' => $total_pairs_estimate,
                'cursor_i' => $cursor_i,
                'cursor_j' => $cursor_j,
            ]);

            $payload = [
                "response" => true,
                "message" => __($message),
                "run_id" => (int) $run_id,
                "app_id" => $app_id,
                "total_referrers" => $total_referrers,
                "total_pairs_estimate" => $total_pairs_estimate,
                "cursor_i" => $cursor_i,
                "cursor_j" => $cursor_j,
            ];
        } catch (Exception $e) {
            $payload = [
                "response" => false,
                "message" => __($e->getMessage()),
            ];
        }
        $this->_sendJson($payload);
    }

    public function nextpairsAction() {
        try {
            $reportapi = new Migareference_Model_Reportapi();
            $affinity = new Migareference_Model_Affinity();
            $data = $this->getRequest()->getPost();

            $token = isset($data['token']) ? trim($data['token']) : '';
            if (empty($token) || strlen($token) != 35) {
                throw new Exception(__("Token Mismatchd"));
            }
            $pre_report_settings = $reportapi->validateToken($token);
            if (!count($pre_report_settings)) {
                throw new Exception(__("Token Mismatched"));
            }
            $app_id = (int) $pre_report_settings[0]['app_id'];

            $run_id = isset($data['run_id']) ? (int) $data['run_id'] : 0;
            if ($run_id <= 0) {
                throw new Exception(__("Run ID is required."));
            }

            $batch_pairs = isset($data['batch_pairs']) ? (int) $data['batch_pairs'] : 20;
            if ($batch_pairs <= 0) {
                $batch_pairs = 20;
            }
            if ($batch_pairs > 200) {
                $batch_pairs = 200;
            }

            $run = $affinity->getAffinityRun($run_id);
            if (!$run || (int) $run['app_id'] !== $app_id) {
                throw new Exception(__("Run not found."));
            }

            $eligible_ids = $affinity->getEligibleReferrerIds($app_id);
            $eligible_ids = array_values($eligible_ids);
            $total_referrers = count($eligible_ids);
            $total_pairs_estimate = ($total_referrers * ($total_referrers - 1)) / 2;

            $pairs = [];
            $status = $run['status'];
            $cursor_i = (int) $run['cursor_i'];
            $cursor_j = (int) $run['cursor_j'];
            $processed_pairs = (int) $run['processed_pairs'];

            if ($total_referrers < 2) {
                $status = 'completed';
                $cursor_i = 0;
                $cursor_j = 0;
            } else {
                $i = $cursor_i;
                $j = $cursor_j;
                $generated = 0;
                $max_i = $total_referrers - 1;
                while ($i < $max_i && $generated < $batch_pairs) {
                    if ($j <= $i) {
                        $j = $i + 1;
                    }
                    if ($j >= $total_referrers) {
                        $i++;
                        $j = $i + 1;
                        continue;
                    }
                    $pairs[] = [
                        "a" => $eligible_ids[$i],
                        "b" => $eligible_ids[$j],
                        "i" => $i,
                        "j" => $j,
                    ];
                    $generated++;
                    $j++;
                }
                $processed_pairs += $generated;
                $cursor_i = $i;
                $cursor_j = $j;
                if ($cursor_i >= $max_i) {
                    $status = 'completed';
                } else {
                    $status = 'running';
                }
            }

            $affinity->updateAffinityRun($run_id, [
                'status' => $status,
                'cursor_i' => $cursor_i,
                'cursor_j' => $cursor_j,
                'total_referrers' => $total_referrers,
                'total_pairs_estimate' => $total_pairs_estimate,
                'processed_pairs' => $processed_pairs,
            ]);

            $payload = [
                "response" => true,
                "message" => __("Next pairs generated."),
                "run_id" => (int) $run_id,
                "batch_pairs" => $batch_pairs,
                "pairs" => $pairs,
                "cursor_i" => $cursor_i,
                "cursor_j" => $cursor_j,
                "processed_pairs" => $processed_pairs,
                "total_pairs_estimate" => $total_pairs_estimate,
                "status" => $status,
            ];
        } catch (Exception $e) {
            $payload = [
                "response" => false,
                "message" => __($e->getMessage()),
            ];
        }
        $this->_sendJson($payload);
    }
}
