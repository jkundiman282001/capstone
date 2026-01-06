<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;

$documents = Document::latest()->take(10)->get();

foreach ($documents as $doc) {
    echo "ID: " . $doc->id . " | Path: " . $doc->filepath . "\n";
}
