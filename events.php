<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$info=''; $error='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['form'] ?? '') === 'create') {
        $title = trim($_POST['title']);
        $location = trim($_POST['location']);
        $event_date = $_POST['event_date'];
        $description = trim($_POST['description']);
        if ($title && $event_date) {
            $stmt = $conn->prepare("INSERT INTO events (title, location, event_date, description) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $title, $location, $event_date, $description);
            $stmt->execute();
            $info = "Event created.";
        } else { $error = "Title and date are required."; }
    }
    if (($_POST['form'] ?? '') === 'update') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $location = trim($_POST['location']);
        $event_date = $_POST['event_date'];
        $description = trim($_POST['description']);
        $stmt = $conn->prepare("UPDATE events SET title=?, location=?, event_date=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $location, $event_date, $description, $id);
        $stmt->execute();
        $info = "Event updated.";
    }
}

if (($_GET['action'] ?? '') === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $info = "Event deleted.";
}

$events = [];
$res = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
if ($res) { while ($row = $res->fetch_assoc()) { $events[] = $row; } }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Events | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>Events</h1></div>
    <div class="nav">
      <a class="btn" href="admin.php">Admin</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <h2>Create Event</h2>
    <?php if ($info): ?><div class="alert alert-ok"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-bad"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" class="grid grid-2">
      <input type="hidden" name="form" value="create" />
      <div>
        <label>Title</label>
        <input name="title" required />
      </div>
      <div>
        <label>Location</label>
        <input name="location" placeholder="Auditorium / Lab 2 / Ground" />
      </div>
      <div>
        <label>Date</label>
        <input type="date" name="event_date" required />
      </div>
      <div>
        <label>Description</label>
        <textarea name="description" rows="3" placeholder="Short summary..."></textarea>
      </div>
      <div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h2>All Events</h2>
    <table>
      <tr><th>ID</th><th>Title</th><th>Location</th><th>Date</th><th>Description</th><th>Actions</th></tr>
      <?php foreach ($events as $e): ?>
      <tr>
        <td><?php echo (int)$e['id']; ?></td>
        <td><?php echo htmlspecialchars($e['title']); ?></td>
        <td><?php echo htmlspecialchars($e['location']); ?></td>
        <td><?php echo htmlspecialchars($e['event_date']); ?></td>
        <td><?php echo nl2br(htmlspecialchars($e['description'])); ?></td>
        <td>
          <details>
            <summary class="btn">Edit</summary>
            <form method="post" class="grid grid-2" style="margin-top:10px;">
              <input type="hidden" name="form" value="update" />
              <input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>" />
              <div><label>Title</label><input name="title" value="<?php echo htmlspecialchars($e['title']); ?>" /></div>
              <div><label>Location</label><input name="location" value="<?php echo htmlspecialchars($e['location']); ?>" /></div>
              <div><label>Date</label><input type="date" name="event_date" value="<?php echo htmlspecialchars($e['event_date']); ?>" /></div>
              <div><label>Description</label><textarea name="description" rows="2"><?php echo htmlspecialchars($e['description']); ?></textarea></div>
              <div><button class="btn" type="submit">Save</button> <a class="btn btn-danger" href="?action=delete&id=<?php echo (int)$e['id']; ?>" onclick="return confirm('Delete event?')">Delete</a></div>
            </form>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
