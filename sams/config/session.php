<?php
/**
 * Session Management File
 * SAMS - Student Academic Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Student Session Functions
function setStudentSession($student) {
    $_SESSION['user_id'] = $student['student_id'];
    $_SESSION['user_name'] = $student['student_name'];
    $_SESSION['user_email'] = $student['email'];
    $_SESSION['user_type'] = 'student';
    $_SESSION['department_id'] = $student['department_id'];
    $_SESSION['login_time'] = time();
}

function isStudentLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
}

// Admin Session Functions
function setAdminSession($admin) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['admin_name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_role'] = $admin['role_id'];
    $_SESSION['admin_role_name'] = getAdminRoleName($admin['role_id']);
    $_SESSION['user_type'] = 'admin';
    $_SESSION['login_time'] = time();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Check if session has expired
function isSessionExpired() {
    if (isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            return true;
        }
    }
    return false;
}

// Destroy all sessions
function destroySession() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

// Require student login
function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        header('Location: /auth/login.php?error=Please login to continue');
        exit();
    }
    if (isSessionExpired()) {
        destroySession();
        header('Location: /auth/login.php?error=Session expired. Please login again');
        exit();
    }
    // Update last activity
    $_SESSION['login_time'] = time();
}

// Require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /auth/admin-login.php?error=Please login as admin');
        exit();
    }
    if (isSessionExpired()) {
        destroySession();
        header('Location: /auth/admin-login.php?error=Session expired. Please login again');
        exit();
    }
    $_SESSION['login_time'] = time();
}

// Get admin role name
function getAdminRoleName($role_id) {
    $sql = "SELECT role_name FROM admin_role WHERE role_id = ?";
    $result = dbGetRow($sql, [$role_id]);
    return $result ? $result['role_name'] : 'Unknown';
}

// Get current user info
function getCurrentUser() {
    if (isStudentLoggedIn()) {
        return dbGetRow("SELECT * FROM student WHERE student_id = ?", [$_SESSION['user_id']]);
    } elseif (isAdminLoggedIn()) {
        return dbGetRow("SELECT * FROM admin WHERE admin_id = ?", [$_SESSION['admin_id']]);
    }
    return null;
}

// Check admin permission
function hasAdminPermission($permission) {
    if (!isAdminLoggedIn()) return false;
    
    $sql = "SELECT CheckAdminPermission(?, ?) as has_permission";
    $result = dbGetRow($sql, [$_SESSION['admin_id'], $permission]);
    return $result && $result['has_permission'] == 1;
}
function logAdminAction($admin_id, $action_type, $table_name, $record_id, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    dbExecute("CALL LogAdminAction(?, ?, ?, ?, ?, ?, ?)", [
        $admin_id, $action_type, $table_name, $record_id, $description, $ip, $user_agent
    ]);
}
?>