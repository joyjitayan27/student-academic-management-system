<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'Student Dashboard';
$student_id = $_SESSION['user_id'];

// Get student information
$student = dbGetRow("
    SELECT s.*, d.department_name, a.teacher_name as advisor_name, 
           sem.name as semester_name, sem.year as semester_year
    FROM student s
    LEFT JOIN department d ON s.department_id = d.department_id
    LEFT JOIN advisor ad ON s.advisor_id = ad.advisor_id
    LEFT JOIN teacher a ON ad.teacher_id = a.teacher_id
    LEFT JOIN semester sem ON s.current_semester_id = sem.semester_id
    WHERE s.student_id = ?
", [$student_id]);

// Get statistics
$stats = getStudentStats($student_id);

// Get recent results (last 5)
$recent_results = dbGetAll("
    SELECT r.*, c.course_code, c.course_title, c.credit, sem.name as semester_name, sem.year
    FROM result r
    JOIN course c ON r.course_id = c.course_id
    JOIN semester sem ON r.semester_id = sem.semester_id
    WHERE r.student_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
", [$student_id]);

// Get recent attendance
$recent_attendance = dbGetAll("
    SELECT a.*, c.course_code, c.course_title
    FROM attendance a
    JOIN course c ON a.course_id = c.course_id
    WHERE a.student_id = ?
    ORDER BY a.date DESC
    LIMIT 5
", [$student_id]);

// Get pending applications
$pending_apps = dbGetAll("
    SELECT * FROM application 
    WHERE student_id = ? AND status IN ('pending', 'processing')
    ORDER BY created_at DESC
", [$student_id]);

// Get current semester courses
$current_semester = getCurrentSemester();
$current_courses = [];
if ($current_semester) {
    $current_courses = getRegisteredCourses($student_id, $current_semester['semester_id']);
}

// Chart data for CGPA trend
$cgpa_history = dbGetAll("
    SELECT 
        sem.name, sem.year,
        ROUND(AVG(r.gpa), 2) as semester_cgpa
    FROM result r
    JOIN semester sem ON r.semester_id = sem.semester_id
    WHERE r.student_id = ? AND r.gpa IS NOT NULL AND r.gpa > 0
    GROUP BY sem.semester_id, sem.name, sem.year
    ORDER BY sem.year, sem.semester_id
", [$student_id]);

$semester_labels = [];
$cgpa_values = [];
foreach ($cgpa_history as $cgpa) {
    $semester_labels[] = $cgpa['name'] . ' ' . $cgpa['year'];
    $cgpa_values[] = $cgpa['semester_cgpa'];
}
?>

<?php include '../includes/header.php'; ?>

<!-- Welcome Section -->
<div class="page-header fade-in">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($student['student_name']); ?>!</p>
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
            <i class="fas fa-book"></i>
            <h3 class="mt-2"><?php echo $stats['total_courses']; ?></h3>
            <p class="text-muted mb-0">Current Courses</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-check-circle"></i>
            <h3 class="mt-2"><?php echo $stats['courses_completed']; ?></h3>
            <p class="text-muted mb-0">Courses Completed</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-chart-line"></i>
            <h3 class="mt-2"><?php echo number_format($stats['cgpa'], 2); ?></h3>
            <p class="text-muted mb-0">Current CGPA</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-file-alt"></i>
            <h3 class="mt-2"><?php echo $stats['pending_applications']; ?></h3>
            <p class="text-muted mb-0">Pending Applications</p>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- CGPA Trend Chart -->
        <?php if (!empty($cgpa_history)): ?>
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> CGPA Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="cgpaChart" height="200"></canvas>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Current Semester Courses -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt text-primary"></i> Current Semester Courses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($current_courses)): ?>
                    <div class="alert alert-info">No courses registered for current semester.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Teacher</th>
                                    <th>Schedule</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($current_courses as $course): ?>
                                <tr>
                                    <td><strong><?php echo $course['course_code']; ?></strong></td>
                                    <td><?php echo $course['course_title']; ?></td>
                                    <td><?php echo $course['teacher_name']; ?></td>
                                    <td><?php echo $course['schedule'] ?? 'TBA'; ?></td>
                                    <td><?php echo $course['room'] ?? 'TBA'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Results -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-graduation-cap text-primary"></i> Recent Results</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_results)): ?>
                    <div class="alert alert-info">No results available yet.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Semester</th>
                                    <th>Course</th>
                                    <th>Grade</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_results as $result): ?>
                                <tr>
                                    <td><?php echo $result['semester_name'] . ' ' . $result['year']; ?></td>
                                    <td><?php echo $result['course_code'] . ' - ' . $result['course_title']; ?></td>
                                    <td><?php echo getGradeBadge($result['grade']); ?></td>
                                    <td><?php echo number_format($result['gpa'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="results.php" class="btn btn-sm btn-link">View All Results →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Profile Summary -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-circle text-primary"></i> Profile Summary</h5>
            </div>
            <div class="card-body text-center">
                <div class="profile-avatar mx-auto">
                    <i class="fas fa-user-graduate fa-3x"></i>
                </div>
                <h5 class="mt-3"><?php echo htmlspecialchars($student['student_name']); ?></h5>
                <p class="text-muted">Student ID: <?php echo $student['student_id']; ?></p>
                <hr>
                <div class="text-start">
                    <p><i class="fas fa-building"></i> <strong>Department:</strong> <?php echo $student['department_name']; ?></p>
                    <p><i class="fas fa-chalkboard-user"></i> <strong>Advisor:</strong> <?php echo $student['advisor_name'] ?? 'Not Assigned'; ?></p>
                    <p><i class="fas fa-calendar"></i> <strong>Batch:</strong> <?php echo $student['batch']; ?></p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo $student['email']; ?></p>
                    <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <?php echo $student['phone']; ?></p>
                </div>
                <a href="profile.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
        
        <!-- Pending Applications -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-clock text-warning"></i> Pending Applications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pending_apps)): ?>
                    <p class="text-muted text-center mb-0">No pending applications</p>
                <?php else: ?>
                    <?php foreach ($pending_apps as $app): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <strong><?php echo ucfirst($app['application_type']); ?></strong>
                            <br>
                            <small class="text-muted">Requested: <?php echo formatDate($app['request_date']); ?></small>
                        </div>
                        <div>
                            <?php echo getStatusBadge($app['status']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="applications.php" class="btn btn-primary btn-sm w-100 mt-2">
                    <i class="fas fa-plus"></i> New Application
                </a>
            </div>
        </div>
        
        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-calendar-check text-success"></i> Recent Attendance</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_attendance)): ?>
                    <p class="text-muted text-center mb-0">No attendance records yet</p>
                <?php else: ?>
                    <?php foreach ($recent_attendance as $att): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <strong><?php echo $att['course_code']; ?></strong>
                            <br>
                            <small><?php echo formatDate($att['date']); ?></small>
                        </div>
                        <div>
                            <?php 
                            $status_color = [
                                'present' => 'success',
                                'absent' => 'danger',
                                'late' => 'warning'
                            ];
                            $color = $status_color[$att['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo ucfirst($att['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="attendance.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    <i class="fas fa-chart-line"></i> View Full Attendance
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links Section (NEW) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bolt text-warning"></i> Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <a href="courses.php" class="text-decoration-none">
                            <i class="fas fa-book fa-2x text-primary mb-2 d-block"></i>
                            <span>My Courses</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="attendance.php" class="text-decoration-none">
                            <i class="fas fa-calendar-check fa-2x text-success mb-2 d-block"></i>
                            <span>Attendance</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="results.php" class="text-decoration-none">
                            <i class="fas fa-chart-line fa-2x text-info mb-2 d-block"></i>
                            <span>Results</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="continuous_assessment.php" class="text-decoration-none">
                            <i class="fas fa-chart-line fa-2x text-info mb-2 d-block"></i>
                            <span>Continuous Assessment</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="evaluations.php" class="text-decoration-none">
                            <i class="fas fa-star fa-2x text-warning mb-2 d-block"></i>
                            <span>Evaluate Teachers</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="applications.php" class="text-decoration-none">
                            <i class="fas fa-file-alt fa-2x text-danger mb-2 d-block"></i>
                            <span>Applications</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="advisor.php" class="text-decoration-none">
                            <i class="fas fa-chalkboard-user fa-2x text-secondary mb-2 d-block"></i>
                            <span>Advisor</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="clearance.php" class="text-decoration-none">
                            <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                            <span>Exam Clearance</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // CGPA Trend Chart
    <?php if (!empty($cgpa_history)): ?>
    const ctx = document.getElementById('cgpaChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($semester_labels); ?>,
            datasets: [{
                label: 'Semester CGPA',
                data: <?php echo json_encode($cgpa_values); ?>,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4,
                    title: {
                        display: true,
                        text: 'CGPA'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Semester'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `CGPA: ${context.raw.toFixed(2)}`;
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>