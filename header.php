<?php
// Admin Header
require '../includes/config.php';
require '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../index.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_data($pdo, $user_id);

if ($user['role'] !== 'admin') {
    die("Access Denied: Admin only.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - RewardPlatform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar { background: #1a1d26; } 
        .admin-badge { background: #E74C3C; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="brand">
            <i class="fas fa-shield-alt" style="color: var(--danger-color);"></i>
            <span>Admin<span style="color: white;">Panel</span></span>
        </div>
        
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="tasks.php" class="nav-link"><i class="fas fa-tasks"></i> Manage Tasks</a></li>
            <li><a href="withdrawals.php" class="nav-link"><i class="fas fa-wallet"></i> Withdrawals</a></li>
            <li><a href="../dashboard.php" class="nav-link"><i class="fas fa-arrow-left"></i> Back to User Area</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Admin Area</h2>
            <div class="user-profile">
                <span><?php echo $user['username']; ?> <span class="admin-badge">ADMIN</span></span>
            </div>
        </div>
