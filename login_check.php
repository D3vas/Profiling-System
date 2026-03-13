<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

$data = mysqli_connect($host, $user, $password, $db);

if ($data === false) {
    die("Connection error");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // Validate input
    if (empty($name) || empty($pass)) {
        $_SESSION['loginMessage'] = "Please fill in both username and password.";
        header("location:login.php");
        exit();
    }

    // Check if the input is an email or username
    $stmt = null;
    if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
        // Input is an email
        $stmt = $data->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $name);
    } else {
        // Input is a username
        $stmt = $data->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $name);
    }

    // Check if the user exists in the users table (for admin)
    $stmt->execute();
    $result = $stmt->get_result();
    $userRow = $result->fetch_assoc();

    if ($userRow) {
        // Admin login (admin can log in directly without approval)
        if ($userRow['usertype'] === 'admin') {
            // Verify the password (assuming it is hashed)
            if (password_verify($pass, $userRow['password'])) {
                // Set session variables for admin
                $_SESSION['username'] = $userRow['username'];
                $_SESSION['usertype'] = 'admin';

                // Redirect to admin home page
                header("location:adminhome.php");
                exit();
            } else {
                $_SESSION['loginMessage'] = "Invalid password.";
                header("location:login.php");
                exit();
            }
        }
    }

    // If not found in users table, check the register table for students
    if (!$userRow) {
        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            // Input is an email
            $stmt = $data->prepare("SELECT * FROM register WHERE email = ?");
            $stmt->bind_param("s", $name);
        } else {
            // Input is a username
            $stmt = $data->prepare("SELECT * FROM register WHERE username = ?");
            $stmt->bind_param("s", $name);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $registerRow = $result->fetch_assoc();

        if ($registerRow) {
            // Verify the password (assuming it is hashed)
            if (password_verify($pass, $registerRow['password'])) {
                // Check if the student's account is approved
                if ($registerRow['status'] === 'approved') {
                    // Set session variables for student
                    $_SESSION['username'] = $registerRow['username'];
                    $_SESSION['usertype'] = 'student';

                    // Redirect to student home page
                    header("location:studenthome.php");
                    exit();
                } else {
                    $_SESSION['loginMessage'] = "Your account is not approved yet. Please wait for admin approval.";
                    header("location:login.php");
                    exit();
                }
            } else {
                $_SESSION['loginMessage'] = "Invalid password.";
                header("location:login.php");
                exit();
            }
        } else {
            $_SESSION['loginMessage'] = "Username or email does not match.";
            header("location:login.php");
            exit();
        }
    }

    // Close statement and connection
    $stmt->close();
    $data->close();
}
?>
