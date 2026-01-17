<?php
require 'includes/config.php';
require 'includes/functions.php';

if (!is_logged_in()) {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_data($pdo, $user_id);
$page_title = "Tasks";
$current_page = "tasks";

$msg = '';
$msg_type = '';

// Handle Task Completion
if (isset($_POST['complete_task'])) {
    $task_id = $_POST['task_id'];
    
    // Check if task exists and is active
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND is_active = 1");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    if ($task) {
        // Check if already completed
        $check = $pdo->prepare("SELECT id FROM user_tasks WHERE user_id = ? AND task_id = ?");
        $check->execute([$user_id, $task_id]);
        
        if ($check->rowCount() == 0) {
            // Mark as complete
            $pdo->beginTransaction();
            try {
                $insert = $pdo->prepare("INSERT INTO user_tasks (user_id, task_id) VALUES (?, ?)");
                $insert->execute([$user_id, $task_id]);
                
                $update = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $update->execute([$task['reward'], $user_id]);
                
                $pdo->commit();
                $msg = "Task completed! You earned {$task['reward']} Pts.";
                $msg_type = "success";
                
                // Refresh user data
                $user = get_user_data($pdo, $user_id);
            } catch (Exception $e) {
                $pdo->rollBack();
                $msg = "Error completing task.";
                $msg_type = "danger";
            }
        } else {
            $msg = "You have already completed this task.";
            $msg_type = "danger";
        }
    }
}

// Fetch all available tasks
$stmt = $pdo->prepare("
    SELECT t.*, 
    (SELECT count(*) FROM user_tasks ut WHERE ut.task_id = t.id AND ut.user_id = ?) as is_completed 
    FROM tasks t 
    WHERE t.is_active = 1
    ORDER BY t.created_at DESC
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

require 'includes/header.php';
?>

<div class="container">
    <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="tasks-grid">
        <?php if(count($tasks) > 0): ?>
            <?php foreach($tasks as $task): ?>
                <div class="task-card" style="<?php echo $task['is_completed'] ? 'opacity: 0.7;' : ''; ?>">
                    <div class="task-header">
                        <div class="task-icon">
                            <?php 
                                $icon = 'fa-tasks';
                                if($task['type'] == 'telegram') $icon = 'fa-telegram';
                                elseif($task['type'] == 'youtube') $icon = 'fa-youtube';
                                elseif($task['type'] == 'link') $icon = 'fa-link';
                            ?>
                            <i class="fab <?php echo $icon; ?>"></i>
                        </div>
                        <div class="task-reward">+<?php echo number_format($task['reward'], 0); ?> Pts</div>
                    </div>
                    <div>
                        <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                        <div class="task-desc"><?php echo htmlspecialchars($task['description']); ?></div>
                    </div>
                    
                    <?php if($task['is_completed']): ?>
                        <button class="btn-task" disabled style="background: #2ECC71; color: white; cursor: default;">
                            <i class="fas fa-check"></i> Completed
                        </button>
                    <?php else: ?>
                        <form method="POST" target="_blank" action="<?php echo $task['url']; ?>" onsubmit="setTimeout(() => { document.getElementById('form-<?php echo $task['id']; ?>').submit(); }, 2000);">
                             <!-- This is a trick: clicking opens URL, and we autosubmit the completion form after 2s delay. 
                                  In real production, use AJAX and verify via API if possible. -->
                             <button type="submit" class="btn-task">Open Task</button>
                        </form>
                        
                        <!-- Actual completion trigger form (hidden for user, triggered by logic or separate button for simplicity here) -->
                        <form method="POST" id="form-<?php echo $task['id']; ?>" style="margin-top: 10px;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <input type="hidden" name="complete_task" value="1">
                            <button type="submit" class="btn-task" style="background: var(--secondary-color); color: white;">Verify & Claim</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted); grid-column: 1/-1; text-align: center;">No tasks available right now.</p>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
