<?php

if (!class_exists('Migareference_Model_Db_Table_Phonebook')) {
    class Migareference_Model_Db_Table_Phonebook {}
}

if (!class_exists('Migareference_Model_OpenaiConfig')) {
    class Migareference_Model_OpenaiConfig {}
}

if (!class_exists('Migareference_Model_Db_Table_Migareference')) {
    class Migareference_Model_Db_Table_Migareference {}
}

require_once dirname(__DIR__) . '/Model/RelationshipNoteService.php';

$service = new Migareference_Model_RelationshipNoteService();

function testBuildPromptHandlesMissingFields($service)
{
    $prompt = $service->buildPrompt([
        'name' => '',
        'surname' => 'Doe',
        'email' => '',
        'mobile' => null,
    ]);

    assert(strpos($prompt, 'First Name: Unknown') !== false, 'First name fallback missing');
    assert(strpos($prompt, 'Surname: Doe') !== false, 'Surname should be present');
    assert(strpos($prompt, 'Email: Unknown') !== false, 'Email fallback missing');
    assert(strpos($prompt, 'Phone: Unknown') !== false, 'Phone fallback missing');
}

function testParseResponsePrefersJsonNote($service)
{
    $response = json_encode(['note' => 'Keep calls brief', 'used_external_research' => false]);
    $note = $service->parseNoteFromResponse($response);
    assert($note === 'Keep calls brief', 'Should parse note from JSON payload');
}

function testFilterMissingNotesDetectsEmptyNotes($service)
{
    $rows = [
        ['note' => ''],
        ['note' => '   '],
        ['note' => 'Ready'],
    ];

    $missing = $service->filterMissingNotes($rows);
    assert(count($missing) === 2, 'Two rows should be detected as missing notes');
}

testBuildPromptHandlesMissingFields($service);
testParseResponsePrefersJsonNote($service);
testFilterMissingNotesDetectsEmptyNotes($service);

echo "All relationship note tests passed\n";
