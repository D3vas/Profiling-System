<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login']; // Can be either email or username
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'profiling_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user data only from the register table for students
    $query = $conn->prepare("SELECT id, username, password, usertype FROM register WHERE (email = ? OR username = ?) AND usertype = 'student'");
    $query->bind_param("ss", $login, $login);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables for student
            $_SESSION['student_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['usertype'] = $user['usertype'];

            // Redirect to student home page
            header("Location: studenthome.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid email, username, or user type.";
    }

    $query->close();
    $conn->close();
}

// Check for login message if exists
if (isset($_SESSION['loginMessage'])) {
    echo "<p>" . htmlspecialchars($_SESSION['loginMessage']) . "</p>";
    unset($_SESSION['loginMessage']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
    <style>
        /* Your existing CSS */
        body {
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b); /* Gradient from light to dark blue */
}


        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .login-container {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 40px;
    max-width: 500px;
    width: 100%;
    text-align: center;
    position: absolute;
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -50%);
}

.form-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-image {
    flex: 2;
    text-align: center;
    padding: 10px;
}

.form-image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

.form-fields {
    flex: 2;
    padding-left: 20px;
}

.form-group input {
    width: 100%;
    padding: 10px;
    font-weight: bold;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
}


        .btn-login {
            background-color:rgb(63, 99, 219);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color:rgb(85, 176, 228);
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
<div class="container">
    <div class="right-side">
        <div class="login-container">
            <h1>Login</h1>
            <form action="" method="POST">
                <div class="form-content">
                    <div class="form-image">
                        <img src="images/logo2.jpg" alt="Login Image">
                    </div>
                    <div class="form-fields">
                        <div class="form-group">
                            <label for="login">Email or Username</label>
                            <input type="text" id="login" name="login" placeholder="Enter your email or username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div style="position: relative;">
                                <input type="password" id="password" name="password" placeholder="Enter your password" required style="padding-right: 40px;">
                                <span id="toggle-password" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); cursor: pointer;">
                                    <img id="eye-icon" src="https://img.icons8.com/ios-glyphs/30/000000/visible.png" alt="Toggle Password" style="width: 20px;">
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn-login">Login</button>
                        <p style="margin-top: 15px;">Don't have an account? 
                            <a href="register.php" style="color: #1e90ff; text-decoration: none; font-weight: bold;">Register here</a></p>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.src = "https://img.icons8.com/ios-glyphs/30/000000/invisible.png";
        } else {
            passwordField.type = 'password';
            eyeIcon.src = "https://img.icons8.com/ios-glyphs/30/000000/visible.png";
        }
    });
</script>
</body>
</html>
