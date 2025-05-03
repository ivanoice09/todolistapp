// fab_modal.js
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Check if we're on one of the allowed pages for FAB
    const allowedPages = ['today.php', 'upcoming.php', 'calendar.php'];
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

            const formData = new FormData(taskForm);

            fetch('task_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal and refresh page
                        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
                        modal.hide();
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the task.');
                });
        });
    }
});