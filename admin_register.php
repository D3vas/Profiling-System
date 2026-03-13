<?php
session_start();

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

// Connect to the database
$data = mysqli_connect($host, $user, $password, $db);

if ($data === false) {
    die("Connection error");
}

// Check if the form is submitted
if (isset($_POST['Register'])) {
    $user_id = $_POST['id_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $verify_password = $_POST['verify_password'];
    $usertype = "admin"; // Automatically set as admin

    // Validate user inputs
    if (empty($user_id) || empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($verify_password)) {
        echo "<script>alert('All fields are required.');</script>";
        exit;
    }

    // Verify if the passwords match
    if ($password !== $verify_password) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert data into the users table
    $stmt = $data->prepare("INSERT INTO users (id, first_name, last_name, username, email, phone, password, usertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("isssssss", $user_id, $first_name, $last_name, $username, $email, $phone, $hashed_password, $usertype);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Registration Failed. SQL Error: " . $stmt->error . "');</script>";
    }

    // Close statement and connection
    $stmt->close();
    $data->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            overflow: hidden;
        }

        .register-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            width: 80%;
            max-width: 900px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #34495e;
        }

        .form-group label {
            font-weight: bold;
            color: #34495e;
        }

        .btn-register {
            background-color: #3f63db;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-register:hover {
            background-color: #5588f0;
        }

        .row .form-group {
            margin-bottom: 15px;
        }
         /* Back button styling */
         .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            background-color: #34495e;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #2c3e50;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<!-- Back Button -->
<a href="homepage.php" class="back-button">&larr; </a>
<div class="register-container">
    <h1>Admin Registration</h1>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="id_number">ID Number</label>
                    <input type="number" id="id_number" name="id_number" class="form-control" placeholder="Enter your ID number" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter your first name" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter your last name" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Username</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter your username" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
                </div>
            </div>
        </div>

        <!-- Adjust Password & Verify Password to be more aligned -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="verify_password">Verify Password</label>
                    <input type="password" id="verify_password" name="verify_password" class="form-control" placeholder="Re-enter your password" required>
                </div>
            </div>
        </div>

        <button type="submit" name="Register" class="btn btn-register">Register</button>
        <!-- Login Link -->
<p style="text-align: center; margin-top: 15px;">
    Already have an account? 
    <a href="admin_login.php" style="color: #1e90ff; text-decoration: none; font-weight: bold;">Login here</a>
</p>
    </form>
</div>

</body>
</html>
