<?php
session_start();

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

// Connect to the database
$data = mysqli_connect($host, $user, $password, $db);

// Check database connection
if (!$data) {
    die("Connection error: " . mysqli_connect_error());
}

// Check if the form is submitted
if (isset($_POST['Register'])) {
    // Sanitize user inputs
    $user_id      = mysqli_real_escape_string($data, $_POST['id_number']);
    $first_name   = mysqli_real_escape_string($data, $_POST['first_name']);
    $middle_name  = mysqli_real_escape_string($data, $_POST['middle_name']);
    $last_name    = mysqli_real_escape_string($data, $_POST['last_name']);
    $gender       = mysqli_real_escape_string($data, $_POST['gender']);
    $address      = mysqli_real_escape_string($data, $_POST['address']);
    $username     = mysqli_real_escape_string($data, $_POST['name']);
    $email        = mysqli_real_escape_string($data, $_POST['email']);
    $phone        = mysqli_real_escape_string($data, $_POST['phone']);
    $year_section = mysqli_real_escape_string($data, $_POST['year_section']);
    $program      = mysqli_real_escape_string($data, $_POST['program']);
    $status       = mysqli_real_escape_string($data, $_POST['status']);
    $password     = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Combine names into full_name
    $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);

    // Validate user inputs
    if (empty($user_id) || empty($first_name) || empty($middle_name) || empty($last_name) || empty($gender) || empty($address) || empty($username) || empty($email) || empty($phone) || empty($year_section) || empty($program) || empty($status) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.');</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set user type to "student"
    $user_type = "student"; // Default user type

    // Insert user into the register table
    $stmt = $data->prepare("INSERT INTO register (id, first_name, middle_name, last_name, full_name, gender, address, username, email, phone, year_section, program, status, password, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssssssss", $user_id, $first_name, $middle_name, $last_name, $full_name, $gender, $address, $username, $email, $phone, $year_section, $program, $status, $hashed_password, $user_type);

    // Execute the statement
    if ($stmt) {
        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Registration Failed. SQL Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database Error: " . $data->error . "');</script>";
    }

    $data->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
        /* Styling for the page */
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
        }
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
        .register-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            max-width: 900px;
            width: 100%;
        }
        h1 {
            font-size: 28px;
            color: #34495e;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            color: #34495e;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #8e44ad;
            outline: none;
            box-shadow: 0 0 8px rgba(142, 68, 173, 0.3);
        }
        .btn-register {
            background-color: rgb(63, 99, 219);
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;
        }
        .btn-register:hover {
            background-color: rgb(85, 176, 228);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<a href="homepage.php" class="back-button">&larr; </a>
<div class="register-container">
    <h1>Register</h1>
    <form action="" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="id_number">ID Number</label>
                <input type="number" id="id_number" name="id_number" placeholder="Enter your ID number" required>
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
            </div>
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" placeholder="Enter your username" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="year_section">Year and Section</label>
                <select id="year_section" name="year_section" required>
                    <option value="">Select Year and Section</option>
                    <option value="1A">1A</option>
                    <option value="1B">1B</option>
                    <option value="1C">1C</option>
                    <option value="1D">1D</option>
                    <option value="1E">1E</option>
                    <option value="2A">2A</option>
                    <option value="2B">2B</option>
                    <option value="2C">2C</option>
                    <option value="2D">2D</option>
                    <option value="2E">2E</option>
                    <option value="3A">3A</option>
                    <option value="3B">3B</option>
                    <option value="3C">3C</option>
                    <option value="3D">3D</option>
                    <option value="3E">3E</option>
                    <option value="4A">4A</option>
                    <option value="4B">4B</option>
                    <option value="4C">4C</option>
                    <option value="4D">4D</option>
                    <option value="4E">4E</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Regular">Regular</option>
                    <option value="Irregular">Irregular</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="program">Program</label>
                <select id="program" name="program" required>
                    <option value="">Select Program</option>
                    <option value="Day">Day</option>
                    <option value="Night">Night</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
        </div>
        <button type="submit" name="Register" class="btn-register">Register</button>
        <p style="text-align: center; margin-top: 15px;">
            Already have an account? 
            <a href="login.php" style="color: #1e90ff; text-decoration: none; font-weight: bold;">Login here</a>
        </p>
    </form>
</div>
</body>
</html>
