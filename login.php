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

            if($_SESSION['username']=='admin'){
              header("Location: admin.php");
            }
            else{
            header("Location: index.php");
            }
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
  <title>Login - College Event Management System</title>
  <link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<div class="container">
  <div class="card" style="height:80vh; ">
    <h2 style="text-align: center;">Login</h2>
    <?php if ($msg): ?><div class="alert alert-ok"><?php echo $msg; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-bad"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="username">Username</label>
      <input id="username" name="username" required />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required />
      <div style="margin-top:50px;display:block;align-items:center;">
        <button class="btn btn-primary" type="submit">Sign In</button>
        <small class="muted">Default user: uoc / uoc</small>
      </div>
    </form>
  </div>

  <div class="footer">© 2025 CEMS</div>
</div>
</body>
</html>
