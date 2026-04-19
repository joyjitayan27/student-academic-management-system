<?php
// =============================================
// HELPER FUNCTIONS FOR SAMS
// =============================================

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function getDepartmentName($dept_id) {
    $r = dbGetRow("SELECT department_name FROM department WHERE department_id = ?", [$dept_id]);
    return $r ? $r['department_name'] : 'N/A';
}

function getTeacherName($teacher_id) {
    $r = dbGetRow("SELECT teacher_name FROM teacher WHERE teacher_id = ?", [$teacher_id]);
    return $r ? $r['teacher_name'] : 'N/A';
}

function getCourseTitle($course_id) {
    $r = dbGetRow("SELECT course_code, course_title FROM course WHERE course_id = ?", [$course_id]);
    return $r ? $r['course_code'] . ' - ' . $r['course_title'] : 'N/A';
}

function getSemesterName($sem_id) {
    $r = dbGetRow("SELECT name, year FROM semester WHERE semester_id = ?", [$sem_id]);
    return $r ? $r['name'] . ' ' . $r['year'] : 'N/A';
}

function calculateCGPA($student_id) {
    $r = dbGetRow("SELECT COALESCE(ROUND(AVG(gpa), 2), 0) as cgpa FROM result WHERE student_id = ? AND gpa > 0", [$student_id]);
    return $r['cgpa'];
}

function getTotalCredits($student_id) {
    $r = dbGetRow("SELECT COALESCE(SUM(c.credit), 0) as total FROM result r JOIN course c ON r.course_id = c.course_id WHERE r.student_id = ? AND r.gpa > 0", [$student_id]);
    return $r['total'];
}

function getAttendancePercentage($student_id, $course_id, $sem_id) {
    $r = dbGetRow("SELECT COUNT(*) as total, COUNT(CASE WHEN status='present' THEN 1 END) as present FROM attendance WHERE student_id = ? AND course_id = ? AND semester_id = ?", [$student_id, $course_id, $sem_id]);
    return $r['total'] > 0 ? round(($r['present'] / $r['total']) * 100, 2) : 0;
}

function getRegisteredCourses($student_id, $sem_id) {
    return dbGetAll("
        SELECT c.*, ct.teacher_id, t.teacher_name, ct.section, ct.room, ct.schedule
        FROM course_registration cr
        JOIN course c ON cr.course_id = c.course_id
        JOIN course_teacher ct ON c.course_id = ct.course_id AND ct.semester_id = cr.semester_id
        JOIN teacher t ON ct.teacher_id = t.teacher_id
        WHERE cr.student_id = ? AND cr.semester_id = ? AND cr.is_dropped = 0
    ", [$student_id, $sem_id]);
}

function getStatusBadge($status) {
    $badges = [
        'pending' => 'warning', 'processing' => 'info', 'approved' => 'success',
        'rejected' => 'danger', 'confirmed' => 'success', 'issued' => 'success',
        'active' => 'success', 'graduated' => 'primary', 'suspended' => 'danger',
        'withdrawn' => 'secondary'
    ];
    $c = $badges[$status] ?? 'secondary';
    return "<span class='badge bg-$c'>" . ucfirst($status) . "</span>";
}

function getGradeBadge($grade) {
    $colors = [
        'A' => 'success', 'A-' => 'success', 'B+' => 'info', 'B' => 'info',
        'B-' => 'info', 'C+' => 'warning', 'C' => 'warning', 'C-' => 'warning',
        'D' => 'danger', 'F' => 'danger', 'I' => 'secondary', 'W' => 'secondary'
    ];
    $c = $colors[$grade] ?? 'secondary';
    return "<span class='badge bg-$c'>$grade</span>";
}

function formatDate($date, $format = DATE_FORMAT) {
    return ($date && $date != '0000-00-00') ? date($format, strtotime($date)) : 'N/A';
}

function getStudentStats($student_id) {
    $stats = [];
    $stats['total_courses'] = dbGetRow("SELECT COUNT(*) as total FROM course_registration WHERE student_id = ? AND is_dropped = 0", [$student_id])['total'];
    $stats['courses_completed'] = dbGetRow("SELECT COUNT(*) as completed FROM result WHERE student_id = ? AND gpa > 0", [$student_id])['completed'];
    $stats['cgpa'] = calculateCGPA($student_id);
    $stats['total_credits'] = getTotalCredits($student_id);
    $stats['pending_applications'] = dbGetRow("SELECT COUNT(*) as pending FROM application WHERE student_id = ? AND status IN ('pending','processing')", [$student_id])['pending'];
    return $stats;
}

function getAdminStats() {
    $stats['total_students'] = dbGetRow("SELECT COUNT(*) as total FROM student WHERE status = 'active'")['total'];
    $stats['total_teachers'] = dbGetRow("SELECT COUNT(*) as total FROM teacher")['total'];
    $stats['total_courses'] = dbGetRow("SELECT COUNT(*) as total FROM course WHERE is_active = 1")['total'];
    $stats['pending_applications'] = dbGetRow("SELECT COUNT(*) as total FROM application WHERE status = 'pending'")['total'];
    return $stats;
}

function getCurrentSemester() {
    return dbGetRow("SELECT semester_id, name, year FROM semester WHERE is_current = 1 LIMIT 1");
}

function canApplyForLaptop($student_id) {
    $s = dbGetRow("SELECT batch FROM student WHERE student_id = ?", [$student_id]);
    if ($s) {
        $year_diff = date('Y') - $s['batch'];
        return $year_diff >= 2;
    }
    return false;
}

// =============================================
// ADMIN PERMISSION FUNCTIONS (ADD THESE)
// =============================================


?>