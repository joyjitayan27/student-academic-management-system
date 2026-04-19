<?php
require_once '../config/bootstrap.php';

// Only Senior DBA and Junior DBA can view logs (based on permission)
if (!hasAdminPermission('view_reports')) {
    header('Location: dashboard.php?error=Access denied');
    exit();
}

requireAdminLogin();

$page_title = 'Activity Logs';

// Filters
$action_filter = $_GET['action_type'] ?? '';
$admin_filter = $_GET['admin_id'] ?? '';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Get logs with filters
$sql = "SELECT l.*, a.admin_name, a.position 
        FROM admin_log l
        JOIN admin a ON l.admin_id = a.admin_id
        WHERE DATE(l.action_timestamp) BETWEEN ? AND ?";
$params = [$date_from, $date_to];

if ($action_filter) {
    $sql .= " AND l.action_type = ?";
    $params[] = $action_filter;
}
if ($admin_filter) {
    $sql .= " AND l.admin_id = ?";
    $params[] = $admin_filter;
}
$sql .= " ORDER BY l.action_timestamp DESC";

$logs = dbGetAll($sql, $params);

// Get filter options
$action_types = dbGetAll("SELECT DISTINCT action_type FROM admin_log ORDER BY action_type");
$admins = dbGetAll("SELECT admin_id, admin_name FROM admin WHERE is_active = 1 ORDER BY admin_name");
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-history"></i> Activity Logs</h2>
    <p class="text-muted">View and monitor all admin activities</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Logs</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Action Type</label>
                <select name="action_type" class="form-select">
                    <option value="">All Actions</option>
                    <?php foreach ($action_types as $type): ?>
                        <option value="<?php echo $type['action_type']; ?>" <?php echo $action_filter == $type['action_type'] ? 'selected' : ''; ?>>
                            <?php echo $type['action_type']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Admin</label>
                <select name="admin_id" class="form-select">
                    <option value="">All Admins</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?php echo $admin['admin_id']; ?>" <?php echo $admin_filter == $admin['admin_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($admin['admin_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="logs.php" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-list"></i> Activity Logs (<?php echo count($logs); ?> records)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="alert alert-info">No logs found for the selected criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo date('d M Y H:i:s', strtotime($log['action_timestamp'])); ?></td>
                            <td><?php echo htmlspecialchars($log['admin_name']); ?><br><small class="text-muted"><?php echo $log['position']; ?></small></td>
                            <td>
                                <?php 
                                $badge_class = '';
                                switch ($log['action_type']) {
                                    case 'CREATE': $badge_class = 'success'; break;
                                    case 'UPDATE': $badge_class = 'info'; break;
                                    case 'DELETE': $badge_class = 'danger'; break;
                                    case 'APPROVE': $badge_class = 'success'; break;
                                    case 'REJECT': $badge_class = 'danger'; break;
                                    case 'LOGIN': $badge_class = 'primary'; break;
                                    default: $badge_class = 'secondary';
                                }
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $log['action_type']; ?></span>
                            </td>
                            <td><?php echo $log['table_name'] ?? '-'; ?></td>
                            <td><?php echo $log['record_id'] ?? '-'; ?></td>
                            <td><?php echo htmlspecialchars($log['action_description']); ?></td>
                            <td><?php echo $log['ip_address'] ?? '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>