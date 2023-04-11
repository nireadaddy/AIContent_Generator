<?php

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

$apiKey = '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';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = $_POST['keyword'];
    $template = $_POST['template'];

    $generatedContent = generateContent($apiKey, $keyword, $template);
    echo $generatedContent;
}

function generateContent($apiKey, $keyword, $template) {
    $client = new Client([
        'base_uri' => 'https://api.openai.com',
        'headers' => [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ],
    ]);

    $headlinePrompt = "Write a news headline about " . $keyword;
    $summaryPrompt = "Write a brief summary about " . $keyword;

    $prompts = [$headlinePrompt, $summaryPrompt];

    $responses = [];
    foreach ($prompts as $prompt) {
        $response = $client->post('/v4/engines/gpt-3.5-turbo/completions', [
            'json' => [
                'prompt' => $prompt,
                'max_tokens' => 50,
                'n' => 1,
                'stop' => null,
                'temperature' => 0.7,
            ],
        ]);

        $responseJson = json_decode($response->getBody(), true);
        $responses[] = $responseJson['choices'][0]['text'];
    }

    $content = str_replace(['{headline}', '{summary}', '{keyword}'], [$responses[0], $responses[1], $keyword], $template);

    return $content;
}
