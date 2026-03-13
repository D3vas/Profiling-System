<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("location:login.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

$data = mysqli_connect($host, $user, $password, $db);
if (!$data) {
    $_SESSION['error'] = "Database connection failed: " . mysqli_connect_error();
    header("location:view_student.php");
    exit();
}

// Check if `id` exists in the GET request
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the input as an integer

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $data->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id); // Bind `id` as an integer

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student with ID $id has been successfully deleted.";
        } else {
            $_SESSION['error'] = "Failed to delete student. Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to prepare delete query.";
    }
} else {
    $_SESSION['error'] = "Invalid request. Student ID is missing.";
}

$data->close();

// Redirect back to view_student.php
header("location:view_student.php");
exit();
?>
