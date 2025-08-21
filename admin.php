<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$action = $_GET['action'] ?? 'reports'; // default to reports
$info = ''; $error = '';

// ---- USER MANAGEMENT ----
if ($action === 'users' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['form'] ?? '') === 'create_user') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        if ($username && $password) {
            $hash = hash('sha256', $password);
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?,?,?)");
            $stmt->bind_param("sss", $username, $hash, $role);
            $stmt->execute();
            $info = "User created.";
        } else { $error = "Username and password required."; }
    }
    if (($_POST['form'] ?? '') === 'update_user') {
        $id = (int)$_POST['id'];
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        if (!empty($_POST['password'])) {
            $hash = hash('sha256', $_POST['password']);
            $stmt = $conn->prepare("UPDATE users SET role=?, password_hash=? WHERE id=?");
            $stmt->bind_param("ssi", $role, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
            $stmt->bind_param("si", $role, $id);
        }
        $stmt->execute();
        $info = "User updated.";
    }
}
if ($action === 'delete_user' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $info = "User deleted.";
    $action = 'users';
}

// ---- EVENT MANAGEMENT ----
if ($action === 'events' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['form'] ?? '') === 'create_event') {
        $title = trim($_POST['title']);
        $location = trim($_POST['location']);
        $event_date = trim($_POST['event_date']);
        $desc = trim($_POST['description']);
        if ($title && $event_date) {
            $stmt = $conn->prepare("INSERT INTO events (title, location, event_date, description) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $title, $location, $event_date, $desc);
            $stmt->execute();
            $info = "Event created.";
        } else { $error = "Title and date required."; }
    }
    if (($_POST['form'] ?? '') === 'update_event') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $location = trim($_POST['location']);
        $event_date = trim($_POST['event_date']);
        $desc = trim($_POST['description']);
        $stmt = $conn->prepare("UPDATE events SET title=?, location=?, event_date=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $location, $event_date, $desc, $id);
        $stmt->execute();
        $info = "Event updated.";
    }
}
if ($action === 'delete_event' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $info = "Event deleted.";
    $action = 'events';
}

// ---- LOAD DATA ----
$users = [];
if ($action === 'users') {
    $res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
    if ($res) { while ($row = $res->fetch_assoc()) { $users[] = $row; } }
}

$events = [];
if ($action === 'events') {
    $res = $conn->query("SELECT id, title, location, event_date, description FROM events ORDER BY id ASC");
    if ($res) { while ($row = $res->fetch_assoc()) { $events[] = $row; } }
}

$reports = [];
if ($action === 'reports') {
    $res = $conn->query("SELECT e.id, e.title, COUNT(r.id) as total 
                         FROM events e 
                         LEFT JOIN registrations r ON r.event_id=e.id 
                         GROUP BY e.id, e.title ORDER BY e.id ASC");
    if ($res) { while ($row = $res->fetch_assoc()) { $reports[] = $row; } }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { display:flex; margin:0; font-family:Arial; }
    .sidebar { width:220px; background:#2c3e50; color:#fff; min-height:100vh; padding:20px 10px; }
    .sidebar h2 { color:#ecf0f1; }
    .sidebar a { display:block; color:#ecf0f1; text-decoration:none; padding:10px; margin:5px 0; border-radius:5px; }
    .sidebar a.active, .sidebar a:hover { background:#34495e; }
    .main { flex:1; padding:20px; background:#ecf0f1; min-height:100vh; }
    .card { background:#fff; padding:20px; margin-bottom:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th,td { border:1px solid #ddd; padding:8px; text-align:left; }
    th { background:#f4f4f4; }
    .btn { padding:6px 12px; border-radius:4px; background:#3498db; color:#fff; text-decoration:none; border:none; cursor:pointer; }
    .btn-danger { background:#e74c3c; }
    .btn:hover { opacity:0.9; }
    .alert-ok { background:#2ecc71; color:#fff; padding:10px; border-radius:4px; margin-bottom:10px; }
    .alert-bad { background:#e74c3c; color:#fff; padding:10px; border-radius:4px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin</h2>
    <a href="?action=reports" class="<?php if($action==='reports') echo 'active'; ?>">Reports</a>
    <a href="?action=users" class="<?php if($action==='users') echo 'active'; ?>">Manage Users</a>
    <a href="?action=events" class="<?php if($action==='events') echo 'active'; ?>">Manage Events</a>
    <a href="logout.php" class="btn-danger" style="margin-top:20px; display:block;">Logout</a>
  </div>
  <div class="main">
    <?php if ($info): ?><div class="alert-ok"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-bad"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <?php if ($action === 'reports'): ?>
      <div class="card">
        <h2>Registrations per Event</h2>
        <table>
          <tr><th>Event ID</th><th>Title</th><th>Total Registrations</th></tr>
          <?php foreach ($reports as $r): ?>
            <tr><td><?php echo (int)$r['id']; ?></td><td><?php echo htmlspecialchars($r['title']); ?></td><td><?php echo (int)$r['total']; ?></td></tr>
          <?php endforeach; ?>
          <?php if (count($reports)===0): ?><tr><td colspan="3">No data.</td></tr><?php endif; ?>
        </table>
      </div>
    <?php elseif ($action === 'users'): ?>
      <div class="card">
        <h2>Create User</h2>
        <form method="post">
          <input type="hidden" name="form" value="create_user" />
          <label>Username</label><br>
          <input name="username" required><br>
          <label>Password</label><br>
          <input name="password" type="password" required><br>
          <label>Role</label><br>
          <select name="role"><option value="user">user</option><option value="admin">admin</option></select><br><br>
          <button class="btn">Create</button>
        </form>
      </div>
      <div class="card">
        <h2>All Users</h2>
        <table>
          <tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td>
              <form method="post" style="display:flex;gap:5px;">
                <input type="hidden" name="form" value="update_user" />
                <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>" />
                <select name="role">
                  <option value="user" <?php if($u['role']==='user') echo 'selected'; ?>>user</option>
                  <option value="admin" <?php if($u['role']==='admin') echo 'selected'; ?>>admin</option>
                </select>
                <input name="password" type="password" placeholder="New password (optional)">
                <button class="btn">Save</button>
              </form>
            </td>
            <td><?php echo htmlspecialchars($u['created_at']); ?></td>
            <td><a class="btn btn-danger" href="?action=delete_user&id=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete user?')">Delete</a></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php elseif ($action === 'events'): ?>
      <div class="card">
        <h2>Create Event</h2>
        <form method="post">
          <input type="hidden" name="form" value="create_event" />
          <label>Title</label><br>
          <input name="title" required><br>
          <label>Location</label><br>
          <input name="location"><br>
          <label>Date</label><br>
          <input name="event_date" type="date" required><br>
          <label>Description</label><br>
          <textarea name="description"></textarea><br><br>
          <button class="btn">Create</button>
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
            <td><?php echo htmlspecialchars($e['description']); ?></td>
            <td>
              <form method="post" style="display:inline-block;">
                <input type="hidden" name="form" value="update_event" />
                <input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>" />
                <input type="text" name="title" value="<?php echo htmlspecialchars($e['title']); ?>" required>
                <input type="text" name="location" value="<?php echo htmlspecialchars($e['location']); ?>">
                <input type="date" name="event_date" value="<?php echo htmlspecialchars($e['event_date']); ?>">
                <input type="text" name="description" value="<?php echo htmlspecialchars($e['description']); ?>">
                <button class="btn">Save</button>
              </form>
              <a class="btn btn-danger" href="?action=delete_event&id=<?php echo (int)$e['id']; ?>" onclick="return confirm('Delete event?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
