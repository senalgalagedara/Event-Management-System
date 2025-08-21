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
<html>

<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=logout" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Events | CEMS</title>
  <link rel="stylesheet" href="styles/events.css">
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="container">
      <div class="brand">College Event Management</div>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="logout.php"><span style="top:14px; font-size:18px; position:absolute;" class="material-symbols-outlined">
            logout
          </span></a>
      </div>
    </div>
  </nav>


  <section class="container">
    <header class="hero">
      <div class="hero-overlay">
        <h1>My Registered Events</h1>
      </div>
    </header>
    <?php if ($info): ?><div class="alert success"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>

    <div class="grid">
      <?php foreach ($rows as $r): ?>
        <div class="event-card">
          <div class="event-content">
            <h3><?php echo htmlspecialchars($r['title']); ?></h3>
            <p class="date-location"><?php echo htmlspecialchars($r['event_date']); ?> • <?php echo htmlspecialchars($r['location']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>

            <form method="post" style="margin-top:10px;">
              <input type="hidden" name="form" value="unregister" />
              <input type="hidden" name="event_id" value="<?php echo (int)$r['id']; ?>">
              <button class="btn-warning" type="submit">Unregister</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (count($rows) === 0): ?>
        <p>You haven’t registered for any events yet.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer" style="margin-top: 55vh; ">
    <p>© 2025 CEMS</p>
  </footer>

</body>

</html>