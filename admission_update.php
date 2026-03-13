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

// Establish connection
$data = mysqli_connect($host, $user, $password, $db);

// Check for connection error
if (!$data) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        $status = 'approved';
    } else if ($action == 'reject') {
        $status = 'rejected';
    }

    // Update the student's status
    $sql = "UPDATE register SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($data, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to admission page after approval/rejection
        echo "<script>
            alert('Student status updated successfully');
            window.location.href = 'admission.php';
        </script>";
    } else {
        echo "<script>
            alert('Error updating student status');
            window.location.href = 'admission.php';
        </script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($data);
?>
