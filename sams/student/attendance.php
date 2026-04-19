<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'My Attendance';
$student_id = $_SESSION['user_id'];

// Get current semester
$current_semester = getCurrentSemester();
$current_semester_id = $current_semester ? $current_semester['semester_id'] : null;

// Get all semesters
$semesters = dbGetAll("SELECT semester_id, name, year FROM semester ORDER BY year DESC, semester_id DESC");

// Get selected semester (default to current)
$selected_semester = isset($_GET['semester_id']) ? intval($_GET['semester_id']) : $current_semester_id;

// Get courses for selected semester
$courses = [];
if ($selected_semester) {
    $courses = getRegisteredCourses($student_id, $selected_semester);
}

// Get attendance summary for each course
$attendance_summary = [];
$overall_present = 0;
$overall_total = 0;

foreach ($courses as $course) {
    $sql = "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late
            FROM attendance 
            WHERE student_id = ? AND course_id = ? AND semester_id = ?";
    $stats = dbGetRow($sql, [$student_id, $course['course_id'], $selected_semester]);
    
    $attendance_summary[$course['course_id']] = [
        'total' => $stats['total'],
        'present' => $stats['present'],
        'absent' => $stats['absent'],
        'late' => $stats['late'],
        'percentage' => $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0
    ];
    
    $overall_present += $stats['present'];
    $overall_total += $stats['total'];
}

$overall_percentage = $overall_total > 0 ? round(($overall_present / $overall_total) * 100, 2) : 0;

// Get detailed attendance records for selected course (if specified)
$selected_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;
$attendance_records = [];
$selected_course_info = null;

if ($selected_course_id && $selected_semester) {
    $selected_course_info = dbGetRow("SELECT course_code, course_title FROM course WHERE course_id = ?", [$selected_course_id]);
    $attendance_records = dbGetAll("
        SELECT * FROM attendance 
        WHERE student_id = ? AND course_id = ? AND semester_id = ?
        ORDER BY date DESC
    ", [$student_id, $selected_course_id, $selected_semester]);
}

// Get monthly attendance summary for chart
$monthly_attendance = dbGetAll("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'present' THEN 1 END) as present
    FROM attendance
    WHERE student_id = ? AND semester_id = ?
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month
", [$student_id, $selected_semester]);

$month_labels = [];
$month_percentages = [];
foreach ($monthly_attendance as $month) {
    $month_labels[] = date('M Y', strtotime($month['month'] . '-01'));
    $month_percentages[] = $month['total'] > 0 ? round(($month['present'] / $month['total']) * 100, 2) : 0;
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-calendar-check"></i> My Attendance</h2>
            <p class="text-muted mb-0">Track your class attendance percentage</p>
        </div>
        <div class="col-md-6">
            <form method="GET" action="" class="row g-2 justify-content-end">
                <div class="col-auto">
                    <label class="col-form-label">Select Semester:</label>
                </div>
                <div class="col-auto">
                    <select name="semester_id" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem['semester_id']; ?>" 
                                <?php echo $selected_semester == $sem['semester_id'] ? 'selected' : ''; ?>>
                                <?php echo $sem['name'] . ' ' . $sem['year']; ?>
                                <?php echo $sem['semester_id'] == $current_semester_id ? '(Current)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($selected_semester && $current_semester_id && $selected_semester == $current_semester_id): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i> 
    <strong>Important:</strong> Maintaining at least 75% attendance is mandatory for appearing in final examinations.
    <?php if ($overall_percentage < 75): ?>
        <span class="badge bg-danger">Your attendance is below 75%!</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Overall Attendance Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-center">
            <div class="card-body">
                <h4>Overall Attendance for <?php echo $selected_semester ? getSemesterName($selected_semester) : 'Selected Semester'; ?></h4>
                <div class="row align-items-center mt-3">
                    <div class="col-md-4">
                        <div class="display-1 fw-bold <?php echo $overall_percentage >= 75 ? 'text-success' : ($overall_percentage >= 60 ? 'text-warning' : 'text-danger'); ?>">
                            <?php echo $overall_percentage; ?>%
                        </div>
                        <p class="text-muted">Overall Attendance</p>
                    </div>
                    <div class="col-md-8">
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-<?php echo $overall_percentage >= 75 ? 'success' : ($overall_percentage >= 60 ? 'warning' : 'danger'); ?>" 
                                 style="width: <?php echo $overall_percentage; ?>%">
                                <?php echo $overall_percentage; ?>%
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <small class="text-success">Present: <?php echo $overall_present; ?></small>
                            </div>
                            <div class="col-4">
                                <small class="text-warning">Total Classes: <?php echo $overall_total; ?></small>
                            </div>
                            <div class="col-4">
                                <small class="text-danger">Required: 75%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Attendance Chart -->
<?php if (!empty($monthly_attendance)): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-chart-bar text-primary"></i> Monthly Attendance Trend</h5>
    </div>
    <div class="card-body">
        <canvas id="monthlyChart" height="100"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Course-wise Attendance -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-book text-primary"></i> Course-wise Attendance</h5>
    </div>
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                <p class="mb-0">No courses registered for this semester.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Total</th>
                            <th>Attendance %</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): 
                            $stats = $attendance_summary[$course['course_id']];
                            $status_color = $stats['percentage'] >= 75 ? 'success' : ($stats['percentage'] >= 60 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><strong><?php echo $course['course_code']; ?></strong></td>
                            <td><?php echo $course['course_title']; ?></td>
                            <td class="text-center"><?php echo $stats['present']; ?></td>
                            <td class="text-center"><?php echo $stats['absent']; ?></td>
                            <td class="text-center"><?php echo $stats['late']; ?></td>
                            <td class="text-center"><?php echo $stats['total']; ?></td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php echo $status_color; ?>" 
                                         style="width: <?php echo $stats['percentage']; ?>%">
                                        <?php echo $stats['percentage']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($stats['percentage'] >= 75): ?>
                                    <span class="badge bg-success">Good</span>
                                <?php elseif ($stats['percentage'] >= 60): ?>
                                    <span class="badge bg-warning">Warning</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Critical</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="?semester_id=<?php echo $selected_semester; ?>&course_id=<?php echo $course['course_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-list"></i> Details
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detailed Attendance Records for Selected Course -->
<?php if ($selected_course_id && $selected_course_info && !empty($attendance_records)): ?>
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-calendar-alt text-primary"></i> 
            Detailed Attendance: <?php echo $selected_course_info['course_code'] . ' - ' . $selected_course_info['course_title']; ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo formatDate($record['date']); ?></td>
                        <td>
                            <?php 
                            $status_badge = [
                                'present' => 'success',
                                'absent' => 'danger',
                                'late' => 'warning'
                            ];
                            ?>
                            <span class="badge bg-<?php echo $status_badge[$record['status']]; ?>">
                                <i class="fas fa-<?php echo $record['status'] == 'present' ? 'check' : ($record['status'] == 'absent' ? 'times' : 'clock'); ?>"></i>
                                <?php echo ucfirst($record['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $record['remarks'] ?? '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="attendance.php?semester_id=<?php echo $selected_semester; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Course List
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Attendance Rules -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-gavel text-warning"></i> Attendance Rules</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Minimum 75% attendance required for exam eligibility</li>
                    <li>Students with less than 60% attendance may be restricted from exams</li>
                    <li>Late arrivals (after 15 minutes) are marked as 'Late'</li>
                    <li>Medical emergencies require valid documentation</li>
                    <li>Attendance is calculated on a per-course basis</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-simple text-info"></i> How to Improve Attendance</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Set reminders for class timings</li>
                    <li>Plan your commute in advance</li>
                    <li>Inform faculty in case of emergency</li>
                    <li>Submit medical certificates for sick days</li>
                    <li>Track your attendance weekly</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($monthly_attendance)): ?>
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($month_labels); ?>,
        datasets: [{
            label: 'Attendance Percentage',
            data: <?php echo json_encode($month_percentages); ?>,
            backgroundColor: 'rgba(52, 152, 219, 0.5)',
            borderColor: '#3498db',
            borderWidth: 2,
            borderRadius: 5
        }, {
            label: 'Target (75%)',
            data: Array(<?php echo count($month_labels); ?>).fill(75),
            type: 'line',
            borderColor: '#e74c3c',
            borderWidth: 2,
            borderDash: [5, 5],
            fill: false,
            pointRadius: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Percentage (%)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Month'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw}%`;
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>