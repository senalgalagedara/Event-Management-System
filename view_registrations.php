<?php
require_once 'config.php';
require_once 'auth.php';
require_login();

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if (!$event_id) {
    header("Location: events.php");
    exit();
}

// Get event details
$stmt = $conn->prepare("SELECT title, location, event_date, description FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: events.php");
    exit();
}

// Get registered users
$stmt = $conn->prepare("SELECT u.id, u.username, u.email, r.registered_at 
                       FROM registrations r 
                       JOIN users u ON u.id = r.user_id 
                       WHERE r.event_id = ? 
                       ORDER BY r.registered_at DESC");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}

$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registered Users | College Event Management System</title>
  <link rel="stylesheet" href="styles/events.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
  <nav class="navbar">
    <div class="container">
      <div class="brand">UOC Events</div>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="events.php">Events</a>
        <a href="help.php">Help</a>
      </div>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?><a href="admin.php" class="admin-link">Admin</a><?php endif; ?>
        <span class="user">👋 Hi, <?php echo htmlspecialchars($user['username']); ?></span>
        <a href="logout.php" class="logout-btn">
          <span class="material-symbols-outlined">logout</span>
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <section class="container">
    <div class="registrations-header">
      <h1>Registered Users</h1>
      <div class="event-info">
        <h2><?php echo htmlspecialchars($event['title']); ?></h2>
        <p><span class="material-symbols-outlined">calendar_month</span> <?php echo htmlspecialchars($event['event_date']); ?></p>
        <p><span class="material-symbols-outlined">location_on</span> <?php echo htmlspecialchars($event['location']); ?></p>
      </div>
    </div>

    <div class="registrations-table">
      <div class="table-header">
        <h3>Total Registrations: <?php echo count($registrations); ?></h3>
      </div>
      
      <?php if (count($registrations) > 0): ?>
        <table class="users-table">
          <thead>
            <tr>
              <th>Username</th>
              <th>Email</th>
              <th>Registered At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($registrations as $reg): ?>
              <tr>
                <td><?php echo htmlspecialchars($reg['username']); ?></td>
                <td><?php echo htmlspecialchars($reg['email']); ?></td>
                <td><?php echo date('M d, Y H:i', strtotime($reg['registered_at'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="no-registrations">
          <p>No users have registered for this event yet.</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="back-button">
      <a href="events.php" class="btn-back">← Back to Events</a>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 College Event Management System</p>
  </footer>

</body>
</html>