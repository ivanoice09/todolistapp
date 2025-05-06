// Add this function for deleting completed tasks
function deleteTask(taskId, element) {
    Swal.fire({
        title: 'Delete Task?',
        text: "This will permanently delete this task",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ task_id: taskId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove task card from DOM
                    element.closest('.completed-task').remove();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Task has been deleted',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to delete task', 'error');
                }
            });
        }
    });
}