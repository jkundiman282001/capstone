<?php
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

ob_start();

try {
    require 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());
    
    $docs = DB::table('documents')->orderBy('id', 'desc')->limit(20)->get();
    echo "Total Docs in DB: " . DB::table('documents')->count() . "\n\n";

    foreach ($docs as $doc) {
        echo "ID: {$doc->id} | User: {$doc->user_id}\n";
        echo "Filepath: {$doc->filepath}\n";
        
        $trimmed = ltrim($doc->filepath, '/');
        $publicDiskExists = Storage::disk('public')->exists($doc->filepath) ? 'YES' : 'NO';
        $publicTrimmedExists = Storage::disk('public')->exists($trimmed) ? 'YES' : 'NO';
        
        echo "Public Disk Exists: $publicDiskExists\n";
        echo "Public Disk Trimmed Exists: $publicTrimmedExists\n";
        
        $fullPath = storage_path('app/public/' . $trimmed);
        echo "Physical Path: $fullPath\n";
        echo "Physical File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "-------------------\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

$output = ob_get_clean();
file_put_contents('debug_output.txt', $output);
echo "Debug finished. Check debug_output.txt\n";
