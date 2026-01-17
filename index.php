<?php
require 'includes/config.php';
require 'includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        redirect('dashboard.php');
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RewardPlatform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Welcome Back</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter password">
            </div>
            
            <button type="submit" class="btn-primary">Login</button>
        </form>
        
        <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register</a>
        </p>
    </div>
</div>

</body>
</html>
