-- ======================================================
-- STUDENT ACADEMIC MANAGEMENT SYSTEM (SAMS) - DATABASE SCHEMA ONLY
-- (NO SAMPLE DATA - Tables only)
-- ======================================================

DROP DATABASE IF EXISTS sams;
CREATE DATABASE sams;
USE sams;

-- ======================================================
-- 1. department
-- ======================================================
CREATE TABLE department (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    building VARCHAR(50),
    established_year YEAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ======================================================
-- 2. teacher
-- ======================================================
CREATE TABLE teacher (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    designation VARCHAR(50),
    department_id INT NOT NULL,
    joining_date DATE,
    office_room VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_teacher_department (department_id),
    INDEX idx_teacher_email (email)
);

-- ======================================================
-- 3. semester
-- ======================================================
CREATE TABLE semester (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(10) NOT NULL,
    year INT NOT NULL,
    is_current BOOLEAN DEFAULT FALSE,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_semester (name, year)
);

-- ======================================================
-- 4. advisor
-- ======================================================
CREATE TABLE advisor (
    advisor_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNIQUE NOT NULL,
    max_students INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_advisor_teacher (teacher_id)
);

-- ======================================================
-- 5. student
-- ======================================================
CREATE TABLE student (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    department_id INT NOT NULL,
    advisor_id INT NOT NULL,
    batch INT,
    current_semester_id INT,
    admission_date DATE,
    status ENUM('active', 'graduated', 'suspended', 'withdrawn') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (advisor_id) REFERENCES advisor(advisor_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (current_semester_id) REFERENCES semester(semester_id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_student_email (email),
    INDEX idx_student_department (department_id),
    INDEX idx_student_advisor (advisor_id),
    INDEX idx_student_status (status)
);

-- ======================================================
-- 6. course
-- ======================================================
CREATE TABLE course (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_title VARCHAR(100) NOT NULL,
    credit DECIMAL(3,1) NOT NULL,
    department_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_credit CHECK (credit > 0),
    FOREIGN KEY (department_id) REFERENCES department(department_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_course_department (department_id),
    INDEX idx_course_code (course_code)
);

-- ======================================================
-- 7. course_teacher
-- ======================================================
CREATE TABLE course_teacher (
    course_teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    semester_id INT NOT NULL,
    section VARCHAR(10) DEFAULT 'A',
    room VARCHAR(20),
    schedule VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_course_teacher_semester_section (course_id, teacher_id, semester_id, section),
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_course_teacher_course (course_id),
    INDEX idx_course_teacher_teacher (teacher_id),
    INDEX idx_course_teacher_semester (semester_id)
);

-- ======================================================
-- 8. course_registration
-- ======================================================
CREATE TABLE course_registration (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    registration_date DATE,
    is_dropped BOOLEAN DEFAULT FALSE,
    drop_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (student_id, course_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_registration_student (student_id),
    INDEX idx_registration_course (course_id),
    INDEX idx_registration_semester (semester_id)
);

-- ======================================================
-- 9. result
-- ======================================================
CREATE TABLE result (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    grade VARCHAR(2),
    gpa DECIMAL(3,2),
    is_retake BOOLEAN DEFAULT FALSE,
    previous_grade VARCHAR(2),
    published_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_result (student_id, course_id, semester_id),
    CONSTRAINT chk_grade CHECK (grade IN ('A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D', 'D+', 'F', 'I', 'W')),
    CONSTRAINT chk_gpa CHECK (gpa BETWEEN 0 AND 4),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_result_student (student_id),
    INDEX idx_result_course (course_id),
    INDEX idx_result_semester (semester_id)
);

-- ======================================================
-- 10. continuous_assessment
-- ======================================================
CREATE TABLE continuous_assessment (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    quiz DECIMAL(5,2) DEFAULT 0,
    mid DECIMAL(5,2) DEFAULT 0,
    assignment DECIMAL(5,2) DEFAULT 0,
    project DECIMAL(5,2) DEFAULT 0,
    final_exam DECIMAL(5,2) DEFAULT 0,
    total DECIMAL(5,2) GENERATED ALWAYS AS (
        COALESCE(quiz,0) + COALESCE(mid,0) + COALESCE(assignment,0) + 
        COALESCE(project,0) + COALESCE(final_exam,0)
    ) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_assessment (student_id, course_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_assessment_student (student_id),
    INDEX idx_assessment_course (course_id)
);

-- ======================================================
-- 11. attendance
-- ======================================================
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') DEFAULT 'present',
    remarks VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, course_id, date),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_attendance_date (date),
    INDEX idx_attendance_student (student_id),
    INDEX idx_attendance_course (course_id)
);

-- ======================================================
-- 12. teacher_evaluation
-- ======================================================
CREATE TABLE teacher_evaluation (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    course_id INT NOT NULL,
    semester_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    evaluation_date DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_evaluation (student_id, teacher_id, course_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teacher(teacher_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_evaluation_teacher (teacher_id),
    INDEX idx_evaluation_course (course_id)
);

-- ======================================================
-- 13. exam_clearance
-- ======================================================
CREATE TABLE exam_clearance (
    clearance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    library_clearance BOOLEAN DEFAULT FALSE,
    library_clearance_date DATE,
    lab_clearance BOOLEAN DEFAULT FALSE,
    lab_clearance_date DATE,
    accounts_clearance BOOLEAN DEFAULT FALSE,
    accounts_clearance_date DATE,
    status ENUM('cleared', 'not cleared') GENERATED ALWAYS AS (
        CASE 
            WHEN library_clearance = TRUE 
             AND lab_clearance = TRUE 
             AND accounts_clearance = TRUE 
            THEN 'cleared'
            ELSE 'not cleared'
        END
    ) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_clearance (student_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_clearance_student (student_id)
);

-- ======================================================
-- 14. unified_application
-- ======================================================
CREATE TABLE application (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    application_type VARCHAR(30) NOT NULL,
    request_date DATE DEFAULT CURRENT_DATE,
    status ENUM('pending', 'approved', 'rejected', 'processing') DEFAULT 'pending',
    approved_date DATE,
    rejection_reason TEXT,
    
    -- Certificate specific fields
    certificate_type VARCHAR(50),
    
    -- Transcript specific fields
    copies INT DEFAULT 1,
    
    -- Scholarship specific fields
    cgpa DECIMAL(3,2),
    family_income DECIMAL(10,2),
    
    -- Transport card specific fields
    route VARCHAR(50),
    pickup_point VARCHAR(50),
    
    -- Convocation specific fields
    graduation_year YEAR,
    
    -- Laptop specific fields
    laptop_application_date DATE,
    
    -- Additional flexible data
    extra_notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_application_type CHECK (application_type IN (
        'certificate', 'transcript', 'scholarship', 'laptop', 'transport_card', 'convocation'
    )),
    
    FOREIGN KEY (student_id) REFERENCES student(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_application_student (student_id),
    INDEX idx_application_type (application_type),
    INDEX idx_application_status (status),
    INDEX idx_application_cgpa (cgpa)
);

-- ======================================================
-- 15. application_log
-- ======================================================
CREATE TABLE application_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20),
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changed_by VARCHAR(100),
    remarks TEXT,
    FOREIGN KEY (application_id) REFERENCES application(application_id) ON DELETE CASCADE,
    INDEX idx_log_application (application_id)
);

-- ======================================================
-- 16. ADMIN SYSTEM (Professional Role Management)
-- ======================================================

-- 16.1 admin_role table
CREATE TABLE admin_role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_level INT NOT NULL UNIQUE,
    permission_level ENUM('full', 'partial', 'readonly') NOT NULL,
    can_manage_admins BOOLEAN DEFAULT FALSE,
    can_manage_students BOOLEAN DEFAULT FALSE,
    can_manage_teachers BOOLEAN DEFAULT FALSE,
    can_manage_courses BOOLEAN DEFAULT FALSE,
    can_manage_applications BOOLEAN DEFAULT FALSE,
    can_view_reports BOOLEAN DEFAULT FALSE,
    can_manage_system BOOLEAN DEFAULT FALSE,
    can_approve_applications BOOLEAN DEFAULT FALSE,
    can_delete_records BOOLEAN DEFAULT FALSE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 16.2 admin table
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    position VARCHAR(50) NOT NULL,
    employee_id VARCHAR(50) UNIQUE,
    joining_date DATE,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (role_id) REFERENCES admin_role(role_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES admin(admin_id) ON DELETE SET NULL,
    
    INDEX idx_admin_email (email),
    INDEX idx_admin_role (role_id),
    INDEX idx_admin_active (is_active)
);

-- 16.3 admin_log table
CREATE TABLE admin_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type ENUM('CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'LOGIN', 'LOGOUT', 'VIEW', 'EXPORT') NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    action_description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE CASCADE,
    
    INDEX idx_log_admin (admin_id),
    INDEX idx_log_action (action_type),
    INDEX idx_log_timestamp (action_timestamp),
    INDEX idx_log_table (table_name)
);

-- 16.4 admin_session table
CREATE TABLE admin_session (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE CASCADE,
    
    INDEX idx_session_token (session_token),
    INDEX idx_session_admin (admin_id),
    INDEX idx_session_active (is_active)
);

-- ======================================================
-- 17. STORED PROCEDURES
-- ======================================================

DELIMITER //

-- Get Admin Permissions
CREATE PROCEDURE GetAdminPermissions(IN p_admin_id INT)
BEGIN
    SELECT 
        a.admin_id,
        a.admin_name,
        a.position,
        r.role_name,
        r.permission_level,
        r.can_manage_admins,
        r.can_manage_students,
        r.can_manage_teachers,
        r.can_manage_courses,
        r.can_manage_applications,
        r.can_view_reports,
        r.can_manage_system,
        r.can_approve_applications,
        r.can_delete_records
    FROM admin a
    JOIN admin_role r ON a.role_id = r.role_id
    WHERE a.admin_id = p_admin_id AND a.is_active = TRUE;
END//

-- Log Admin Action
CREATE PROCEDURE LogAdminAction(
    IN p_admin_id INT,
    IN p_action_type VARCHAR(20),
    IN p_table_name VARCHAR(50),
    IN p_record_id INT,
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO admin_log (admin_id, action_type, table_name, record_id, action_description, ip_address, user_agent)
    VALUES (p_admin_id, p_action_type, p_table_name, p_record_id, p_description, p_ip_address, p_user_agent);
END//

-- Check Admin Permission Function
CREATE FUNCTION CheckAdminPermission(
    p_admin_id INT,
    p_permission_type VARCHAR(50)
) 
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE has_permission BOOLEAN DEFAULT FALSE;
    DECLARE permission_level VARCHAR(20);
    DECLARE can_manage BOOLEAN DEFAULT FALSE;
    
    SELECT r.permission_level,
           CASE 
               WHEN p_permission_type = 'manage_admins' THEN r.can_manage_admins
               WHEN p_permission_type = 'manage_students' THEN r.can_manage_students
               WHEN p_permission_type = 'manage_teachers' THEN r.can_manage_teachers
               WHEN p_permission_type = 'manage_courses' THEN r.can_manage_courses
               WHEN p_permission_type = 'manage_applications' THEN r.can_manage_applications
               WHEN p_permission_type = 'view_reports' THEN r.can_view_reports
               WHEN p_permission_type = 'manage_system' THEN r.can_manage_system
               WHEN p_permission_type = 'approve_applications' THEN r.can_approve_applications
               WHEN p_permission_type = 'delete_records' THEN r.can_delete_records
               ELSE FALSE
           END INTO permission_level, can_manage
    FROM admin a
    JOIN admin_role r ON a.role_id = r.role_id
    WHERE a.admin_id = p_admin_id AND a.is_active = TRUE;
    
    IF permission_level = 'full' THEN
        SET has_permission = TRUE;
    ELSEIF permission_level = 'partial' AND can_manage = TRUE THEN
        SET has_permission = TRUE;
    ELSEIF permission_level = 'readonly' AND p_permission_type = 'view_reports' THEN
        SET has_permission = TRUE;
    ELSEIF permission_level = 'readonly' AND p_permission_type = 'approve_applications' AND can_manage = TRUE THEN
        SET has_permission = TRUE;
    ELSE
        SET has_permission = FALSE;
    END IF;
    
    RETURN has_permission;
END//

-- Admin Login Log Trigger
CREATE TRIGGER admin_login_log
AFTER UPDATE ON admin
FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO admin_log (admin_id, action_type, action_description, action_timestamp)
        VALUES (NEW.admin_id, 'LOGIN', CONCAT('Admin logged in at ', NEW.last_login), NEW.last_login);
    END IF;
END//

DELIMITER ;

-- ======================================================
-- 18. VIEWS (Admin)
-- ======================================================

-- Admin Activity Summary View
CREATE VIEW admin_activity_summary AS
SELECT 
    a.admin_id,
    a.admin_name,
    a.position,
    r.role_name,
    COUNT(l.log_id) AS total_actions,
    COUNT(CASE WHEN l.action_type = 'CREATE' THEN 1 END) AS create_actions,
    COUNT(CASE WHEN l.action_type = 'UPDATE' THEN 1 END) AS update_actions,
    COUNT(CASE WHEN l.action_type = 'DELETE' THEN 1 END) AS delete_actions,
    COUNT(CASE WHEN l.action_type = 'APPROVE' THEN 1 END) AS approve_actions,
    MAX(l.action_timestamp) AS last_action_time
FROM admin a
JOIN admin_role r ON a.role_id = r.role_id
LEFT JOIN admin_log l ON a.admin_id = l.admin_id
GROUP BY a.admin_id, a.admin_name, a.position, r.role_name;

-- Active Admins View
CREATE VIEW active_admins AS
SELECT 
    admin_id,
    admin_name,
    email,
    position,
    role_id,
    last_login,
    created_at
FROM admin
WHERE is_active = TRUE
ORDER BY role_id ASC;

-- ======================================================
-- 19. VIEWS (Student)
-- ======================================================

-- Student academic summary
CREATE VIEW student_academic_summary AS
SELECT 
    s.student_id,
    s.student_name,
    s.email,
    d.department_name,
    s.batch,
    sem.name AS current_semester_name,
    sem.year AS current_year,
    s.status,
    COALESCE(ROUND(AVG(r.gpa), 2), 0.00) AS cgpa,
    COUNT(DISTINCT CASE WHEN r.gpa IS NOT NULL THEN r.course_id END) AS courses_completed,
    COALESCE(SUM(c.credit), 0) AS total_credits_completed
FROM student s
LEFT JOIN department d ON s.department_id = d.department_id
LEFT JOIN semester sem ON s.current_semester_id = sem.semester_id
LEFT JOIN result r ON s.student_id = r.student_id AND r.gpa IS NOT NULL AND r.gpa > 0
LEFT JOIN course c ON r.course_id = c.course_id
GROUP BY s.student_id, s.student_name, s.email, d.department_name, 
         s.batch, sem.name, sem.year, s.status;

-- Student attendance summary
CREATE VIEW student_attendance_summary AS
SELECT 
    a.student_id,
    s.student_name,
    a.course_id,
    c.course_code,
    c.course_title,
    a.semester_id,
    sem.name AS semester_name,
    sem.year,
    COUNT(*) AS total_classes,
    COUNT(CASE WHEN a.status = 'present' THEN 1 END) AS present_classes,
    ROUND(100.0 * COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*), 2) AS attendance_percentage
FROM attendance a
JOIN student s ON a.student_id = s.student_id
JOIN course c ON a.course_id = c.course_id
JOIN semester sem ON a.semester_id = sem.semester_id
GROUP BY a.student_id, s.student_name, a.course_id, c.course_code, c.course_title, a.semester_id, sem.name, sem.year;

-- Scholarship eligible students
CREATE VIEW scholarship_eligible_students AS
SELECT 
    s.student_id,
    s.student_name,
    s.email,
    d.department_name,
    sas.cgpa,
    sas.total_credits_completed,
    s.batch
FROM student s
JOIN student_academic_summary sas ON s.student_id = sas.student_id
JOIN department d ON s.department_id = d.department_id
WHERE sas.cgpa >= 3.50
  AND s.status = 'active'
  AND sas.total_credits_completed >= 30;

-- ======================================================
-- 20. TRIGGERS (Student)
-- ======================================================

DELIMITER //

-- Trigger to update student's current semester
CREATE TRIGGER update_student_current_semester
AFTER INSERT ON course_registration
FOR EACH ROW
BEGIN
    UPDATE student 
    SET current_semester_id = NEW.semester_id
    WHERE student_id = NEW.student_id
      AND (current_semester_id IS NULL OR current_semester_id < NEW.semester_id);
END//

-- Trigger to prevent duplicate results without retake flag
CREATE TRIGGER check_result_before_insert
BEFORE INSERT ON result
FOR EACH ROW
BEGIN
    DECLARE existing_count INT;
    SELECT COUNT(*) INTO existing_count
    FROM result
    WHERE student_id = NEW.student_id 
      AND course_id = NEW.course_id;
    
    IF existing_count > 0 AND NEW.is_retake = FALSE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Student already has a grade for this course. Use is_retake flag.';
    END IF;
END//

-- Trigger to log application status changes
CREATE TRIGGER log_application_status_change
AFTER UPDATE ON application
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO application_log (application_id, old_status, new_status, change_date)
        VALUES (NEW.application_id, OLD.status, NEW.status, NOW());
    END IF;
END//

DELIMITER ;

-- ======================================================
-- END OF DATABASE SCHEMA (NO SAMPLE DATA)
-- ======================================================