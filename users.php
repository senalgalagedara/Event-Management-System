<?php
require_once 'config.php';
require_once 'auth.php';
require_admin();

$action = $_GET['action'] ?? '';
$info = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['form'] ?? '') === 'create') {
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
    if (($_POST['form'] ?? '') === 'update') {
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

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $info = "User deleted.";
}

$users = [];
$res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
if ($res) { while ($row = $res->fetch_assoc()) { $users[] = $row; } }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users | CEMS</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo"></div><h1>Users</h1></div>
    <div class="nav">
      <a class="btn" href="admin.php">Admin</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <h2>Create User</h2>
    <?php if ($info): ?><div class="alert alert-ok"><?php echo htmlspecialchars($info); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-bad"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" class="grid grid-3">
      <input type="hidden" name="form" value="create" />
      <div>
        <label>Username</label>
        <input name="username" required />
      </div>
      <div>
        <label>Password</label>
        <input name="password" type="password" required />
      </div>
      <div>
        <label>Role</label>
        <select name="role"><option value="user">user</option><option value="admin">admin</option></select>
      </div>
      <div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
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
          <form method="post" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="form" value="update" />
            <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>" />
            <select name="role">
              <option value="user" <?php if($u['role']==='user') echo 'selected'; ?>>user</option>
              <option value="admin" <?php if($u['role']==='admin') echo 'selected'; ?>>admin</option>
            </select>
            <input name="password" type="password" placeholder="New password (optional)" />
            <button class="btn" type="submit">Save</button>
          </form>
        </td>
        <td><?php echo htmlspecialchars($u['created_at']); ?></td>
        <td><a class="btn btn-danger" href="?action=delete&id=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete user?')">Delete</a></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
