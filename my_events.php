<?php
require_once 'config.php';
require_once 'auth.php';
require_login();
$user = current_user();

$rows = [];
$stmt = $conn->prepare("SELECT e.title, e.location, e.event_date, e.description
                        FROM registrations r
                        JOIN events e ON e.id = r.event_id
                        WHERE r.user_id=?
                        ORDER BY e.event_date ASC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$res = $stmt->get_result();
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Events | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>My Events</h1></div>
    <div class="nav">
      <a class="btn" href="index.php">Home</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <h2>Registered Events</h2>
    <table>
      <tr><th>Title</th><th>Location</th><th>Date</th><th>Description</th></tr>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['title']); ?></td>
          <td><?php echo htmlspecialchars($r['location']); ?></td>
          <td><?php echo htmlspecialchars($r['event_date']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($r['description'])); ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (count($rows)===0): ?>
        <tr><td colspan="4">No registrations yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
