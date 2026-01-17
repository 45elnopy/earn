<?php
require 'includes/config.php';
require 'includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->rowCount() > 0) {
            $error = "Email or Username already taken";
        } else {
            // Register
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ip = $_SERVER['REMOTE_ADDR'];
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, ip_address) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed, $ip])) {
                header("Location: index.php"); // Redirect to login
                exit;
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RewardPlatform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create Account</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Choose a username">
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Create password">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm password">
            </div>
            
            <button type="submit" class="btn-primary">Sign Up</button>
        </form>
        
        <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">
            Already have an account? <a href="index.php" style="color: var(--primary-color);">Login</a>
        </p>
    </div>
</div>

</body>
</html>
