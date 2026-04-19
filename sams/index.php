<?php
require_once 'config/bootstrap.php';  

$page_title = 'Home';
?>
<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="text-center py-5">
            <h1 class="display-4 fw-bold text-primary">Welcome to <?php echo APP_NAME; ?></h1>
            <p class="lead">Your complete solution for academic management</p>
            <hr class="my-4 w-50 mx-auto">
            <p>Manage courses, results, attendance, applications and more from a single platform.</p>
            
            <?php if (!isStudentLoggedIn() && !isAdminLoggedIn()): ?>
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-primary btn-lg mx-2">
                    <i class="fas fa-sign-in-alt"></i> Student Login
                </a>
                <a href="<?php echo BASE_URL; ?>auth/admin-login.php" class="btn btn-outline-primary btn-lg mx-2">
                    <i class="fas fa-user-shield"></i> Admin Login
                </a>
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-success btn-lg mx-2">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mt-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Course Management</h5>
                <p class="card-text">Register for courses, view schedules, and track your academic progress.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                <h5 class="card-title">Results & CGPA</h5>
                <p class="card-text">View your semester results and calculate your CGPA instantly.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-3x text-info mb-3"></i>
                <h5 class="card-title">Attendance Tracking</h5>
                <p class="card-text">Monitor your attendance percentage course-wise in real-time.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-star fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Teacher Evaluation</h5>
                <p class="card-text">Provide feedback and rate your teachers anonymously.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-file-alt fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Student Applications</h5>
                <p class="card-text">Apply for certificates, transcripts, scholarships and more.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center dashboard-card">
            <div class="card-body">
                <i class="fas fa-user-shield fa-3x text-secondary mb-3"></i>
                <h5 class="card-title">Admin Panel</h5>
                <p class="card-text">Complete admin control with role-based access management.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>