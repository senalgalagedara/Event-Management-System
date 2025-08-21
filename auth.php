<?php
// auth.php - session and access control helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ];
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php?msg=Please+login");
        exit();
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: index.php?error=Access+denied");
        exit();
    }
}
?>