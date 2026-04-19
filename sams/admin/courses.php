<?php
require_once '../config/bootstrap.php';

requireAdminLogin();

$page_title = 'Manage Courses';
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle delete
if ($action == 'delete' && isset($_GET['id'])) {
    $course_id = intval($_GET['id']);
    if (hasAdminPermission('delete_records')) {
        $result = dbExecute("DELETE FROM course WHERE course_id = ?", [$course_id]);
        if ($result) {
            $message = "Course deleted successfully";
            logAdminAction($_SESSION['admin_id'], 'DELETE', 'course', $course_id, "Deleted course ID: $course_id");
        } else {
            $error = "Failed to delete course (may have associated registrations)";
        }
    } else {
        $error = "You don't have permission to delete records";
    }
    $action = 'list';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_course'])) {
    $course_code = strtoupper(sanitize($_POST['course_code']));
    $course_title = sanitize($_POST['course_title']);
    $credit = floatval($_POST['credit']);
    $department_id = intval($_POST['department_id']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $course_id = intval($_POST['course_id'] ?? 0);
    
    $errors = [];
    if (empty($course_code)) $errors[] = "Course code required";
    if (empty($course_title)) $errors[] = "Course title required";
    if ($credit <= 0) $errors[] = "Credit must be positive";
    if ($department_id <= 0) $errors[] = "Department required";
    
    if (empty($errors)) {
        if ($course_id > 0) {
            $sql = "UPDATE course SET course_code=?, course_title=?, credit=?, department_id=?, is_active=? WHERE course_id=?";
            $result = dbExecute($sql, [$course_code, $course_title, $credit, $department_id, $is_active, $course_id]);
            if ($result !== false) {
                $message = "Course updated successfully";
                logAdminAction($_SESSION['admin_id'], 'UPDATE', 'course', $course_id, "Updated course: $course_code");
            } else {
                $error = "Update failed";
            }
        } else {
            $sql = "INSERT INTO course (course_code, course_title, credit, department_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $result = dbInsert($sql, [$course_code, $course_title, $credit, $department_id, $is_active]);
            if ($result) {
                $message = "Course added successfully";
                logAdminAction($_SESSION['admin_id'], 'CREATE', 'course', $result, "Added course: $course_code");
            } else {
                $error = "Insert failed (course code may already exist)";
            }
        }
    } else {
        $error = implode('<br>', $errors);
    }
    $action = 'list';
}

// Get departments
$departments = dbGetAll("SELECT department_id, department_name FROM department ORDER BY department_name");

// For edit mode
$edit_course = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $edit_course = dbGetRow("SELECT * FROM course WHERE course_id = ?", [$_GET['id']]);
    if (!$edit_course) {
        $action = 'list';
        $error = "Course not found";
    }
}

// Get all courses
$courses = dbGetAll("
    SELECT c.*, d.department_name 
    FROM course c
    LEFT JOIN department d ON c.department_id = d.department_id
    ORDER BY c.course_code
");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-book"></i> Manage Courses</h2>
            <p class="text-muted mb-0">Add, edit, or remove course offerings</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Course
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
        <h5 class="mb-0"><?php echo $action == 'add' ? 'Add New Course' : 'Edit Course'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="course_id" value="<?php echo $edit_course['course_id'] ?? 0; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" name="course_code" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_course['course_code'] ?? ''); ?>" 
                           placeholder="e.g., CSE101">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course Title <span class="text-danger">*</span></label>
                    <input type="text" name="course_title" class="form-control" required 
                           value="<?php echo htmlspecialchars($edit_course['course_title'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Credit Hours <span class="text-danger">*</span></label>
                    <input type="number" step="0.5" name="credit" class="form-control" required 
                           value="<?php echo $edit_course['credit'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>" 
                                <?php echo ($edit_course['department_id'] ?? '') == $dept['department_id'] ? 'selected' : ''; ?>>
                                <?php echo $dept['department_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                               <?php echo ($edit_course['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Course is active (available for registration)</label>
                    </div>
                </div>
            </div>
            <button type="submit" name="save_course" class="btn btn-primary">Save Course</button>
            <a href="?action=list" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Course List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Credit</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><strong><?php echo $c['course_code']; ?></strong></td>
                        <td><?php echo htmlspecialchars($c['course_title']); ?></td>
                        <td><?php echo $c['credit']; ?></td>
                        <td><?php echo $c['department_name']; ?></td>
                        <td><?php echo $c['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $c['course_id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (hasAdminPermission('delete_records')): ?>
                            <a href="?action=delete&id=<?php echo $c['course_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?')">
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