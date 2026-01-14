<?php

class Migareference_Model_AffinityScoringService
{
    private $lastRawResponse = '';
    private $lastError = '';
    private $lastPrompt = '';
    private $requestMade = false;
    private $lastHttpStatus = null;
    private $lastErrorsCount = 0;

    /**
     * Score a primary profile against a batch of compare profiles.
     *
     * @param array $primaryProfile
     * @param array $compareProfiles
     * @param array $settings
     * @return array
     */
    public function scoreBatch(array $primaryProfile, array $compareProfiles, array $settings)
    {
        $this->lastRawResponse = '';
        $this->lastError = '';
        $this->lastPrompt = '';
        $this->requestMade = false;
        $this->lastHttpStatus = null;
        $this->lastErrorsCount = 0;

        if (!count($compareProfiles)) {
            return [];
        }

        $allowedIds = [];
        $comparePayload = [];
        foreach ($compareProfiles as $compareId => $profile) {
            $compareId = (int) $compareId;
            $allowedIds[$compareId] = true;
            $comparePayload[] = [
                'compare_id' => $compareId,
                'profile' => $this->normalizeProfile($profile),
            ];
        }

        $payload = [
            'primary' => $this->normalizeProfile($primaryProfile),
            'compare' => $comparePayload,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $prompt = $this->buildPrompt($payload, $payloadJson, $settings['prompt_template'] ?? null);
        $this->lastPrompt = $prompt;

        $apiKey = $settings['api_key'] ?? '';
        $apiUrl = $settings['api_url'] ?? '';
        if ($apiKey === '' || $apiUrl === '') {
            $this->lastError = 'Missing API credentials.';
            return [];
        }

        $model = $settings['model'] ?? 'gpt-4o-mini';
        $endpointType = $this->getEndpointType($apiUrl);
        if ($endpointType === null) {
            $this->lastError = 'Unsupported API endpoint. Use /v1/chat/completions or /v1/completions.';
            return [];
        }

        $isChatModel = $this->isChatModel($model);
        if ($endpointType === 'chat' && !$isChatModel) {
            $this->lastError = 'Model type mismatch: chat endpoint requires a chat model.';
            return [];
        }
        if ($endpointType === 'completions' && $isChatModel) {
            $this->lastError = 'Model type mismatch: completions endpoint requires a completions model.';
            return [];
        }

        $data = [
            'model' => $model,
            'temperature' => isset($settings['temperature']) ? (float) $settings['temperature'] : 0.2,
        ];

        if ($endpointType === 'chat') {
            $data['messages'] = [
                [
                    'role' => 'system',
                    'content' => $settings['system_prompt'] ?? 'You are a precise scoring assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ];
        } else {
            $systemPrompt = $settings['system_prompt'] ?? '';
            $combinedPrompt = trim($systemPrompt) !== '' ? ($systemPrompt . "\n\n" . $prompt) : $prompt;
            $data['prompt'] = $combinedPrompt;
        }

        if (isset($settings['max_tokens'])) {
            $data['max_tokens'] = (int) $settings['max_tokens'];
        }

        $curl = curl_init();
        $this->requestMade = true;
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $this->lastHttpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curlError) {
            $this->lastError = $curlError;
            return [];
        }

        $decoded = json_decode($response, true);
        $content = '';
        if ($endpointType === 'chat' && isset($decoded['choices'][0]['message']['content'])) {
            $content = $decoded['choices'][0]['message']['content'];
        }
        if ($endpointType === 'completions' && isset($decoded['choices'][0]['text'])) {
            $content = $decoded['choices'][0]['text'];
        }

        $this->lastRawResponse = $content !== '' ? $content : $response;

        $jsonPayload = $this->extractJsonPayload($content);
        $parsed = json_decode($jsonPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($parsed['scores']) || !is_array($parsed['scores'])) {
            $this->lastError = 'Invalid JSON response.';
            $this->lastErrorsCount += 1;
            return [];
        }

        $scores = [];
        foreach ($parsed['scores'] as $entry) {
            if (!is_array($entry)) {
                $this->lastErrorsCount += 1;
                continue;
            }
            if (!isset($entry['compare_id'], $entry['score'])) {
                $this->lastErrorsCount += 1;
                continue;
            }
            $compareId = filter_var($entry['compare_id'], FILTER_VALIDATE_INT);
            if ($compareId === false || !isset($allowedIds[(int) $compareId])) {
                $this->lastErrorsCount += 1;
                continue;
            }
            $score = filter_var($entry['score'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 10],
            ]);
            if ($score === false) {
                $this->lastErrorsCount += 1;
                continue;
            }
            $scores[(int) $compareId] = (int) $score;
        }

        return $scores;
    }

    public function getLastRawResponse()
    {
        return $this->lastRawResponse;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function getLastPrompt()
    {
        return $this->lastPrompt;
    }

    public function wasRequestMade()
    {
        return $this->requestMade;
    }

    public function getLastHttpStatus()
    {
        return $this->lastHttpStatus;
    }

    public function getLastErrorsCount()
    {
        return $this->lastErrorsCount;
    }

    private function buildPrompt(array $payload, string $payloadJson, ?string $template = null)
    {
        $defaultPrompt = "Score affinity between the primary referrer and each compare referrer. " .
            "Use only the provided fields. " .
            "Respond with JSON only in this exact shape: " .
            "{\"scores\":[{\"compare_id\":123,\"score\":7}]}";

        $template = $template && trim($template) !== '' ? $template : $defaultPrompt;

        $wrapper = "Return ONLY JSON with schema: " .
            "{\"scores\":[{\"compare_id\":<int>,\"score\":<int 1..10>}]}." .
            " Rubric: 1 conflict, 2-5 weak/adjacent, 6-8 good strategic, 9-10 highly complementary." .
            " NO markdown, NO code fences. Use only provided fields. Do not invent or infer missing details.";

        $prompt = $this->applyTemplate($template, $payload, $payloadJson);
        if ($prompt === $template) {
            $prompt .= "\n\nProfiles:\n" . $payloadJson;
        }

        return $prompt . "\n\n" . $wrapper;
    }

    private function applyTemplate(string $template, array $payload, string $payloadJson)
    {
        $primaryJson = json_encode($payload['primary'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $compareJson = json_encode($payload['compare'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $replacements = [
            '{{payload}}' => $payloadJson,
            '{{payload_json}}' => $payloadJson,
            '{{primary}}' => $primaryJson,
            '{{primary_profile}}' => $primaryJson,
            '{{compare}}' => $compareJson,
            '{{compare_profiles}}' => $compareJson,
        ];

        return strtr($template, $replacements);
    }

    private function extractJsonPayload($content)
    {
        if (!is_string($content) || trim($content) === '') {
            return '';
        }

        $trimmed = trim($content);
        $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed);
        $trimmed = preg_replace('/\s*```$/', '', $trimmed);

        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');
        if ($start === false || $end === false || $end <= $start) {
            return $trimmed;
        }

        return substr($trimmed, $start, $end - $start + 1);
    }

    private function getEndpointType($apiUrl)
    {
        if (strpos($apiUrl, '/v1/chat/completions') !== false) {
            return 'chat';
        }
        if (strpos($apiUrl, '/v1/completions') !== false) {
            return 'completions';
        }
        return null;
    }

    private function isChatModel($model)
    {
        $model = strtolower((string) $model);
        $completionHints = ['instruct', 'davinci', 'curie', 'babbage', 'ada', 'text-'];
        foreach ($completionHints as $hint) {
            if (strpos($model, $hint) !== false) {
                return false;
            }
        }
        return true;
    }

    private function normalizeProfile(array $profile)
    {
        $province = $this->stringValue($profile['province'] ?? '');
        $country = $this->stringValue($profile['country'] ?? '');
        // Guard against numeric IDs leaking into the model prompt.
        if ($province !== '' && is_numeric($province)) {
            $province = '';
        }
        if ($country !== '' && is_numeric($country)) {
            $country = '';
        }

        return [
            'name' => $this->stringValue($profile['name'] ?? ''),
            'surname' => $this->stringValue($profile['surname'] ?? ''),
            'job' => $this->stringValue($profile['job'] ?? ''),
            'profession' => $this->stringValue($profile['profession'] ?? ''),
            'province' => $province,
            'country' => $country,
            'notes' => $this->stringValue($profile['notes'] ?? ''),
            'reciprocity_notes' => $this->stringValue($profile['reciprocity_notes'] ?? ''),
            'rating' => isset($profile['rating']) ? (int) $profile['rating'] : 0,
        ];
    }

    private function stringValue($value)
    {
        if ($value === null) {
            return '';
        }
        $value = trim((string) $value);
        return $value;
    }
}
