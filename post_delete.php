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

// Check if an ID is provided for deletion
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Delete the post from the database
    $sql = "DELETE FROM posts WHERE id = $id";

    if (mysqli_query($data, $sql)) {
        // Post deleted successfully, redirect with a success message
        $_SESSION['message'] = "Post deleted successfully.";
        header("Location: post_management.php");  // Redirect to the post management page
        exit();
    } else {
        // Error occurred, redirect with an error message
        $_SESSION['message'] = "Error deleting post: " . mysqli_error($data);
        header("Location: post_management.php");  // Redirect to the post management page
        exit();
    }
} else {
    // No post ID provided, redirect with an error message
    $_SESSION['message'] = "No post ID provided.";
    header("Location: post_management.php");  // Redirect to the post management page
    exit();
}

// Close the database connection
mysqli_close($data);
?>
