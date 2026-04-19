<?php
require_once '../config/bootstrap.php';

// Require student login
requireStudentLogin();

$page_title = 'Teacher Evaluation';
$student_id = $_SESSION['user_id'];

// Get current semester
$current_semester = getCurrentSemester();
$current_semester_id = $current_semester ? $current_semester['semester_id'] : null;

// Get courses for current semester that haven't been evaluated yet
$courses_to_evaluate = [];
if ($current_semester_id) {
    $courses_to_evaluate = dbGetAll("
        SELECT DISTINCT 
            c.course_id,
            c.course_code,
            c.course_title,
            ct.teacher_id,
            t.teacher_name,
            ct.section
        FROM course_registration cr
        JOIN course c ON cr.course_id = c.course_id
        JOIN course_teacher ct ON c.course_id = ct.course_id AND ct.semester_id = cr.semester_id
        JOIN teacher t ON ct.teacher_id = t.teacher_id
        WHERE cr.student_id = ? 
            AND cr.semester_id = ?
            AND cr.is_dropped = FALSE
            AND NOT EXISTS (
                SELECT 1 FROM teacher_evaluation te 
                WHERE te.student_id = cr.student_id 
                    AND te.course_id = cr.course_id 
                    AND te.semester_id = cr.semester_id
            )
    ", [$student_id, $current_semester_id]);
}

// Get previously completed evaluations
$past_evaluations = dbGetAll("
    SELECT 
        te.*,
        c.course_code,
        c.course_title,
        t.teacher_name,
        s.name as semester_name,
        s.year
    FROM teacher_evaluation te
    JOIN course c ON te.course_id = c.course_id
    JOIN teacher t ON te.teacher_id = t.teacher_id
    JOIN semester s ON te.semester_id = s.semester_id
    WHERE te.student_id = ?
    ORDER BY te.created_at DESC
", [$student_id]);

// Handle evaluation submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_evaluation'])) {
    $course_id = intval($_POST['course_id']);
    $teacher_id = intval($_POST['teacher_id']);
    $rating = intval($_POST['rating']);
    $feedback = sanitize($_POST['feedback'] ?? '');
    
    // Validation
    $errors = [];
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Invalid rating. Please select 1-5.';
    }
    if (empty($feedback)) {
        $errors[] = 'Please provide feedback.';
    }
    
    if (empty($errors)) {
        $sql = "INSERT INTO teacher_evaluation (student_id, teacher_id, course_id, semester_id, rating, feedback, evaluation_date) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
        $result = dbInsert($sql, [$student_id, $teacher_id, $course_id, $current_semester_id, $rating, $feedback]);
        
        if ($result) {
            $success_message = 'Thank you for your valuable feedback!';
            // Refresh the page to remove evaluated course from list
            header("refresh:2");
        } else {
            $error_message = 'Failed to submit evaluation. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get evaluation statistics for the student
$eval_stats = dbGetRow("
    SELECT 
        COUNT(*) as total_evaluations,
        ROUND(AVG(rating), 1) as avg_rating
    FROM teacher_evaluation
    WHERE student_id = ?
", [$student_id]);
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-star"></i> Teacher Evaluation</h2>
    <p class="text-muted">Your feedback helps improve teaching quality</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Evaluation Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card text-center">
            <i class="fas fa-chalkboard-user fa-2x text-primary"></i>
            <h3 class="mt-2"><?php echo $eval_stats['total_evaluations']; ?></h3>
            <p class="text-muted mb-0">Total Evaluations Given</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card text-center">
            <i class="fas fa-chart-line fa-2x text-warning"></i>
            <h3 class="mt-2"><?php echo $eval_stats['avg_rating'] ? number_format($eval_stats['avg_rating'], 1) : 'N/A'; ?></h3>
            <p class="text-muted mb-0">Your Average Rating Given</p>
        </div>
    </div>
</div>

<!-- Courses to Evaluate -->
<?php if (!empty($courses_to_evaluate)): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-edit text-primary"></i> Courses to Evaluate (Current Semester)</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($courses_to_evaluate as $course): ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">
                            <strong><?php echo $course['course_code']; ?></strong> - <?php echo $course['course_title']; ?>
                        </h6>
                        <p class="card-text">
                            <i class="fas fa-chalkboard-user"></i> Teacher: <?php echo $course['teacher_name']; ?><br>
                            <i class="fas fa-users"></i> Section: <?php echo $course['section']; ?>
                        </p>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                data-bs-target="#evalModal<?php echo $course['course_id']; ?>">
                            <i class="fas fa-star"></i> Evaluate Now
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Evaluation Modal for each course -->
            <div class="modal fade" id="evalModal<?php echo $course['course_id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="">
                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                            <input type="hidden" name="teacher_id" value="<?php echo $course['teacher_id']; ?>">
                            
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-star"></i> Evaluate: <?php echo $course['course_code']; ?> - <?php echo $course['teacher_name']; ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Rating (1-5):</label>
                                    <div class="rating-stars">
                                        <div class="btn-group" role="group">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <input type="radio" class="btn-check" name="rating" id="star<?php echo $i . '_' . $course['course_id']; ?>" 
                                                   value="<?php echo $i; ?>" autocomplete="off" required>
                                            <label class="btn btn-outline-warning" for="star<?php echo $i . '_' . $course['course_id']; ?>">
                                                <i class="fas fa-star"></i> <?php echo $i; ?>
                                            </label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="rating-labels mt-2">
                                        <small class="text-muted">1 = Poor | 2 = Fair | 3 = Good | 4 = Very Good | 5 = Excellent</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Feedback / Comments:</label>
                                    <textarea name="feedback" class="form-control" rows="5" required 
                                              placeholder="Please share your experience... What did the teacher do well? What could be improved?"></textarea>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <small>Your feedback is anonymous and helps improve teaching quality. Please be honest and constructive.</small>
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="submit_evaluation" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Evaluation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> 
    You have completed all evaluations for the current semester. Thank you for your feedback!
</div>
<?php endif; ?>

<!-- Past Evaluations -->
<?php if (!empty($past_evaluations)): ?>
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Your Past Evaluations</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Course</th>
                        <th>Teacher</th>
                        <th>Rating</th>
                        <th>Feedback</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($past_evaluations as $eval): ?>
                    <tr>
                        <td><?php echo $eval['semester_name'] . ' ' . $eval['year']; ?></td>
                        <td><?php echo $eval['course_code']; ?></td>
                        <td><?php echo $eval['teacher_name']; ?></td>
                        <td>
                            <div class="text-warning">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $eval['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                (<?php echo $eval['rating']; ?>)
                            </div>
                        </td>
                        <td><?php echo substr($eval['feedback'], 0, 100); ?>...</td>
                        <td><?php echo formatDate($eval['evaluation_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Evaluation Guidelines -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb text-warning"></i> Evaluation Guidelines</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="fas fa-gavel fa-2x text-danger mb-2"></i>
                            <p class="small">Be Honest & Fair</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="fas fa-comment-dots fa-2x text-info mb-2"></i>
                            <p class="small">Provide Constructive Feedback</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="fas fa-user-secret fa-2x text-success mb-2"></i>
                            <p class="small">Anonymous & Confidential</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                            <p class="small">Helps Improve Quality</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle"></i>
                    <small>Your feedback is valuable and helps maintain high teaching standards. All evaluations are anonymous and cannot be traced back to individual students.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars .btn-check:checked + .btn {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}
.rating-stars .btn {
    font-size: 1.2rem;
    padding: 8px 16px;
}
</style>

<?php include '../includes/footer.php'; ?>