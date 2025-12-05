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
     * Extract GPA from PDF file using Gemini AI
     */
    public function extractGPAFromPdf($pdfPath)
    {
        if (!file_exists($pdfPath)) {
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
                                'data' => $pdfData
                            ]
                        ],
                        [
                            'text' => 'Extract ONLY the GPA (Grade Point Average) from this student grade report PDF. Look for values labeled as "GPA", "Grade Point Average", "GWA", "General Weighted Average", or similar. Return ONLY the numeric GPA value (e.g., 3.85, 95, 1.5) as a single number. If multiple GPAs are found, return the most recent or overall GPA. If no GPA is found, return "N/A".'
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
            return $this->parseGPA($text);
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
     * Extract GPA from image file using Gemini AI
     * Supports: JPG, JPEG, PNG, GIF, WEBP
     */
    public function extractGPAFromImage($imagePath)
    {
        if (!file_exists($imagePath)) {
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

        if (!isset($allowedMimes[$mimeType])) {
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
                                'data' => $imageData
                            ]
                        ],
                        [
                            'text' => 'Extract ONLY the GPA (Grade Point Average) from this student grade report image. Look for values labeled as "GPA", "Grade Point Average", "GWA", "General Weighted Average", or similar. Return ONLY the numeric GPA value (e.g., 3.85, 95, 1.5) as a single number. If multiple GPAs are found, return the most recent or overall GPA. If no GPA is found, return "N/A".'
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
            return $this->parseGPA($text);
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
     * Extract GPA from file (automatically detects PDF or Image)
     * Returns the GPA value as a float or null if not found
     */
    public function extractGPA($filePath)
    {
        if (!file_exists($filePath)) {
            \Log::error('Grades file not found', ['path' => $filePath]);
            return null;
        }

        $mimeType = mime_content_type($filePath);

        // Check if it's a PDF
        if ($mimeType === 'application/pdf') {
            return $this->extractGPAFromPdf($filePath);
        }

        // Check if it's an image
        $imageMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($mimeType, $imageMimes)) {
            return $this->extractGPAFromImage($filePath);
        }

        \Log::error('Unsupported file type for GPA extraction', [
            'mime' => $mimeType,
            'path' => $filePath
        ]);

        return null;
    }

    /**
     * Parse GPA value from text response
     * Extracts numeric GPA value from various formats
     */
    private function parseGPA($text)
    {
        if (empty($text) || stripos($text, 'N/A') !== false || stripos($text, 'not found') !== false) {
            return null;
        }

        // Remove whitespace and convert to lowercase for easier parsing
        $text = trim($text);
        
        // Try to extract number patterns (supports formats like 3.85, 95, 1.5, etc.)
        // Look for decimal numbers (GPA format: 1.0-5.0 or percentage: 75-100)
        if (preg_match('/(\d+\.?\d*)/', $text, $matches)) {
            $value = (float) $matches[1];
            
            // If it's a percentage format (75-100), convert to GPA scale (1.0-5.0)
            if ($value >= 75 && $value <= 100) {
                // Convert percentage to GPA: (percentage - 75) / 25 * 4 + 1
                // Example: 95% = (95-75)/25*4+1 = 4.2
                $value = (($value - 75) / 25) * 4 + 1;
            }
            
            // Validate GPA range (typically 1.0 to 5.0 or 0.0 to 4.0)
            if ($value >= 0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        \Log::warning('Could not parse GPA from text', ['text' => $text]);
        return null;
    }
}
