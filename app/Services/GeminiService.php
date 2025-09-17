<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent';
    }

    /**
     * Send PDF to Gemini AI and extract grades text
     */
    public function extractGradesTextFromPdf($pdfPath)
    {
        if (!file_exists($pdfPath)) {
            \Log::error('Grades PDF file not found', ['path' => $pdfPath]);
            return '';
        }
        $pdfData = base64_encode(file_get_contents($pdfPath));
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => 'application/pdf',
                                'data' => $pdfData
                            ]
                        ],
                        [
                            'text' => 'Extract all readable text from this student grade report PDF.'
                        ]
                    ]
                ]
            ]
        ];
        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, $payload);
        if ($response->ok()) {
            $output = $response->json();
            $text = $output['candidates'][0]['content']['parts'][0]['text'] ?? '';
            return $text;
        } else {
            \Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $this->endpoint,
                'payload' => $payload
            ]);
        }
        return '';
    }
}
