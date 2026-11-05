<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid form token. Please reload the page and try again.');
    }

    $file_no = $conn->real_escape_string($_POST['file_no']);
    $file_type = $conn->real_escape_string($_POST['file_type']);
    $file_subject = $conn->real_escape_string($_POST['file_subject']);
    $origin_zone = $conn->real_escape_string($_POST['origin_zone']);
    $origin_section = $conn->real_escape_string($_POST['origin_section']);
    $current_zone = $conn->real_escape_string($_POST['current_zone']);
    $current_section = $conn->real_escape_string($_POST['current_section']);
    $current_officer = $conn->real_escape_string($_POST['current_officer']);
    $received_date = $conn->real_escape_string($_POST['received_date']);
    $priority = $conn->real_escape_string($_POST['priority']);
    $current_status = $conn->real_escape_string($_POST['current_status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $sql = "INSERT INTO files (file_no, file_type, file_subject, origin_zone, origin_section, current_zone, current_section, current_officer, received_date, priority, current_status, remarks) 
            VALUES ('$file_no', '$file_type', '$file_subject', '$origin_zone', '$origin_section', '$current_zone', '$current_section', '$current_officer', '$received_date', '$priority', '$current_status', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        header("Location: file_register.php?msg=added");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
