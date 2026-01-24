<?php
// Database connection configuration
$host = 'localhost';
$db = 'securebank';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);

     // Ensure settings table exists
     $pdo->exec("CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(50) PRIMARY KEY, setting_value TEXT)");
     // Seed defaults if empty
     $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
     if ($stmt->fetchColumn() == 0) {
          $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES 
            ('bank_name', 'Trust Mora Bank'), 
            ('transfer_fee', '10.00'), 
            ('maintenance_mode', '0'), 
            ('currency_symbol', '৳')");
     }

     // AUTO-MIGRATION: Ensure nominee columns exist in customer_details
     $stmt = $pdo->query("SHOW COLUMNS FROM customer_details LIKE 'nominee_name'");
     if (!$stmt->fetch()) {
          $pdo->exec("ALTER TABLE customer_details ADD COLUMN nominee_name VARCHAR(100)");
     }
     $stmt = $pdo->query("SHOW COLUMNS FROM customer_details LIKE 'nominee_relationship'");
     if (!$stmt->fetch()) {
          $pdo->exec("ALTER TABLE customer_details ADD COLUMN nominee_relationship VARCHAR(50)");
     }
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>