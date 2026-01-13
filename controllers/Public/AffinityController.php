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
 * - processAction calls OpenAI for batch scoring.
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

    public function processAction() {
        $openai_requests_made = 0;
        $errors_count = 0;

        try {
            $reportapi = new Migareference_Model_Reportapi();
            $affinity = new Migareference_Model_Affinity();
            $openaiConfig = new Migareference_Model_OpenaiConfig();
            $scoringService = new Migareference_Model_AffinityScoringService();
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

            $primary_id = isset($data['primary_id']) ? (int) $data['primary_id'] : 0;
            if ($primary_id <= 0) {
                throw new Exception(__("Primary referrer ID is required."));
            }

            $compare_ids = $this->parseCompareIds($data['compare_ids'] ?? []);
            if (!count($compare_ids)) {
                throw new Exception(__("Compare referrer IDs are required."));
            }

            $run = $affinity->getAffinityRun($run_id);
            if (!$run || (int) $run['app_id'] !== $app_id) {
                throw new Exception(__("Run not found."));
            }

            $profile_rows = $affinity->getReferrerProfiles($app_id, array_merge([$primary_id], $compare_ids));
            if (!isset($profile_rows[$primary_id])) {
                throw new Exception(__("Primary referrer not found."));
            }

            $primaryProfile = $this->buildAffinityProfile($profile_rows[$primary_id]);
            $compareProfiles = [];
            foreach ($compare_ids as $compare_id) {
                if (!isset($profile_rows[$compare_id])) {
                    $errors_count++;
                    continue;
                }
                $compareProfiles[$compare_id] = $this->buildAffinityProfile($profile_rows[$compare_id]);
            }

            if (!count($compareProfiles)) {
                throw new Exception(__("No valid compare profiles found."));
            }

            $openai_config = $openaiConfig->findAll(['app_id'=> $app_id])->toArray();
            if (!count($openai_config)) {
                throw new Exception(__("Missing OpenAI configuration."));
            }
            $openai_config = $openai_config[0];

            $api_key = $openai_config['openai_apikey'];
            $api_url = 'https://api.openai.com/v1/chat/completions';
            if ($openai_config['gpt_api'] == 'perplexity') {
                $api_url = 'https://api.perplexity.ai/chat/completions';
                $api_key = $openai_config['perplexity_apikey'];
            }
            if (empty($api_key)) {
                throw new Exception(__("Missing OpenAI API key."));
            }

            $settings = [
                'model' => isset($data['model']) && trim($data['model']) !== ''
                    ? trim($data['model'])
                    : ($openai_config['matching_ai_model'] ?: $openai_config['call_script_ai_model']),
                'temperature' => isset($data['temperature'])
                    ? (float) $data['temperature']
                    : (float) $openai_config['openai_temperature'],
                'max_tokens' => isset($data['max_tokens'])
                    ? (int) $data['max_tokens']
                    : (int) $openai_config['openai_token'],
                'api_key' => $api_key,
                'api_url' => $api_url,
                'system_prompt' => $openai_config['system_prompt'] ?? '',
                'prompt_template' => $openai_config['user_prompt'] ?? '',
            ];

            $scores = $scoringService->scoreBatch($primaryProfile, $compareProfiles, $settings);
            if ($scoringService->wasRequestMade()) {
                $openai_requests_made = 1;
            }
            if ($scoringService->getLastError()) {
                $errors_count++;
            }

            $raw_response = $scoringService->getLastRawResponse();
            foreach ($scores as $compare_id => $score) {
                $affinity->upsertAffinityEdge($app_id, $run_id, $primary_id, $compare_id, $score, $raw_response);
            }

            $affinity->updateAffinityRun($run_id, [
                'model' => $settings['model'],
                'temperature' => $settings['temperature'],
                'prompt_hash' => sha1($scoringService->getLastPrompt()),
            ]);

            $payload = [
                "response" => true,
                "message" => __("Affinity batch processed."),
                "run_id" => (int) $run_id,
                "primary_id" => (int) $primary_id,
                "scores" => $scores,
                "openai_requests_made" => $openai_requests_made,
                "errors_count" => $errors_count,
            ];
        } catch (Exception $e) {
            $payload = [
                "response" => false,
                "message" => __($e->getMessage()),
                "openai_requests_made" => $openai_requests_made,
                "errors_count" => $errors_count ? $errors_count : 1,
            ];
        }
        $this->_sendJson($payload);
    }

    private function parseCompareIds($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                $value = explode(',', $value);
            }
        }

        if (!is_array($value)) {
            return [];
        }

        $ids = array_values(array_filter(array_map('intval', $value)));
        return array_values(array_unique($ids));
    }

    private function buildAffinityProfile(array $row)
    {
        $note = $row['note'] ?? '';
        if ($note === '' && isset($row['phone_note'])) {
            $note = $row['phone_note'];
        }

        return [
            'name' => $this->stringValue($row['name'] ?? ''),
            'surname' => $this->stringValue($row['surname'] ?? ''),
            'job' => $this->stringValue($row['job_title'] ?? ''),
            'profession' => $this->stringValue($row['profession_title'] ?? ''),
            'province' => $this->stringValue($row['province'] ?? ''),
            'country' => $this->stringValue($row['address_country_id'] ?? ''),
            'notes' => $this->stringValue($note),
            'reciprocity_notes' => $this->stringValue($row['reciprocity_notes'] ?? ''),
            'rating' => isset($row['rating']) ? (int) $row['rating'] : 0,
        ];
    }

    private function stringValue($value)
    {
        if ($value === null) {
            return '';
        }
        return trim((string) $value);
    }
}
