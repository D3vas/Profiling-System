<?php
session_start();

// Debugging: Check if the session variable is set
if (!isset($_SESSION['username'])) {
    // If the username is not set in session, print debug message and redirect to login page
    error_log("No username found in session. Redirecting to login page.");
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student ID from the register table based on username
$username = $_SESSION['username'];
$student_query = $conn->prepare("SELECT id FROM register WHERE username = ?");
$student_query->bind_param("s", $username);
$student_query->execute();
$student_result = $student_query->get_result();

if ($student_result->num_rows > 0) {
    // Fetch the student ID from the query result
    $student_data = $student_result->fetch_assoc();
    $student_id = $student_data['id'];
} else {
    // If no student found, redirect to login page
    error_log("No student found for the given username. Redirecting to login page.");
    header("Location: login.php");
    exit();
}

// Fetch the student's responses
$responses_query = $conn->prepare("SELECT form_responses.form_id, forms.form_title, form_responses.response_data
                                  FROM form_responses 
                                  JOIN forms ON form_responses.form_id = forms.id
                                  WHERE form_responses.student_id = ?");
$responses_query->bind_param("i", $student_id);
$responses_query->execute();
$responses_result = $responses_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Responses - Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="nav-side">
        <h2><i class="fas fa-clipboard-list"></i> Responses</h2>
        <ul>
            <li><a href="studenthome.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="student_profile.php"><i class="fas fa-pencil-alt"></i> Fill Forms</a></li>
            <li class="active"><a href="view_responses.php"><i class="fas fa-eye"></i> View My Responses</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

<div class="content-container">
    <h1>My Form Responses</h1>
    <?php if ($responses_result->num_rows > 0): ?>
        <?php while ($response = $responses_result->fetch_assoc()): ?>
            <div class="form-container">
                <h3><?= htmlspecialchars($response['form_title']); ?></h3>
                <?php
                $response_data = json_decode($response['response_data'], true);
                if ($response_data):
                    foreach ($response_data as $entry): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($entry['field_name']); ?>:</label>
                            <p><?= htmlspecialchars($entry['field_value']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No responses available for this form.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No form responses found.</p>
    <?php endif; ?>
</div>
</body>
</html>


