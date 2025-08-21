<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Functionalities | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>Functionalities</h1></div>
    <div class="nav">
      <a class="btn" href="index.php">Home</a>
      <a class="btn" href="help.php">Help</a>
      <?php if (is_logged_in()): ?><a class="btn" href="my_events.php">My Events</a><?php endif; ?>
    </div>
  </div>

  <div class="card">
    <h2>Features available</h2>
    <ul>
      <li>User authentication (login/logout) with role-based access (admin, user)</li>
      <li>Admin: manage users, events, and view reports</li>
      <li>Users: view events and register</li>
      <li>Home page adapts by login/role; default for guests</li>
      <li>Help page to guide usage</li>
      <li>Unauthorized users are redirected to login when needed</li>
    </ul>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
