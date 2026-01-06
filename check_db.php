<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=capstone", "root", "");
    echo "Connected successfully to XAMPP MySQL (3306)\n";
} catch (PDOException $e) {
    echo "Failed to connect to XAMPP MySQL (3306): " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=capstone", "capstone_user", "password");
    echo "Connected successfully to Docker MySQL (3307)\n";
} catch (PDOException $e) {
    echo "Failed to connect to Docker MySQL (3307): " . $e->getMessage() . "\n";
}
