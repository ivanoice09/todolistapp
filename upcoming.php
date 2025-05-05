<?php
session_start();
require_once 'db.php';

// Get all upcoming tasks ordered by date
$tasks_by_date = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id, title, description, 
                          DATE(due_datetime) as due_date, 
                          TIME(due_datetime) as due_time 
                          FROM tasks 
                          WHERE user_id = ? AND completed = 0
                          ORDER BY due_datetime ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $due_date = $row['due_date'];
        if (!isset($tasks_by_date[$due_date])) {
            $tasks_by_date[$due_date] = [];
        }
        $tasks_by_date[$due_date][] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- sweetalert link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .date-header {
            background-color: #f8f9fa;
            padding: 8px 15px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #4361ee;
            font-weight: 500;
        }

        .today-header {
            background-color: #e3f2fd;
            border-left-color: #2196f3;
        }

        .task-card {
            transition: all 0.2s;
            margin-bottom: 10px;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .task-time {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .no-tasks {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <!-- sidemenu -->
    <?php require 'sidenavbar.php'; ?>
    <!-- task modal -->
    <?php require 'task_modal.php'; ?>

    <div class="main-content">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Upcoming Tasks</h1>
                <span class="text-muted"><?php echo date('F j, Y'); ?></span>
            </div>

            <?php if (empty($tasks_by_date)): ?>
                <div class="no-tasks text-center">
                    <div>
                        <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">No upcoming tasks</h4>
                        <p class="text-muted">Add tasks with future due dates to see them here</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="upcoming-tasks">
                    <?php foreach ($tasks_by_date as $date => $tasks): ?>
                        <?php
                        $date_obj = new DateTime($date);
                        $is_today = $date === date('Y-m-d');
                        $is_tomorrow = $date === date('Y-m-d', strtotime('+1 day'));
                        ?>

                        <div class="date-header <?php echo $is_today ? 'today-header' : ''; ?>">
                            <?php if ($is_today): ?>
                                <i class="bi bi-star-fill text-warning me-2"></i> Today
                            <?php elseif ($is_tomorrow): ?>
                                <i class="bi bi-sun text-info me-2"></i> Tomorrow
                            <?php else: ?>
                                <i class="bi bi-calendar-date me-2"></i>
                                <?php echo $date_obj->format('l, F j'); ?>
                                <small class="text-muted ms-2"><?php echo $date_obj->format('Y'); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="task-list">
                            <?php foreach ($tasks as $task): ?>
                                <div class="card task-card shadow-sm">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($task['title']); ?></h5>
                                                <?php if (!empty($task['description'])): ?>
                                                    <p class="card-text text-muted small mb-1"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($task['due_time'])): ?>
                                                    <p class="task-time mb-0">
                                                        <i class="bi bi-clock"></i>
                                                        <?php echo date('g:i A', strtotime($task['due_time'])); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="task-actions">
                                                <button class="btn btn-sm btn-outline-success me-2"
                                                    onclick="markTaskComplete(<?php echo $task['id']; ?>)">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteTask(<?php echo $task['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/upcoming.js"></script>
</body>

</html>