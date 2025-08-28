<?php
require_once 'config.php';
require_once 'auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Help | CEMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/help.css">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <div class="brand">UOC Events</div>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="my_events.php">My Events</a>
        <a href="help.php">Help</a>
      </div>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?><a href="admin.php" class="admin-link">Admin</a><?php endif; ?>
        <span class="user">Hi, <?php echo htmlspecialchars($user['username']); ?></span>
        <a href="logout.php" class="logout-btn"><span class="material-symbols-outlined">logout</span></a>
      <?php else: ?>
        <a class="btn-login" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="header-banner">
    <h1>Help & Support</h1>
    <p>Find answers, request new events, and reach out to us for assistance related to University of Colombo events.</p>
  </section>
  <section class="contact">
    <h2>Contact Us</h2>
    <div class="underline"></div>
    <p>Email: uoc@stu.cmb.ac.lk</p>
    <p>Phone: +94 77 123 4567</p>
    <p>Address: 123, Kumarathunga Munidasa Mawatha, Colombo, Sri Lanka</p>
  </section>

  <section class="event-form">
    <h2>Request an Event</h2>
    <form action="submit_request.php" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="text" name="title" placeholder="Event Title" required>
      <input type="date" name="event_date" required>
      <input type="text" name="venue" placeholder="Venue">
      <textarea name="description" placeholder="Event Description"></textarea>
      <textarea name="message" placeholder="Additional Message"></textarea>
      <button type="submit" class="btn-green">Submit Request</button>
    </form>
  </section>

  <footer>
    <p>© 2025 CEMS</p>
  </footer>

  <script src="scripts/help.js"></script>
</body>
</html>
