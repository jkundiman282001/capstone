<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GradeExtractionService
{
    /**
     * Extract GPA from file (PDF or Image) without AI
     * Uses OCR for images and text extraction for PDFs
     */
    public function extractGPA($filePath)
    {
        if (!file_exists($filePath)) {
            Log::error('Grades file not found', ['path' => $filePath]);
            return null;
        }

        $mimeType = mime_content_type($filePath);
        Log::info('Starting GPA extraction', ['path' => $filePath, 'mime_type' => $mimeType]);

        // Extract text based on file type
        $text = '';
        if ($mimeType === 'application/pdf') {
            $text = $this->extractTextFromPdf($filePath);
            Log::info('PDF text extraction result', ['text_length' => strlen($text), 'preview' => substr($text, 0, 200)]);
        } elseif (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
            $text = $this->extractTextFromImage($filePath);
            Log::info('Image OCR extraction result', ['text_length' => strlen($text), 'preview' => substr($text, 0, 200)]);
        } else {
            Log::error('Unsupported file type for GPA extraction', [
                'mime' => $mimeType,
                'path' => $filePath
            ]);
            return null;
        }

        if (empty($text)) {
            Log::warning('No text extracted from document', ['path' => $filePath, 'mime_type' => $mimeType]);
            return null;
        }

        // Parse GPA from extracted text
        $gpa = $this->parseGPA($text);
        if ($gpa === null) {
            Log::warning('GPA parsing failed', [
                'text_preview' => substr($text, 0, 500),
                'text_length' => strlen($text)
            ]);
        } else {
            Log::info('GPA extracted successfully', ['gpa' => $gpa]);
        }
        
        return $gpa;
    }

    /**
     * Extract text from PDF using PHP libraries
     */
    private function extractTextFromPdf($pdfPath)
    {
        try {
            // Try using smalot/pdfparser if available
            if (class_exists('\Smalot\PdfParser\Parser')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($pdfPath);
                return $pdf->getText();
            }

            // Fallback: Try using pdftotext command if available (requires poppler-utils)
            $output = [];
            $returnVar = 0;
            @exec("pdftotext \"$pdfPath\" - 2>&1", $output, $returnVar);
            if ($returnVar === 0 && !empty($output)) {
                return implode("\n", $output);
            }

            Log::warning('PDF text extraction failed - no parser available', ['path' => $pdfPath]);
            return '';
        } catch (\Exception $e) {
            Log::error('Error extracting text from PDF', [
                'path' => $pdfPath,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Extract text from image using Tesseract OCR
     */
    private function extractTextFromImage($imagePath)
    {
        try {
            // Check if Tesseract is available
            $tesseractPath = $this->findTesseractPath();
            if (!$tesseractPath) {
                Log::warning('Tesseract OCR not found. Please install Tesseract OCR for image text extraction.', [
                    'image_path' => $imagePath
                ]);
                return '';
            }

            Log::info('Tesseract found, starting OCR', ['tesseract_path' => $tesseractPath, 'image_path' => $imagePath]);

            // Run Tesseract OCR - use temp file method (more reliable)
            $tempFile = tempnam(sys_get_temp_dir(), 'tesseract_');
            $output = [];
            $returnVar = 0;
            
            // Tesseract command: tesseract input_image output_base [options]
            // We'll output to a temp file
            $command = "\"$tesseractPath\" \"$imagePath\" \"$tempFile\" 2>&1";
            Log::info('Running Tesseract command', ['command' => $command]);
            
            @exec($command, $output, $returnVar);
            
            $outputText = implode("\n", $output);
            Log::info('Tesseract execution result', [
                'return_var' => $returnVar,
                'output' => $outputText,
                'temp_file' => $tempFile . '.txt',
                'temp_file_exists' => file_exists($tempFile . '.txt')
            ]);
            
            if ($returnVar === 0 && file_exists($tempFile . '.txt')) {
                $text = file_get_contents($tempFile . '.txt');
                @unlink($tempFile . '.txt');
                @unlink($tempFile); // Clean up base temp file too
                
                if (!empty($text)) {
                    Log::info('Tesseract OCR successful', ['text_length' => strlen($text)]);
                    return $text;
                } else {
                    Log::warning('Tesseract OCR returned empty text', ['image_path' => $imagePath]);
                }
            } else {
                Log::warning('Tesseract OCR extraction failed', [
                    'path' => $imagePath,
                    'output' => $outputText,
                    'return_var' => $returnVar,
                    'temp_file_exists' => file_exists($tempFile . '.txt')
                ]);
            }
            
            // Clean up temp file if it exists
            if (file_exists($tempFile . '.txt')) {
                @unlink($tempFile . '.txt');
            }
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            return '';
        } catch (\Exception $e) {
            Log::error('Error extracting text from image', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Find Tesseract executable path
     */
    private function findTesseractPath()
    {
        $possiblePaths = [
            'tesseract', // In PATH
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            // Windows common installation paths
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            'C:\\Tesseract-OCR\\tesseract.exe',
            // Check ProgramData (some installers use this)
            getenv('ProgramFiles') . '\\Tesseract-OCR\\tesseract.exe',
            getenv('ProgramFiles(x86)') . '\\Tesseract-OCR\\tesseract.exe',
        ];

        foreach ($possiblePaths as $path) {
            if (empty($path)) continue;
            
            $output = [];
            $returnVar = 0;
            // Use where command on Windows, which command on Unix
            if (PHP_OS_FAMILY === 'Windows') {
                @exec("\"$path\" --version 2>&1", $output, $returnVar);
            } else {
                @exec("$path --version 2>&1", $output, $returnVar);
            }
            
            if ($returnVar === 0) {
                return $path;
            }
        }

        // Last attempt: try to find via 'where' command on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            @exec('where tesseract 2>&1', $output, $returnVar);
            if ($returnVar === 0 && !empty($output[0]) && file_exists($output[0])) {
                return $output[0];
            }
        }

        return null;
    }

    /**
     * Parse GPA value from extracted text using regex patterns
     */
    private function parseGPA($text)
    {
        if (empty($text)) {
            return null;
        }

        // Keep original text for better matching (case-insensitive matching instead of lowercasing)
        $originalText = $text;
        $text = preg_replace('/\s+/', ' ', trim($text));

        // Pattern 1: Look for "GPA: 1.93" or "GPA 1.93" or "GPA=1.93" (most specific)
        // Match decimal numbers with 1-2 decimal places after GPA
        if (preg_match('/\bgpa\s*[:\s=]+\s*(\d+\.\d{1,2})\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        // Pattern 1b: Look for "GPA: 1.93" with optional spaces and case variations
        if (preg_match('/\bg\.?p\.?\s*a\.?\s*[:\s=]+\s*(\d+\.\d{1,2})\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        // Pattern 2: Look for "Grade Point Average: 1.93"
        if (preg_match('/\bgrade\s+point\s+average\s*[:\s=]+\s*(\d+\.\d{1,2})\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        // Pattern 3: Look for "GWA: 95" or "General Weighted Average: 95" (percentage format)
        if (preg_match('/\b(?:gwa|general\s+weighted\s+average)\s*[:\s=]+\s*(\d+\.?\d*)\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            return $this->normalizeGPA($value);
        }

        // Pattern 4: Look for GPA in table format (e.g., "GPA" followed by number in next column)
        // Match "GPA" followed by whitespace and then a decimal number
        if (preg_match('/\bgpa\b[^\d]*(\d+\.\d{1,2})\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        // Pattern 5: Look for GPA values in common formats (1.0-5.0 scale) that appear near GPA keyword
        // Only match if "GPA" appears within 50 characters before the number
        if (preg_match('/\bgpa\b.{0,50}?(\d+\.\d{1,2})\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                return round($value, 2);
            }
        }

        // Pattern 6: Look for percentage values (75-100) near GPA/GWA keywords
        if (preg_match('/\b(?:gpa|gwa)\b.{0,50}?\b(9[0-9]|100|8[5-9]|7[5-9])(?:\.\d+)?\b/i', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 75 && $value <= 100) {
                return $this->normalizeGPA($value);
            }
        }

        // Pattern 7: Last resort - look for decimal numbers in GPA range (1.0-5.0) but only if very specific
        // This is the least reliable, so we make it very strict
        if (preg_match('/\b([1-4]\.\d{1,2}|5\.0{1,2})\b/', $text, $matches)) {
            $value = (float) $matches[1];
            if ($value >= 1.0 && $value <= 5.0) {
                // Only return if we found "gpa" somewhere in the text (case insensitive)
                if (stripos($text, 'gpa') !== false || stripos($text, 'grade point') !== false) {
                    return round($value, 2);
                }
            }
        }

        Log::warning('Could not parse GPA from text', ['text_sample' => substr($text, 0, 500)]);
        return null;
    }

    /**
     * Normalize GPA value (handles percentage to GPA conversion)
     */
    private function normalizeGPA($value)
    {
        // If it's a percentage format (75-100), convert to GPA (1.0-5.0)
        if ($value >= 75 && $value <= 100) {
            // Convert percentage to GPA: (percentage - 75) / 25 * 4 + 1
            // Example: 95% = (95-75)/25*4+1 = 4.2
            $value = (($value - 75) / 25) * 4 + 1;
        }

        // Validate and round GPA (typically 1.0 to 5.0 or 0.0 to 4.0)
        if ($value >= 0 && $value <= 5.0) {
            return round($value, 2);
        }

        return null;
    }
}

