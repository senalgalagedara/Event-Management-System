<?php
require_once 'config.php';
require_once 'auth.php';
require_login();
$user = current_user();

$info = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'unregister') {
  $event_id = (int)$_POST['event_id'];
  $stmt = $conn->prepare("DELETE FROM registrations WHERE user_id=? AND event_id=?");
  $stmt->bind_param("ii", $user['id'], $event_id);
  $stmt->execute();
  $info = "You have unregistered from the event.";
}

$rows = [];
$stmt = $conn->prepare("SELECT e.id, e.title, e.location, e.event_date, e.description
                        FROM registrations r
                        JOIN events e ON e.id = r.event_id
                        WHERE r.user_id=?
                        ORDER BY e.event_date ASC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
  while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Events | CEMS</title>
  <link rel="stylesheet" href="styles/events.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
        <span class="user">👋 Hi, <?php echo htmlspecialchars($user['username']); ?></span>
        <a href="logout.php" class="logout-btn">
          <span class="material-symbols-outlined">logout</span>
        </a>
      <?php else: ?>
        <a class="btn-login" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>
  <header class="hero">
    <div class="hero-overlay animate-fade">
      <h1>My Registered Events</h1>
      <p>Track, manage, and unregister from your upcoming events.</p>
    </div>
  </header>

  <section class="container animate-slide">
    <?php if ($info): ?>
      <div class="alert success"><?php echo htmlspecialchars($info); ?></div>
    <?php endif; ?>

    <div class="grid">
      <?php foreach ($rows as $r): ?>
        <div class="event-card animate-zoom">
          <div class="event-content">
            <h3><?php echo htmlspecialchars($r['title']); ?></h3>
            <p class="date-location">
              📅 <?php echo htmlspecialchars($r['event_date']); ?> <br> 📍 <?php echo htmlspecialchars($r['location']); ?>
            </p>
            <p><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>

            <form method="post" style="margin-top:15px;">
              <input type="hidden" name="form" value="unregister" />
              <input type="hidden" name="event_id" value="<?php echo (int)$r['id']; ?>">
              <button class="btn-warning" type="submit">Unregister</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (count($rows) === 0): ?>
        <p class="no-events animate-fade">🚫 You haven’t registered for any events yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 CEMS</p>
  </footer>

</body>
</html>
