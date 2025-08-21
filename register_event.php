<?php
require_once 'config.php';
require_once 'auth.php';
require_login();

$event_id = (int)($_POST['event_id'] ?? 0);
$user = current_user();

if ($event_id > 0) {
    // prevent duplicate registrations
    $stmt = $conn->prepare("SELECT id FROM registrations WHERE user_id=? AND event_id=?");
    $stmt->bind_param("ii", $user['id'], $event_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?,?)");
        $stmt->bind_param("ii", $user['id'], $event_id);
        $stmt->execute();
    }
}
header("Location: my_events.php");
exit();
?>