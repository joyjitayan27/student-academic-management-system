<?php
require_once '../config/bootstrap.php';

requireAdminLogin();

$page_title = 'Manage Teachers';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle delete
if ($action == 'delete' && isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
    if (hasAdminPermission('delete_records')) {
        $result = dbExecute("DELETE FROM teacher WHERE teacher_id = ?", [$teacher_id]);
        if ($result) {
            $message = "Teacher deleted successfully";
            logAdminAction($_SESSION['admin_id'], 'DELETE', 'teacher', $teacher_id, "Deleted teacher ID: $teacher_id");
        } else {
            $error = "Failed to delete teacher (may have associated records)";
        }
    } else {
        $error = "You don't have permission to delete records";
    }
    $action = 'list';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_teacher'])) {
    $teacher_name = sanitize($_POST['teacher_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $designation = sanitize($_POST['designation']);
    $department_id = intval($_POST['department_id']);
    $joining_date = $_POST['joining_date'];
    $office_room = sanitize($_POST['office_room']);
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    
    $errors = [];
    if (empty($teacher_name)) $errors[] = "Name required";
    if (empty($email)) $errors[] = "Email required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if ($department_id <= 0) $errors[] = "Department required";
    
    if (empty($errors)) {
        if ($teacher_id > 0) {
            // Update
            $sql = "UPDATE teacher SET teacher_name=?, email=?, phone=?, designation=?, department_id=?, joining_date=?, office_room=? WHERE teacher_id=?";
            $params = [$teacher_name, $email, $phone, $designation, $department_id, $joining_date, $office_room, $teacher_id];
            $result = dbExecute($sql, $params);
            if ($result !== false) {
                $message = "Teacher updated successfully";
                logAdminAction($_SESSION['admin_id'], 'UPDATE', 'teacher', $teacher_id, "Updated teacher: $teacher_name");
            } else {
                $error = "Update failed";
            }
        } else {
            // Insert
            $sql = "INSERT INTO teacher (teacher_name, email, phone, designation, department_id, joining_date, office_room, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $result = dbInsert($sql, [$teacher_name, $email, $phone, $designation, $department_id, $joining_date, $office_room]);
            if ($result) {
                $message = "Teacher added successfully";
                logAdminAction($_SESSION['admin_id'], 'CREATE', 'teacher', $result, "Added teacher: $teacher_name");
            } else {
                $error = "Insert failed";
            }
        }
    } else {
        $error = implode('<br>', $errors);
    }
    $action = 'list';
}

// Get departments for dropdown
$departments = dbGetAll("SELECT department_id, department_name FROM department ORDER BY department_name");

// For edit mode
$edit_teacher = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $edit_teacher = dbGetRow("SELECT * FROM teacher WHERE teacher_id = ?", [$_GET['id']]);
    if (!$edit_teacher) {
        $action = 'list';
        $error = "Teacher not found";
    }
}

// Get all teachers
$teachers = dbGetAll("
    SELECT t.*, d.department_name 
    FROM teacher t
    LEFT JOIN department d ON t.department_id = d.department_id
    ORDER BY t.teacher_id DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-chalkboard-user"></i> Manage Teachers</h2>
            <p class="text-muted mb-0">Add, edit, or remove faculty members</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Teacher
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
        <h5 class="mb-0"><?php echo $action == 'add' ? 'Add New Teacher' : 'Edit Teacher'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="teacher_id" value="<?php echo $edit_teacher['teacher_id'] ?? 0; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="teacher_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_teacher['teacher_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_teacher['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_teacher['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_teacher['designation'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>" 
                                <?php echo ($edit_teacher['department_id'] ?? '') == $dept['department_id'] ? 'selected' : ''; ?>>
                                <?php echo $dept['department_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" 
                           value="<?php echo $edit_teacher['joining_date'] ?? ''; ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Office Room</label>
                    <input type="text" name="office_room" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_teacher['office_room'] ?? ''); ?>">
                </div>
            </div>
            <button type="submit" name="save_teacher" class="btn btn-primary">Save Teacher</button>
            <a href="?action=list" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Teacher List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                    <tr>
                        <td><?php echo $t['teacher_id']; ?></td>
                        <td><?php echo htmlspecialchars($t['teacher_name']); ?></td>
                        <td><?php echo $t['email']; ?></td>
                        <td><?php echo $t['phone']; ?></td>
                        <td><?php echo $t['designation']; ?></td>
                        <td><?php echo $t['department_name']; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $t['teacher_id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (hasAdminPermission('delete_records')): ?>
                            <a href="?action=delete&id=<?php echo $t['teacher_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this teacher?')">
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