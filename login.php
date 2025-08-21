<?php
require_once 'config.php';
require_once 'auth.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    } else {
        // Using SHA2(256) hashing for simplicity (project requirement: no frameworks)
        $hash = hash('sha256', $password);

        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username=? AND password_hash=?");
        $stmt->bind_param("ss", $username, $hash);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | College Event Management System</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo"></div>
      <h1>College Event Management</h1>
    </div>
    <div class="nav">
      <a class="btn" href="help.php">Help</a>
    </div>
  </div>

  <div class="card" style="max-width:500px;margin:0 auto;">
    <h2>Login</h2>
    <?php if ($msg): ?><div class="alert alert-ok"><?php echo $msg; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-bad"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="username">Username</label>
      <input id="username" name="username" required />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required />
      <div style="margin-top:12px;display:flex;gap:10px;align-items:center;">
        <button class="btn btn-primary" type="submit">Sign In</button>
        <small class="muted">Default user: uoc / uoc</small>
      </div>
    </form>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
