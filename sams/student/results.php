<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'My Results';
$student_id = $_SESSION['user_id'];

// Get student info
$student = dbGetRow("SELECT student_name, student_id, department_id FROM student WHERE student_id = ?", [$student_id]);

// Get all semesters with results
$semesters_with_results = dbGetAll("
    SELECT DISTINCT s.semester_id, s.name, s.year
    FROM result r
    JOIN semester s ON r.semester_id = s.semester_id
    WHERE r.student_id = ?
    ORDER BY s.year, s.semester_id
", [$student_id]);

// Get selected semester (default to latest)
$selected_semester = isset($_GET['semester_id']) ? intval($_GET['semester_id']) : 
                     (!empty($semesters_with_results) ? $semesters_with_results[0]['semester_id'] : null);

// Get results for selected semester
$semester_results = [];
$semester_info = null;
$semester_gpa = 0;
$total_credits_semester = 0;
$earned_points = 0;

if ($selected_semester) {
    $semester_info = dbGetRow("SELECT name, year FROM semester WHERE semester_id = ?", [$selected_semester]);
    
    $semester_results = dbGetAll("
        SELECT r.*, c.course_code, c.course_title, c.credit
        FROM result r
        JOIN course c ON r.course_id = c.course_id
        WHERE r.student_id = ? AND r.semester_id = ?
        ORDER BY c.course_code
    ", [$student_id, $selected_semester]);
    
    // Calculate semester GPA
    foreach ($semester_results as $result) {
        if ($result['gpa'] > 0) {
            $earned_points += $result['gpa'] * $result['credit'];
            $total_credits_semester += $result['credit'];
        }
    }
    $semester_gpa = $total_credits_semester > 0 ? round($earned_points / $total_credits_semester, 2) : 0;
}

// Get overall CGPA and summary
$overall_stats = dbGetRow("
    SELECT 
        COALESCE(ROUND(AVG(r.gpa), 2), 0) as cgpa,
        COUNT(DISTINCT CASE WHEN r.gpa IS NOT NULL AND r.gpa > 0 THEN r.course_id END) as courses_passed,
        COUNT(DISTINCT r.course_id) as courses_taken,
        COALESCE(SUM(c.credit), 0) as total_credits
    FROM result r
    JOIN course c ON r.course_id = c.course_id
    WHERE r.student_id = ? AND r.gpa IS NOT NULL AND r.gpa > 0
", [$student_id]);

// Get all results for transcript
$all_results = dbGetAll("
    SELECT r.*, c.course_code, c.course_title, c.credit, s.name as semester_name, s.year
    FROM result r
    JOIN course c ON r.course_id = c.course_id
    JOIN semester s ON r.semester_id = s.semester_id
    WHERE r.student_id = ?
    ORDER BY s.year, s.semester_id, c.course_code
", [$student_id]);

// Calculate semester-wise CGPA for chart
$semester_cgpa_data = dbGetAll("
    SELECT 
        s.name,
        s.year,
        s.semester_id,
        ROUND(AVG(r.gpa), 2) as semester_cgpa,
        COUNT(r.course_id) as courses_count
    FROM result r
    JOIN semester s ON r.semester_id = s.semester_id
    WHERE r.student_id = ? AND r.gpa IS NOT NULL AND r.gpa > 0
    GROUP BY s.semester_id, s.name, s.year
    ORDER BY s.year, s.semester_id
", [$student_id]);

$semester_labels = [];
$semester_gpas = [];
foreach ($semester_cgpa_data as $data) {
    $semester_labels[] = $data['name'] . ' ' . $data['year'];
    $semester_gpas[] = $data['semester_cgpa'];
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-chart-line"></i> My Results</h2>
            <p class="text-muted mb-0">View your academic performance and CGPA history</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Results
            </button>
        </div>
    </div>
</div>

<!-- Overall CGPA Card -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center bg-primary text-white">
            <i class="fas fa-graduation-cap"></i>
            <h3 class="mt-2 text-white"><?php echo number_format($overall_stats['cgpa'], 2); ?></h3>
            <p class="mb-0">Current CGPA</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center bg-success text-white">
            <i class="fas fa-check-circle"></i>
            <h3 class="mt-2 text-white"><?php echo $overall_stats['courses_passed']; ?></h3>
            <p class="mb-0">Courses Passed</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center bg-info text-white">
            <i class="fas fa-book"></i>
            <h3 class="mt-2 text-white"><?php echo $overall_stats['courses_taken']; ?></h3>
            <p class="mb-0">Courses Taken</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center bg-warning text-white">
            <i class="fas fa-star"></i>
            <h3 class="mt-2 text-white"><?php echo $overall_stats['total_credits']; ?></h3>
            <p class="mb-0">Total Credits</p>
        </div>
    </div>
</div>

<!-- CGPA Trend Chart -->
<?php if (!empty($semester_cgpa_data)): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> CGPA Trend</h5>
    </div>
    <div class="card-body">
        <canvas id="cgpaChart" height="100"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Semester Results -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-calendar-alt text-primary"></i> Semester Results</h5>
            </div>
            <div class="col-md-6">
                <form method="GET" action="" class="row g-2 justify-content-end">
                    <div class="col-auto">
                        <label class="col-form-label">Select Semester:</label>
                    </div>
                    <div class="col-auto">
                        <select name="semester_id" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($semesters_with_results as $sem): ?>
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
    <div class="card-body">
        <?php if (empty($semester_results)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                <p class="mb-0">No results available for this semester.</p>
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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semester_results as $result): ?>
                        <tr>
                            <td><strong><?php echo $result['course_code']; ?></strong></td>
                            <td><?php echo $result['course_title']; ?></td>
                            <td class="text-center"><?php echo $result['credit']; ?></td>
                            <td class="text-center"><?php echo getGradeBadge($result['grade']); ?></td>
                            <td class="text-center"><?php echo number_format($result['gpa'], 2); ?></td>
                            <td class="text-center">
                                <?php if ($result['gpa'] >= 3.50): ?>
                                    <span class="badge bg-success">Excellent</span>
                                <?php elseif ($result['gpa'] >= 3.00): ?>
                                    <span class="badge bg-info">Good</span>
                                <?php elseif ($result['gpa'] >= 2.00): ?>
                                    <span class="badge bg-warning">Satisfactory</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Poor</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">Semester GPA:</td>
                            <td class="text-center"><?php echo $total_credits_semester; ?></td>
                            <td colspan="2" class="text-center"><?php echo number_format($semester_gpa, 2); ?></td>
                            <td class="text-center">
                                <?php if ($semester_gpa >= 3.50): ?>
                                    <span class="badge bg-success">Dean's List</span>
                                <?php elseif ($semester_gpa >= 3.00): ?>
                                    <span class="badge bg-info">Good Standing</span>
                                <?php elseif ($semester_gpa >= 2.00): ?>
                                    <span class="badge bg-warning">Satisfactory</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Probation</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Complete Transcript -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-scroll text-primary"></i> Complete Academic Transcript</h5>
    </div>
    <div class="card-body">
        <?php if (empty($all_results)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No results available.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Semester</th>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit</th>
                            <th>Grade</th>
                            <th>GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $current_semester_display = '';
                        foreach ($all_results as $result): 
                            $semester_display = $result['semester_name'] . ' ' . $result['year'];
                            if ($current_semester_display != $semester_display):
                                $current_semester_display = $semester_display;
                        ?>
                            <tr class="table-secondary">
                                <td colspan="6"><strong><?php echo $semester_display; ?></strong></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td></td>
                            <td><strong><?php echo $result['course_code']; ?></strong></td>
                            <td><?php echo $result['course_title']; ?></td>
                            <td class="text-center"><?php echo $result['credit']; ?></td>
                            <td class="text-center"><?php echo getGradeBadge($result['grade']); ?></td>
                            <td class="text-center"><?php echo number_format($result['gpa'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Grading System:</strong> 
                A=4.00, A-=3.70, B+=3.30, B=3.00, B-=2.70, C+=2.30, C=2.00, C-=1.70, D=1.00, F=0.00
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($semester_cgpa_data)): ?>
const ctx = document.getElementById('cgpaChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($semester_labels); ?>,
        datasets: [{
            label: 'Semester CGPA',
            data: <?php echo json_encode($semester_gpas); ?>,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#3498db',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: 'CGPA Target (3.50)',
            data: Array(<?php echo count($semester_labels); ?>).fill(3.50),
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
                        return `${context.dataset.label}: ${context.raw.toFixed(2)}`;
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<style>
@media print {
    .sidebar, .navbar, .footer, .btn, .no-print, .datatable_length, .datatable_filter, .datatable_info, .datatable_paginate {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
</style>

<?php include '../includes/footer.php'; ?>