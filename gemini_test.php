<?php
require __DIR__ . '/vendor/autoload.php';

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

$client = new Client(env('GEMINI_API_KEY'));
try {
    $response = $client
        ->generativeModel(ModelName::GEMINI_1_5_FLASH)
        ->generateContent(new TextPart('Say hello!'));
    dd($response->text());
} catch (\Throwable $e) {
    dd($e->getMessage());
}
