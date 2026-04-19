<?php
require_once '../config/bootstrap.php';
// Require student login
requireStudentLogin();

$page_title = 'My Courses';
$student_id = $_SESSION['user_id'];

// Get current semester
$current_semester = getCurrentSemester();
$current_semester_id = $current_semester ? $current_semester['semester_id'] : null;

// Get all semesters for filtering
$semesters = dbGetAll("SELECT semester_id, name, year FROM semester ORDER BY year DESC, semester_id DESC");

// Get selected semester (default to current)
$selected_semester = isset($_GET['semester_id']) ? intval($_GET['semester_id']) : $current_semester_id;

// Get registered courses for selected semester
$registered_courses = [];
if ($selected_semester) {
    $registered_courses = getRegisteredCourses($student_id, $selected_semester);
}

// Get semester details
$semester_info = dbGetRow("SELECT name, year FROM semester WHERE semester_id = ?", [$selected_semester]);

// Get course-wise attendance for current semester
$attendance_data = [];
if ($current_semester_id && $selected_semester == $current_semester_id) {
    foreach ($registered_courses as $course) {
        $attendance_data[$course['course_id']] = getAttendancePercentage($student_id, $course['course_id'], $current_semester_id);
    }
}

// Get results for selected semester
$results = dbGetAll("
    SELECT r.*, c.course_code, c.course_title, c.credit
    FROM result r
    JOIN course c ON r.course_id = c.course_id
    WHERE r.student_id = ? AND r.semester_id = ?
    ORDER BY c.course_code
", [$student_id, $selected_semester]);

// Calculate semester GPA
$semester_gpa = 0;
$total_credits = 0;
$earned_points = 0;
foreach ($results as $result) {
    if ($result['gpa'] > 0) {
        $earned_points += $result['gpa'] * $result['credit'];
        $total_credits += $result['credit'];
    }
}
$semester_gpa = $total_credits > 0 ? round($earned_points / $total_credits, 2) : 0;
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-book"></i> My Courses</h2>
            <p class="text-muted mb-0">View your registered courses and academic progress</p>
        </div>
        <div class="col-md-6">
            <form method="GET" action="" class="row g-2">
                <div class="col-auto">
                    <label class="col-form-label">Select Semester:</label>
                </div>
                <div class="col">
                    <select name="semester_id" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem['semester_id']; ?>" 
                                <?php echo $selected_semester == $sem['semester_id'] ? 'selected' : ''; ?>>
                                <?php echo $sem['name'] . ' ' . $sem['year']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($semester_info): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 
    Showing courses for <strong><?php echo $semester_info['name'] . ' ' . $semester_info['year']; ?></strong>
    <?php if ($selected_semester == $current_semester_id): ?>
        <span class="badge bg-success ms-2">Current Semester</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Registered Courses -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list-check text-primary"></i> Registered Courses</h5>
    </div>
    <div class="card-body">
        <?php if (empty($registered_courses)): ?>
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                <p class="mb-0">No courses registered for this semester.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit</th>
                            <th>Teacher</th>
                            <th>Section</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <?php if ($selected_semester == $current_semester_id): ?>
                            <th>Attendance</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registered_courses as $course): ?>
                        <tr>
                            <td><strong><?php echo $course['course_code']; ?></strong></td>
                            <td><?php echo $course['course_title']; ?></td>
                            <td><?php echo $course['credit']; ?></td>
                            <td><?php echo $course['teacher_name']; ?></td>
                            <td><?php echo $course['section']; ?></td>
                            <td><?php echo $course['schedule'] ?? 'TBA'; ?></td>
                            <td><?php echo $course['room'] ?? 'TBA'; ?></td>
                            <?php if ($selected_semester == $current_semester_id): ?>
                            <td>
                                <?php 
                                $att_percent = $attendance_data[$course['course_id']] ?? 0;
                                $badge_color = $att_percent >= 80 ? 'success' : ($att_percent >= 60 ? 'warning' : 'danger');
                                ?>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php echo $badge_color; ?>" 
                                         style="width: <?php echo $att_percent; ?>%"
                                         role="progressbar">
                                        <?php echo $att_percent; ?>%
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Results for Selected Semester -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-graduation-cap text-success"></i> Semester Results</h5>
    </div>
    <div class="card-body">
        <?php if (empty($results)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                <p class="mb-0">Results not published yet for this semester.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit</th>
                            <th>Grade</th>
                            <th>GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_points = 0;
                        $total_credits_result = 0;
                        foreach ($results as $result): 
                            if ($result['gpa'] > 0) {
                                $total_points += $result['gpa'] * $result['credit'];
                                $total_credits_result += $result['credit'];
                            }
                        ?>
                        <tr>
                            <td><?php echo $result['course_code']; ?></td>
                            <td><?php echo $result['course_title']; ?></td>
                            <td class="text-center"><?php echo $result['credit']; ?></td>
                            <td class="text-center"><?php echo getGradeBadge($result['grade']); ?></td>
                            <td class="text-center"><?php echo number_format($result['gpa'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Total / GPA:</th>
                            <th class="text-center"><?php echo $total_credits_result; ?></th>
                            <th colspan="2" class="text-center">
                                <?php 
                                $sem_gpa = $total_credits_result > 0 ? round($total_points / $total_credits_result, 2) : 0;
                                echo number_format($sem_gpa, 2);
                                ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Navigation -->
<div class="row">
    <div class="col-md-4 mb-3">
        <a href="attendance.php" class="text-decoration-none">
            <div class="card text-center dashboard-card">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-2"></i>
                    <h6 class="mb-0">View Full Attendance</h6>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="results.php" class="text-decoration-none">
            <div class="card text-center dashboard-card">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x text-success mb-2"></i>
                    <h6 class="mb-0">View Complete Results</h6>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="advisor.php" class="text-decoration-none">
            <div class="card text-center dashboard-card">
                <div class="card-body">
                    <i class="fas fa-chalkboard-user fa-3x text-info mb-2"></i>
                    <h6 class="mb-0">Contact Advisor</h6>
                </div>
            </div>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>