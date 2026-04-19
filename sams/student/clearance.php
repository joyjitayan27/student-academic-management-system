<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'Exam Clearance';
$student_id = $_SESSION['user_id'];

// Get current semester
$current_semester = getCurrentSemester();
$current_semester_id = $current_semester ? $current_semester['semester_id'] : null;

// Get clearance status for current semester
$clearance = null;
if ($current_semester_id) {
    $clearance = dbGetRow("
        SELECT * FROM exam_clearance 
        WHERE student_id = ? AND semester_id = ?
    ", [$student_id, $current_semester_id]);
}

// If no clearance record exists, create one with default values
if (!$clearance && $current_semester_id) {
    dbInsert("
        INSERT INTO exam_clearance (student_id, semester_id, library_clearance, lab_clearance, accounts_clearance)
        VALUES (?, ?, FALSE, FALSE, FALSE)
    ", [$student_id, $current_semester_id]);
    
    $clearance = dbGetRow("
        SELECT * FROM exam_clearance 
        WHERE student_id = ? AND semester_id = ?
    ", [$student_id, $current_semester_id]);
}

// Get clearance history
$clearance_history = dbGetAll("
    SELECT ec.*, s.name as semester_name, s.year
    FROM exam_clearance ec
    JOIN semester s ON ec.semester_id = s.semester_id
    WHERE ec.student_id = ?
    ORDER BY s.year DESC, s.semester_id DESC
", [$student_id]);

// Get attendance summary to check eligibility
$attendance_summary = [];
if ($current_semester_id) {
    $attendance_summary = dbGetAll("
        SELECT 
            c.course_code,
            c.course_title,
            COUNT(a.attendance_id) as total_classes,
            COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_classes,
            ROUND(100.0 * COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*), 2) as percentage
        FROM attendance a
        JOIN course c ON a.course_id = c.course_id
        WHERE a.student_id = ? AND a.semester_id = ?
        GROUP BY a.course_id
    ", [$student_id, $current_semester_id]);
}

// Calculate overall attendance
$overall_attendance = 0;
$total_classes = 0;
$present_classes = 0;
foreach ($attendance_summary as $att) {
    $total_classes += $att['total_classes'];
    $present_classes += $att['present_classes'];
}
$overall_attendance = $total_classes > 0 ? round(($present_classes / $total_classes) * 100, 2) : 100;

// Check if student is eligible for exams (attendance >= 75% and all clearances true)
$attendance_eligible = $overall_attendance >= 75;
$clearance_eligible = $clearance && $clearance['library_clearance'] && $clearance['lab_clearance'] && $clearance['accounts_clearance'];
$exam_eligible = $attendance_eligible && $clearance_eligible;
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-check-circle"></i> Exam Clearance Status</h2>
    <p class="text-muted">Check your eligibility for semester final examinations</p>
</div>

<!-- Overall Eligibility Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-center <?php echo $exam_eligible ? 'border-success' : 'border-danger'; ?>">
            <div class="card-body">
                <?php if ($exam_eligible): ?>
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h3 class="text-success">You are ELIGIBLE for Final Examinations</h3>
                    <p class="mb-0">All clearance requirements have been met. Good luck with your exams!</p>
                <?php else: ?>
                    <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                    <h3 class="text-danger">NOT ELIGIBLE for Final Examinations</h3>
                    <p class="mb-0">Please complete the pending requirements below.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Clearance Status Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-2x <?php echo ($clearance && $clearance['library_clearance']) ? 'text-success' : 'text-danger'; ?> mb-2"></i>
                <h5>Library Clearance</h5>
                <?php if ($clearance && $clearance['library_clearance']): ?>
                    <span class="badge bg-success">Cleared</span>
                    <p class="small text-muted mt-2">Cleared on: <?php echo formatDate($clearance['library_clearance_date']); ?></p>
                <?php else: ?>
                    <span class="badge bg-danger">Pending</span>
                    <p class="small text-muted mt-2">Return all library books and clear dues</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-flask fa-2x <?php echo ($clearance && $clearance['lab_clearance']) ? 'text-success' : 'text-danger'; ?> mb-2"></i>
                <h5>Lab Clearance</h5>
                <?php if ($clearance && $clearance['lab_clearance']): ?>
                    <span class="badge bg-success">Cleared</span>
                    <p class="small text-muted mt-2">Cleared on: <?php echo formatDate($clearance['lab_clearance_date']); ?></p>
                <?php else: ?>
                    <span class="badge bg-danger">Pending</span>
                    <p class="small text-muted mt-2">Return lab equipment and clear dues</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x <?php echo ($clearance && $clearance['accounts_clearance']) ? 'text-success' : 'text-danger'; ?> mb-2"></i>
                <h5>Accounts Clearance</h5>
                <?php if ($clearance && $clearance['accounts_clearance']): ?>
                    <span class="badge bg-success">Cleared</span>
                    <p class="small text-muted mt-2">Cleared on: <?php echo formatDate($clearance['accounts_clearance_date']); ?></p>
                <?php else: ?>
                    <span class="badge bg-danger">Pending</span>
                    <p class="small text-muted mt-2">Clear all financial dues</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Requirement -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-calendar-check text-primary"></i> Attendance Requirement (75% minimum)</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <div class="display-4 fw-bold <?php echo $attendance_eligible ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $overall_attendance; ?>%
                </div>
                <p>Overall Attendance</p>
            </div>
            <div class="col-md-8">
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar <?php echo $attendance_eligible ? 'bg-success' : 'bg-danger'; ?>" 
                         style="width: <?php echo min($overall_attendance, 100); ?>%">
                        <?php echo $overall_attendance; ?>%
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <small>Required: 75%</small>
                    </div>
                    <div class="col-6 text-center">
                        <small>Your Attendance: <?php echo $overall_attendance; ?>%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($attendance_summary)): ?>
        <hr>
        <h6>Course-wise Attendance Breakdown:</h6>
        <div class="table-responsive mt-3">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Present</th>
                        <th>Total</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_summary as $att): ?>
                    <tr>
                        <td><?php echo $att['course_code']; ?></td>
                        <td><?php echo $att['present_classes']; ?></td>
                        <td><?php echo $att['total_classes']; ?></td>
                        <td><?php echo $att['percentage']; ?>%</td>
                        <td>
                            <?php if ($att['percentage'] >= 75): ?>
                                <span class="badge bg-success">OK</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Shortage</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Clearance History -->
<?php if (count($clearance_history) > 1): ?>
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Previous Semesters Clearance History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Library</th>
                        <th>Lab</th>
                        <th>Accounts</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clearance_history as $ch): ?>
                    <tr>
                        <td><?php echo $ch['semester_name'] . ' ' . $ch['year']; ?></td>
                        <td><?php echo $ch['library_clearance'] ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                        <td><?php echo $ch['lab_clearance'] ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                        <td><?php echo $ch['accounts_clearance'] ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                        <td><?php echo getStatusBadge($ch['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Instructions -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle text-info"></i> How to Get Clearance</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Visit the library to return all books and clear fines</li>
                    <li>Return all lab equipment to respective departments</li>
                    <li>Clear all financial dues at the accounts office</li>
                    <li>Clearance is automatically updated after verification</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Important Notes</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Exam entry requires ALL three clearances</li>
                    <li>Minimum 75% attendance is mandatory</li>
                    <li>Clearance must be completed before the exam schedule</li>
                    <li>Contact respective offices for any discrepancies</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>