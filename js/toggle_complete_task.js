function toggleTaskCompletion(checkbox) {
    const taskId = checkbox.dataset.taskId;
    const taskCard = checkbox.closest('.task-card');

    fetch('complete_task.php', {
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