<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
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
    header("location:admin_request.php");
    exit();
}

// Check if ID is passed
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize ID

    // Delete admin data from the `register` table
    $query = "DELETE FROM register WHERE id = ?";
    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Admin record deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete admin record.";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request. Admin ID is missing.";
}

$data->close();

// Redirect back to admin_request.php
header("location:admin_request.php");
exit();
?>
