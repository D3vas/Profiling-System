<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:login.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

// Establish connection
$data = mysqli_connect($host, $user, $password, $db);
if (!$data) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if a delete request has been made (for form responses)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // SQL query to delete the form responses for this student
    $delete_sql = "DELETE FROM form_responses WHERE student_id = $delete_id";  // Assuming student_forms table contains the responses
    if (mysqli_query($data, $delete_sql)) {
        echo "<script>alert('Student form responses deleted successfully.'); window.location.href = 'view_student.php';</script>";
    } else {
        echo "<script>alert('Error deleting form responses.');</script>";
    }
}

// Retrieve student records (id, first name, last name)
$sql = "SELECT id, first_name, last_name FROM register"; // Change 'register' table for student data
$result = mysqli_query($data, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - View Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            display: flex;
            height: 100vh;
        }

        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: background 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #1abc9c;
            padding-left: 30px;
        }
        
        /* Content */
        .content {
            margin-left: 280px; /* Adjusted to fit sidebar */
            padding: 30px;
            width: calc(100% - 270px);
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Table Design */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table th,
        table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #34495e;
            color: white;
        }

        table tr:hover {
            background-color: #f2f2f2;
        }

        .btn-primary {
            border: none;
            border-radius: 5px;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            background-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
<h2>Students</h2>
<ul>
<a href="adminhome.php"  class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin.php" ><i class="fas fa-cogs"></i> Manage Form</a>
    <a href="view_student.php"><i class="fas fa-users"></i> Manage Students</a>
    <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post</a>
    <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</ul>
</div>

<!-- Main Content -->
<div class="content">
    <h1>View Student Records</h1>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>View Form</th>
                <th>Remove Responses</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are any student records
            if (mysqli_num_rows($result) > 0) {
                // Display each student record
                while ($info = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($info['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($info['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($info['last_name']) . "</td>";
                    echo "<td><a href='view_form.php?id=" . intval($info['id']) . "' class='btn btn-primary'><i class='fas fa-eye'></i> View Form</a></td>";
                    echo "<td><a href='?delete_id=" . intval($info['id']) . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this student's form responses?\");'><i class='fas fa-trash-alt'></i> Remove Responses</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No student records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
