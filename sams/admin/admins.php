<?php
require_once '../config/bootstrap.php';

// Only Senior DBA can manage admins
if (!hasAdminPermission('manage_admins')) {
    header('Location: dashboard.php?error=Access denied');
    exit();
}

requireAdminLogin();

$page_title = 'Manage Administrators';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle delete
if ($action == 'delete' && isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);
    if ($admin_id == $_SESSION['admin_id']) {
        $error = "You cannot delete your own account";
    } else {
        $result = dbExecute("DELETE FROM admin WHERE admin_id = ?", [$admin_id]);
        if ($result) {
            $message = "Admin deleted successfully";
            logAdminAction($_SESSION['admin_id'], 'DELETE', 'admin', $admin_id, "Deleted admin ID: $admin_id");
        } else {
            $error = "Failed to delete admin";
        }
    }
    $action = 'list';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_admin'])) {
    $admin_name = sanitize($_POST['admin_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $role_id = intval($_POST['role_id']);
    $position = sanitize($_POST['position']);
    $employee_id = sanitize($_POST['employee_id']);
    $joining_date = $_POST['joining_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'] ?? '';
    $admin_id = intval($_POST['admin_id'] ?? 0);
    
    $errors = [];
    if (empty($admin_name)) $errors[] = "Name required";
    if (empty($email)) $errors[] = "Email required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if ($role_id <= 0) $errors[] = "Role required";
    if (empty($position)) $errors[] = "Position required";
    
    // Check email uniqueness
    $existing = dbGetRow("SELECT admin_id FROM admin WHERE email = ? AND admin_id != ?", [$email, $admin_id]);
    if ($existing) $errors[] = "Email already exists";
    
    if (empty($errors)) {
        if ($admin_id > 0) {
            // Update
            if (!empty($password)) {
                $sql = "UPDATE admin SET admin_name=?, email=?, phone=?, role_id=?, position=?, employee_id=?, joining_date=?, is_active=?, password_hash=? WHERE admin_id=?";
                $params = [$admin_name, $email, $phone, $role_id, $position, $employee_id, $joining_date, $is_active, hash('sha256', $password), $admin_id];
            } else {
                $sql = "UPDATE admin SET admin_name=?, email=?, phone=?, role_id=?, position=?, employee_id=?, joining_date=?, is_active=? WHERE admin_id=?";
                $params = [$admin_name, $email, $phone, $role_id, $position, $employee_id, $joining_date, $is_active, $admin_id];
            }
            $result = dbExecute($sql, $params);
            if ($result !== false) {
                $message = "Admin updated successfully";
                logAdminAction($_SESSION['admin_id'], 'UPDATE', 'admin', $admin_id, "Updated admin: $admin_name");
            } else {
                $error = "Update failed";
            }
        } else {
            // Insert
            if (empty($password)) $errors[] = "Password required for new admin";
            if (empty($errors)) {
                $sql = "INSERT INTO admin (admin_name, email, password_hash, phone, role_id, position, employee_id, joining_date, is_active, created_by, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $result = dbInsert($sql, [$admin_name, $email, hash('sha256', $password), $phone, $role_id, $position, $employee_id, $joining_date, $is_active, $_SESSION['admin_id']]);
                if ($result) {
                    $message = "Admin added successfully";
                    logAdminAction($_SESSION['admin_id'], 'CREATE', 'admin', $result, "Added admin: $admin_name");
                } else {
                    $error = "Insert failed";
                }
            }
        }
    } else {
        $error = implode('<br>', $errors);
    }
    $action = 'list';
}

// Get roles
$roles = dbGetAll("SELECT role_id, role_name, role_level, permission_level FROM admin_role ORDER BY role_level");

// For edit mode
$edit_admin = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $edit_admin = dbGetRow("SELECT * FROM admin WHERE admin_id = ?", [$_GET['id']]);
    if (!$edit_admin) {
        $action = 'list';
        $error = "Admin not found";
    }
}

// Get all admins
$admins = dbGetAll("
    SELECT a.*, r.role_name, r.role_level 
    FROM admin a
    LEFT JOIN admin_role r ON a.role_id = r.role_id
    ORDER BY r.role_level, a.admin_id
");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-user-shield"></i> Manage Administrators</h2>
            <p class="text-muted mb-0">Add, edit, or remove system administrators</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Admin
            </a>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
<!-- Add/Edit Form -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><?php echo $action == 'add' ? 'Add New Administrator' : 'Edit Administrator'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="admin_id" value="<?php echo $edit_admin['admin_id'] ?? 0; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="admin_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_admin['admin_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_admin['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_admin['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_admin['employee_id'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['role_id']; ?>" 
                                <?php echo ($edit_admin['role_id'] ?? '') == $role['role_id'] ? 'selected' : ''; ?>>
                                <?php echo $role['role_name']; ?> (<?php echo ucfirst($role['permission_level']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Position <span class="text-danger">*</span></label>
                    <input type="text" name="position" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_admin['position'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" 
                           value="<?php echo $edit_admin['joining_date'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                               <?php echo ($edit_admin['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Active Account</label>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Password <?php echo $action == 'add' ? '<span class="text-danger">*</span>' : '(Leave blank to keep unchanged)'; ?></label>
                    <input type="password" name="password" class="form-control" <?php echo $action == 'add' ? 'required' : ''; ?>>
                </div>
            </div>
            <button type="submit" name="save_admin" class="btn btn-primary">Save Admin</button>
            <a href="?action=list" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Admin List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td><?php echo $a['admin_id']; ?></td>
                        <td><?php echo htmlspecialchars($a['admin_name']); ?></td>
                        <td><?php echo $a['email']; ?></td>
                        <td><?php echo $a['role_name']; ?> (L<?php echo $a['role_level']; ?>)</td>
                        <td><?php echo $a['position']; ?></td>
                        <td><?php echo $a['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                        <td><?php echo $a['last_login'] ? formatDate($a['last_login']) : 'Never'; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $a['admin_id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($a['admin_id'] != $_SESSION['admin_id']): ?>
                            <a href="?action=delete&id=<?php echo $a['admin_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this admin?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>