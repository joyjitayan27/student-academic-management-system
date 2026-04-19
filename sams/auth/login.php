<?php
require_once '../config/bootstrap.php';


$page_title = 'Student Login';

if (isStudentLoggedIn()) {
    header('Location: ../student/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        $student = dbGetRow("SELECT * FROM student WHERE email = ? AND status = 'active'", [$email]);
        
        if ($student && hash('sha256', $password) === $student['password_hash']) {
            setStudentSession($student);
            header('Location: ../student/dashboard.php');
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
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .login-card{background:white;border-radius:20px;max-width:450px;width:100%;overflow:hidden;}
        .login-header{background:linear-gradient(135deg,#3498db,#2980b9);color:white;padding:30px;text-align:center;}
        .login-body{padding:30px;}
        .btn-login{border-radius:25px;padding:12px;background:#3498db;width:100%;border:none;}
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header"><i class="fas fa-graduation-cap fa-3x"></i><h2>Student Login</h2></div>
    <div class="login-body">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-login">Login</button>
        </form>
        <hr><div class="text-center"><a href="register.php">Register</a> | <a href="../index.php">Home</a></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>