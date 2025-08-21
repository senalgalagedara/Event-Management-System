<?php
require_once 'config.php';
require_once 'auth.php';
$user = current_user();

// Fetch upcoming events
$events = [];
$res = $conn->query("SELECT id, title, location, event_date, description FROM events ORDER BY event_date ASC LIMIT 12");
if ($res) { while ($row = $res->fetch_assoc()) { $events[] = $row; } }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home | College Event Management System</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo"></div>
      <h1>College Event Management</h1>
    </div>
    <div class="nav">
      <a class="btn" href="index.php">Home</a>
      <a class="btn" href="functionalities.php">Functionalities</a>
      <a class="btn" href="help.php">Help</a>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?><a class="btn" href="admin.php">Admin</a><?php endif; ?>
        <a class="btn" href="my_events.php">My Events</a>
        <span class="btn">Hi, <?php echo htmlspecialchars($user['username']); ?></span>
        <a class="btn btn-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-primary" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <h2><?php echo $user ? "Welcome back, " . htmlspecialchars($user['username']) : "Welcome to College Event Management"; ?></h2>
    <p class="muted"><?php echo $user ? "Here are upcoming events you can explore or manage." : "Browse upcoming events, register with the default account, or login to manage your own."; ?></p>
    <div class="grid grid-3">
      <?php foreach ($events as $e): ?>
        <div class="card">
          <h3><?php echo htmlspecialchars($e['title']); ?></h3>
          <p><small class="muted"><?php echo htmlspecialchars($e['location']); ?> • <?php echo htmlspecialchars($e['event_date']); ?></small></p>
          <p><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
          <form method="post" action="register_event.php" style="margin-top:8px;">
            <input type="hidden" name="event_id" value="<?php echo (int)$e['id']; ?>">
            <button class="btn btn-primary" type="submit">Register</button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if (count($events) === 0): ?>
        <div>No events yet. Admins can add events from the Admin page.</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
