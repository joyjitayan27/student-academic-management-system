<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #34495e;
            color: white;
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            padding: 20px;
            background: #f5f6fa;
            min-height: calc(100vh - 56px);
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card i {
            font-size: 40px;
            color: #3498db;
        }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .footer {
            background: white;
            padding: 15px;
            text-align: center;
            margin-top: 30px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
<!-- Navbar for logged-in users -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
            <i class="fas fa-graduation-cap"></i> <?php echo APP_SHORT_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isStudentLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>student/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>student/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">No new notifications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="<?php echo BASE_URL; ?>auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                <?php elseif (isAdminLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/students.php">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/teachers.php">
                            <i class="fas fa-chalkboard-user"></i> Teachers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/courses.php">
                            <i class="fas fa-book"></i> Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/applications.php">
                            <i class="fas fa-file-alt"></i> Applications
                        </a>
                    </li>
                    <?php if (hasAdminPermission('manage_admins')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>admin/admins.php">
                            <i class="fas fa-user-shield"></i> Admins
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-cog"></i> <?php echo $_SESSION['admin_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text"><small>Role: <?php echo $_SESSION['admin_role_name']; ?></small></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/change-password.php">Change Password</a></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar">
            <div class="nav flex-column">
                <?php if (isStudentLoggedIn()): ?>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/profile.php">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/courses.php">
                        <i class="fas fa-book"></i> My Courses
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/results.php">
                        <i class="fas fa-chart-line"></i> Results & CGPA
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/attendance.php">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                    <!-- NEW: Continuous Assessment Link -->
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'continuous_assessment.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/continuous_assessment.php">
                        <i class="fas fa-chart-line"></i> Continuous Assessment
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'advisor.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/advisor.php">
                        <i class="fas fa-chalkboard-user"></i> Advisor Info
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'evaluations.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/evaluations.php">
                        <i class="fas fa-star"></i> Teacher Evaluation
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/applications.php">
                        <i class="fas fa-file-alt"></i> Applications
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'clearance.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/clearance.php">
                        <i class="fas fa-check-circle"></i> Exam Clearance
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>student/settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                <?php elseif (isAdminLoggedIn()): ?>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/students.php">
                        <i class="fas fa-users"></i> Manage Students
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/teachers.php">
                        <i class="fas fa-chalkboard-user"></i> Manage Teachers
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/courses.php">
                        <i class="fas fa-book"></i> Manage Courses
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/applications.php">
                        <i class="fas fa-file-alt"></i> Applications
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <?php if (hasAdminPermission('manage_admins')): ?>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/admins.php">
                        <i class="fas fa-user-shield"></i> Manage Admins
                    </a>
                    <?php endif; ?>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/logs.php">
                        <i class="fas fa-history"></i> Activity Logs
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 main-content">
<?php else: ?>
<!-- Navbar for guests -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
            <i class="fas fa-graduation-cap"></i> <?php echo APP_SHORT_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>auth/login.php">
                        <i class="fas fa-sign-in-alt"></i> Student Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>auth/admin-login.php">
                        <i class="fas fa-user-shield"></i> Admin Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>auth/register.php">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
<?php endif; ?>