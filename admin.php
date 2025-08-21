<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();
$user = current_user();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo"></div>
      <h1>Admin Panel</h1>
    </div>
    <div class="nav">
      <a class="btn" href="index.php">Home</a>
      <a class="btn" href="users.php">Manage Users</a>
      <a class="btn" href="events.php">Manage Events</a>
      <a class="btn" href="reports.php">Reports</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <h2>Administrative Tasks</h2>
    <div class="grid grid-3">
      <div class="card">
        <h3>Users</h3>
        <p>Add, edit, delete users and roles.</p>
        <a class="btn btn-primary" href="users.php">Open Users</a>
      </div>
      <div class="card">
        <h3>Events</h3>
        <p>Create events, update details, and remove outdated ones.</p>
        <a class="btn btn-primary" href="events.php">Open Events</a>
      </div>
      <div class="card">
        <h3>Reports</h3>
        <p>View registrations per event and export lists.</p>
        <a class="btn btn-primary" href="reports.php">Open Reports</a>
      </div>
    </div>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
