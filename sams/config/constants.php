<?php
/**
 * System Constants File
 * SAMS - Student Academic Management System
 */

// Application Info
define('APP_NAME', 'Student Academic Management System');
define('APP_SHORT_NAME', 'SAMS');
define('APP_VERSION', '1.0.0');
define('APP_YEAR', date('Y'));

// File Paths
define('BASE_URL', 'http://localhost/sams/');
define('BASE_PATH', dirname(__DIR__));

// Upload Directories
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('PROFILE_IMG_PATH', UPLOAD_PATH . 'profiles/');
define('DOCUMENT_PATH', UPLOAD_PATH . 'documents/');

// Application Types
define('APPLICATION_TYPES', [
    'certificate' => 'Certificate Application',
    'transcript' => 'Transcript Application',
    'scholarship' => 'Scholarship Application',
    'laptop' => 'Laptop Application',
    'transport_card' => 'Transport Card Application',
    'convocation' => 'Convocation Application'
]);

// Grade Points
define('GRADE_POINTS', [
    'A' => 4.00, 'A-' => 3.70, 'B+' => 3.30, 'B' => 3.00,
    'B-' => 2.70, 'C+' => 2.30, 'C' => 2.00, 'C-' => 1.70,
    'D' => 1.00, 'F' => 0.00, 'I' => 0.00, 'W' => 0.00
]);

// Student Statuses
define('STUDENT_STATUSES', [
    'active' => 'Active',
    'graduated' => 'Graduated',
    'suspended' => 'Suspended',
    'withdrawn' => 'Withdrawn'
]);

// Application Statuses
define('APP_STATUSES', [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'approved' => 'Approved',
    'rejected' => 'Rejected'
]);

// Attendance Statuses
define('ATTENDANCE_STATUSES', [
    'present' => 'Present',
    'absent' => 'Absent',
    'late' => 'Late'
]);

// Pagination
define('ITEMS_PER_PAGE', 15);

// Date Format
define('DATE_FORMAT', 'd M, Y');
define('DATE_FORMAT_DB', 'Y-m-d');
define('DATETIME_FORMAT', 'd M, Y h:i A');

// Admin Role Levels
define('ADMIN_ROLE_SENIOR', 1);
define('ADMIN_ROLE_JUNIOR', 2);
define('ADMIN_ROLE_TRAINEE', 3);
?>