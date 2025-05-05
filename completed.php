<?php
session_start();
require_once 'db.php';

// Get today's completed tasks
$completed_tasks = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $today = date('Y-m-d');

    $stmt = $conn->prepare("SELECT id, title, description, TIME(due_datetime) as due_time, 
                          completed_at FROM tasks 
                          WHERE user_id = ? AND DATE(due_datetime) = ? AND completed = 1
                          ORDER BY completed_at DESC");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $completed_tasks[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="css/completed.css" rel="stylesheet">
</head>

<body>
    <?php require 'sidenavbar.php'; ?>

    <div class="main-content">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Completed Tasks</h1>
                    <span class="text-muted"><?php echo date('F j, Y'); ?></span>
                </div>
                <a href="today.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Back to Today
                </a>
            </div>

            <?php if (empty($completed_tasks)): ?>
                <div class="alert alert-info">
                    No completed tasks for today.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($completed_tasks as $task): ?>
                        <div class="col-12">
                            <div class="card completed-task shadow-sm mb-2">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 1.5rem;"></i>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1 text-decoration-line-through"><?php echo htmlspecialchars($task['title']); ?></h5>
                                            <?php if (!empty($task['description'])): ?>
                                                <p class="card-text text-muted small mb-0"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                                            <?php endif; ?>
                                            <small class="completed-at">
                                                Completed at <?php echo date('g:i A', strtotime($task['completed_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>