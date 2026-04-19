<?php
require_once '../config/bootstrap.php';

requireStudentLogin();

$page_title = 'Continuous Assessment';
$student_id = $_SESSION['user_id'];

// Get all semesters (for filter)
$semesters = dbGetAll("SELECT semester_id, name, year FROM semester ORDER BY year DESC, semester_id DESC");

// Get current semester ID
$current_semester = getCurrentSemester();
$current_semester_id = $current_semester ? $current_semester['semester_id'] : null;

// Selected semester (default to current)
$selected_semester = isset($_GET['semester_id']) ? intval($_GET['semester_id']) : $current_semester_id;

// Fetch continuous assessment records for the selected semester
$assessments = [];
if ($selected_semester) {
    $assessments = dbGetAll("
        SELECT ca.*, c.course_code, c.course_title, c.credit
        FROM continuous_assessment ca
        JOIN course c ON ca.course_id = c.course_id
        WHERE ca.student_id = ? AND ca.semester_id = ?
        ORDER BY c.course_code
    ", [$student_id, $selected_semester]);
}

// Get semester name for display
$semester_info = $selected_semester ? getSemesterName($selected_semester) : '';
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Continuous Assessment</h2>
    <p class="text-muted">View your ongoing assessment marks (quiz, mid, assignment, project, final exam)</p>
</div>

<!-- Semester Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Select Semester:</label>
            </div>
            <div class="col-auto">
                <select name="semester_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Semester --</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?php echo $sem['semester_id']; ?>" 
                            <?php echo $selected_semester == $sem['semester_id'] ? 'selected' : ''; ?>>
                            <?php echo $sem['name'] . ' ' . $sem['year']; ?>
                            <?php echo $sem['semester_id'] == $current_semester_id ? '(Current)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <a href="continuous_assessment.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<?php if (!$selected_semester): ?>
    <div class="alert alert-info">Please select a semester to view assessments.</div>
<?php elseif (empty($assessments)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> No continuous assessment records found for <?php echo $semester_info; ?>.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-table"></i> Assessment Marks – <?php echo $semester_info; ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Quiz (25)</th>
                            <th>Mid (40)</th>
                            <th>Assignment (20)</th>
                            <th>Project (15)</th>
                            <th>Final (100)</th>
                            <th>Total (200)</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assessments as $ass): 
                            $total = $ass['quiz'] + $ass['mid'] + $ass['assignment'] + $ass['project'] + $ass['final_exam'];
                            $percentage = ($total / 200) * 100;
                            $badge_color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo $ass['course_code']; ?></strong><br>
                                <small><?php echo $ass['course_title']; ?></small>
                            </td>
                            <td class="text-center"><?php echo $ass['quiz']; ?> / 25</td>
                            <td class="text-center"><?php echo $ass['mid']; ?> / 40</td>
                            <td class="text-center"><?php echo $ass['assignment']; ?> / 20</td>
                            <td class="text-center"><?php echo $ass['project']; ?> / 15</td>
                            <td class="text-center"><?php echo $ass['final_exam']; ?> / 100</td>
                            <td class="text-center">
                                <strong><?php echo $total; ?></strong> / 200
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php echo $badge_color; ?>" 
                                         style="width: <?php echo $percentage; ?>%;">
                                        <?php echo round($percentage, 1); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Note:</strong> Marks shown are subject to change until final results are published. The total is calculated automatically from components.
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>