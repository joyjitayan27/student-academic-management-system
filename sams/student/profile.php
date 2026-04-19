<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'My Profile';
$student_id = $_SESSION['user_id'];

// Get student information with joins
$student = dbGetRow("
    SELECT s.*, d.department_name, 
           t.teacher_name as advisor_name, t.email as advisor_email, t.phone as advisor_phone,
           sem.name as semester_name, sem.year as semester_year
    FROM student s
    LEFT JOIN department d ON s.department_id = d.department_id
    LEFT JOIN advisor a ON s.advisor_id = a.advisor_id
    LEFT JOIN teacher t ON a.teacher_id = t.teacher_id
    LEFT JOIN semester sem ON s.current_semester_id = sem.semester_id
    WHERE s.student_id = ?
", [$student_id]);

$error = '';
$success = '';

// Handle profile update (same logic as settings.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $student_name = sanitize($_POST['student_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    $errors = [];
    if (empty($student_name)) {
        $errors[] = 'Name is required';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = 'Invalid phone number format (must be 11 digits starting with 01)';
    }
    
    if (empty($errors)) {
        $result = dbExecute("UPDATE student SET student_name = ?, phone = ? WHERE student_id = ?", 
                           [$student_name, $phone, $student_id]);
        if ($result !== false) {
            $success = 'Profile updated successfully';
            $_SESSION['user_name'] = $student_name;
            // Refresh student data
            $student = dbGetRow("
                SELECT s.*, d.department_name, 
                       t.teacher_name as advisor_name, t.email as advisor_email, t.phone as advisor_phone,
                       sem.name as semester_name, sem.year as semester_year
                FROM student s
                LEFT JOIN department d ON s.department_id = d.department_id
                LEFT JOIN advisor a ON s.advisor_id = a.advisor_id
                LEFT JOIN teacher t ON a.teacher_id = t.teacher_id
                LEFT JOIN semester sem ON s.current_semester_id = sem.semester_id
                WHERE s.student_id = ?
            ", [$student_id]);
        } else {
            $error = 'Failed to update profile';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Get academic statistics
$academic_stats = dbGetRow("
    SELECT 
        COUNT(DISTINCT CASE WHEN r.gpa IS NOT NULL THEN r.course_id END) as courses_completed,
        COALESCE(SUM(c.credit), 0) as total_credits,
        COALESCE(ROUND(AVG(r.gpa), 2), 0) as cgpa
    FROM student s
    LEFT JOIN result r ON s.student_id = r.student_id AND r.gpa IS NOT NULL AND r.gpa > 0
    LEFT JOIN course c ON r.course_id = c.course_id
    WHERE s.student_id = ?
", [$student_id]);
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-user-circle"></i> My Profile</h2>
    <p class="text-muted">View and manage your personal information</p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-edit text-primary"></i> Edit Profile</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="student_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" value="<?php echo $student['student_id']; ?>" disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo $student['email']; ?>" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?php echo $student['phone']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" value="<?php echo $student['department_name']; ?>" disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batch Year</label>
                            <input type="text" class="form-control" value="<?php echo $student['batch']; ?>" disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Semester</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo $student['semester_name'] . ' ' . $student['semester_year']; ?>" disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission Date</label>
                            <input type="text" class="form-control" value="<?php echo formatDate($student['admission_date']); ?>" disabled>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                <?php echo getStatusBadge($student['status']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    
                    <!-- FIXED: Use BASE_URL to point to auth/change-password.php -->
                    <a href="<?php echo BASE_URL; ?>auth/change-password.php" class="btn btn-warning">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Advisor Information -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chalkboard-user text-success"></i> Academic Advisor</h5>
            </div>
            <div class="card-body">
                <?php if ($student['advisor_name']): ?>
                    <div class="text-center mb-3">
                        <div class="advisor-avatar mx-auto mb-3">
                            <i class="fas fa-user-tie fa-4x text-primary"></i>
                        </div>
                        <h5><?php echo $student['advisor_name']; ?></h5>
                        <p class="text-muted">Academic Advisor</p>
                    </div>
                    <hr>
                    <div class="advisor-details">
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo $student['advisor_email']; ?></p>
                        <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <?php echo $student['advisor_phone'] ?? 'N/A'; ?></p>
                        <p><i class="fas fa-building"></i> <strong>Department:</strong> <?php echo $student['department_name']; ?></p>
                    </div>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <small>For academic guidance, course registration issues, or any academic concerns, please contact your advisor.</small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mb-0 mt-2">No advisor assigned yet. Please contact the department office.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Academic Summary Card -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-line text-info"></i> Academic Summary</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-4 fw-bold text-primary"><?php echo number_format($academic_stats['cgpa'], 2); ?></div>
                    <p class="text-muted">Current CGPA</p>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4><?php echo $academic_stats['courses_completed']; ?></h4>
                        <small class="text-muted">Courses Completed</small>
                    </div>
                    <div class="col-6">
                        <h4><?php echo $academic_stats['total_credits']; ?></h4>
                        <small class="text-muted">Total Credits</small>
                    </div>
                </div>
                <hr>
                <a href="results.php" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-chart-line"></i> View Full Academic Record
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bolt text-warning"></i> Quick Actions</h5>
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
                        <a href="evaluations.php" class="text-decoration-none">
                            <i class="fas fa-star fa-2x text-warning mb-2 d-block"></i>
                            <span>Evaluate Teachers</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="applications.php" class="text-decoration-none">
                            <i class="fas fa-file-alt fa-2x text-info mb-2 d-block"></i>
                            <span>New Application</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>