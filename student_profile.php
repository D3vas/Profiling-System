<?php 
session_start();

// Ensure the user is logged in and is a student
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = intval($_SESSION['student_id'] ?? 0);

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate CSRF token if not already generated
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle profile picture upload
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $student_id . '_' . uniqid() . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $file_name;

        // Check file type
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $upload_message = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        } elseif (move_uploaded_file($file_tmp, $target_file)) {
            // Update the database to save the profile picture in the 'register' table
            $update_query = "UPDATE register SET profile_picture = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $target_file, $student_id);
            if ($update_stmt->execute()) {
                $upload_message = "Profile picture updated successfully!";
            } else {
                $upload_message = "Database update failed.";
            }
        } else {
            $upload_message = "Failed to upload file.";
        }
    } else {
        $upload_message = "Please select a valid file.";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_custom_form'])) {
    $form_id = intval($_POST['form_id']);
    $csrf_token = $_POST['csrf_token'];

   // Validate CSRF token
    if ($csrf_token !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Prepare and save form responses
    if (!empty($_POST['field_name']) && !empty($_POST['field_value'])) {
        $responses = [];
        for ($i = 0; $i < count($_POST['field_name']); $i++) {
            $field_name = $_POST['field_name'][$i];
            $field_value = $_POST['field_value'][$i];
            $responses[] = ['field_name' => $field_name, 'field_value' => $field_value];
        }

        $response_data = json_encode($responses, JSON_UNESCAPED_UNICODE);
        $timestamp = date("Y-m-d H:i:s");

        // Check if the response already exists
        $check_query = "SELECT id FROM form_responses WHERE form_id = ? AND student_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $form_id, $student_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Update the existing response
            $update_query = "UPDATE form_responses SET response_data = ?, last_updated_at = ? WHERE form_id = ? AND student_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssii", $response_data, $timestamp, $form_id, $student_id);
            $update_stmt->execute();
        } else {
            // Insert a new response
            $insert_query = "INSERT INTO form_responses (form_id, student_id, response_data, last_updated_at) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iiss", $form_id, $student_id, $response_data, $timestamp);
            $insert_stmt->execute();
        }
    }


    // Redirect to the same page to fetch updated data
    header("Location: student_profile.php");
    exit();
}

// Fetch student data
$student_query = "SELECT profile_picture FROM register WHERE id = ?";
$student_stmt = $conn->prepare($student_query);
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_data = $student_stmt->get_result()->fetch_assoc();

// Fetch forms and their responses
$forms_query = "
    SELECT f.*, fr.response_data, fr.last_updated_at 
    FROM forms f 
    LEFT JOIN form_responses fr 
        ON f.id = fr.form_id AND fr.student_id = ?
";
$forms_stmt = $conn->prepare($forms_query);
$forms_stmt->bind_param("i", $student_id);
$forms_stmt->execute();
$forms_result = $forms_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .profile-picture {
        position: relative;
        width: 200px;  /* Adjust the width */
        height: 200px; /* Adjust the height */
        margin: 0 auto 15px;
        border: 1px solid #6c757d; /* Keep a border around the image */
    }

    .profile-picture img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensure the image fills the space */
    }

    .profile-picture input[type="file"] {
        display: block; /* Make the file input visible */
    }

    .profile-picture button {
        display: inline-block; /* Show the button normally */
        margin-top: 10px; /* Adjust position if needed */
    }

    /* Fix the alert container height to prevent movement */
    .alert-container {
        height: 50px; /* Set the height to match the alert message */
        overflow: hidden; /* Hide overflow to avoid shifting content */
        margin-bottom: 20px; /* Space between alert and available forms */
    }

    #uploadMessage {
        display: none; /* Hide the alert initially */
    }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="nav-side">
        <h2><i class="fas fa-clipboard-list"></i> Forms</h2>
        <ul>
            <li><a href="studenthome.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="active"><a href="student_profile.php"><i class="fas fa-pencil-alt"></i> Fill Forms</a></li>
            <li><a href="logout.php" class="nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Student Profile</h1>

        <!-- Profile Picture Section -->
        <div class="profile-picture">
            <img src="<?= htmlspecialchars($student_data['profile_picture'] ?? 'uploads/default_profile.png'); ?>?<?= time(); ?>" alt="Profile Picture">
            <form id="profile-picture-form" action="student_profile.php" method="POST" enctype="multipart/form-data">
                <!-- File input hidden -->
                <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" style="display: none;">
                
                <!-- Custom upload button -->
                <button type="button" class="btn btn-primary" id="upload-button">Upload</button>
                
                <!-- Submit button for form -->
                <button type="submit" name="upload_picture" class="btn btn-primary" style="display: none;" id="submit-button">Submit</button>
            </form>
        </div>

        <div class="alert-container">
            <?php if ($upload_message): ?>
                <div class="alert alert-info" id="uploadMessage"><?= htmlspecialchars($upload_message); ?></div>
                <script>
                    // Display and hide the alert message after 3 seconds
                    setTimeout(function() {
                        var message = document.getElementById('uploadMessage');
                        if (message) {
                            message.style.display = 'none';
                        }
                    }, 300);  // Hide after 3 seconds
                    // Show the message immediately
                    document.getElementById('uploadMessage').style.display = 'block';
                </script>
            <?php endif; ?>
        </div>

        <!-- Available Forms Section -->
        <div class="available-forms">
            <h1>Available Forms</h1>
            <?php if ($forms_result && $forms_result->num_rows > 0): ?>
                <?php while ($form = $forms_result->fetch_assoc()): ?>
                    <div class="form-container">
                        <h3><?= htmlspecialchars($form['form_title']); ?></h3>
                        <form action="student_profile.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="form_id" value="<?= intval($form['id']); ?>">
                            <?php
            $response_data = $form['response_data'] ? json_decode($form['response_data'], true) : [];
            $fields_query = $conn->prepare("SELECT * FROM form_fields WHERE form_id = ?");
            $fields_query->bind_param("i", $form['id']);
            $fields_query->execute();
            $fields_result = $fields_query->get_result();

            while ($field = $fields_result->fetch_assoc()):
                $current_value = '';
                foreach ($response_data as $response) {
                    if ($response['field_name'] === $field['name']) {
                        $current_value = htmlspecialchars_decode($response['field_value'], ENT_QUOTES);
                        break;
                    }
                }
            ?>
                <div class="form-group">
                    <label><?= htmlspecialchars_decode($field['name']); ?>:</label>
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea name="field_value[]" required><?= htmlspecialchars($current_value); ?></textarea>
                    <?php else: ?>
                        <input type="<?= htmlspecialchars($field['type']); ?>" name="field_value[]" value="<?= htmlspecialchars($current_value); ?>" required>
                    <?php endif; ?>
                    <input type="hidden" name="field_name[]" value="<?= htmlspecialchars($field['name']); ?>">
                </div>
            <?php endwhile; ?>
                            <button type="submit" name="submit_custom_form" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No forms available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>


    <script>
        // Get references to the relevant DOM elements
        const uploadButton = document.getElementById('upload-button');
        const fileInput = document.getElementById('profile-picture-input');
        const submitButton = document.getElementById('submit-button');

        // Add event listener to the upload button
        uploadButton.addEventListener('click', function() {
            fileInput.click();  // Trigger the file input when the button is clicked
        });

        // When a file is selected, automatically submit the form
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                submitButton.click();  // Trigger form submission when a file is selected
            }
        });
    </script>
</body>
</html>
