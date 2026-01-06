<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

echo "Checking documents in database...\n";
$documents = Document::all();

if ($documents->isEmpty()) {
    echo "No documents found in database.\n";
} else {
    foreach ($documents as $doc) {
        $exists = Storage::disk('public')->exists($doc->filepath);
        $fullPath = storage_path('app/public/' . $doc->filepath);
        $realExists = file_exists($fullPath);
        
        echo "ID: {$doc->id} | User: {$doc->user_id} | Type: {$doc->type}\n";
        echo "DB Path: {$doc->filepath}\n";
        echo "Disk Check: " . ($exists ? "EXISTS" : "MISSING") . "\n";
        echo "Full Path: $fullPath\n";
        echo "Real Check: " . ($realExists ? "EXISTS" : "MISSING") . "\n";
        echo "-----------------------------------\n";
    }
}
