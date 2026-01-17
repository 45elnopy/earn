<?php require 'header.php'; 

// Process Withdrawals
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = clean_input($_GET['id']);
    
    $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ? AND status = 'pending'");
    $stmt->execute([$id]);
    $tx = $stmt->fetch();
    
    if ($tx) {
        if ($action == 'approve') {
            $pdo->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = ?")->execute([$id]);
            // In a real system, trigger TON payout via API here
        } elseif ($action == 'reject') {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE withdrawals SET status = 'rejected' WHERE id = ?")->execute([$id]);
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$tx['amount'], $tx['user_id']]); // Refund
            $pdo->commit();
        }
    }
    redirect('withdrawals.php');
}

$withdrawals = $pdo->query("SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC")->fetchAll();
?>

<div class="auth-card" style="margin: 0; max-width: 100%;">
    <h3>Withdrawal Requests</h3>
    <div style="overflow-x: auto; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">User</th>
                    <th style="padding: 10px;">Amount</th>
                    <th style="padding: 10px;">TON Address</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($withdrawals as $w): ?>
                    <tr style="border-bottom: 1px solid #2F3336;">
                        <td style="padding: 10px;">#<?php echo $w['id']; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($w['username']); ?></td>
                        <td style="padding: 10px; font-weight: bold;"><?php echo number_format($w['amount'], 2); ?></td>
                        <td style="padding: 10px; font-size: 0.8rem; font-family: monospace;"><?php echo htmlspecialchars($w['ton_address']); ?></td>
                        <td style="padding: 10px;">
                            <?php echo ucfirst($w['status']); ?>
                        </td>
                        <td style="padding: 10px;">
                            <?php if($w['status'] == 'pending'): ?>
                                <a href="?action=approve&id=<?php echo $w['id']; ?>" style="color: var(--success-color); margin-right: 10px;" title="Approve"><i class="fas fa-check"></i></a>
                                <a href="?action=reject&id=<?php echo $w['id']; ?>" style="color: var(--danger-color);" title="Reject" onclick="return confirm('Reject and refund?')"><i class="fas fa-times"></i></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
