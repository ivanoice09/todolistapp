<?php
session_start();
require_once 'db.php';

// Check which view to show (today or all)
$view = $_GET['view'] ?? 'today'; // Default to today's tasks
$today = date('Y-m-d');

// Get today's completed tasks
$completed_tasks = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    if ($view === 'today') {
        $stmt = $conn->prepare("SELECT id, title, description, TIME(due_datetime) as due_time, 
                          completed_at FROM tasks 
                          WHERE user_id = ? AND DATE(due_datetime) = ? AND completed = 1
                          ORDER BY completed_at DESC");
        $stmt->bind_param("is", $user_id, $today);
    } else {
        $stmt = $conn->prepare("SELECT id, title, description, TIME(due_datetime) as due_time, 
                              DATE(due_datetime) as due_date, completed_at FROM tasks 
                              WHERE user_id = ? AND completed = 1
                              ORDER BY due_datetime DESC");
        $stmt->bind_param("i", $user_id);
    }

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
                <h1 class="h3 mb-0">Completed Tasks</h1>
                <a href="today.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Back to Today
                </a>
            </div>

            <!-- View Toggle -->
            <div class="view-toggle btn-group w-100 mb-4">
                <a href="?view=today" class="btn <?= $view === 'today' ? 'btn-success' : 'btn-outline-success' ?>">
                    Today's task
                </a>
                <a href="?view=all" class="btn <?= $view === 'all' ? 'btn-success' : 'btn-outline-success' ?>">
                    All tasks
                </a>
            </div>

            <?php if (empty($completed_tasks)): ?>
                <div class="alert alert-info">
                    No completed tasks found.
                </div>
            <?php else: ?>
                <?php if ($view === 'all'): ?>
                    <!-- Group by date for "All" view -->
                    <?php
                    $grouped_tasks = [];
                    foreach ($completed_tasks as $task) {
                        $date = date('Y-m-d', strtotime($task['due_date']));
                        $grouped_tasks[$date][] = $task;
                    }
                    ?>

                    <?php foreach ($grouped_tasks as $date => $tasks): ?>
                        <div class="date-header">
                            <i class="bi bi-calendar-date me-2"></i>
                            <?= date('l, F j, Y', strtotime($date)) ?>
                        </div>

                        <?php foreach ($tasks as $task): ?>
                            <!-- Task card (same as below) -->
                            <?php include 'completed_task_card.php'; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                <?php else: ?>
                    <!-- Simple list for "Today" view -->
                    <?php foreach ($completed_tasks as $task): ?>
                        <?php include 'completed_task_card.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>