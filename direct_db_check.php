<?php
$host = '127.0.0.1';
$db   = 'capstone';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connected to DB\n";
     
     $stmt = $pdo->query('SELECT id, user_id, type, filename, filepath FROM documents ORDER BY id DESC LIMIT 10');
     $docs = $stmt->fetchAll();
     
     echo "Found " . count($docs) . " documents\n";
     
     foreach ($docs as $doc) {
         echo "ID: {$doc['id']} | User: {$doc['user_id']} | Type: {$doc['type']}\n";
         echo "  Filepath: {$doc['filepath']}\n";
         
         $publicPath = "storage/app/public/" . ltrim($doc['filepath'], '/');
         $localPath = "storage/app/" . ltrim($doc['filepath'], '/');
         
         echo "  Checking: $publicPath -> " . (file_exists($publicPath) ? "EXISTS" : "MISSING") . "\n";
         echo "  Checking: $localPath -> " . (file_exists($localPath) ? "EXISTS" : "MISSING") . "\n";
     }
} catch (\PDOException $e) {
     echo "Connection failed: " . $e->getMessage() . "\n";
}
