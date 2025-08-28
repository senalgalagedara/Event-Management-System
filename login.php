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

            if($_SESSION['role'] == 'admin'){
              header("Location: admin.php");
            } else {
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
  <link rel="stylesheet" href="styles/auth.css" />
</head>
<body>
<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <h2>Welcome Back</h2>
      <p>Sign in to your account</p>
    </div>
    
    <?php if ($msg): ?>
      <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post" class="auth-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
      </div>
      
      <button class="auth-btn" type="submit">Sign In</button>
    </form>
    
    <div class="auth-footer">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
      <small class="demo-info">Demo: username: <strong>uoc</strong> / password: <strong>uoc</strong></small>
    </div>
  </div>
</div>
</body>
</html>