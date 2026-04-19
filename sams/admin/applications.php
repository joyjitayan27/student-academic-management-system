<?php
require_once '../config/bootstrap.php';

requireAdminLogin();

$page_title = 'Manage Applications';
$message = '';
$error = '';

// Handle status update (approve/reject)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $application_id = intval($_POST['application_id']);
    $new_status = $_POST['new_status'];
    $rejection_reason = sanitize($_POST['rejection_reason'] ?? '');
    
    if (!in_array($new_status, ['approved', 'rejected', 'processing'])) {
        $error = "Invalid status";
    } else {
        $update_fields = "status = ?";
        $params = [$new_status];
        if ($new_status == 'approved') {
            $update_fields .= ", approved_date = CURDATE()";
        } elseif ($new_status == 'rejected') {
            $update_fields .= ", rejection_reason = ?";
            $params = [$new_status, $rejection_reason];
        }
        $sql = "UPDATE application SET $update_fields WHERE application_id = ?";
        $params[] = $application_id;
        $result = dbExecute($sql, $params);
        if ($result !== false) {
            $message = "Application status updated";
            logAdminAction($_SESSION['admin_id'], strtoupper($new_status), 'application', $application_id, "Application $new_status");
        } else {
            $error = "Update failed";
        }
    }
}

// Filtering
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';

$sql = "SELECT a.*, s.student_name, s.email, s.student_id 
        FROM application a 
        JOIN student s ON a.student_id = s.student_id 
        WHERE 1=1";
$params = [];
if ($status_filter) {
    $sql .= " AND a.status = ?";
    $params[] = $status_filter;
}
if ($type_filter) {
    $sql .= " AND a.application_type = ?";
    $params[] = $type_filter;
}
$sql .= " ORDER BY a.created_at DESC";

$applications = dbGetAll($sql, $params);

// Get counts for filters
$status_counts = dbGetAll("SELECT status, COUNT(*) as count FROM application GROUP BY status");
$type_counts = dbGetAll("SELECT application_type, COUNT(*) as count FROM application GROUP BY application_type");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt"></i> Manage Applications</h2>
            <p class="text-muted mb-0">Review and process student applications</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Application Type</label>
                <select name="type" class="form-select">
                    <option value="">All</option>
                    <?php foreach (APPLICATION_TYPES as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $type_filter == $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="applications.php" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Applications Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Details</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo $app['application_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($app['student_name']); ?><br>
                            <small class="text-muted">ID: <?php echo $app['student_id']; ?></small>
                        </td>
                        <td><?php echo ucfirst($app['application_type']); ?></td>
                        <td>
                            <?php if ($app['application_type'] == 'certificate' && $app['certificate_type']): ?>
                                Certificate: <?php echo $app['certificate_type']; ?>
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
                        <td><?php echo formatDate($app['request_date']); ?></td>
                        <td><?php echo getStatusBadge($app['status']); ?></td>
                        <td>
                            <?php if ($app['status'] == 'pending' || $app['status'] == 'processing'): ?>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $app['application_id']; ?>">
                                    <i class="fas fa-check"></i> Process
                                </button>
                            <?php else: ?>
                                <span class="text-muted">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- Status Update Modal -->
                    <div class="modal fade" id="statusModal<?php echo $app['application_id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <input type="hidden" name="application_id" value="<?php echo $app['application_id']; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Application Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="new_status" class="form-select" required>
                                                <option value="processing" <?php echo $app['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3" id="rejectReason<?php echo $app['application_id']; ?>" style="display: none;">
                                            <label class="form-label">Rejection Reason</label>
                                            <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.querySelector('#statusModal<?php echo $app['application_id']; ?> select[name="new_status"]').addEventListener('change', function() {
                            const reasonDiv = document.getElementById('rejectReason<?php echo $app['application_id']; ?>');
                            reasonDiv.style.display = this.value === 'rejected' ? 'block' : 'none';
                        });
                    </script>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>