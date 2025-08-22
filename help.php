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
<html lang="en">
<head>
  <meta charset="UTF-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=logout" />
  <title>Help | CEMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/help.css">
</head>
<body>
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
<div class="header-section">
  <div class="header-left">
    <img src="img/help.jpg" style="width: 100%;" alt="Website Banner">
  </div>
  <div class="header-right">
    <h1>Welcome to CEMS</h1>
    <p>CEMS (College Event Management System) helps students and admins manage, discover, and organize campus events efficiently. From browsing upcoming activities to registering and managing events, everything is simplified for you.</p>
  </div>
</div>

<!-- FAQ Section -->
<section class="faq">
  <h2>Frequently Asked Questions</h2>
  <div class="faq-item">
    <button class="faq-question">How do I register for an event?</button>
    <div class="faq-answer">Log in, browse the event list, and click the <strong>Register</strong> button on the event page.</div>
  </div>
  <div class="faq-item">
    <button class="faq-question">Can admins create new events?</button>
    <div class="faq-answer">Yes, admins can manage users and events from the admin dashboard.</div>
  </div>
  <div class="faq-item">
    <button class="faq-question">Where can I see my registered events?</button>
    <div class="faq-answer">All your registered events appear under <strong>My Events</strong> page after login.</div>
  </div>
</section>

<!-- Event Request Form -->
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

<script>
  // FAQ Toggle
  document.querySelectorAll(".faq-question").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("active");
      let answer = btn.nextElementSibling;
      answer.style.display = answer.style.display === "block" ? "none" : "block";
    });
  });
</script>

</body>
</html>
