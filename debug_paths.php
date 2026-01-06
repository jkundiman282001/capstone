<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

$docs = Document::latest()->take(5)->get();
echo "Total Documents: " . Document::count() . "\n\n";

foreach ($docs as $doc) {
    echo "ID: {$doc->id}\n";
    echo "Filename: {$doc->filename}\n";
    echo "Filepath: {$doc->filepath}\n";
    
    $trimmed = ltrim($doc->filepath, '/');
    echo "Public Disk Exists: " . (Storage::disk('public')->exists($doc->filepath) ? 'YES' : 'NO') . "\n";
    echo "Public Disk (Trimmed) Exists: " . (Storage::disk('public')->exists($trimmed) ? 'YES' : 'NO') . "\n";
    echo "Local Disk Exists: " . (Storage::disk('local')->exists($doc->filepath) ? 'YES' : 'NO') . "\n";
    
    $fullPath = storage_path('app/public/' . $trimmed);
    echo "Full Path: $fullPath\n";
    echo "File Exists on Disk: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    echo "-------------------\n";
}
