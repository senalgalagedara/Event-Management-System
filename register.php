<?php
require_once 'config.php';
require_once 'auth.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Email already exists.";
            } else {
                $hash = hash('sha256', $password);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,?)");
                $role = 'user';
                $stmt->bind_param("ssss", $username, $email, $hash, $role);
                
                if ($stmt->execute()) {
                    $success = "Registration successful! You can now login.";
                    header("refresh:2;url=login.php?msg=Registration+successful");
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
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
  <title>Register - College Event Management System</title>
  <link rel="stylesheet" href="styles/auth.css" />
</head>
<body>
<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <h2>Create Account</h2>
      <p>Join our college event community</p>
    </div>
    
    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="post" class="auth-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username ?? ''); ?>" required />
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required />
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" name="confirm_password" type="password" required />
      </div>
      
      <button class="auth-btn" type="submit">Create Account</button>
    </form>
    
    <div class="auth-footer">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</div>
</body>
</html>