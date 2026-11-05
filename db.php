<?php
mysqli_report(MYSQLI_REPORT_OFF);

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile) || !is_readable($configFile)) {
    http_response_code(500);
    exit('Application configuration is missing. Create config.php using config.example.php.');
}

$config = require $configFile;
$database = $config['database'] ?? [];
$dbHost = $database['host'] ?? '';
$dbUser = $database['username'] ?? '';
$dbPassword = $database['password'] ?? '';
$dbName = $database['name'] ?? '';

if ($dbHost === '' || $dbUser === '' || $dbName === '') {
    http_response_code(500);
    exit('Database configuration is incomplete. Check config.php.');
}

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_errno) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection failed. Check that MySQL is running in XAMPP and the database configuration is correct.');
}

$conn->set_charset('utf8mb4');
?>
