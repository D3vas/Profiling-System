<?php
error_reporting(0);
session_start();
session_destroy();

if ($_SESSION['message']) {
    $message = $_SESSION['message'];

    echo "<script type='text/javascript'>
    alert('$message');
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
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
            background: linear-gradient(135deg, #34495e, #1abc9c);
            color: #fff;
        }

        /* Navbar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .nav-logo {
            font-size: 24px;
            font-weight: bold;
            color: #f4c10f;
            text-decoration: none;
        }

        .nav-buttons a {
            margin-left: 15px;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .nav-buttons a:hover {
            background-color: #2980b9;
        }

        /* Hero Section */
        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
            text-align: center;
            background-image: url('First.jpg'); /* Use your desired image */
            background-size: cover;
            background-position: center;
            position: relative;
            color: white;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Adds a dark overlay for better text contrast */
        }

        .hero-text {
            z-index: 1;
            font-size: 50px;
            font-weight: bold;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
        }

        /* Modal - About Section */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto; /* Centered */
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            color: #333;
        }

        .modal h2 {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .modal p {
            font-size: 18px;
            line-height: 1.6;
        }

        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Footer Section */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: absolute;
            width: 100%;
            bottom: 0;
        }
    </style>

    <script>
        // Function to toggle modal visibility
        function toggleAbout() {
            var modal = document.getElementById('aboutModal');
            modal.style.display = modal.style.display === 'none' || modal.style.display === '' ? 'block' : 'none';
        }

        // Close the modal when the user clicks on the "X"
        function closeModal() {
            var modal = document.getElementById('aboutModal');
            modal.style.display = 'none';
        }
    </script>
</head>
<body>

<!-- Navbar -->
<nav>
    <a href="#" class="nav-logo">BSIT Student Profiling System</a>
    <div class="nav-buttons">
        <a href="login.php" class="btn btn-success">Login</a>
       <!-- <a href="register.php" class="btn btn-primary">Sign Up</a> -->
        <a href="javascript:void(0);" class="btn btn-info" onclick="toggleAbout()">About</a> <!-- About Button -->
    </div>
</nav>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-text">
        Welcome to the Student Profiling System
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>About the Student Profiling System</h2>
        <p>
            The BSIT Student Profiling System is a comprehensive platform designed to streamline the management and tracking of student data. It provides administrators with the ability to view and manage student profiles, including personal details, academic records, and other essential information. The system aims to facilitate a more efficient and organized way of managing student records, making it easier for educational institutions to maintain up-to-date information.
        </p>
    </div>
</div>

<!-- Footer Section -->
<footer>
    <p>&copy;2025 BSIT Student Profiling System. All rights reserved.<a href="admin_register.php" style="color: white; text-decoration: none;">|Admin</a></p>
</footer>

</body>
</html>
