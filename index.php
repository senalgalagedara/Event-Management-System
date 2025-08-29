<?php
require_once 'config.php';
require_once 'auth.php';
$user = current_user();

$events = [];
$res = $conn->query("SELECT id, title, location, event_date, description FROM events ORDER BY event_date ASC LIMIT 3");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $events[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home | College Event Management System</title>
  <link rel="stylesheet" href="styles/custom.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
  <nav class="navbar">
    <div class="container">
      <div class="brand">UOC Events</div>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="my_events.php">Events</a>
        <a href="help.php">Help</a>
      </div>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?><a href="admin.php" class="admin-link">Admin</a><?php endif; ?>
        <span class="user">Hi, <?php echo htmlspecialchars($user['username']); ?></span>
        <a href="logout.php" class="logout-btn">
          <span class="material-symbols-outlined">logout</span>
        </a>
      <?php else: ?>
        <a class="btn-login" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>
  
  <header class="hero">
    <div class="hero-overlay">
      <h1 class="animate-fade">Don't miss out!</h1>
      <p class="animate-slide">Discover and register for upcoming campus events</p>
      <a href="my_events.php" class="btn-explore">Explore All Events</a>
    </div>
  </header>
  
  <section id="events" class="events container">
    <h2>Upcoming Events</h2>
    <div class="grid">
      <?php foreach ($events as $e): ?>
        <div class="event-card animate-zoom">
          <?php 
            $imgPath = "img/event".$e['id'].".jpg";
            if (!file_exists($imgPath)) {
              $imgPath = "img/event5.jpg";
            }
          ?>
          <img src="<?php echo $imgPath; ?>" alt="Event Image">

          <div class="event-content">
            <h3><?php echo htmlspecialchars($e['title']); ?></h3>
            <p class="date-location">
              <span class="material-symbols-outlined">calendar_month</span> 
              <?php echo htmlspecialchars($e['event_date']); ?><br>
              <span class="material-symbols-outlined">location_on</span> 
              <?php echo htmlspecialchars($e['location']); ?>
            </p>
            <p><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
            
      <?php if (count($events) === 0): ?>
        <div class="no-events">
          <p>No events yet. Admins can add events from the Admin page.</p>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="btn-explore" style="width:20%; display:block; margin: 10px auto;">
      <a href="events.php" class="btn-explore">View All Events</a>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 College Event Management System </p>
  </footer>

</body>

</html>