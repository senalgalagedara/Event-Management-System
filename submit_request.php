<?php
require_once 'config.php';
require_once 'auth.php';

// Ensure user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $event_date = $_POST['event_date'];
    $venue = trim($_POST['venue']);
    $description = trim($_POST['description']);
    $message = trim($_POST['message']);

    if ($name && $title && $event_date) {
        $stmt = $conn->prepare("INSERT INTO event_requests (name, title, event_date, venue, description, message) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name, $title, $event_date, $venue, $description, $message);
        $stmt->execute();
    }
}

header("Location: help.php?msg=request_submitted");
exit();
?>
