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

$data = mysqli_connect($host, $user, $password, $db);

$id = $_GET['id']; // Updated for student_id

// Fetch student data
$sql = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($data, $sql);

if ($result->num_rows > 0) {
    $info = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "No  ID $id";
    header("location:view_student.php");
    exit();
}

// Handle update
if (isset($_POST['update'])) {
	$student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $birthdate = $_POST['birthdate']; // Added birthdate
    $course = $_POST['course'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $query = "UPDATE users 
              SET student_id='$student_id', username='$name', email='$email', age='$age', birthdate='$birthdate', course='$course', phone='$phone', password='$password' 
              WHERE id='$id'";
    $result2 = mysqli_query($data, $query);

    if ($result2) {
        $_SESSION['message'] = 'Student information updated successfully.';
        header("location:view_student.php");
        exit();
    } else {
        $_SESSION['error'] = 'Failed to update student information.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
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

        /* Sidebar */
        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            padding-top: 60px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        .sidebar ul li.active a {
            background-color: #1abc9c;
            color: white;
        }

        /* Content */
        .content {
            margin-left: 270px;
            padding: 30px;
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Form Design */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: auto;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
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
            width: 100%;
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

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="admin.php">Manage Form</a></li>
        <li><a href="admission.php">Admission</a></li>
        <li><a href="add_student.php">Add Student</a></li>
        <li><a href="view_student.php">View Students</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <h1>Update Student Information</h1>
    <div class="form-container">
        <form action="#" method="POST">
        	<div class="form-group">
                <label for="name">Student ID</label>
                <input type="number" id="student_id" name="student_id" value="<?php echo htmlspecialchars($info['student_id']); ?>" required>
            </div>

            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($info['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($info['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($info['age']); ?>" required>
            </div>

            <div class="form-group">
                <label for="birthdate">Birthdate</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($info['birthdate']); ?>" required>
            </div>

            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($info['course']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($info['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($info['password']); ?>" required>
            </div>

            <div class="form-group">
                <button type="submit" name="update" class="btn-submit">Update</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
