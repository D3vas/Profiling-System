<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:login.php");
    exit();
}

$host = "localhost";
$users = "root";
$password = "";
$db = "profiling_system";

// Establish database connection
$data = mysqli_connect($host, $users, $password, $db);

if (isset($_POST['add_student'])) {
	$user_student_id = $_POST['student_id'];
    $username = $_POST['name'];
    $user_email = $_POST['email'];
    $user_age = $_POST['age'];
    $user_birthdate = $_POST['birthdate'];
    $user_course = $_POST['course'];
    $user_phone = $_POST['phone'];
    $user_password = $_POST['password'];
    $usertype = "student";

    $check = "SELECT * FROM users WHERE username='$username'";
    $check_user = mysqli_query($data, $check);
    $row_count = mysqli_num_rows($check_user);

    if ($row_count == 1) {
        echo "<script>alert('Username Already Exists. Try Another One.');</script>";
    } else {
        $sql = "INSERT INTO users (student_id, username, email, age, birthdate, course, phone, usertype, password) 
                VALUES ('$user_student_id', '$username', '$user_email', '$user_age', '$user_birthdate', '$user_course', '$user_phone', '$usertype', '$user_password')";

        $result = mysqli_query($data, $sql);

        if ($result) {
            echo "<script>alert('Student Added Successfully.');</script>";
        } else {
            echo "<script>alert('Failed to Add Student.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            
        }

        /* Header */
        nav {
            background: #333;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

         /* Content */
         .content {
            margin-left: 100px;
            padding: 50px;
            height: 120vh;
            background-image: url('First.jpg'); /* Replace with your image URL */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

/* Content */
        .content h1 {
           font-size: 28px; 
         margin-bottom: 20px;
         color: #333;
         font-weight: bold; /* Make the heading text bold */
}


        .nav-logo {
            font-size: 24px;
            font-weight: bold;
            color: #f4c10f;
            text-decoration: none;
        }

        .nav-logout {
            background-color: #e74c3c;
            padding: 10px 15px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-logout:hover {
            background-color: #c0392b;
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


        /* Content */
        .content {
            margin-left: 1px;
            padding: 30px;
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Form Design */
        .form-container {
            background: transparent;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container form {
            margin-top: 50px;
        }

        .form-group label {
            font-weight: bold;
            color: black;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .btn-submit {
            background-color: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #1e8449;
        }
    </style>
</head>
<body>
<!-- Navigation -->
<nav>
    <a href="#" class="nav-logo">Admin Panel</a>
    <a href="logout.php" class="nav-logout">Logout</a>
</nav>

<!-- Top Navigation -->
<div class="top-nav">
    <ul>
    <li><a href="adminhome.php">Dashboard</a></li>
        <li><a href="admin.php">Manage Form</a></li>
        <li><a href="admission.php">Admission</a></li>
        <li><a href="admin_request.php">Admin Requests</a></li>
        <li class="active"><a href="add_student.php">Add Student</a></li>
        <li><a href="view_student.php">View Students</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <h1>Add New Student</h1>

    <div class="form-container">
        <form action="" method="POST">
        	<div class="form-group">
                <label for="name">Student ID</label>
                <input type="number" id="student_id" name="student_id" required>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required>
            </div>

            <div class="form-group">
                <label for="age">Birthdate</label>
                <input type="date" id="birthdate" name="birthdate" required>
            </div>

            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit" name="add_student" class="btn-submit">Add Student</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
