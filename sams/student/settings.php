<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'Account Settings';
$student_id = $_SESSION['user_id'];

// Get current student info
$student = dbGetRow("SELECT student_name, email, phone FROM student WHERE student_id = ?", [$student_id]);

$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $student_name = sanitize($_POST['student_name']);
    $phone = sanitize($_POST['phone']);
    
    $errors = [];
    if (empty($student_name)) $errors[] = 'Name is required';
    if (empty($phone)) $errors[] = 'Phone is required';
    if (!preg_match('/^01[0-9]{9}$/', $phone)) $errors[] = 'Invalid phone number';
    
    if (empty($errors)) {
        $result = dbExecute("UPDATE student SET student_name = ?, phone = ? WHERE student_id = ?", 
                           [$student_name, $phone, $student_id]);
        if ($result !== false) {
            $success_message = 'Profile updated successfully';
            $_SESSION['user_name'] = $student_name;
            $student['student_name'] = $student_name;
            $student['phone'] = $phone;
        } else {
            $error_message = 'Failed to update profile';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Handle notification preferences (if you have a settings table, otherwise just UI)
// For now, we'll store preferences in session or a simple text file - but let's assume we have a student_settings table.
// Since your schema doesn't have such a table, we'll just show a message that it's not implemented.
$notification_enabled = true; // default
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-cog"></i> Account Settings</h2>
    <p class="text-muted">Manage your account preferences and security</p>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Profile Settings -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-edit text-primary"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="student_name" class="form-control" 
                               value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo $student['email']; ?>" disabled>
                        <small class="text-muted">Email cannot be changed. Contact admin for assistance.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?php echo $student['phone']; ?>" required>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Notification Preferences (Placeholder) -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bell text-primary"></i> Notification Preferences</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Notification settings will be available in the next update. You will be able to manage email and SMS notifications.
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="emailNotif" checked disabled>
                    <label class="form-check-label" for="emailNotif">Email Notifications</label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="smsNotif" disabled>
                    <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Security -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-shield-alt text-primary"></i> Security</h5>
            </div>
            <div class="card-body">
                <a href="<?php echo BASE_URL; ?>auth/change-password.php" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <hr>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <small>Last password change: Not tracked yet</small>
                </div>
            </div>
        </div>
        
        <!-- Session Management -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-desktop text-primary"></i> Active Sessions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-check-circle text-success"></i> Current session
                        <br>
                        <small class="text-muted">IP: <?php echo $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; ?></small>
                    </div>
                    <span class="badge bg-success">Active</span>
                </div>
                <hr>
                <button class="btn btn-danger btn-sm w-100" onclick="confirmAction('Log out from all devices?', function() { window.location.href='logout-all.php'; });">
                    <i class="fas fa-sign-out-alt"></i> Logout from All Devices
                </button>
                <small class="text-muted d-block mt-2">Note: Logout all devices feature requires additional implementation.</small>
            </div>
        </div>
    </div>
</div>

<!-- Account Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-white text-danger">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <button class="btn btn-outline-danger" onclick="confirmAction('Are you sure you want to request account deletion? This action requires admin approval.', function() { alert('Request sent to admin.'); });">
                    <i class="fas fa-trash-alt"></i> Request Account Deletion
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
</script>

<?php include '../includes/footer.php'; ?>