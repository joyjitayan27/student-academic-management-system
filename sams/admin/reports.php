<?php
require_once '../config/bootstrap.php';

requireAdminLogin();

$page_title = 'Reports';
$report_type = $_GET['type'] ?? 'students';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-t');
$format = $_GET['format'] ?? 'html';

// Fetch data based on report type
$report_data = [];
$report_title = '';

switch ($report_type) {
    case 'students':
        $report_title = 'Student List Report';
        $report_data = dbGetAll("
            SELECT s.student_id, s.student_name, s.email, s.phone, s.batch, 
                   d.department_name, s.status, s.admission_date
            FROM student s
            LEFT JOIN department d ON s.department_id = d.department_id
            ORDER BY s.student_id
        ");
        break;
        
    case 'teachers':
        $report_title = 'Teacher List Report';
        $report_data = dbGetAll("
            SELECT t.teacher_id, t.teacher_name, t.email, t.phone, t.designation,
                   d.department_name, t.joining_date
            FROM teacher t
            LEFT JOIN department d ON t.department_id = d.department_id
            ORDER BY t.teacher_id
        ");
        break;
        
    case 'courses':
        $report_title = 'Course List Report';
        $report_data = dbGetAll("
            SELECT c.course_code, c.course_title, c.credit, d.department_name, 
                   c.is_active, c.created_at
            FROM course c
            LEFT JOIN department d ON c.department_id = d.department_id
            ORDER BY c.course_code
        ");
        break;
        
    case 'applications':
        $report_title = 'Applications Report (' . date('d M Y', strtotime($date_from)) . ' - ' . date('d M Y', strtotime($date_to)) . ')';
        $report_data = dbGetAll("
            SELECT a.application_id, a.application_type, a.request_date, a.status,
                   s.student_name, s.student_id
            FROM application a
            JOIN student s ON a.student_id = s.student_id
            WHERE a.request_date BETWEEN ? AND ?
            ORDER BY a.request_date DESC
        ", [$date_from, $date_to]);
        break;
        
    case 'attendance':
        $report_title = 'Attendance Summary Report';
        $report_data = dbGetAll("
            SELECT s.student_name, s.student_id, c.course_code, 
                   COUNT(a.attendance_id) as total,
                   COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present,
                   ROUND(100 * COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*), 2) as percentage
            FROM attendance a
            JOIN student s ON a.student_id = s.student_id
            JOIN course c ON a.course_id = c.course_id
            GROUP BY a.student_id, a.course_id
            ORDER BY percentage ASC
        ");
        break;
        
    case 'results':
        $report_title = 'Results Summary Report';
        $report_data = dbGetAll("
            SELECT s.student_name, s.student_id, c.course_code, r.grade, r.gpa,
                   sem.name as semester_name, sem.year
            FROM result r
            JOIN student s ON r.student_id = s.student_id
            JOIN course c ON r.course_id = c.course_id
            JOIN semester sem ON r.semester_id = sem.semester_id
            ORDER BY sem.year DESC, sem.semester_id DESC, s.student_id
        ");
        break;
}

// Export to CSV
if ($format == 'csv' && !empty($report_data)) {
    $filename = $report_type . '_report_' . date('Y-m-d');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    $output = fopen('php://output', 'w');
    
    // Add headers
    if (!empty($report_data)) {
        fputcsv($output, array_keys($report_data[0]));
    }
    
    // Add data
    foreach ($report_data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Reports</h2>
    <p class="text-muted">Generate and export system reports</p>
</div>

<!-- Report Filters -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Report Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="students" <?php echo $report_type == 'students' ? 'selected' : ''; ?>>Student List</option>
                    <option value="teachers" <?php echo $report_type == 'teachers' ? 'selected' : ''; ?>>Teacher List</option>
                    <option value="courses" <?php echo $report_type == 'courses' ? 'selected' : ''; ?>>Course List</option>
                    <option value="applications" <?php echo $report_type == 'applications' ? 'selected' : ''; ?>>Applications</option>
                    <option value="attendance" <?php echo $report_type == 'attendance' ? 'selected' : ''; ?>>Attendance Summary</option>
                    <option value="results" <?php echo $report_type == 'results' ? 'selected' : ''; ?>>Results Summary</option>
                </select>
            </div>
            <?php if ($report_type == 'applications'): ?>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generate</button>
                <?php if (!empty($report_data)): ?>
                <a href="?type=<?php echo $report_type; ?>&format=csv&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                   class="btn btn-success ms-2">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Report Results -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-table"></i> <?php echo $report_title; ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($report_data)): ?>
            <div class="alert alert-info">No data found for the selected criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <?php foreach (array_keys($report_data[0]) as $column): ?>
                                <th><?php echo ucwords(str_replace('_', ' ', $column)); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <small class="text-muted">Total records: <?php echo count($report_data); ?></small>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>