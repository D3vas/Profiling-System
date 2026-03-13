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

// Retrieve applications from the database
$sql = "SELECT * FROM register WHERE status = 'pending'";
$result = mysqli_query($data, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admission Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>

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

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #34495e;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 15px;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #1abc9c;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .content {
            margin-left: 270px; /* Adjusted to fit sidebar */
            padding: 30px;
            width: calc(100% - 270px);
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Top Navigation */
        .top-nav {
            background-color: #34495e;
            color: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }

        .top-nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .top-nav ul li {
            margin: 0 15px;
        }

        .top-nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .top-nav ul li a:hover {
            background-color: #1abc9c;
        }

        .top-nav ul li.active a {
            background-color: #1abc9c;
            color: white;
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

        .btn-danger,
        .btn-success {
            border: none;
            border-radius: 5px;
            color: white;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #1e8449;
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
<h2>Admission</h2>
    <a href="adminhome.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin.php"><i class="fas fa-cogs"></i> Manage Form</a>
    <a href="admission.php" class="active"><i class="fas fa-user-plus"></i> Admission</a>
    <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post Management</a>
    <a href="view_student.php"><i class="fas fa-users"></i> View Students</a>
    <a href="logout.php" class="nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>


<!-- Main Content -->
<div class="content">
    <h1>Applications for Admission</h1>

    <table>
        <thead>
            <tr>
            	<th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Check if there are any applications
    if (mysqli_num_rows($result) > 0) {
        // Display each application in a table row
        while ($info = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . (isset($info['id']) ? htmlspecialchars($info['id']) : 'N/A') . "</td>";
            echo "<td>" . (isset($info['username']) ? htmlspecialchars($info['username']) : 'N/A') . "</td>";
            echo "<td>" . (isset($info['email']) ? htmlspecialchars($info['email']) : 'N/A') . "</td>";
            echo "<td>
                <form action='admission_update.php' method='POST' style='display:inline;'>
                    <input type='hidden' name='id' value='" . intval($info['id']) . "'>
                    <button type='submit' name='action' value='approve' class='btn btn-success' onclick='return confirm(\"Are you sure you want to approve this student?\")'>Approve</button>
                    <button type='submit' name='action' value='reject' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to reject this student?\")'>Reject</button>
                </form>
            </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align:center;'>No pending applications found.</td></tr>";
    }
    ?>
</tbody>

    </table>
</div>

</body>
</html>
