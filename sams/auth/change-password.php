<?php
require_once '../config/bootstrap.php';


$page_title = 'Change Password';

// Require login for both students and admins
if (!isStudentLoggedIn() && !isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($current_password)) {
        $error = 'Current password is required';
    } elseif (empty($new_password)) {
        $error = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } else {
        // Verify current password
        $verified = false;
        
        if (isStudentLoggedIn()) {
            $sql = "SELECT password_hash FROM student WHERE student_id = ?";
            $user = dbGetRow($sql, [$_SESSION['user_id']]);
            if ($user && hash('sha256', $current_password) === $user['password_hash']) {
                $verified = true;
                $update_sql = "UPDATE student SET password_hash = ? WHERE student_id = ?";
                dbExecute($update_sql, [hash('sha256', $new_password), $_SESSION['user_id']]);
            }
        } elseif (isAdminLoggedIn()) {
            $sql = "SELECT password_hash FROM admin WHERE admin_id = ?";
            $user = dbGetRow($sql, [$_SESSION['admin_id']]);
            if ($user && hash('sha256', $current_password) === $user['password_hash']) {
                $verified = true;
                $update_sql = "UPDATE admin SET password_hash = ? WHERE admin_id = ?";
                dbExecute($update_sql, [hash('sha256', $new_password), $_SESSION['admin_id']]);
                
                // Log admin action
                logAdminAction($_SESSION['admin_id'], 'UPDATE', 'admin', $_SESSION['admin_id'], 'Changed password');
            }
        }
        
        if ($verified) {
            $success = 'Password changed successfully! Please login again.';
            
            // Logout and redirect to login page after 2 seconds
            header("refresh:2;url=logout.php");
        } else {
            $error = 'Current password is incorrect';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-key"></i> Change Password</h2>
    <p class="text-muted">Update your account password</p>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Change Password
                    </button>
                    
                    <a href="<?php echo isStudentLoggedIn() ? '../student/dashboard.php' : '../admin/dashboard.php'; ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> 
            <strong>Password Guidelines:</strong>
            <ul class="mb-0 mt-2">
                <li>Use at least 6 characters</li>
                <li>Use a mix of letters and numbers</li>
                <li>Avoid common passwords</li>
                <li>Don't share your password with anyone</li>
            </ul>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>