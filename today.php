<?php
session_start();
require_once 'db.php';

// Get today's date
$today = date('Y-m-d');

// Get incomplete tasks for today
$tasks = [];
$completed_tasks = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Incomplete tasks
    $stmt = $conn->prepare("SELECT id, title, description, TIME(due_datetime) as due_time 
                          FROM tasks 
                          WHERE user_id = ? AND DATE(due_datetime) = ? AND completed = 0
                          ORDER BY due_datetime ASC");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $stmt->close();

    // Completed tasks (for the badge)
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tasks 
                          WHERE user_id = ? AND DATE(due_datetime) = ? AND completed = 1");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $completed_count = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Today's Tasks</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- bootstrap icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- sweetalert link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <link href="css/today.css" rel="stylesheet">
</head>

<body>
    <!-- sidemenu -->
    <?php require 'sidenavbar.php'; ?>
    <!-- task modal -->
    <?php require 'task_modal.php'; ?>

    <div class="main-content">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Today's Tasks</h1>
                    <span class="text-muted"><?php echo date('F j, Y'); ?></span>
                </div>
                <?php if ($completed_count > 0): ?>
                    <a href="completed.php" class="btn btn-sm completed-badge text-white">
                        <i class="bi bi-check-circle"></i> <?php echo $completed_count; ?> completed
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($tasks)): ?>
                <div class="no-tasks text-center py-5">
                    <i class="bi bi-check-circle-fill text-muted mb-3" style="font-size: 3rem;"></i>
                    <h4 class="mb-2">All clear for today!</h4>
                    <p class="text-muted">Add a task using the + button below</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($tasks as $task): ?>
                        <div class="col-12">
                            <div class="card task-card shadow-sm mb-2">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox"
                                            class="form-check-input task-checkbox"
                                            data-task-id="<?php echo $task['id']; ?>"
                                            onchange="toggleTaskCompletion(this)">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title task-title mb-1"><?php echo htmlspecialchars($task['title']); ?></h5>
                                            <?php if (!empty($task['description'])): ?>
                                                <p class="card-text text-muted small mb-0"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="task-actions">
                                            <?php if (!empty($task['due_time'])): ?>
                                                <span class="badge bg-light text-dark me-2">
                                                    <i class="bi bi-clock"></i> <?php echo date('g:i A', strtotime($task['due_time'])); ?>
                                                </span>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(<?php echo $task['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

    <!-- Bootstrap's 5 js plugin -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- custom js -->
    <script src="js/today.js"></script>
</body>

</html>