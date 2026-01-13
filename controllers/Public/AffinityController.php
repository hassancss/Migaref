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

            $existing_run = $affinity->getLatestRunningRun($app_id);
            if ($existing_run) {
                $payload = [
                    "response" => true,
                    "message" => __("Run already in progress."),
                    "run_id" => (int) $existing_run['id'],
                    "status" => $existing_run['status'],
                ];
                $this->_sendJson($payload);
                return;
            }

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

            $run = $affinity->getAffinityRun($run_id);
            if (!$run || (int) $run['app_id'] !== $app_id) {
                throw new Exception(__("Run not found."));
            }

            $pair_data = $affinity->getNextPairs($app_id, $run_id, $batch_pairs);
            if ($pair_data === null) {
                throw new Exception(__("Run not found."));
            }

            $payload = [
                "response" => true,
                "message" => __("Next pairs generated."),
                "run_id" => (int) $run_id,
                "batch_pairs" => $pair_data['batch_pairs'],
                "pairs" => $pair_data['pairs'],
                "cursor_i" => $pair_data['cursor_i'],
                "cursor_j" => $pair_data['cursor_j'],
                "processed_pairs" => $pair_data['processed_pairs'],
                "total_pairs_estimate" => $pair_data['total_pairs_estimate'],
                "status" => $pair_data['status'],
            ];
        } catch (Exception $e) {
            $payload = [
                "response" => false,
                "message" => __($e->getMessage()),
            ];
        }
        $this->_sendJson($payload);
    }

    public function cronrunnerAction() {
        $openai_requests_made = 0;
        $errors_count = 0;
        $errors = [];
        $last_error = '';
        $http_status = null;
        $provider = null;
        $raw_provider_response_sample = null;
        $inserted_edges_count = 0;
        $last_insert_pair = null;
        $lock_token = null;
        $run_id = 0;

        try {
            $reportapi = new Migareference_Model_Reportapi();
            $affinity = new Migareference_Model_Affinity();
            $request = $this->getRequest();
            $data = $request->getPost();
            if (!count($data)) {
                $data = $request->getParams();
            }

            $token = isset($data['token']) ? trim($data['token']) : '';
            if (empty($token) || strlen($token) != 35) {
                throw new Exception(__("Token Mismatchd"));
            }
            $pre_report_settings = $reportapi->validateToken($token);
            if (!count($pre_report_settings)) {
                throw new Exception(__("Token Mismatched"));
            }
            $app_id = (int) $pre_report_settings[0]['app_id'];

            $max_pairs = isset($data['max_pairs']) ? (int) $data['max_pairs'] : 200;
            if ($max_pairs <= 0) {
                $max_pairs = 200;
            }
            if ($max_pairs > 200) {
                $max_pairs = 200;
            }

            $existing_run = $affinity->getLatestRunningRun($app_id);
            if ($existing_run) {
                $run_id = (int) $existing_run['id'];
            } else {
                $eligible_ids = $affinity->getEligibleReferrerIds($app_id);
                $total_referrers = count($eligible_ids);
                $total_pairs_estimate = ($total_referrers * ($total_referrers - 1)) / 2;

                $status = 'running';
                $cursor_i = 0;
                $cursor_j = 1;
                if ($total_referrers < 2) {
                    $status = 'completed';
                    $cursor_i = 0;
                    $cursor_j = 0;
                }

                $run_id = (int) $affinity->createRun($app_id, [
                    'status' => $status,
                    'total_referrers' => $total_referrers,
                    'total_pairs_estimate' => $total_pairs_estimate,
                    'cursor_i' => $cursor_i,
                    'cursor_j' => $cursor_j,
                ]);
            }

            $lock_token = bin2hex(random_bytes(16));
            $lock_acquired = $affinity->acquireRunLock($run_id, $lock_token, 10);
            if (!$lock_acquired) {
                $payload = [
                    "response" => false,
                    "message" => __("Job already running by another worker."),
                    "run_id" => $run_id,
                ];
                $this->_sendJson($payload);
                return;
            }

            $pair_data = $affinity->getNextPairs($app_id, $run_id, $max_pairs);
            if ($pair_data === null) {
                throw new Exception(__("Run not found."));
            }

            if (!count($pair_data['pairs'])) {
                $run = $affinity->getAffinityRun($run_id);
                $payload = [
                    "response" => true,
                    "message" => __("Run completed."),
                    "run_id" => $run_id,
                    "status" => $run ? $run['status'] : 'completed',
                    "batch_pairs" => $pair_data['batch_pairs'],
                    "processed_pairs_total" => $run ? (int) $run['processed_pairs'] : 0,
                    "cursor_i" => $run ? (int) $run['cursor_i'] : 0,
                    "cursor_j" => $run ? (int) $run['cursor_j'] : 0,
                    "total_pairs_estimate" => $run ? (int) $run['total_pairs_estimate'] : 0,
                ];
                $this->_sendJson($payload);
                return;
            }

            $groups = [];
            foreach ($pair_data['pairs'] as $pair) {
                $a = (int) $pair['a'];
                $b = (int) $pair['b'];
                if (!isset($groups[$a])) {
                    $groups[$a] = [];
                }
                $groups[$a][] = $b;
            }

            foreach ($groups as $primary_id => $compare_ids) {
                $result = $this->processPairGroup(
                    $app_id,
                    $run_id,
                    (int) $primary_id,
                    $compare_ids,
                    $openai_requests_made,
                    $errors_count
                );
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
                if (isset($result['last_error']) && $result['last_error'] !== '') {
                    $last_error = $result['last_error'];
                }
                if (array_key_exists('http_status', $result) && $result['http_status'] !== null) {
                    $http_status = $result['http_status'];
                }
                if (!empty($result['provider'])) {
                    $provider = $result['provider'];
                }
                if (!empty($result['raw_response_sample'])) {
                    $raw_provider_response_sample = $result['raw_response_sample'];
                }
                if (isset($result['inserted_edges_count'])) {
                    $inserted_edges_count += (int) $result['inserted_edges_count'];
                }
                if (!empty($result['last_insert_pair'])) {
                    $last_insert_pair = $result['last_insert_pair'];
                }
            }

            $run = $affinity->getAffinityRun($run_id);
            $payload = [
                "response" => true,
                "message" => __("Affinity batch processed."),
                "run_id" => $run_id,
                "status" => $run ? $run['status'] : 'running',
                "batch_pairs" => $pair_data['batch_pairs'],
                "processed_pairs_total" => $run ? (int) $run['processed_pairs'] : 0,
                "cursor_i" => $run ? (int) $run['cursor_i'] : 0,
                "cursor_j" => $run ? (int) $run['cursor_j'] : 0,
                "total_pairs_estimate" => $run ? (int) $run['total_pairs_estimate'] : 0,
                "openai_requests_made" => $openai_requests_made,
                "inserted_edges_count" => $inserted_edges_count,
                "last_insert_pair" => $last_insert_pair,
                "errors" => array_slice($errors, 0, 10),
                "errors_count" => $errors_count,
                "last_error" => $last_error,
                "http_status" => $http_status,
                "provider" => $provider,
                "raw_provider_response_sample" => $raw_provider_response_sample,
            ];
        } catch (Exception $e) {
            $payload = [
                "response" => false,
                "message" => __($e->getMessage()),
                "openai_requests_made" => $openai_requests_made,
                "inserted_edges_count" => $inserted_edges_count,
                "last_insert_pair" => $last_insert_pair,
                "errors" => array_slice($errors, 0, 10),
                "errors_count" => $errors_count ? $errors_count : 1,
                "last_error" => $last_error,
                "http_status" => $http_status,
                "provider" => $provider,
                "raw_provider_response_sample" => $raw_provider_response_sample,
            ];
        } finally {
            if ($run_id && $lock_token) {
                $affinity = new Migareference_Model_Affinity();
                $affinity->releaseRunLock($run_id, $lock_token);
            }
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

            $result = $this->processPairGroup(
                $app_id,
                $run_id,
                $primary_id,
                $compare_ids,
                $openai_requests_made,
                $errors_count,
                $data
            );

            $payload = [
                "response" => true,
                "message" => __("Affinity batch processed."),
                "run_id" => (int) $run_id,
                "primary_id" => (int) $primary_id,
                "scores" => $result['scores'],
                "openai_requests_made" => $openai_requests_made,
                "inserted_edges_count" => $result['inserted_edges_count'],
                "last_insert_pair" => $result['last_insert_pair'],
                "errors" => array_slice($result['errors'], 0, 10),
                "errors_count" => $errors_count,
                "last_error" => $result['last_error'],
                "http_status" => $result['http_status'],
                "provider" => $result['provider'],
                "raw_provider_response_sample" => $result['raw_response_sample'],
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

    private function processPairGroup(
        $app_id,
        $run_id,
        $primary_id,
        array $compare_ids,
        &$openai_requests_made,
        &$errors_count,
        array $overrides = []
    ) {
        $affinity = new Migareference_Model_Affinity();
        $openaiConfig = new Migareference_Model_OpenaiConfig();
        $scoringService = new Migareference_Model_AffinityScoringService();
        $errors = [];
        $inserted_edges_count = 0;
        $last_insert_pair = null;

        $profile_rows = $affinity->getReferrerProfiles($app_id, array_merge([$primary_id], $compare_ids));
        if (!isset($profile_rows[$primary_id])) {
            throw new Exception(__("Primary referrer not found."));
        }

        $primaryProfile = $this->buildAffinityProfile($profile_rows[$primary_id]);
        $compareProfiles = [];
        foreach ($compare_ids as $compare_id) {
            if (!isset($profile_rows[$compare_id])) {
                $errors[] = [
                    'type' => 'profile_missing',
                    'primary_id' => (int) $primary_id,
                    'compare_id' => (int) $compare_id,
                    'reason' => 'Compare referrer profile not found.',
                ];
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

        $provider = 'openai';
        $api_key = $openai_config['openai_apikey'];
        $api_url = 'https://api.openai.com/v1/chat/completions';
        if ($openai_config['gpt_api'] == 'perplexity') {
            $api_url = 'https://api.perplexity.ai/chat/completions';
            $api_key = $openai_config['perplexity_apikey'];
            $provider = 'perplexity';
        }
        if (empty($api_key)) {
            throw new Exception(__("Missing OpenAI API key."));
        }

        $settings = [
            'model' => isset($overrides['model']) && trim($overrides['model']) !== ''
                ? trim($overrides['model'])
                : ($openai_config['matching_ai_model'] ?: $openai_config['call_script_ai_model']),
            'temperature' => isset($overrides['temperature'])
                ? (float) $overrides['temperature']
                : (float) $openai_config['openai_temperature'],
            'max_tokens' => isset($overrides['max_tokens'])
                ? (int) $overrides['max_tokens']
                : (int) $openai_config['openai_token'],
            'api_key' => $api_key,
            'api_url' => $api_url,
            'system_prompt' => $openai_config['system_prompt'] ?? '',
            'prompt_template' => $openai_config['user_prompt'] ?? '',
        ];

        $scores = $scoringService->scoreBatch($primaryProfile, $compareProfiles, $settings);
        if ($scoringService->wasRequestMade()) {
            $openai_requests_made += 1;
        }
        $last_error = $scoringService->getLastError();
        $raw_response = $scoringService->getLastRawResponse();
        $http_status = $scoringService->getLastHttpStatus();
        if ($last_error !== '') {
            $errors[] = [
                'type' => 'scoring',
                'primary_id' => (int) $primary_id,
                'reason' => $last_error,
                'last_error' => $last_error,
                'http_status' => $http_status,
                'provider' => $provider,
            ];
        }
        if (!count($scores)) {
            if ($last_error === '') {
                $last_error = 'No scores returned.';
            }
            $errors[] = [
                'type' => 'scoring',
                'primary_id' => (int) $primary_id,
                'reason' => 'No scores returned.',
                'last_error' => $last_error,
                'http_status' => $http_status,
                'provider' => $provider,
            ];
        }

        foreach ($compareProfiles as $compare_id => $profile) {
            if (!array_key_exists($compare_id, $scores)) {
                $errors[] = [
                    'type' => 'score_missing',
                    'primary_id' => (int) $primary_id,
                    'compare_id' => (int) $compare_id,
                    'reason' => 'Missing score for compare_id.',
                ];
            }
        }

        foreach ($scores as $compare_id => $score) {
            $score = (int) $score;
            if ($score < 1 || $score > 10) {
                $errors[] = [
                    'type' => 'score_invalid',
                    'primary_id' => (int) $primary_id,
                    'compare_id' => (int) $compare_id,
                    'score' => $score,
                    'reason' => 'Score must be between 1 and 10.',
                ];
                continue;
            }
            try {
                $affinity->upsertAffinityEdge($app_id, $run_id, $primary_id, $compare_id, $score, $raw_response);
                $inserted_edges_count++;
                $last_insert_pair = [
                    'a' => (int) $primary_id,
                    'b' => (int) $compare_id,
                ];
            } catch (Exception $e) {
                $errors[] = [
                    'type' => 'insert_failed',
                    'a' => (int) $primary_id,
                    'b' => (int) $compare_id,
                    'reason' => 'Failed to upsert affinity edge.',
                    'sql_error' => $e->getMessage(),
                ];
            }
        }

        $affinity->updateAffinityRun($run_id, [
            'model' => $settings['model'],
            'temperature' => $settings['temperature'],
            'prompt_hash' => sha1($scoringService->getLastPrompt()),
        ]);

        $errors_count += count($errors);

        return [
            'scores' => $scores,
            'errors' => $errors,
            'last_error' => $last_error,
            'http_status' => $http_status,
            'provider' => $provider,
            'raw_response' => $raw_response,
            'raw_response_sample' => $raw_response !== '' ? substr($raw_response, 0, 500) : null,
            'inserted_edges_count' => $inserted_edges_count,
            'last_insert_pair' => $last_insert_pair,
        ];
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
