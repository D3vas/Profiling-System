<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'student') {
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

// Fetch all posts
$sql = "SELECT * FROM posts ORDER BY id DESC";
$result = mysqli_query($data, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        background-image: url('images/logo2.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
</style>


</head>
<body>

    <!-- Sidebar Navigation -->
<nav>
    <a href="#" class="nav-logo"><i class="fas fa-graduation-cap"></i> Student Dashboard</a>
    <ul class="nav-links">
        <li class="active">
            <a href="studenthome.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
            <a href="student_profile.php"><i class="fas fa-pencil-alt"></i> Fill Forms</a>
        </li>

        <li>
            <a href="logout.php" class="nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</nav>


    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-welcome">
            <h2>Welcome to Your Dashboard</h2>
            <p>Access your profile, posts, and more. Use the navigation to explore the system.</p>
        </div>

        <!-- Display All Posts -->
        <h1>Announcements</h1>
        <div class="post-grid">
            <?php
            // Check if there are posts
            if (mysqli_num_rows($result) > 0) {
                while ($post = $result->fetch_assoc()) {
                    echo "<div class='post'>";
                    echo "<h3>" . htmlspecialchars($post['description']) . "</h3>";

                    // Display image if it exists
                    if (!empty($post['image']) && file_exists($post['image'])) {
                        echo "<img src='" . htmlspecialchars($post['image']) . "' alt='Post Image'>";
                    } else {
                        echo "<p>[No image available]</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No posts available.</p>";
            }

            // Close database connection
            mysqli_close($data);
            ?>
        </div>
    </div>
</div>
</body>

</html>
