<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "Please log in first to access this page.";
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate the logged-in user type
$username = $_SESSION['username'];
$query = "SELECT usertype FROM register WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    $_SESSION['message'] = "Invalid session. Please log in again.";
    header("Location: login.php");
    exit();
} else {
    $user = $result->fetch_assoc();
    if ($user['usertype'] !== 'student') {
        session_destroy();
        $_SESSION['message'] = "Unauthorized access. Only students can access this page.";
        header("Location: login.php");
        exit();
    }
}

// Message retrieval
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "";
unset($_SESSION['message']);

if ($message) {
    echo "<script>alert(" . json_encode($message) . ");</script>";
}

// Fetch dynamic form fields
$query = "SELECT * FROM form_fields";
$result = $conn->query($query);
$fields = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fields[] = $row;
    }
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('First.jpg'); 
            color: #fff;
        }

        .form-container {
            margin: 5% auto;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            color: black;
        }
        

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #f4c10f;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.5);
        }

        .logout-container {
            text-align: right;
            bottom: 5%;
            margin: 25px 0;
        }

        .btn-logout {
            background-color: #e74c3c;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logout-container">
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>

    <div class="form-container">
        <h2>Admission Form</h2>
        <form action="data_check.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <?php foreach ($fields as $field): ?>
                <div class="form-group">
                    <label><?= htmlspecialchars($field['name']); ?></label>
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea name="<?= htmlspecialchars($field['name']); ?>" rows="4" class="form-control" <?= $field['required'] ? 'required' : ''; ?>></textarea>
                    <?php else: ?>
                        <input type="<?= htmlspecialchars($field['type']); ?>" name="<?= htmlspecialchars($field['name']); ?>" class="form-control" <?= $field['required'] ? 'required' : ''; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>
</div>

</body>
</html>