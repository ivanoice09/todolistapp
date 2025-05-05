function toggleTaskCompletion(checkbox) {
    const taskId = checkbox.dataset.taskId;
    const taskCard = checkbox.closest('.task-card');

    fetch('toggle_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            task_id: taskId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                taskCard.classList.toggle('completed');
                taskCard.querySelector('.task-title').classList.toggle('text-decoration-line-through');
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        });
}

function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch('delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                task_id: taskId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
    }
}