<?php
require_once 'config.php';
require_once 'auth.php';
require_login(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int)($_POST['event_id'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($event_id && $user_id) {
        $check = $conn->prepare("SELECT id FROM registrations WHERE event_id=? AND user_id=?");
        $check->bind_param("ii", $event_id, $user_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $event_id, $user_id);
            $stmt->execute();
        }
    }
}

header("Location: index.php?msg=registered");
exit;
