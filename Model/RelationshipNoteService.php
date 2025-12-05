<?php

class Migareference_Model_RelationshipNoteService
{
    /** @var Migareference_Model_Db_Table_Phonebook */
    private $phonebook;

    /** @var Migareference_Model_OpenaiConfig */
    private $openaiConfig;

    /** @var Migareference_Model_Db_Table_Migareference */
    private $migareference;

    public function __construct(
        ?Migareference_Model_Db_Table_Phonebook $phonebook = null,
        ?Migareference_Model_OpenaiConfig $openaiConfig = null,
        ?Migareference_Model_Db_Table_Migareference $migareference = null
    ) {
        $this->phonebook = $phonebook ?: new Migareference_Model_Db_Table_Phonebook();
        $this->openaiConfig = $openaiConfig ?: new Migareference_Model_OpenaiConfig();
        $this->migareference = $migareference ?: new Migareference_Model_Db_Table_Migareference();
    }

    /**
     * Returns rows missing a relationship note.
     */
    public function filterMissingNotes(array $referrers)
    {
        return array_values(array_filter($referrers, function ($referrer) {
            $note = isset($referrer['note']) ? trim($referrer['note']) : '';
            return $note === '';
        }));
    }

    /**
     * Build an AI prompt for the given referrer data.
     */
    public function buildPrompt(array $referrer, ?string $promptTemplate = null)
    {
        $firstName = $this->fallbackValue($referrer, 'name');
        $surname = $this->fallbackValue($referrer, 'surname');
        $email = $this->fallbackValue($referrer, 'email');
        $phone = $this->fallbackValue($referrer, 'mobile');
        $job = $this->fallbackValue($referrer, 'job_title');
        $profession = $this->fallbackValue($referrer, 'profession_title');

        $defaultPrompt = "You are creating a concise Relationship Note (max 80 words) for a referrer. " .
            "Use only the provided details unless the online identity is an extremely certain match. " .
            "If you are not confident the online person matches, skip external research and rely on the provided data. " .
            "Summarize key relationship cues, tone, and next-steps to help strengthen rapport. " .
            "Provide your response as JSON: {\\\"note\\\": \\\"<text>\\\", \\\"used_external_research\\\": <true|false>}  " .
            "Referrer: @@referrer_name@@ @@surname@@  Email: @@email@@  Phone: @@phone@@  Profession: @@job@@  Sector: @@sector@@";

        $template = $promptTemplate && trim($promptTemplate) !== '' ? $promptTemplate : $defaultPrompt;

        $replacements = [
            '@@referrer_name@@' => $firstName,
            '@@surname@@' => $surname,
            '@@email@@' => $email,
            '@@phone@@' => $phone,
            '@@job@@' => $job,
            '@@sector@@' => $profession,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Parse a note from the AI response.
     */
    public function parseNoteFromResponse($response)
    {
        if (empty($response)) {
            return '';
        }

        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['note'])) {
            return trim($decoded['note']);
        }

        // Fallback to plain text
        if (is_string($response)) {
            return trim($response);
        }

        return '';
    }

    /**
     * Identify referrers without relationship notes and attempt to populate them via AI.
     */
    public function generateMissingNotes($appId, $batchSize = 5)
    {
        $config = $this->openaiConfig->findAll(['app_id' => $appId])->toArray();
        if (!count($config)) {
            $this->log('RelationshipNoteService', "Missing OpenAI configuration for app {$appId}");
            return [];
        }

        $config = $config[0];
        if (empty($config['openai_apikey']) && $config['gpt_api'] === 'openai') {
            $this->log('RelationshipNoteService', "Missing OpenAI API key for app {$appId}");
            return [];
        }

        $candidates = $this->phonebook->getReferrersMissingRelationshipNote($appId, $batchSize);
        $missing = $this->filterMissingNotes($candidates);
        $updated = [];

        foreach ($missing as $referrer) {
            $prompt = $this->buildPrompt($referrer, $config['relationship_note_prompt'] ?? null);
            $response = $this->callAi($config, $prompt);

            if (empty($response['raw'])) {
                $this->log('RelationshipNoteService', "Empty AI response for referrer {$referrer['migarefrence_phonebook_id']}");
                continue;
            }

            $note = $this->parseNoteFromResponse($response['raw']);
            if (empty($note)) {
                $this->log('RelationshipNoteService', "Unable to parse note for referrer {$referrer['migarefrence_phonebook_id']}");
                continue;
            }

            $note = $this->markNoteAsAiGenerated($note);

            $this->migareference->update_phonebook([
                'note' => $note,
            ], $referrer['migarefrence_phonebook_id'], 9999, 0);
            $updated[] = [
                'referrer_id' => $referrer['migarefrence_phonebook_id'],
                'used_external_research' => $response['used_external_research'],
            ];

            sleep(1); // rudimentary rate limiting
        }

        return $updated;
    }

    private function callAi(array $config, $prompt)
    {
        $apiKey = $config['openai_apikey'];
        $apiUrl = 'https://api.openai.com/v1/chat/completions';
        if (isset($config['gpt_api']) && $config['gpt_api'] === 'perplexity') {
            $apiKey = $config['perplexity_apikey'];
            $apiUrl = 'https://api.perplexity.ai/chat/completions';
        }

        $data = [
            "model" => isset($config['relationship_ai_model']) && $config['relationship_ai_model'] ? $config['relationship_ai_model'] : $config['call_script_ai_model'],
            "temperature" => (float)$config['openai_temperature'],
            "top_p" => 1,
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant that produces concise relationship notes."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ];

        $curl = curl_init();
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

        $aiResponse = curl_exec($curl);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            $this->log('RelationshipNoteService', "API error: {$curlError}");
            return ['raw' => '', 'used_external_research' => false];
        }

        $decoded = json_decode($aiResponse, true);
        $content = '';
        if (isset($decoded['choices'][0]['message']['content'])) {
            $content = $decoded['choices'][0]['message']['content'];
        }

        $usedExternalResearch = false;
        $parsed = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($parsed['used_external_research'])) {
            $usedExternalResearch = (bool)$parsed['used_external_research'];
        }

        return ['raw' => $content ?: $aiResponse, 'used_external_research' => $usedExternalResearch];
    }

    private function fallbackValue($referrer, $key)
    {
        if (isset($referrer[$key]) && trim($referrer[$key]) !== '') {
            return $referrer[$key];
        }
        return 'Unknown';
    }

    private function markNoteAsAiGenerated($note)
    {
        $tag = '[AI Generated]';

        if (stripos($note, $tag) !== false) {
            return $note;
        }

        return trim($tag . ' ' . $note);
    }

    private function log($channel, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        error_log("[{$timestamp}] {$channel}: {$message}");
    }
}
