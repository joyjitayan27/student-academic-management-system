<?php
require_once '../config/bootstrap.php';

requireAdminLogin();

$page_title = 'Manage Students';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle delete
if ($action == 'delete' && isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    if (hasAdminPermission('delete_records')) {
        $result = dbExecute("DELETE FROM student WHERE student_id = ?", [$student_id]);
        if ($result) {
            $message = "Student deleted successfully";
        } else {
            $error = "Failed to delete student";
        }
    } else {
        $error = "You don't have permission to delete records";
    }
    $action = 'list';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_student'])) {
    $student_name = sanitize($_POST['student_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $department_id = intval($_POST['department_id']);
    $advisor_id = intval($_POST['advisor_id']);
    $batch = intval($_POST['batch']);
    $admission_date = $_POST['admission_date'];
    $status = $_POST['status'];
    $password = $_POST['password'] ?? '';
    $student_id = intval($_POST['student_id'] ?? 0);
    
    $errors = [];
    if (empty($student_name)) $errors[] = "Name required";
    if (empty($email)) $errors[] = "Email required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
    if (empty($phone)) $errors[] = "Phone required";
    if ($department_id <= 0) $errors[] = "Department required";
    if ($advisor_id <= 0) $errors[] = "Advisor required";
    if ($batch < 2000 || $batch > date('Y')+5) $errors[] = "Invalid batch";
    
    if (empty($errors)) {
        if ($student_id > 0) {
            // Update
            $sql = "UPDATE student SET student_name=?, email=?, phone=?, department_id=?, advisor_id=?, batch=?, admission_date=?, status=? WHERE student_id=?";
            $params = [$student_name, $email, $phone, $department_id, $advisor_id, $batch, $admission_date, $status, $student_id];
            if (!empty($password)) {
                $sql = "UPDATE student SET student_name=?, email=?, phone=?, department_id=?, advisor_id=?, batch=?, admission_date=?, status=?, password_hash=? WHERE student_id=?";
                $params = [$student_name, $email, $phone, $department_id, $advisor_id, $batch, $admission_date, $status, hash('sha256', $password), $student_id];
            }
            $result = dbExecute($sql, $params);
            if ($result !== false) {
                $message = "Student updated successfully";
                logAdminAction($_SESSION['admin_id'], 'UPDATE', 'student', $student_id, "Updated student: $student_name");
            } else {
                $error = "Update failed";
            }
        } else {
            // Insert
            if (empty($password)) $errors[] = "Password required for new student";
            if (empty($errors)) {
                $sql = "INSERT INTO student (student_name, email, password_hash, phone, department_id, advisor_id, batch, admission_date, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $result = dbInsert($sql, [$student_name, $email, hash('sha256', $password), $phone, $department_id, $advisor_id, $batch, $admission_date, $status]);
                if ($result) {
                    $message = "Student added successfully";
                    logAdminAction($_SESSION['admin_id'], 'CREATE', 'student', $result, "Added student: $student_name");
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

// Get departments for dropdown
$departments = dbGetAll("SELECT department_id, department_name FROM department ORDER BY department_name");

// Get advisors for dropdown
$advisors = dbGetAll("
    SELECT a.advisor_id, t.teacher_name, d.department_name 
    FROM advisor a
    JOIN teacher t ON a.teacher_id = t.teacher_id
    JOIN department d ON t.department_id = d.department_id
    ORDER BY d.department_name, t.teacher_name
");

// For edit mode, fetch student data
$edit_student = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $edit_student = dbGetRow("SELECT * FROM student WHERE student_id = ?", [$_GET['id']]);
    if (!$edit_student) {
        $action = 'list';
        $error = "Student not found";
    }
}

// Get all students for listing
$students = dbGetAll("
    SELECT s.*, d.department_name, t.teacher_name as advisor_name
    FROM student s
    LEFT JOIN department d ON s.department_id = d.department_id
    LEFT JOIN advisor a ON s.advisor_id = a.advisor_id
    LEFT JOIN teacher t ON a.teacher_id = t.teacher_id
    ORDER BY s.student_id DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-users"></i> Manage Students</h2>
            <p class="text-muted mb-0">Add, edit, or remove student records</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Student
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
        <h5 class="mb-0"><?php echo $action == 'add' ? 'Add New Student' : 'Edit Student'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="student_id" value="<?php echo $edit_student['student_id'] ?? 0; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="student_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_student['student_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_student['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_student['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Batch <span class="text-danger">*</span></label>
                    <input type="number" name="batch" class="form-control" required 
                           value="<?php echo $edit_student['batch'] ?? date('Y'); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>" 
                                <?php echo ($edit_student['department_id'] ?? '') == $dept['department_id'] ? 'selected' : ''; ?>>
                                <?php echo $dept['department_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Advisor <span class="text-danger">*</span></label>
                    <select name="advisor_id" class="form-select" required>
                        <option value="">Select Advisor</option>
                        <?php foreach ($advisors as $adv): ?>
                            <option value="<?php echo $adv['advisor_id']; ?>"
                                <?php echo ($edit_student['advisor_id'] ?? '') == $adv['advisor_id'] ? 'selected' : ''; ?>>
                                <?php echo $adv['teacher_name'] . ' (' . $adv['department_name'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Admission Date</label>
                    <input type="date" name="admission_date" class="form-control" 
                           value="<?php echo $edit_student['admission_date'] ?? date('Y-m-d'); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo ($edit_student['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="graduated" <?php echo ($edit_student['status'] ?? '') == 'graduated' ? 'selected' : ''; ?>>Graduated</option>
                        <option value="suspended" <?php echo ($edit_student['status'] ?? '') == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        <option value="withdrawn" <?php echo ($edit_student['status'] ?? '') == 'withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Password <?php echo $action == 'add' ? '<span class="text-danger">*</span>' : '(Leave blank to keep unchanged)'; ?></label>
                    <input type="password" name="password" class="form-control" <?php echo $action == 'add' ? 'required' : ''; ?>>
                </div>
            </div>
            <button type="submit" name="save_student" class="btn btn-primary">Save Student</button>
            <a href="?action=list" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Student List -->
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
                        <th>Department</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?php echo $s['student_id']; ?></td>
                        <td><?php echo htmlspecialchars($s['student_name']); ?></td>
                        <td><?php echo $s['email']; ?></td>
                        <td><?php echo $s['phone']; ?></td>
                        <td><?php echo $s['department_name']; ?></td>
                        <td><?php echo $s['batch']; ?></td>
                        <td><?php echo getStatusBadge($s['status']); ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $s['student_id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (hasAdminPermission('delete_records')): ?>
                            <a href="?action=delete&id=<?php echo $s['student_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')">
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