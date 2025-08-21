<?php
require_once 'config.php';
require_once 'auth.php';
$user = current_user();

$events = [];
$res = $conn->query("SELECT id, title, location, event_date, description FROM events ORDER BY event_date ASC LIMIT 12");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $events[] = $row;
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=logout" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home | College Event Management System</title>
  <link rel="stylesheet" href="styles/custom.css">
</head>

<body style="  background: #fff;
">

  <nav class="navbar">
    <div class="container">
      <div class="brand">College Event Management</div>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="my_events.php">My Events</a>
        <a href="help.php">Help</a>
      </div>
      <?php if ($user): ?>
          <?php if ($user['role'] === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
          <span class="user">Hi, <?php echo htmlspecialchars($user['username']); ?></span>
          <a href="logout.php"><span class="material-symbols-outlined">
              logout
            </span></a>
        <?php else: ?>
          <a class="btn-login" href="login.php">Login</a>
        <?php endif; ?>
    </div>
  </nav>

  <header class="hero">
    <div class="hero-overlay">
      <h1>Don’t miss out!</h1>
      <p>Explore upcoming events happening.</p>
    </div>
  </header>

  <section class="events container">
    <h2>Upcoming Events</h2>
    <div class="grid">
      <?php foreach ($events as $e): ?>
        <div class="event-card">
          <div class="event-content">
            <h3><?php echo htmlspecialchars($e['title']); ?></h3>
            <p class="date-location"><?php echo htmlspecialchars($e['event_date']); ?> • <?php echo htmlspecialchars($e['location']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
            <form method="post" action="register_event.php">
              <input type="hidden" name="event_id" value="<?php echo (int)$e['id']; ?>">
              <button type="submit" class="btn-primary">Register</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (count($events) === 0): ?>
        <p>No events yet. Admins can add events from the Admin page.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <p>© 2025 CEMS</p>
  </footer>

</body>

</html>