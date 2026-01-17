<?php require 'header.php'; 

// Add Task
if (isset($_POST['add_task'])) {
    $title = clean_input($_POST['title']);
    $desc = clean_input($_POST['description']);
    $url = clean_input($_POST['url']);
    $reward = clean_input($_POST['reward']);
    $type = clean_input($_POST['type']);
    
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, url, reward, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $desc, $url, $reward, $type]);
    $msg = "Task added successfully";
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([$id]);
    redirect('tasks.php');
}

$tasks = $pdo->query("SELECT * FROM tasks ORDER BY id DESC")->fetchAll();
?>

<div class="dashboard-container" style="display: block; padding: 0;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        
        <!-- Add Task Form -->
        <div class="auth-card" style="margin: 0; max-width: 100%;">
            <h3>Add New Task</h3>
            <?php if(isset($msg)) echo "<p style='color: green'>$msg</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" name="url" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Reward (Pts)</label>
                    <input type="number" name="reward" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="telegram">Telegram</option>
                        <option value="youtube">YouTube</option>
                        <option value="link">Link Visit</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" name="add_task" class="btn-primary">Add Task</button>
            </form>
        </div>

        <!-- Task List -->
        <div class="auth-card" style="margin: 0; max-width: 100%;">
            <h3>Running Tasks</h3>
            <div style="overflow-x: auto; margin-top: 20px;">
                <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Title</th>
                            <th style="padding: 10px;">Reward</th>
                            <th style="padding: 10px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tasks as $t): ?>
                            <tr style="border-bottom: 1px solid #2F3336;">
                                <td style="padding: 10px;">#<?php echo $t['id']; ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($t['title']); ?></td>
                                <td style="padding: 10px;"><?php echo number_format($t['reward']); ?></td>
                                <td style="padding: 10px;">
                                    <a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Delete task?')" style="color: var(--danger-color);"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
