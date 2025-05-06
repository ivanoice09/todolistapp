<?php
session_start();
require_once 'db.php';

// Fetch tasks for FullCalendar
$events = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT 
        id, 
        title, 
        description, 
        due_datetime as start,
        CASE 
            WHEN section = 'today' THEN '#4361ee'
            WHEN section = 'upcoming' THEN '#3a0ca3'
            WHEN section = 'inbox' THEN '#7209b7'
            ELSE '#f72585'
        END as color,
        completed
        FROM tasks WHERE user_id = ? AND due_datetime IS NOT NULL");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'color' => $row['completed'] ? '#4cc9f0' : $row['color'],
            'extendedProps' => [
                'description' => $row['description'],
                'completed' => $row['completed']
            ]
        ];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- sweetalert link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <!-- Moment.js for date handling -->
    <script src='https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js'></script>
    <!-- Custom CSS -->
    <link href="css/calendar.css" rel="stylesheet">
</head>

<body>
    <!-- sidemenu -->
    <?php require 'sidenavbar.php'; ?>
    <!-- task modal -->
    <?php require 'task_modal.php'; ?>

    <div class="main-content">
        <div id='calendar-container'>
            <div id='calendar'></div>
        </div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="eventModalDescription"></p>
                    <p><strong>Due:</strong> <span id="eventModalTime"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="completeTaskBtn">
                        <i class="bi bi-check-circle"></i> Mark Complete
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteTaskBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js'></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            let currentEventId = null;

            // Initialize calendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: <?php echo json_encode($events); ?>,
                eventClick: function(info) {
                    currentEventId = info.event.id;
                    document.getElementById('eventModalTitle').textContent = info.event.title;
                    document.getElementById('eventModalDescription').textContent =
                        info.event.extendedProps.description || 'No description';
                    document.getElementById('eventModalTime').textContent =
                        moment(info.event.start).format('MMMM Do YYYY, h:mm a');

                    // Toggle complete button based on status
                    const completeBtn = document.getElementById('completeTaskBtn');
                    if (info.event.extendedProps.completed) {
                        completeBtn.classList.add('d-none');
                    } else {
                        completeBtn.classList.remove('d-none');
                    }

                    eventModal.show();
                },
                eventContent: function(arg) {
                    // Create a container div
                    const container = document.createElement('div');
                    container.style.overflow = 'hidden';
                    container.style.textOverflow = 'ellipsis';
                    container.style.whiteSpace = 'nowrap';
                    container.style.width = '100%';

                    // Create content div
                    const content = document.createElement('div');
                    content.className = 'fc-event-main-frame';

                    if (arg.event.extendedProps.completed) {
                        content.classList.add('completed-event');
                    }

                    // Time element
                    if (arg.event.start) {
                        const timeEl = document.createElement('span');
                        timeEl.className = 'fc-event-time';
                        timeEl.textContent = moment(arg.event.start).format('h:mm') + ' ';
                        content.appendChild(timeEl);
                    }

                    // Title element
                    const titleEl = document.createElement('span');
                    titleEl.className = 'fc-event-title';
                    titleEl.textContent = arg.event.title;
                    content.appendChild(titleEl);

                    container.appendChild(content);
                    return {
                        domNodes: [container]
                    };
                },
                eventDidMount: function(arg) {
                    if (arg.event.title) {
                        let tooltipContent = arg.event.title;
                        if (arg.event.start) {
                            tooltipContent += '\n' + moment(arg.event.start).format('MMM D, h:mm A');
                        }
                        if (arg.event.extendedProps.description) {
                            tooltipContent += '\n\n' + arg.event.extendedProps.description;
                        }

                        new bootstrap.Tooltip(arg.el, {
                            title: tooltipContent,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        });
                    }
                },
                editable: true,
                eventDrop: function(info) {
                    // Handle drag-and-drop rescheduling
                    updateTaskDate(info.event.id, info.event.start);
                },
                eventResize: function(info) {
                    // Handle event duration changes
                    updateTaskDate(info.event.id, info.event.start, info.event.end);
                },
                nowIndicator: true,
                navLinks: true,
                dayMaxEvents: true,
                selectable: true,
                select: function(info) {
                    // Create new task on date click
                    const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                    document.getElementById('taskDueDate').value = moment(info.start).format('YYYY-MM-DD');
                    modal.show();
                }
            });

            calendar.render();

            // Event handlers
            document.getElementById('completeTaskBtn').addEventListener('click', function() {
                if (currentEventId) {
                    markTaskComplete(currentEventId);
                }
            });

            document.getElementById('deleteTaskBtn').addEventListener('click', function() {
                if (currentEventId) {
                    deleteTask(currentEventId);
                }
            });

            // Helper functions
            function updateTaskDate(taskId, startDate, endDate = null) {
                const formattedDate = moment(startDate).format('YYYY-MM-DD HH:mm:ss');

                fetch('update_task_date.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            task_id: taskId,
                            due_datetime: formattedDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            calendar.refetchEvents();
                            Swal.fire('Error', 'Failed to update task date', 'error');
                        }
                    });
            }

            function markTaskComplete(taskId) {
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
                            eventModal.hide();
                            calendar.refetchEvents();
                            Swal.fire('Success', 'Task marked as complete', 'success');
                        } else {
                            Swal.fire('Error', 'Failed to complete task', 'error');
                        }
                    });
            }

            function deleteTask(taskId) {
                Swal.fire({
                    title: 'Delete Task?',
                    text: "This cannot be undone!",
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
                                body: JSON.stringify({
                                    task_id: taskId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    eventModal.hide();
                                    calendar.refetchEvents();
                                    Swal.fire('Deleted!', 'Task has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error', 'Failed to delete task', 'error');
                                }
                            });
                    }
                });
            }
        });
    </script>

</body>

</html>