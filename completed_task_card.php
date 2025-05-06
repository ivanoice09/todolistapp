<div class="card completed-task shadow-sm mb-2">
    <div class="card-body py-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 1.5rem;"></i>
            <div class="flex-grow-1">
                <h5 class="card-title mb-1 text-decoration-line-through"><?= htmlspecialchars($task['title']) ?></h5>
                <?php if (!empty($task['description'])): ?>
                    <p class="card-text text-muted small mb-0"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                <?php endif; ?>
                <small class="text-muted">
                    Completed: <?= date('g:i A', strtotime($task['completed_at'])) ?>
                    <?php if ($view === 'all' && isset($task['due_time'])): ?>
                        | Due: <?= date('g:i A', strtotime($task['due_time'])) ?>
                    <?php endif; ?>
                </small>
            </div>
            <div class="task-actions">
                <button class="btn btn-sm btn-outline-danger"
                    onclick="deleteTask(<?= $task['id'] ?>, this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<script src="js/delete_task.js"></script>