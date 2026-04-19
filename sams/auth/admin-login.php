<?php
require_once '../config/bootstrap.php';


$page_title = 'Admin Login';

if (isAdminLoggedIn()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        $admin = dbGetRow("SELECT a.*, r.role_name FROM admin a JOIN admin_role r ON a.role_id = r.role_id WHERE a.email = ? AND a.is_active = 1", [$email]);
        
        if ($admin && hash('sha256', $password) === $admin['password_hash']) {
            setAdminSession($admin);
            dbExecute("UPDATE admin SET last_login = NOW() WHERE admin_id = ?", [$admin['admin_id']]);
            header('Location: ../admin/dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:linear-gradient(135deg,#1e3c72,#2a5298);min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .login-card{background:white;border-radius:20px;max-width:450px;width:100%;overflow:hidden;}
        .login-header{background:#2c3e50;color:white;padding:30px;text-align:center;}
        .login-body{padding:30px;}
        .btn-login{background:#2c3e50;border:none;width:100%;padding:12px;border-radius:25px;}
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header"><i class="fas fa-user-shield fa-3x"></i><h2>Admin Login</h2></div>
    <div class="login-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-login">Login</button>
        </form>
        <hr><div class="text-center"><a href="../index.php">Home</a></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>