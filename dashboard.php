<?php
require 'includes/config.php';
require 'includes/functions.php';

if (!is_logged_in()) {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_data($pdo, $user_id);
$page_title = "Dashboard";
$current_page = "dashboard";

// Handle Daily Reward
$daily_msg = '';
$daily_status = '';

if (isset($_POST['claim_daily'])) {
    if (can_claim_daily_reward($user['last_daily_reward'])) {
        $reward_amount = 10.00; // Fixed reward
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ?, last_daily_reward = NOW() WHERE id = ?");
        if ($stmt->execute([$reward_amount, $user_id])) {
            $user['balance'] += $reward_amount; // Update local var for display
            $daily_msg = "You claimed your daily reward of $reward_amount Pts!";
            $daily_status = "success";
        }
    } else {
        $daily_msg = "Please wait 24 hours between claims.";
        $daily_status = "danger";
    }
}

require 'includes/header.php';
?>

<!-- Ticker Section -->
<div class="ticker-wrap">
    <div class="ticker">
        <!-- Placeholder Companies -->
        <div class="ticker-item"><i class="fab fa-google"></i> Google Ads</div>
        <div class="ticker-item"><i class="fab fa-amazon"></i> Amazon Affiliates</div>
        <div class="ticker-item"><i class="fab fa-apple"></i> Apple Store</div>
        <div class="ticker-item"><i class="fab fa-spotify"></i> Spotify Premium</div>
        <div class="ticker-item"><i class="fab fa-discord"></i> Discord Nitro</div>
        <div class="ticker-item"><i class="fab fa-twitch"></i> Twitch Prime</div>
        <!-- Duplicate for seamless loop -->
        <div class="ticker-item"><i class="fab fa-google"></i> Google Ads</div>
        <div class="ticker-item"><i class="fab fa-amazon"></i> Amazon Affiliates</div>
        <div class="ticker-item"><i class="fab fa-apple"></i> Apple Store</div>
        <div class="ticker-item"><i class="fab fa-spotify"></i> Spotify Premium</div>
        <div class="ticker-item"><i class="fab fa-discord"></i> Discord Nitro</div>
        <div class="ticker-item"><i class="fab fa-twitch"></i> Twitch Prime</div>
    </div>
</div>

<?php if($daily_msg): ?>
    <div class="alert alert-<?php echo $daily_status; ?>">
        <?php echo $daily_msg; ?>
    </div>
<?php endif; ?>

<!-- Stats Blocks -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($user['balance'], 2); ?></h3>
            <p>Current Balance</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="color: var(--secondary-color); background: rgba(255, 117, 76, 0.1);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>0</h3>
            <p>Tasks Completed</p>
        </div>
    </div>

    <!-- Daily Reward Card -->
    <div class="stat-card" style="background: linear-gradient(135deg, #1F212E 0%, #252836 100%);">
        <div class="stat-icon" style="color: #F1C40F; background: rgba(241, 196, 15, 0.1);">
             <i class="fas fa-gift"></i>
        </div>
        <div class="stat-info" style="flex: 1;">
            <h3>Daily Reward</h3>
            <p>Claim your free points</p>
        </div>
        <form method="POST">
             <button type="submit" name="claim_daily" class="btn-primary" style="margin-top: 0; padding: 8px 16px; font-size: 0.8rem; width: auto;">Claim</button>
        </form>
    </div>
</div>

<!-- Recent Tasks Preview -->
<h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Available Tasks</h2>
<div class="tasks-grid">
    <!-- Static Preview Task -->
    <div class="task-card">
        <div class="task-header">
            <div class="task-icon"><i class="fab fa-telegram" style="color: #0088cc;"></i></div>
            <div class="task-reward">+50 Pts</div>
        </div>
        <div>
            <div class="task-title">Join Telegram Channel</div>
            <div class="task-desc">Join our official channel to get latest updates.</div>
        </div>
        <a href="tasks.php" class="btn-task" style="text-align: center; display: block;">Start Task</a>
    </div>

    <div class="task-card">
        <div class="task-header">
            <div class="task-icon"><i class="fab fa-youtube" style="color: #ff0000;"></i></div>
            <div class="task-reward">+100 Pts</div>
        </div>
        <div>
            <div class="task-title">Watch Video</div>
            <div class="task-desc">Watch a short video and like it.</div>
        </div>
        <a href="tasks.php" class="btn-task" style="text-align: center; display: block;">Start Task</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
