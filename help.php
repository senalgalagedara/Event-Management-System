<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Help | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>Help</h1></div>
    <div class="nav">
      <a class="btn" href="index.php">Home</a>
      <a class="btn" href="functionalities.php">Functionalities</a>
    </div>
  </div>

  <div class="card">
    <h2>How to Use</h2>
    <ol>
      <li>Open <strong>login.php</strong> and sign in. Default ordinary user: <strong>uoc / uoc</strong>.</li>
      <li>Admins can access <strong>admin.php</strong> to manage users and events.</li>
      <li>All users can browse events on <strong>index.php</strong> and click <em>Register</em>.</li>
      <li>See your registrations in <strong>my_events.php</strong>.</li>
      <li>Explore features in <strong>functionalities.php</strong>.</li>
    </ol>
    <h3>Security</h3>
    <p>Passwords are stored as SHA-256 hashes and verified server-side. Protected pages redirect to login if unauthorized.</p>
    <h3>Deployment</h3>
    <ol>
      <li>Import <strong>db/setup.sql</strong> to MySQL.</li>
      <li>Edit <strong>config.php</strong> with your DB credentials.</li>
      <li>Place the folder on Apache/PHP server (e.g., htdocs).</li>
    </ol>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
