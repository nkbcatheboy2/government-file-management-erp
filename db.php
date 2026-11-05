<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli('localhost', 'root', '', 'file_tracker_erp');

if ($conn->connect_errno) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection failed. Check that MySQL is running in XAMPP and the database configuration is correct.');
}

$conn->set_charset('utf8mb4');
?>
