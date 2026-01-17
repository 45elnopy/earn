<?php
require 'includes/config.php';
require 'includes/functions.php';

if (!is_logged_in()) {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];
$page_title = "Withdraw";
$current_page = "withdraw";

$msg = '';
$msg_type = '';

// Handle Withdrawal Request
if (isset($_POST['request_withdraw'])) {
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
    $address = clean_input($_POST['ton_address']);
    
    // Minimum withdraw amount
    $min_withdraw = 50.00;
    
    // Check balance
    $user = get_user_data($pdo, $user_id); // Fresh data
    
    if ($amount < $min_withdraw) {
        $msg = "Minimum withdrawal is $min_withdraw Pts.";
        $msg_type = "danger";
    } elseif ($amount > $user['balance']) {
        $msg = "Insufficient balance.";
        $msg_type = "danger";
    } else {
        $pdo->beginTransaction();
        try {
            // Deduct balance
            $update = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $update->execute([$amount, $user_id]);
            
            // Create record
            $insert = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, ton_address) VALUES (?, ?, ?)");
            $insert->execute([$user_id, $amount, $address]);
            
            $pdo->commit();
            $msg = "Withdrawal requested successfully!";
            $msg_type = "success";
        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = "Error processing request.";
            $msg_type = "danger";
        }
    }
}

// Refresh user data for display
$user = get_user_data($pdo, $user_id);

// Fetch History
$stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();

require 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($user['balance'], 2); ?></h3>
            <p>Available for Withdraw</p>
        </div>
    </div>
</div>

<div class="dashboard-container" style="display: block; padding: 0;"> <!-- Resetting flex from dashboard -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        
        <!-- Request Form -->
        <div class="auth-card" style="margin: 0; max-width: 100%;">
            <h2 style="text-align: left;">Request Withdrawal</h2>
            <?php if($msg): ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Amount (Pts)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="50" required placeholder="Min 50">
                </div>
                
                <div class="form-group">
                    <label>TON Wallet Address</label>
                    <input type="text" name="ton_address" class="form-control" required placeholder="EQC...">
                </div>
                
                <button type="submit" name="request_withdraw" class="btn-primary">Request Withdraw</button>
            </form>
        </div>
        
        <!-- History -->
        <div class="auth-card" style="margin: 0; max-width: 100%;">
            <h2 style="text-align: left;">Recent History</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 10px;">Date</th>
                            <th style="padding: 10px;">Amount</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($history) > 0): ?>
                            <?php foreach($history as $tx): ?>
                                <tr style="border-bottom: 1px solid #2F3336;">
                                    <td style="padding: 10px; font-size: 0.9rem; color: var(--text-muted);"><?php echo date('M d', strtotime($tx['created_at'])); ?></td>
                                    <td style="padding: 10px; font-weight: bold;"><?php echo number_format($tx['amount'], 2); ?></td>
                                    <td style="padding: 10px;">
                                        <?php
                                            $badge_color = '#f1c40f'; // Pending
                                            if ($tx['status'] == 'approved') $badge_color = '#2ecc71';
                                            if ($tx['status'] == 'rejected') $badge_color = '#e74c3c';
                                        ?>
                                        <span style="color: <?php echo $badge_color; ?>; font-weight: 500; font-size: 0.9rem;">
                                            <?php echo ucfirst($tx['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="padding: 20px; text-align: center; color: var(--text-muted);">No withdrawals yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
