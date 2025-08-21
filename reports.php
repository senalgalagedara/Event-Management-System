<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

// Simple report: registrations per event
$rows = [];
$res = $conn->query("SELECT e.id, e.title, COUNT(r.id) as total FROM events e LEFT JOIN registrations r ON r.event_id=e.id GROUP BY e.id, e.title ORDER BY total DESC, e.title ASC");
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>Reports</h1></div>
    <div class="nav">
      <a class="btn" href="admin.php">Admin</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <h2>Registrations per Event</h2>
    <table>
      <tr><th>Event ID</th><th>Title</th><th>Total Registrations</th></tr>
      <?php foreach ($rows as $r): ?>
        <tr><td><?php echo (int)$r['id']; ?></td><td><?php echo htmlspecialchars($r['title']); ?></td><td><?php echo (int)$r['total']; ?></td></tr>
      <?php endforeach; ?>
      <?php if (count($rows)===0): ?><tr><td colspan="3">No data.</td></tr><?php endif; ?>
    </table>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
