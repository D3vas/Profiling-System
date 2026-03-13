<?php
$conn = new mysqli("localhost", "root", "", "profiling_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['studentId'])) {
    $studentId = $_POST['studentId'];

    // Use prepared statement to avoid SQL injection
    $sql = "SELECT 
                id,
                first_name,
                middle_name,
                last_name,
                gender,
                address,
                email,
                phone,
                year_section,
                status,
                program,
                profile_picture  -- Added the profile picture column here
            FROM register
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId); // Bind the studentId as an integer parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $studentData = $result->fetch_assoc();

        // Query for form responses
        $formSql = "SELECT form_id FROM form_responses WHERE student_id = ?";
        $formStmt = $conn->prepare($formSql);
        $formStmt->bind_param("i", $studentId); // Bind the studentId as an integer parameter
        $formStmt->execute();
        $formResult = $formStmt->get_result();

        $forms = [];
        while ($formRow = $formResult->fetch_assoc()) {
            $forms[] = $formRow['form_id'];
        }

        $studentData['forms'] = $forms; // Add forms to student data

        echo json_encode($studentData);
    } else {
        echo json_encode(['error' => 'No student found with the provided ID.']);
    }

    $stmt->close();
    $formStmt->close();
}

$conn->close();
?>
