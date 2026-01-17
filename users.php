<?php require 'header.php'; 

// Handle Actions (Ban/Delete - Simplified to just delete/toggle for now)
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 50")->fetchAll();
?>

<div class="auth-card" style="margin: 0; max-width: 100%;">
    <h3>Recent Users</h3>
    <div style="overflow-x: auto; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Username</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Balance</th>
                    <th style="padding: 10px;">IP</th>
                    <th style="padding: 10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr style="border-bottom: 1px solid #2F3336;">
                        <td style="padding: 10px;">#<?php echo $u['id']; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u['username']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td style="padding: 10px; font-weight: bold;"><?php echo number_format($u['balance'], 2); ?></td>
                        <td style="padding: 10px; color: var(--text-muted); font-size: 0.8rem;"><?php echo $u['ip_address']; ?></td>
                        <td style="padding: 10px;">
                            <?php if($u['role'] != 'admin'): ?>
                            <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Delete user?')" style="color: var(--danger-color);"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
