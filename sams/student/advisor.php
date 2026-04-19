<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'Advisor Information';
$student_id = $_SESSION['user_id'];

// Get student's advisor information
$advisor_info = dbGetRow("
    SELECT 
        s.student_id,
        s.student_name,
        s.batch,
        d.department_name,
        t.teacher_id,
        t.teacher_name,
        t.email as advisor_email,
        t.phone as advisor_phone,
        t.designation,
        t.office_room,
        t.joining_date,
        a.max_students,
        (SELECT COUNT(*) FROM student WHERE advisor_id = a.advisor_id AND status = 'active') as current_students
    FROM student s
    JOIN advisor a ON s.advisor_id = a.advisor_id
    JOIN teacher t ON a.teacher_id = t.teacher_id
    JOIN department d ON s.department_id = d.department_id
    WHERE s.student_id = ?
", [$student_id]);

// Get advisor's office hours (you can add an office_hours table if needed)
// For now, we'll set default office hours
$office_hours = [
    'Sunday' => '10:00 AM - 12:00 PM',
    'Tuesday' => '2:00 PM - 4:00 PM',
    'Thursday' => '11:00 AM - 1:00 PM'
];

// Get upcoming advising appointments (if you have an appointments table)
// For now, we'll create sample data
$upcoming_appointments = [
    ['date' => '2024-04-20', 'time' => '11:00 AM', 'purpose' => 'Course Registration'],
    ['date' => '2024-05-05', 'time' => '2:30 PM', 'purpose' => 'Academic Progress Review']
];

// Handle message submission
$message_sent = false;
$message_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($subject)) {
        $message_error = 'Please enter a subject';
    } elseif (empty($message)) {
        $message_error = 'Please enter your message';
    } else {
        // Here you would typically save to a messages table or send email
        // For now, we'll simulate success
        $message_sent = true;
        
        // Optional: Send email to advisor
        // sendEmail($advisor_info['advisor_email'], "Student Message: " . $subject, $message);
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-chalkboard-user"></i> Academic Advisor Information</h2>
    <p class="text-muted">Your academic guide and mentor</p>
</div>

<div class="row">
    <!-- Advisor Profile -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="advisor-avatar mb-3">
                    <i class="fas fa-user-tie fa-5x text-primary"></i>
                </div>
                <h4><?php echo $advisor_info['teacher_name']; ?></h4>
                <p class="text-muted"><?php echo $advisor_info['designation']; ?></p>
                <hr>
                <div class="text-start">
                    <p><i class="fas fa-building"></i> <strong>Department:</strong> <?php echo $advisor_info['department_name']; ?></p>
                    <p><i class="fas fa-door-open"></i> <strong>Office Room:</strong> <?php echo $advisor_info['office_room'] ?? 'Not specified'; ?></p>
                    <p><i class="fas fa-users"></i> <strong>Students Advising:</strong> <?php echo $advisor_info['current_students']; ?> / <?php echo $advisor_info['max_students']; ?></p>
                    <p><i class="fas fa-calendar-alt"></i> <strong>Since:</strong> <?php echo formatDate($advisor_info['joining_date']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Office Hours -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-clock text-primary"></i> Office Hours</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <?php foreach ($office_hours as $day => $hours): ?>
                    <li class="mb-2">
                        <strong><?php echo $day; ?>:</strong>
                        <span class="text-muted"><?php echo $hours; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <hr>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Appointment recommended. Please email to confirm.
                </small>
            </div>
        </div>
    </div>
    
    <!-- Advisor Details & Messaging -->
    <div class="col-lg-8">
        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-address-card text-primary"></i> Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <h6>Email</h6>
                            <p><a href="mailto:<?php echo $advisor_info['advisor_email']; ?>"><?php echo $advisor_info['advisor_email']; ?></a></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                            <h6>Phone</h6>
                            <p><?php echo $advisor_info['advisor_phone'] ?? 'Not available'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Appointments -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-calendar-week text-primary"></i> Upcoming Advising Schedule</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_appointments)): ?>
                    <p class="text-muted text-center mb-0">No upcoming appointments scheduled.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo formatDate($appointment['date']); ?></td>
                                    <td><?php echo $appointment['time']; ?></td>
                                    <td><?php echo $appointment['purpose']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <small>To schedule an appointment, please email your advisor or visit during office hours.</small>
                </div>
            </div>
        </div>
        
        <!-- Message to Advisor -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-paper-plane text-primary"></i> Send Message to Advisor</h5>
            </div>
            <div class="card-body">
                <?php if ($message_sent): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Your message has been sent successfully!
                    </div>
                <?php endif; ?>
                
                <?php if ($message_error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $message_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required 
                               placeholder="e.g., Course Registration Question">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required 
                                  placeholder="Type your message here..."></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
                
                <hr>
                
                <div class="alert alert-warning">
                    <i class="fas fa-tips"></i>
                    <strong>Tips for communicating with your advisor:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Be clear about your academic concerns</li>
                        <li>Mention your student ID in the message</li>
                        <li>Allow 2-3 business days for response</li>
                        <li>For urgent matters, visit during office hours</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Information Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-graduate text-primary"></i> Your Information (for advisor reference)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Student ID:</strong> <?php echo $advisor_info['student_id']; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Name:</strong> <?php echo $advisor_info['student_name']; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Department:</strong> <?php echo $advisor_info['department_name']; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Batch:</strong> <?php echo $advisor_info['batch']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: transform 0.3s;
}
.info-box:hover {
    transform: translateY(-5px);
}
.advisor-avatar {
    width: 100px;
    height: 100px;
    background: #e8f4f8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>

<?php include '../includes/footer.php'; ?>