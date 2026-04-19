<?php
require_once '../config/bootstrap.php';

// Require admin login
requireAdminLogin();

$page_title = 'Admin Dashboard';
$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role_name'];

// Get statistics
$stats = getAdminStats();

// Get recent students
$recent_students = dbGetAll("
    SELECT student_id, student_name, email, department_id, created_at 
    FROM student 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Get recent applications
$recent_apps = dbGetAll("
    SELECT a.*, s.student_name 
    FROM application a
    JOIN student s ON a.student_id = s.student_id
    WHERE a.status = 'pending'
    ORDER BY a.created_at DESC
    LIMIT 5
");

// Get department-wise student count
$dept_stats = dbGetAll("
    SELECT d.department_name, COUNT(s.student_id) as student_count
    FROM department d
    LEFT JOIN student s ON d.department_id = s.department_id AND s.status = 'active'
    GROUP BY d.department_id
    ORDER BY student_count DESC
");

// Get monthly registrations for chart
$monthly_reg = dbGetAll("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as count
    FROM student
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

$months = [];
$reg_counts = [];
foreach ($monthly_reg as $m) {
    $months[] = date('M Y', strtotime($m['month'] . '-01'));
    $reg_counts[] = $m['count'];
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['admin_name']; ?> (<?php echo $admin_role; ?>)</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-primary fs-6 p-2">
                <i class="fas fa-calendar-alt"></i> <?php echo date('l, d M Y'); ?>
            </span>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-users fa-2x text-primary"></i>
            <h3 class="mt-2"><?php echo $stats['total_students']; ?></h3>
            <p class="text-muted mb-0">Active Students</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-chalkboard-user fa-2x text-success"></i>
            <h3 class="mt-2"><?php echo $stats['total_teachers']; ?></h3>
            <p class="text-muted mb-0">Teachers</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-book fa-2x text-info"></i>
            <h3 class="mt-2"><?php echo $stats['total_courses']; ?></h3>
            <p class="text-muted mb-0">Active Courses</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-file-alt fa-2x text-warning"></i>
            <h3 class="mt-2"><?php echo $stats['pending_applications']; ?></h3>
            <p class="text-muted mb-0">Pending Applications</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Monthly Registrations Chart -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Student Registrations (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="regChart" height="200"></canvas>
            </div>
        </div>
        
        <!-- Recent Students -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-plus text-primary"></i> Recent Student Registrations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_students as $s): ?>
                            <tr>
                                <td><?php echo $s['student_id']; ?></td>
                                <td><?php echo htmlspecialchars($s['student_name']); ?></td>
                                <td><?php echo $s['email']; ?></td>
                                <td><?php echo getDepartmentName($s['department_id']); ?></td>
                                <td><?php echo formatDate($s['created_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <a href="students.php" class="btn btn-sm btn-link">View All Students →</a>
                </div>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-clock text-warning"></i> Pending Applications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_apps)): ?>
                    <p class="text-muted text-center mb-0">No pending applications</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_apps as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                    <td><?php echo ucfirst($app['application_type']); ?></td>
                                    <td><?php echo formatDate($app['request_date']); ?></td>
                                    <td><?php echo getStatusBadge($app['status']); ?></td>
                                    <td>
                                        <a href="applications.php" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="applications.php" class="btn btn-sm btn-link">View All Applications →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Department Distribution -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-building text-primary"></i> Students by Department</h5>
            </div>
            <div class="card-body">
                <canvas id="deptChart" height="200"></canvas>
                <div class="mt-3">
                    <?php foreach ($dept_stats as $dept): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo $dept['department_name']; ?></span>
                        <span class="badge bg-secondary"><?php echo $dept['student_count']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bolt text-warning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="students.php?action=add" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </a>
                    <a href="teachers.php?action=add" class="btn btn-outline-success">
                        <i class="fas fa-chalkboard-user"></i> Add New Teacher
                    </a>
                    <a href="courses.php?action=add" class="btn btn-outline-info">
                        <i class="fas fa-book"></i> Add New Course
                    </a>
                    <a href="applications.php" class="btn btn-outline-warning">
                        <i class="fas fa-file-alt"></i> Process Applications
                    </a>
                    <?php if (hasAdminPermission('manage_admins')): ?>
                    <a href="admins.php" class="btn btn-outline-danger">
                        <i class="fas fa-user-shield"></i> Manage Admins
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Registration Chart
<?php if (!empty($monthly_reg)): ?>
const regCtx = document.getElementById('regChart').getContext('2d');
new Chart(regCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'New Registrations',
            data: <?php echo json_encode($reg_counts); ?>,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
<?php endif; ?>

// Department Chart
<?php if (!empty($dept_stats)): ?>
const deptCtx = document.getElementById('deptChart').getContext('2d');
const deptLabels = <?php echo json_encode(array_column($dept_stats, 'department_name')); ?>;
const deptData = <?php echo json_encode(array_column($dept_stats, 'student_count')); ?>;
new Chart(deptCtx, {
    type: 'pie',
    data: {
        labels: deptLabels,
        datasets: [{
            data: deptData,
            backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>