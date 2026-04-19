<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'My Applications';
$student_id = $_SESSION['user_id'];

// Get student info for pre-filled data
$student = dbGetRow("SELECT student_name, email, phone, department_id, batch FROM student WHERE student_id = ?", [$student_id]);
$cgpa = calculateCGPA($student_id);
$total_credits = getTotalCredits($student_id);

// Handle new application submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_application'])) {
    $application_type = $_POST['application_type'];
    $extra_data = [];
    
    // Validate based on type
    $errors = [];
    
    switch ($application_type) {
        case 'certificate':
            $certificate_type = sanitize($_POST['certificate_type'] ?? '');
            if (empty($certificate_type)) $errors[] = 'Certificate type is required';
            break;
            
        case 'transcript':
            $copies = intval($_POST['copies'] ?? 1);
            if ($copies < 1 || $copies > 10) $errors[] = 'Number of copies must be between 1 and 10';
            break;
            
        case 'scholarship':
            $family_income = floatval($_POST['family_income'] ?? 0);
            if ($family_income <= 0) $errors[] = 'Family income is required';
            if ($cgpa < 3.0) $errors[] = 'Minimum CGPA 3.00 required for scholarship';
            break;
            
        case 'laptop':
            if (!canApplyForLaptop($student_id)) $errors[] = 'Laptop application is only for 3rd year and above students';
            break;
            
        case 'transport_card':
            $route = sanitize($_POST['route'] ?? '');
            $pickup_point = sanitize($_POST['pickup_point'] ?? '');
            if (empty($route) || empty($pickup_point)) $errors[] = 'Route and pickup point are required';
            break;
            
        case 'convocation':
            $graduation_year = intval($_POST['graduation_year'] ?? date('Y'));
            if ($graduation_year < date('Y')) $errors[] = 'Invalid graduation year';
            break;
    }
    
    if (empty($errors)) {
        // Build insert query with appropriate fields
        $sql = "INSERT INTO application (student_id, application_type, request_date, status, ";
        $params = [$student_id, $application_type, date('Y-m-d'), 'pending'];
        
        switch ($application_type) {
            case 'certificate':
                $sql .= "certificate_type) VALUES (?, ?, ?, ?, ?)";
                $params[] = $certificate_type;
                break;
            case 'transcript':
                $sql .= "copies) VALUES (?, ?, ?, ?, ?)";
                $params[] = $copies;
                break;
            case 'scholarship':
                $sql .= "cgpa, family_income) VALUES (?, ?, ?, ?, ?, ?)";
                $params[] = $cgpa;
                $params[] = $family_income;
                break;
            case 'laptop':
                $sql .= "laptop_application_date) VALUES (?, ?, ?, ?, ?)";
                $params[] = date('Y-m-d');
                break;
            case 'transport_card':
                $sql .= "route, pickup_point) VALUES (?, ?, ?, ?, ?, ?)";
                $params[] = $route;
                $params[] = $pickup_point;
                break;
            case 'convocation':
                $sql .= "graduation_year) VALUES (?, ?, ?, ?, ?)";
                $params[] = $graduation_year;
                break;
        }
        
        $result = dbInsert($sql, $params);
        
        if ($result) {
            $success_message = 'Application submitted successfully!';
        } else {
            $error_message = 'Failed to submit application. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get all applications for this student
$applications = dbGetAll("
    SELECT * FROM application 
    WHERE student_id = ? 
    ORDER BY created_at DESC
", [$student_id]);

// Get statistics
$stats = [
    'total' => count($applications),
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'processing' => 0
];

foreach ($applications as $app) {
    switch ($app['status']) {
        case 'pending': $stats['pending']++; break;
        case 'approved': $stats['approved']++; break;
        case 'rejected': $stats['rejected']++; break;
        case 'processing': $stats['processing']++; break;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> My Applications</h2>
    <p class="text-muted">Apply for certificates, transcripts, scholarships, and more</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-list fa-2x text-primary"></i>
            <h3 class="mt-2"><?php echo $stats['total']; ?></h3>
            <p class="text-muted mb-0">Total Applications</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-clock fa-2x text-warning"></i>
            <h3 class="mt-2"><?php echo $stats['pending'] + $stats['processing']; ?></h3>
            <p class="text-muted mb-0">In Progress</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-check-circle fa-2x text-success"></i>
            <h3 class="mt-2"><?php echo $stats['approved']; ?></h3>
            <p class="text-muted mb-0">Approved</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card text-center">
            <i class="fas fa-times-circle fa-2x text-danger"></i>
            <h3 class="mt-2"><?php echo $stats['rejected']; ?></h3>
            <p class="text-muted mb-0">Rejected</p>
        </div>
    </div>
</div>

<!-- New Application Button -->
<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newApplicationModal">
        <i class="fas fa-plus"></i> New Application
    </button>
</div>

<!-- Applications List -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Application History</h5>
    </div>
    <div class="card-body">
        <?php if (empty($applications)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                <p class="mb-0">You haven't submitted any applications yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Application Type</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Details</th>
                            <th>Approved/Rejected Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo ucfirst($app['application_type']); ?></strong></td>
                            <td><?php echo formatDate($app['request_date']); ?></td>
                            <td><?php echo getStatusBadge($app['status']); ?></td>
                            <td>
                                <?php if ($app['application_type'] == 'certificate' && $app['certificate_type']): ?>
                                    <?php echo $app['certificate_type']; ?>
                                <?php elseif ($app['application_type'] == 'transcript' && $app['copies']): ?>
                                    Copies: <?php echo $app['copies']; ?>
                                <?php elseif ($app['application_type'] == 'scholarship'): ?>
                                    CGPA: <?php echo $app['cgpa']; ?>, Income: ৳<?php echo number_format($app['family_income']); ?>
                                <?php elseif ($app['application_type'] == 'transport_card'): ?>
                                    Route: <?php echo $app['route']; ?>, Pickup: <?php echo $app['pickup_point']; ?>
                                <?php elseif ($app['application_type'] == 'convocation' && $app['graduation_year']): ?>
                                    Year: <?php echo $app['graduation_year']; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['approved_date']): ?>
                                    <?php echo formatDate($app['approved_date']); ?>
                                <?php elseif ($app['rejection_reason']): ?>
                                    <span class="text-danger">Rejected</span>
                                <?php else: ?>
                                    -
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

<!-- New Application Modal -->
<div class="modal fade" id="newApplicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="" id="applicationForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> New Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($error_message && isset($_POST['submit_application'])): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Application Type <span class="text-danger">*</span></label>
                        <select name="application_type" id="appType" class="form-select" required>
                            <option value="">Select Application Type</option>
                            <option value="certificate">Certificate Application</option>
                            <option value="transcript">Transcript Application</option>
                            <option value="scholarship">Scholarship Application</option>
                            <option value="laptop">Laptop Application</option>
                            <option value="transport_card">Transport Card Application</option>
                            <option value="convocation">Convocation Application</option>
                        </select>
                    </div>
                    
                    <!-- Certificate Fields -->
                    <div id="certificateFields" class="app-type-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Certificate Type <span class="text-danger">*</span></label>
                            <select name="certificate_type" class="form-select">
                                <option value="">Select Certificate Type</option>
                                <option value="Birth Certificate">Birth Certificate</option>
                                <option value="Character Certificate">Character Certificate</option>
                                <option value="Student ID Card">Student ID Card</option>
                                <option value="Bonafide Certificate">Bonafide Certificate</option>
                                <option value="Degree Certificate">Degree Certificate</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Transcript Fields -->
                    <div id="transcriptFields" class="app-type-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Number of Copies <span class="text-danger">*</span></label>
                            <input type="number" name="copies" class="form-control" min="1" max="10" value="1">
                            <small class="text-muted">Maximum 10 copies</small>
                        </div>
                    </div>
                    
                    <!-- Scholarship Fields -->
                    <div id="scholarshipFields" class="app-type-fields" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Your current CGPA: <strong><?php echo number_format($cgpa, 2); ?></strong>
                            <?php if ($cgpa >= 3.5): ?>
                                <span class="badge bg-success">Eligible for Merit Scholarship</span>
                            <?php elseif ($cgpa >= 3.0): ?>
                                <span class="badge bg-warning">Eligible for Need-based Scholarship</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Minimum CGPA 3.00 required</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Family Annual Income (BDT) <span class="text-danger">*</span></label>
                            <input type="number" name="family_income" class="form-control" placeholder="e.g., 300000">
                        </div>
                    </div>
                    
                    <!-- Transport Card Fields -->
                    <div id="transportFields" class="app-type-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Route <span class="text-danger">*</span></label>
                            <select name="route" class="form-select">
                                <option value="">Select Route</option>
                                <option value="Route 1">Route 1: Mirpur - Uttara</option>
                                <option value="Route 2">Route 2: Dhanmondi - Mohakhali</option>
                                <option value="Route 3">Route 3: Gulshan - Banani</option>
                                <option value="Route 4">Route 4: Motijheel - Farmgate</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pickup Point <span class="text-danger">*</span></label>
                            <input type="text" name="pickup_point" class="form-control" placeholder="e.g., Mirpur 12">
                        </div>
                    </div>
                    
                    <!-- Convocation Fields -->
                    <div id="convocationFields" class="app-type-fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Graduation Year <span class="text-danger">*</span></label>
                            <select name="graduation_year" class="form-select">
                                <?php 
                                $current_year = date('Y');
                                for ($i = $current_year; $i <= $current_year + 2; $i++): 
                                ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Laptop Fields (no extra fields, just eligibility check) -->
                    <div id="laptopFields" class="app-type-fields" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-laptop"></i> 
                            Laptop application eligibility: 3rd year or above students.
                            <?php if (canApplyForLaptop($student_id)): ?>
                                <span class="badge bg-success">You are eligible</span>
                            <?php else: ?>
                                <span class="badge bg-danger">You are not eligible yet</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_application" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('appType').addEventListener('change', function() {
    // Hide all type-specific fields
    document.querySelectorAll('.app-type-fields').forEach(function(el) {
        el.style.display = 'none';
    });
    
    // Show selected type fields
    var selected = this.value;
    if (selected === 'certificate') {
        document.getElementById('certificateFields').style.display = 'block';
    } else if (selected === 'transcript') {
        document.getElementById('transcriptFields').style.display = 'block';
    } else if (selected === 'scholarship') {
        document.getElementById('scholarshipFields').style.display = 'block';
    } else if (selected === 'transport_card') {
        document.getElementById('transportFields').style.display = 'block';
    } else if (selected === 'convocation') {
        document.getElementById('convocationFields').style.display = 'block';
    } else if (selected === 'laptop') {
        document.getElementById('laptopFields').style.display = 'block';
    }
});
</script>

<?php include '../includes/footer.php'; ?>