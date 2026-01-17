<?php require 'header.php'; 

// Fetch Stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_withdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status='pending'")->fetchColumn();
$total_tasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE is_active=1")->fetchColumn();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="color: #3498db; background: rgba(52, 152, 219, 0.1);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="color: #e74c3c; background: rgba(231, 76, 60, 0.1);">
             <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_withdrawals; ?></h3>
            <p>Pending Withdrawals</p>
        </div>
        <a href="withdrawals.php" class="btn-primary" style="width: auto; padding: 5px 10px; font-size: 0.8rem;">View</a>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #2ecc71; background: rgba(46, 204, 113, 0.1);">
             <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_tasks; ?></h3>
            <p>Active Tasks</p>
        </div>
    </div>
</div>

<div class="auth-card" style="margin: 0; max-width: 100%;">
    <h3>System Status</h3>
    <p style="color: var(--text-muted); margin-top: 10px;">System is running smoothly. Database connection active.</p>
</div>

</body> <!-- Quick close for simplicity, ideally footer -->
</html>
