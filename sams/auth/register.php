<?php
require_once '../config/bootstrap.php';


$page_title = 'Student Registration';

if (isStudentLoggedIn()) {
    header('Location: ../student/dashboard.php');
    exit();
}

$error = '';
$success = '';

$departments = dbGetAll("SELECT department_id, department_name FROM department ORDER BY department_name");
$advisors = dbGetAll("
    SELECT a.advisor_id, t.teacher_name, d.department_name 
    FROM advisor a 
    JOIN teacher t ON a.teacher_id = t.teacher_id 
    JOIN department d ON t.department_id = d.department_id
    ORDER BY d.department_name, t.teacher_name
");

$current_semester = getCurrentSemester();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = sanitize($_POST['student_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $department_id = intval($_POST['department_id'] ?? 0);
    $advisor_id = intval($_POST['advisor_id'] ?? 0);
    $batch = intval($_POST['batch'] ?? date('Y'));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    if(empty($student_name)) $errors[]="Name required";
    if(empty($email)) $errors[]="Email required";
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]="Invalid email";
    if(empty($phone)) $errors[]="Phone required";
    if(!preg_match('/^01[0-9]{9}$/',$phone)) $errors[]="Invalid phone (01XXXXXXXXX)";
    if($department_id<=0) $errors[]="Select department";
    if($advisor_id<=0) $errors[]="Select advisor";
    if(empty($password)) $errors[]="Password required";
    if(strlen($password)<6) $errors[]="Password min 6 chars";
    if($password!==$confirm) $errors[]="Passwords do not match";
    if(dbGetRow("SELECT student_id FROM student WHERE email=?",[$email])) $errors[]="Email already registered";
    
    if(empty($errors)){
        $hash = hash('sha256',$password);
        $sem_id = $current_semester ? $current_semester['semester_id'] : null;
        $sql = "INSERT INTO student (student_name,email,password_hash,phone,department_id,advisor_id,batch,current_semester_id,admission_date,status) VALUES (?,?,?,?,?,?,?,?,CURDATE(),'active')";
        $id = dbInsert($sql,[$student_name,$email,$hash,$phone,$department_id,$advisor_id,$batch,$sem_id]);
        if($id){
            $success = "Registration successful! <a href='login.php'>Login here</a>";
            $_POST = [];
        } else $error = "Registration failed";
    } else $error = implode('<br>',$errors);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:linear-gradient(135deg,#667eea,#764ba2);padding:50px 0;}
        .register-card{background:white;border-radius:20px;max-width:600px;margin:auto;overflow:hidden;}
        .register-header{background:#27ae60;color:white;padding:25px;text-align:center;}
        .register-body{padding:30px;}
        .btn-register{background:#27ae60;border:none;width:100%;padding:12px;border-radius:25px;}
    </style>
</head>
<body>
<div class="container">
    <div class="register-card">
        <div class="register-header"><i class="fas fa-user-graduate fa-3x"></i><h2>Register</h2></div>
        <div class="register-body">
            <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST">
                <div class="mb-3"><label>Full Name</label><input type="text" name="student_name" class="form-control" required value="<?=htmlspecialchars($_POST['student_name']??'')?>"></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required value="<?=htmlspecialchars($_POST['email']??'')?>"></div>
                <div class="mb-3"><label>Phone (01XXXXXXXXX)</label><input type="tel" name="phone" class="form-control" required value="<?=htmlspecialchars($_POST['phone']??'')?>"></div>
                <div class="mb-3"><label>Batch Year</label><input type="number" name="batch" class="form-control" required value="<?=htmlspecialchars($_POST['batch']??date('Y'))?>"></div>
                <div class="mb-3"><label>Department</label><select name="department_id" class="form-select" required>
                    <option value="">Select</option>
                    <?php foreach($departments as $d): ?>
                        <option value="<?=$d['department_id']?>" <?=($_POST['department_id']??'')==$d['department_id']?'selected':''?>><?=$d['department_name']?></option>
                    <?php endforeach; ?>
                </select></div>
                <div class="mb-3"><label>Advisor</label><select name="advisor_id" class="form-select" required>
                    <option value="">Select</option>
                    <?php foreach($advisors as $a): ?>
                        <option value="<?=$a['advisor_id']?>" <?=($_POST['advisor_id']??'')==$a['advisor_id']?'selected':''?>><?=$a['teacher_name']?> (<?=$a['department_name']?>)</option>
                    <?php endforeach; ?>
                </select></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                <button type="submit" class="btn btn-primary btn-register">Register</button>
            </form>
            <hr><div class="text-center"><a href="login.php">Login</a> | <a href="../index.php">Home</a></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>