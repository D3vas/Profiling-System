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

    // Retrieve the admin's data from the `register` table
    $query = "SELECT * FROM register WHERE id = ?";
    $stmt = $data->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Check if `student_id` exists and include it in the query if required
        if (isset($admin['student_id'])) {
            $insert_query = "INSERT INTO users (username, email, phone, password, usertype, student_id) VALUES (?, ?, ?, ?, 'admin', ?)";
            $insert_stmt = $data->prepare($insert_query);
            $insert_stmt->bind_param("ssssi", $admin['username'], $admin['email'], $admin['phone'], $admin['password'], $admin['student_id']);
        } else {
            $insert_query = "INSERT INTO users (username, email, phone, password, usertype) VALUES (?, ?, ?, ?, 'admin')";
            $insert_stmt = $data->prepare($insert_query);
            $insert_stmt->bind_param("ssss", $admin['username'], $admin['email'], $admin['phone'], $admin['password']);
        }

        if ($insert_stmt->execute()) {
            // Delete data from the `register` table
            $delete_query = "DELETE FROM register WHERE id = ?";
            $delete_stmt = $data->prepare($delete_query);
            $delete_stmt->bind_param("i", $id);

            if ($delete_stmt->execute()) {
                $_SESSION['message'] = "Admin verified and moved to users table successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete admin from register table.";
            }
        } else {
            $_SESSION['error'] = "Failed to insert admin into users table.";
        }

        $insert_stmt->close();
    } else {
        $_SESSION['error'] = "Admin not found.";
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
