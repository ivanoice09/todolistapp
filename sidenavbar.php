<!-- sweetalert link -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="css/sidenavbar.css" rel="stylesheet">
<div>
    <!-- button to open offcanvas (menu sidebar) -->
    <button class="btn btn-secondary m-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
        <i class="bi bi-list"></i>
    </button>

    <!-- offcanvas sidemenu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">

        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item"> <!-- Add "dropdown" class here -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person"></i> Account
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="#">Activity log</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" id="logoutLink">Logout</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" aria-current="page" data-bs-toggle="modal" data-bs-target="#taskModal">
                    <i class="bi bi-plus-circle"></i> New task
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-search"></i> Filter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inbox.php">
                    <i class="bi bi-inbox"></i>
                    <span class="menu-text">Inbox</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="today.php">
                    <i class="bi bi-calendar"></i> Today
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="upcoming.php">
                    <i class="bi bi-calendar-week"></i> Upcoming
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="calendar.php">
                    <i class="bi bi-calendar-month"></i> Calendar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="completed.php">
                    <i class="bi bi-check-circle"></i> Completed
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-list-task"></i> Lists
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Invoke function from sweetalerts.js -->
<script src="js/logout_sweetalert.js"></script>