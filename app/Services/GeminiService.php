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
     * Extract GWA from PDF file using Gemini AI
     */
    public function extractGWAFromPdf($pdfPath)
    {
        if (! file_exists($pdfPath)) {
            \Log::error('Grades PDF file not found', ['path' => $pdfPath]);

            return null;
        }
        $pdfData = base64_encode(file_get_contents($pdfPath));
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => 'application/pdf',
                                'data' => $pdfData,
                            ],
                        ],
                        [
                            'text' => 'Extract ONLY the GWA (General Weighted Average) from this student grade report PDF. Look for values labeled as "GWA", "General Weighted Average", "GPA", "Grade Point Average", or similar. Return ONLY the numeric GWA value (e.g., 95, 1.5, 3.85) as a single number. If multiple GWAs are found, return the most recent or overall GWA. If no GWA is found, return "N/A". Note: We prefer the 75-100 scale.',
                        ],
                    ],
                ],
            ],
        ];
        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, $payload);
        if ($response->ok()) {
            $output = $response->json();
            $text = $output['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return $this->parseGWA($text);
        } else {
            \Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $this->endpoint,
            ]);
        }

        return null;
    }

    /**
     * Extract GWA from image file using Gemini AI
     * Supports: JPG, JPEG, PNG, GIF, WEBP
     */
    public function extractGWAFromImage($imagePath)
    {
        if (! file_exists($imagePath)) {
            \Log::error('Grades image file not found', ['path' => $imagePath]);

            return null;
        }

        // Detect MIME type
        $mimeType = mime_content_type($imagePath);
        $allowedMimes = [
            'image/jpeg' => 'image/jpeg',
            'image/jpg' => 'image/jpeg',
            'image/png' => 'image/png',
            'image/gif' => 'image/gif',
            'image/webp' => 'image/webp',
        ];

        if (! isset($allowedMimes[$mimeType])) {
            \Log::error('Unsupported image MIME type', ['mime' => $mimeType, 'path' => $imagePath]);

            return null;
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $allowedMimes[$mimeType],
                                'data' => $imageData,
                            ],
                        ],
                        [
                            'text' => 'Extract ONLY the GWA (General Weighted Average) from this student grade report image. Look for values labeled as "GWA", "General Weighted Average", "GPA", "Grade Point Average", or similar. Return ONLY the numeric GWA value (e.g., 95, 1.5, 3.85) as a single number. If multiple GWAs are found, return the most recent or overall GWA. If no GWA is found, return "N/A". Note: We prefer the 75-100 scale.',
                        ],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, $payload);

        if ($response->ok()) {
            $output = $response->json();
            $text = $output['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return $this->parseGWA($text);
        } else {
            \Log::error('Gemini API error for image', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $this->endpoint,
            ]);
        }

        return null;
    }

    /**
     * Extract GWA from file (automatically detects PDF or Image)
     * Returns the GWA value as a float or null if not found
     */
    public function extractGWA($filePath)
    {
        if (! file_exists($filePath)) {
            \Log::error('Grades file not found', ['path' => $filePath]);

            return null;
        }

        $mimeType = mime_content_type($filePath);

        // Check if it's a PDF
        if ($mimeType === 'application/pdf') {
            return $this->extractGWAFromPdf($filePath);
        }

        // Check if it's an image
        $imageMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($mimeType, $imageMimes)) {
            return $this->extractGWAFromImage($filePath);
        }

        \Log::error('Unsupported file type for GWA extraction', [
            'mime' => $mimeType,
            'path' => $filePath,
        ]);

        return null;
    }

    /**
     * Parse GWA value from text response
     * Extracts numeric GWA value from various formats
     */
    private function parseGWA($text)
    {
        if (empty($text) || stripos($text, 'N/A') !== false || stripos($text, 'not found') !== false) {
            return null;
        }

        // Remove whitespace and convert to lowercase for easier parsing
        $text = trim($text);

        // Try to extract number patterns (supports formats like 3.85, 95, 1.5, etc.)
        if (preg_match('/(\d+\.?\d*)/', $text, $matches)) {
            $value = (float) $matches[1];

            // If it's already in GWA format (75-100), just return it
            if ($value >= 75 && $value <= 100) {
                return round($value, 2);
            }

            // If it's in GPA format (1.0-5.0), convert to GWA (75-100)
            if ($value >= 1.0 && $value <= 5.0) {
                // In Philippine system, 1.0 is the BEST, 3.0 is PASSING (75%), 5.0 is FAILING.
                // We map 1.0 -> 100% and 3.0 -> 75%
                // Formula: GWA = 100 - (GPA - 1) * 12.5
                $gwa = 100 - ($value - 1) * 12.5;
                // Clamp to 0-100 range
                return round(max(0, min(100, $gwa)), 2);
            }
        }

        \Log::warning('Could not parse GWA from text', ['text' => $text]);

        return null;
    }
}
