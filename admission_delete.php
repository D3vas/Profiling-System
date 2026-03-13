<?php
session_start();

// Ensure only logged-in admins can access this page
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:login.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

// Connect to the database
$conn = new mysqli($host, $user, $password, $db);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if an ID is provided in the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize the ID

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM admission WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Record deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting record: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Close the database connection
$conn->close();

// Redirect back to the admission dashboard
header("Location: admission.php");
exit();
?>
