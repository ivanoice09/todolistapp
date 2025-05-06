// fab_modal.js
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Check if we're on one of the allowed pages for FAB
    const allowedPages = ['inbox.php', 'today.php', 'upcoming.php', 'calendar.php'];
    const currentPage = window.location.pathname.split('/').pop();

    if (allowedPages.includes(currentPage)) {
        // Create and append the FAB
        const fab = document.createElement('button');
        fab.id = 'fab';
        fab.className = 'btn btn-primary rounded-circle fab-button';
        fab.innerHTML = '<i class="bi bi-plus"></i>';
        fab.setAttribute('data-bs-toggle', 'modal');
        fab.setAttribute('data-bs-target', '#taskModal');
        fab.setAttribute('title', 'Add new task');
        fab.setAttribute('data-bs-placement', 'left');
        document.body.appendChild(fab);

        // Initialize tooltip for FAB
        new bootstrap.Tooltip(fab);
    }

    // Add event listener for sidebar button if it exists
    const sidebarTaskBtn = document.querySelector('.sidebar-task-btn');
    if (sidebarTaskBtn) {
        sidebarTaskBtn.addEventListener('click', function () {
            showTaskModal(currentPage);
        });
    }

    // Modal show function with page context
    function showTaskModal(page) {
        const modal = new bootstrap.Modal(document.getElementById('taskModal'));

        // If on today.php, set today's date automatically
        if (page === 'today.php') {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('taskDueDate').value = formattedDate;
            document.getElementById('taskDueDate').readOnly = true;
        
        } else {
            document.getElementById('taskDueDate').readOnly = false;
        }

        modal.show();
    }

    // Handle form submission
    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Disable submit button to prevent multiple submissions
            const submitBtn = taskForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

            const formData = new FormData(taskForm);

            fetch('task_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    // First check if the response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        // Close modal and redirect
                        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
                        modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Task Saved',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Redirect to appropriate page based on section
                            if (formData.get('due_date')) {
                                window.location.href = 'today.php';
                            } else {
                                window.location.href = 'inbox.php';
                            }
                        });

                    } else if (data) {
                        Swal.fire('Error', data.message || 'Failed to create task', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while saving the task.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Task';
                });
        });
    }
});