<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate form ID
if (isset($_GET['form_id'])) {
    $form_id = intval($_GET['form_id']);
    $username = $_SESSION['username'];

    // Get student ID
    $student_query = $conn->prepare("SELECT id FROM register WHERE username = ?");
    $student_query->bind_param("s", $username);
    $student_query->execute();
    $student_result = $student_query->get_result();

    if ($student_result->num_rows > 0) {
        $student_data = $student_result->fetch_assoc();
        $student_id = $student_data['id'];

        // Fetch the response data
        $response_query = $conn->prepare("SELECT response_data FROM form_responses WHERE student_id = ? AND form_id = ?");
        $response_query->bind_param("ii", $student_id, $form_id);
        $response_query->execute();
        $response_result = $response_query->get_result();

        if ($response_result->num_rows > 0) {
            $response = $response_result->fetch_assoc();
            $response_data = json_decode($response['response_data'], true);

            // Generate Word Document content
            $content = "Form ID: $form_id\n";
            $content .= "Student: " . htmlspecialchars($username) . "\n\n";
            $content .= "Form Responses:\n";
            
            if ($response_data) {
                foreach ($response_data as $entry) {
                    $content .= htmlspecialchars($entry['field_name']) . ": " . htmlspecialchars($entry['field_value']) . "\n";
                }
            } else {
                $content .= "No responses available for this form.";
            }

            // Set headers for Word document download
            header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
            header("Content-Disposition: attachment; filename=form_response_$form_id.docx");
            echo nl2br($content); // Convert newlines to <br> tags for better formatting in Word
            exit();
        } else {
            echo "No response data found.";
        }
    } else {
        echo "Student not found.";
    }
} else {
    echo "Invalid request.";
}
?>
