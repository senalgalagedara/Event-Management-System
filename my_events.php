<?php
require_once 'config.php';
require_once 'auth.php';
$user = current_user();

$events = [];
$res = $conn->query("SELECT id, title, location, event_date, description, type FROM events ORDER BY event_date ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $events[] = $row;
  }
}

$registeredEvents = [];
if ($user) {
  $stmt = $conn->prepare("SELECT event_id FROM registrations WHERE user_id = ?");
  $stmt->bind_param("i", $user['id']);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $registeredEvents[] = $row['event_id'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Events | College Event Management System</title>
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
    <div class="hero-overlay animate-fade">
      <h1>All Events</h1>
      <p>Discover and participate in amazing campus events</p>
    </div>
  </header>

  <section class="container animate-slide" style="padding: 50px 70px;">
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
            
            <?php if ($user): ?>
              <?php if ($e['type'] === 'register'): ?>
                  <form method="post" action="register_event.php">
                    <input type="hidden" name="event_id" value="<?php echo (int)$e['id']; ?>">
                    <button type="submit" class="btn-primary">Register Now</button>
                  </form>
              <?php else: ?>
                <button class="btn-view">View Details</button>
              <?php endif; ?>
            <?php else: ?>
              <a href="login.php" class="btn-login-required">Login to Register</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (count($events) === 0): ?>
        <div class="no-events">
          <p>No events available at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 College Event Management System</p>
  </footer>

  <div id="eventModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <div id="modalContent"></div>
    </div>
  </div>

  <script>
    function showEventDetails(eventId) {
      const events = <?php echo json_encode($events); ?>;
      const event = events.find(e => e.id == eventId);
      
      if (event) {
        document.getElementById('modalContent').innerHTML = `
          <h2>${event.title}</h2>
          <p><strong>Date:</strong> ${event.event_date}</p>
          <p><strong>Location:</strong> ${event.location}</p>
          <p><strong>Description:</strong> ${event.description}</p>
        `;
        document.getElementById('eventModal').style.display = 'block';
      }
    }
    
    function closeModal() {
      document.getElementById('eventModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
      const modal = document.getElementById('eventModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    } 
  </script>

</body>
</html>
