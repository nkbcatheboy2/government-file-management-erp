<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: movement_log.php');
    exit;
}

$file_id      = intval($_POST['file_id'] ?? 0);
$file_no      = $conn->real_escape_string($_POST['file_no'] ?? '');
$from_zone    = $conn->real_escape_string($_POST['from_zone'] ?? '');
$from_section = $conn->real_escape_string($_POST['from_section'] ?? '');
$from_officer = $conn->real_escape_string($_POST['from_officer'] ?? '');
$to_zone      = $conn->real_escape_string($_POST['to_zone'] ?? '');
$to_section   = $conn->real_escape_string($_POST['to_section'] ?? '');
$to_officer   = $conn->real_escape_string($_POST['to_officer'] ?? '');
$action       = $conn->real_escape_string($_POST['action'] ?? '');
$forwarded_by = $conn->real_escape_string($_POST['forwarded_by'] ?? '');
$acknowledged = $conn->real_escape_string($_POST['acknowledged'] ?? '');
$remarks      = $conn->real_escape_string($_POST['remarks'] ?? '');

if ($file_id === 0 || $to_zone === '' || $to_officer === '') {
    header('Location: movement_log.php?error=' . urlencode('Selecting a file and destination (To Zone/Officer) is required.'));
    exit;
}

$insert_sql = "INSERT INTO movements (file_id, file_no, from_zone, from_section, from_officer, to_zone, to_section, to_officer, action, forwarded_by, acknowledged, remarks, movement_date)
               VALUES ('$file_id', '$file_no', '$from_zone', '$from_section', '$from_officer', '$to_zone', '$to_section', '$to_officer', '$action', '$forwarded_by', '$acknowledged', '$remarks', NOW())";

if ($conn->query($insert_sql) === true) {
    $update_file_sql = "UPDATE files SET
                        current_zone = '$to_zone',
                        current_section = '$to_section',
                        current_officer = '$to_officer',
                        last_movement_date = NOW()
                        WHERE id = '$file_id'";

    if ($conn->query($update_file_sql) === true) {
        header('Location: movement_log.php?msg=moved');
        exit;
    }

    header('Location: movement_log.php?error=' . urlencode($conn->error));
    exit;
}

header('Location: movement_log.php?error=' . urlencode($conn->error));
exit;
?>
