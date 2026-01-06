<?php
// Simple script to check DB and files without full Laravel boot if needed
// But we need Eloquent to check the DB easily.

// Try to boot Laravel
try {
    require 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());
} catch (Exception $e) {
    echo "Laravel Boot Error: " . $e->getMessage() . "\n";
}

use App\Models\Document;
use Illuminate\Support\Facades\DB;

try {
    $count = DB::table('documents')->count();
    echo "Total Documents in DB: " . $count . "\n";

    $docs = DB::table('documents')->latest()->limit(10)->get();
    foreach ($docs as $doc) {
        echo "ID: {$doc->id} | User: {$doc->user_id} | Type: {$doc->type}\n";
        echo "  Filename: {$doc->filename}\n";
        echo "  Filepath: {$doc->filepath}\n";
        
        $fullPathPublic = storage_path('app/public/' . $doc->filepath);
        $fullPathLocal = storage_path('app/' . $doc->filepath);
        
        echo "  Checking Public: $fullPathPublic -> " . (file_exists($fullPathPublic) ? "FOUND" : "MISSING") . "\n";
        echo "  Checking Local: $fullPathLocal -> " . (file_exists($fullPathLocal) ? "FOUND" : "MISSING") . "\n";
        echo "-------------------\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
