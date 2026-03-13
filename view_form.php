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

// Establish connection
$data = mysqli_connect($host, $user, $password, $db);
if (!$data) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the student ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = intval($_GET['id']);
} else {
    echo "Invalid student ID.";
    exit();
}

// Fetch student profile details from the register table
$student_query = "SELECT profile_picture FROM register WHERE id = $student_id";
$student_result = mysqli_query($data, $student_query);

// Set default image path
$default_image = "path/to/default-profile.png"; // Replace with the actual path to your default image

if ($student_result && mysqli_num_rows($student_result) > 0) {
    $student_info = mysqli_fetch_assoc($student_result);
    $profile_picture = !empty($student_info['profile_picture']) ? $student_info['profile_picture'] : $default_image;
} else {
    $profile_picture = $default_image; // Use default image if no record is found
}

// Retrieve form responses and titles for the student
$form_sql = "
    SELECT f.form_title, fr.response_data 
    FROM form_responses fr 
    JOIN forms f ON fr.form_id = f.id 
    WHERE fr.student_id = $student_id
";
$form_result = mysqli_query($data, $form_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: background 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #1abc9c;
            padding-left: 30px;
        }

        .container {
            flex-grow: 1;
            margin-left: 270px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            margin-top: 30px;
            margin-right: 30px;
        }

        .btn-back, .btn-print {
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            color: white;
        }

        .btn-back {
            background-color: #3498db;
        }

        .btn-back:hover {
            background-color: #2980b9;
        }

        .btn-print {
            background-color: #2ecc71;
            margin-left: 10px;
        }

        .btn-print:hover {
            background-color: #27ae60;
        }

        @media print {
            .sidebar, .btn-back, .btn-print {
                display: none;
            }

            body {
                margin: 0;
                background: white;
            }

            .container {
                margin: 0;
                box-shadow: none;
                border: none;
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
        <a href="adminhome.php"  class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin.php" ><i class="fas fa-cogs"></i> Manage Form</a>
    <a href="view_student.php"><i class="fas fa-users"></i> Manage Students</a>
    <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post</a>
    <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </ul>
    </div>

    <div class="container">
        <div>
            <a href="view_student.php" class="btn-back">←</a>
            <button class="btn-print" onclick="printPage()">Print</button>
        </div>
        <form>
    <?php if (mysqli_num_rows($form_result) > 0): ?>
        <?php while ($form = mysqli_fetch_assoc($form_result)): ?>
            <div style="margin-bottom: 20px; text-align: center;">
                <!-- Center Form Title -->
                <h2 style="margin: 0;"><?php echo htmlspecialchars($form['form_title']); ?></h2>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px;">
                <!-- Left Section: Profile Picture (now on the left) -->
                <div>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Image" 
                         style="width: 100px; height: 100px; border-radius: 0%; border: 2px solid #ddd;">
                </div>

                <!-- Right Section: Form Content (now on the right) -->
                <div style="flex: 1; margin-left: 20px;"> <!-- Changed margin-left for spacing -->
                    <?php
                    $response_data = json_decode($form['response_data'], true);
                    if ($response_data && is_array($response_data)):
                    ?>
                        <div class="row" style="margin-top: 10px;">
                            <?php $counter = 0; ?>
                            <?php foreach ($response_data as $field): ?>
                                <?php if ($counter % 3 == 0 && $counter != 0): ?>
                                    </div><div class="row" style="margin-top: 10px;">
                                <?php endif; ?>
                                <div class="col-xs-4" style="margin-bottom: 10px;">
                                    <strong><?php echo htmlspecialchars_decode($field['field_name']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($field['field_value']); ?></span>
                                </div>
                                <?php $counter++; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-forms">Invalid form response data.</p>
                    <?php endif; ?>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-forms">No form responses found for this student.</p>
    <?php endif; ?>
</form>

    </div>
</body>
</html>
