<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$action = $_GET['action'] ?? 'reports';

if ($action === 'users' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['form'] === 'create_user') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    if ($username && $password) {
      $hash = hash('sha256', $password);
      $stmt = $conn->prepare("INSERT INTO users (username,password_hash,role) VALUES (?,?,?)");
      $stmt->bind_param("sss", $username, $hash, $role);
      $stmt->execute();
    }
  }
  if ($_POST['form'] === 'update_user') {
    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    if (!empty($_POST['password'])) {
      $hash = hash('sha256', $_POST['password']);
      $stmt = $conn->prepare("UPDATE users SET username=?,role=?,password_hash=? WHERE id=?");
      $stmt->bind_param("sssi", $username, $role, $hash, $id);
    } else {
      $stmt = $conn->prepare("UPDATE users SET username=?,role=? WHERE id=?");
      $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();
  }
}
if ($action === "delete_user" && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $conn->query("DELETE FROM users WHERE id=$id");
  $action = "users";
}

if ($action === 'events' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['form'] === 'create_event') {
    $stmt = $conn->prepare("INSERT INTO events(title,location,event_date,description) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $_POST['title'], $_POST['location'], $_POST['event_date'], $_POST['description']);
    $stmt->execute();
  }
  if ($_POST['form'] === 'update_event') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("UPDATE events SET title=?,location=?,event_date=?,description=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['title'], $_POST['location'], $_POST['event_date'], $_POST['description'], $id);
    $stmt->execute();
  }
}
if ($action === "delete_event" && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $conn->query("DELETE FROM events WHERE id=$id");
  $action = "events";
}
if ($action === "delete_request" && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $conn->query("DELETE FROM event_requests WHERE id=$id");
  $action = "requested_events";
}

$users = [];
$events = [];
$reports = [];
$requested_events = [];

if ($action === "users") {
  $res = $conn->query("SELECT * FROM users ORDER BY id ASC");
  while ($row = $res->fetch_assoc()) $users[] = $row;
}
if ($action === "events") {
  $res = $conn->query("SELECT * FROM events ORDER BY id ASC");
  while ($row = $res->fetch_assoc()) $events[] = $row;
}
if ($action === "reports") {
  $res = $conn->query("SELECT e.id, e.title, COUNT(r.id) as total 
                        FROM events e 
                        LEFT JOIN registrations r ON r.event_id=e.id 
                        GROUP BY e.id, e.title ORDER BY e.id ASC");
  while ($row = $res->fetch_assoc()) $reports[] = $row;
}
if ($action === "requested_events") {
  $res = $conn->query("SELECT * FROM event_requests ORDER BY id ASC");
  while ($row = $res->fetch_assoc()) $requested_events[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="styles/admin.css">
  <title>Admin</title>
</head>
<body>
  <div class="admin-container">
    <div class="sidebar">
      <div style="display: flex; ">
        <h2 style="color:white;">Admin</h2>
        <a href="logout.php" class="logout" style="margin-top: -10px;">
          <span class="material-symbols-outlined">logout</span>
        </a>
      </div>

      <a class="hov <?= ($action === 'reports' ? 'active' : '') ?>" href="?action=reports">Reports</a>
      <a class="hov <?= ($action === 'users' ? 'active' : '') ?>" href="?action=users">Manage Users</a>
      <a class="hov <?= ($action === 'events' ? 'active' : '') ?>" href="?action=events">Manage Events</a>
      <a class="hov <?= ($action === 'requested_events' ? 'active' : '') ?>" href="?action=requested_events">Requested Events</a>
    </div>

    <div class="content">
      <?php if ($action === "reports"): ?>
        <h1>Registrations per Event</h1>
        <table>
          <tr>
            <th>ID</th>
            <th>Event</th>
            <th>Total Registrations</th>
          </tr>
          <?php foreach ($reports as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><?= htmlspecialchars($r['title']) ?></td>
              <td><?= $r['total'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (count($reports) === 0): ?><tr><td colspan="3">No data</td></tr><?php endif; ?>
        </table>

      <?php elseif ($action === "users"): ?>
        <h1>Manage Users</h1>
        <table>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['username']) ?></td>
              <td><?= $u['role'] ?></td>
              <td><?= $u['created_at'] ?></td>
              <td style="display: flex;">
                <button style="width: 40%; background-color:green; color:white; cursor:pointer;" onclick="document.getElementById('editUser<?= $u['id'] ?>').style.display='flex'"><span class="material-symbols-outlined">edit</span></button>
                <a style="width: 40%; background-color:red; color:white; margin:8px auto; text-align:center; border-radius: 4px;" href="?action=delete_user&id=<?= $u['id'] ?>" onclick="return confirm('Delete?')"><span class="material-symbols-outlined" style="margin-top: 10px;">delete</span></a>
              </td>
            </tr>

            <div id="editUser<?= $u['id'] ?>" class="modal">
              <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                <h3>Edit User</h3>
                <form method="post">
                  <input type="hidden" name="form" value="update_user">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <label>Username</label>
                  <input name="username" value="<?= htmlspecialchars($u['username']) ?>">
                  <label>Password (leave blank to keep)</label>
                  <input type="password" name="password" placeholder="New password">
                  <label>Role</label>
                  <select name="role">
                    <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                  <button class="updte" type="submit">Update</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </table>

        <button class="addinput" onclick="document.getElementById('addUser').style.display='flex'">Add User</button>
        <div id="addUser" class="modal">
          <div class="modal-content">
            <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
            <h3>Add User</h3>
            <form method="post">
              <input type="hidden" name="form" value="create_user">
              <label>Username</label><input name="username" required>
              <label>Password</label><input type="password" name="password" required>
              <label>Role</label>
              <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
              <button class="updte" type="submit">Create</button>
            </form>
          </div>
        </div>

      <?php elseif ($action === "events"): ?>
        <h1>Manage Events</h1>
        <table>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Location</th>
            <th>Date</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($events as $e): ?>
            <tr>
              <td style="width: 40px;"><?= $e['id'] ?></td>
              <td style="width: 200px;"><?= htmlspecialchars($e['title']) ?></td>
              <td style="width: 150px;"><?= htmlspecialchars($e['location']) ?></td>
              <td style="width: 100px;"><?= $e['event_date'] ?></td>
              <td style="width: 290px;"><?= htmlspecialchars($e['description']) ?></td>
              <td style="display: flex;">
                <button style="width: 40%; background-color:green; color:white; cursor:pointer;" onclick="document.getElementById('editEvent<?= $e['id'] ?>').style.display='flex'"><span class="material-symbols-outlined">edit</span></button>
                <a style="width: 40%; background-color:red; color:white; margin:8px auto; text-align:center;border-radius: 4px;" href="?action=delete_event&id=<?= $e['id'] ?>" onclick="return confirm('Delete?')">
                  <span style="margin-top: 10px;" class="material-symbols-outlined">delete</span></a>
              </td>
            </tr>

            <div id="editEvent<?= $e['id'] ?>" class="modal">
              <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                <h3>Edit Event</h3>
                <form method="post">
                  <input type="hidden" name="form" value="update_event">
                  <input type="hidden" name="id" value="<?= $e['id'] ?>">
                  <label>Title</label>
                  <input name="title" value="<?= htmlspecialchars($e['title']) ?>">
                  <label>Location</label>
                  <input name="location" value="<?= htmlspecialchars($e['location']) ?>">
                  <label>Date</label>
                  <input type="date" name="event_date" value="<?= $e['event_date'] ?>">
                  <label>Description</label>
                  <textarea name="description"><?= htmlspecialchars($e['description']) ?></textarea>
                  <button class="updte" type="submit">Update</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </table>

        <button class="addinput" onclick="document.getElementById('addEvent').style.display='flex'">Add Event</button>
        <div id="addEvent" class="modal">
          <div class="modal-content">
            <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
            <h3>Add Event</h3>
            <form method="post">
              <input type="hidden" name="form" value="create_event">
              <label>Title</label><input name="title" required>
              <label>Location</label><input name="location">
              <label>Date</label><input type="date" name="event_date" required>
              <label>Description</label><textarea name="description"></textarea>
              <button class="updte" type="submit">Create</button>
            </form>
          </div>
        </div>

      <?php elseif ($action === "requested_events"): ?>
        <h1>Requested Events</h1>
        <table>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Title</th>
            <th>Venue</th>
            <th>Date</th>
            <th>Description</th>
            <th>Message</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($requested_events as $req): ?>
          <tr>
            <td><?= $req['id'] ?></td>
            <td><?= htmlspecialchars($req['name']) ?></td>
            <td><?= htmlspecialchars($req['title']) ?></td>
            <td><?= htmlspecialchars($req['venue']) ?></td>
            <td><?= $req['event_date'] ?></td>
            <td><?= htmlspecialchars($req['description']) ?></td>
            <td><?= htmlspecialchars($req['message']) ?></td>
            <td>
              <a style="padding:10px 8px; background-color:red; color:white; text-align:center;border-radius: 4px;" href="?action=delete_request&id=<?= $req['id'] ?>" style="background-color:red;color:white;padding:5px 10px;border-radius:4px;text-decoration:none;" onclick="return confirm('Delete this request?')">
                <span class="material-symbols-outlined">delete</span>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (count($requested_events) === 0): ?>
          <tr><td colspan="8">No requested events</td></tr>
          <?php endif; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
