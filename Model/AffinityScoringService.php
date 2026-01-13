<?php

class Migareference_Model_AffinityScoringService
{
    private $lastRawResponse = '';
    private $lastError = '';
    private $lastPrompt = '';
    private $requestMade = false;

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
        $prompt = $this->buildPrompt($payloadJson, $settings['prompt_template'] ?? null);
        $this->lastPrompt = $prompt;

        $apiKey = $settings['api_key'] ?? '';
        $apiUrl = $settings['api_url'] ?? '';
        if ($apiKey === '' || $apiUrl === '') {
            $this->lastError = 'Missing API credentials.';
            return [];
        }

        $data = [
            'model' => $settings['model'] ?? 'gpt-4o-mini',
            'temperature' => isset($settings['temperature']) ? (float) $settings['temperature'] : 0.2,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $settings['system_prompt'] ?? 'You are a precise scoring assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
        ];

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
        curl_close($curl);

        if ($curlError) {
            $this->lastError = $curlError;
            return [];
        }

        $decoded = json_decode($response, true);
        $content = '';
        if (isset($decoded['choices'][0]['message']['content'])) {
            $content = $decoded['choices'][0]['message']['content'];
        }

        $this->lastRawResponse = $content !== '' ? $content : $response;

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($parsed['scores']) || !is_array($parsed['scores'])) {
            $this->lastError = 'Invalid JSON response.';
            return [];
        }

        $scores = [];
        foreach ($parsed['scores'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            if (!isset($entry['compare_id'], $entry['score'])) {
                continue;
            }
            $compareId = filter_var($entry['compare_id'], FILTER_VALIDATE_INT);
            if ($compareId === false || !isset($allowedIds[(int) $compareId])) {
                continue;
            }
            $score = filter_var($entry['score'], FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 10],
            ]);
            if ($score === false) {
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

    private function buildPrompt($payloadJson, ?string $template = null)
    {
        $defaultPrompt = "Score affinity between the primary referrer and each compare referrer. " .
            "Use only the provided fields. " .
            "Rubric: 1-3 = weak fit, 4-6 = moderate fit, 7-8 = strong fit, 9-10 = exceptional fit. " .
            "Respond with JSON only in this exact shape: " .
            "{\"scores\":[{\"compare_id\":123,\"score\":7}]}";

        $template = $template && trim($template) !== '' ? $template : $defaultPrompt;

        $wrapper = "JSON ONLY. Use only provided fields. Do not invent or infer missing details.";

        $prompt = str_replace('{{payload}}', $payloadJson, $template);
        if ($prompt === $template) {
            $prompt .= "\n\nProfiles:\n" . $payloadJson;
        }

        return $prompt . "\n\n" . $wrapper;
    }

    private function normalizeProfile(array $profile)
    {
        return [
            'name' => $this->stringValue($profile['name'] ?? ''),
            'surname' => $this->stringValue($profile['surname'] ?? ''),
            'job' => $this->stringValue($profile['job'] ?? ''),
            'profession' => $this->stringValue($profile['profession'] ?? ''),
            'province' => $this->stringValue($profile['province'] ?? ''),
            'country' => $this->stringValue($profile['country'] ?? ''),
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
