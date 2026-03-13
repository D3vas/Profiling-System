<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    log_error("Database connection failed: " . $conn->connect_error);
    exit();
}

// Constants
define('UPLOAD_DIR', 'uploads/');
define('FIELD_UPLOAD_DIR', UPLOAD_DIR . 'fields/');
$allowed_exts = ['jpg', 'jpeg', 'png'];
$max_file_size = 2 * 1024 * 1024; // 2MB

// Utility functions
function redirect_with_message($location, $message) {
    $_SESSION['message'] = $message;
    header("Location: $location");
    exit();
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function upload_file($file, $upload_dir, $allowed_exts) {
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_exts)) {
        return [null, "Invalid file type. Only JPG, JPEG, and PNG are allowed."];
    }

    if ($file_size > $GLOBALS['max_file_size']) {
        return [null, "File size exceeds 2MB limit."];
    }

    $unique_file_name = $upload_dir . uniqid('file_', true) . '.' . $file_ext;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($file_tmp, $unique_file_name)) {
        return [$unique_file_name, null];
    } else {
        return [null, "Failed to upload file."];
    }
}

function log_error($error_message) {
    error_log($error_message, 3, __DIR__ . '/error_log.log');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        redirect_with_message("admin.php", "Error: Invalid CSRF token.");
    }

    $form_id = intval($_POST['form_id'] ?? 0);

    if (isset($_POST['action']) && $_POST['action'] === 'save_form') {
        $form_title = sanitize_input($_POST['form_title']);
        $field_names = $_POST['field_name'] ?? [];
        $field_types = $_POST['field_type'] ?? [];
        $field_required = $_POST['field_required'] ?? [];
        $field_images = $_FILES['field_image'] ?? [];

        if (empty($form_title)) {
            redirect_with_message("admin.php", "Error: Form title is required.");
        }

        if (empty($field_names)) {
            redirect_with_message("admin.php", "Error: At least one field is required.");
        }

        $stmt = $conn->prepare("INSERT INTO forms (form_title) VALUES (?)");
        $stmt->bind_param("s", $form_title);
        if (!$stmt->execute()) {
            log_error("Failed to save the form: " . $stmt->error);
            redirect_with_message("admin.php", "Error: Failed to save the form.");
        }

        $form_id = $conn->insert_id;

        foreach ($field_names as $index => $field_name) {
            $field_name = sanitize_input($field_name);
            $field_type = sanitize_input($field_types[$index]);
            $field_req = isset($field_required[$index]) ? 1 : 0;

            // Handle file upload if applicable
            $uploaded_file = null;
            if ($field_type === 'image' && isset($field_images['name'][$index]) && !empty($field_images['name'][$index])) {
                $file = [
                    'name' => $field_images['name'][$index],
                    'type' => $field_images['type'][$index],
                    'tmp_name' => $field_images['tmp_name'][$index],
                    'error' => $field_images['error'][$index],
                    'size' => $field_images['size'][$index],
                ];
                [$uploaded_file, $upload_error] = upload_file($file, FIELD_UPLOAD_DIR, $allowed_exts);

                if ($upload_error) {
                    log_error("File upload error: $upload_error");
                    redirect_with_message("admin.php", "Error: $upload_error");
                }
            }

            $stmt = $conn->prepare("INSERT INTO form_fields (form_id, name, type, required, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issis", $form_id, $field_name, $field_type, $field_req, $uploaded_file);

            if (!$stmt->execute()) {
                log_error("Failed to save field '$field_name': " . $stmt->error);
                redirect_with_message("admin.php", "Error: Failed to save field '$field_name'.");
            }
        }

        redirect_with_message("admin.php", "Form created successfully!");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_form' && $form_id > 0) {
        $form_title = sanitize_input($_POST['form_title']);
        $field_names = $_POST['field_name'] ?? [];
        $field_types = $_POST['field_type'] ?? [];
        $field_required = $_POST['field_required'] ?? [];

        if (empty($form_title)) {
            redirect_with_message("admin.php?edit_form=$form_id", "Error: Form title is required.");
        }

        // Update the form title
        $stmt = $conn->prepare("UPDATE forms SET form_title = ? WHERE id = ?");
        $stmt->bind_param("si", $form_title, $form_id);
        if (!$stmt->execute()) {
            log_error("Failed to update the form: " . $stmt->error);
            redirect_with_message("admin.php?edit_form=$form_id", "Error: Failed to update the form.");
        }

        // Clear existing fields and re-add them
        $stmt = $conn->prepare("DELETE FROM form_fields WHERE form_id = ?");
        $stmt->bind_param("i", $form_id);
        $stmt->execute();

        foreach ($field_names as $index => $field_name) {
            $field_name = sanitize_input($field_name);
            $field_type = sanitize_input($field_types[$index]);
            $field_req = isset($field_required[$index]) ? 1 : 0;

            $stmt = $conn->prepare("INSERT INTO form_fields (form_id, name, type, required) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $form_id, $field_name, $field_type, $field_req);

            if (!$stmt->execute()) {
                log_error("Failed to save field '$field_name': " . $stmt->error);
                redirect_with_message("admin.php?edit_form=$form_id", "Error: Failed to save field '$field_name'.");
            }
        }

        redirect_with_message("admin.php", "Form updated successfully!");
    }
}

// Handle form deletion
if (isset($_GET['delete_form'])) {
    $form_id = intval($_GET['delete_form']);
    $stmt = $conn->prepare("DELETE FROM form_fields WHERE form_id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM forms WHERE id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();

    redirect_with_message("admin.php", "Form deleted successfully!");
}

// Fetch forms and current fields for editing
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM forms LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$forms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$current_fields = [];
if (isset($_GET['edit_form'])) {
    $form_id = intval($_GET['edit_form']);
    $stmt = $conn->prepare("SELECT * FROM form_fields WHERE form_id = ?");
    $stmt->bind_param("i", $form_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_fields = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Forms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar styles */
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


       /* Enhanced Content Styles */
.content {
    margin-left: 270px; /* Adjusted spacing between content and sidebar */
    padding: 30px;
    width: calc(100% - 270px);
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 20px;
}

/* Form Container Styles */
.form-container {
    margin-top: 20px;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-container label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    color: #555;
}

.form-container input[type="text"],
.form-container select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
}

.form-container button {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-container button:hover {
    background-color: #2980b9;
}

/* Field Row Styles */
.field-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.field-row input[type="text"] {
    flex: 2;
}

.field-row select {
    flex: 1;
}

.field-row .remove-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.field-row .remove-btn:hover {
    background-color: #c0392b;
}

/* Form List Styles */
.form-list ul {
    list-style: none;
    padding: 0;
}

.form-list ul li {
    background-color: #f7f7f7;
    margin-bottom: 10px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-list ul li a {
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
}

.form-list ul li a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>


<div class="sidebar">
    <h2>Forms</h2>
    <ul>
    <a href="adminhome.php"  class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin.php" ><i class="fas fa-cogs"></i> Manage Form</a>
    <a href="view_student.php"><i class="fas fa-users"></i> Manage Students</a>
    <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </ul>
</div>


<div class="content">
    <h1>Manage Forms</h1>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color: green;'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Button to create new form -->
<a href="#" id="createFormBtn" style="padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px;">Create New Form</a>

<div id="addForm" class="form-container" style="display: none;">
    <form action="admin.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
        <input type="hidden" name="action" value="save_form">
        <label for="form_title">Form Title</label>
        <input type="text" name="form_title" required>
        <div id="fields-container">
            <div class="field-row">
                <input type="text" name="field_name[]" placeholder="Field Name">
                <select name="field_type[]">
                    <option value="text">Text</option>
                    <option value="email">Email</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                    <option value="textarea">Textarea</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="addField()">Add Field</button>
        <button type="submit">Save Form</button>
    </form>
</div>
    <div class="form-list">
        <ul>
            <?php foreach ($forms as $form) : ?>
                <li>
                    <?= $form['form_title']; ?>
                    <div>
                        <a href="admin.php?edit_form=<?= $form['id']; ?>">Edit</a>
                        <a href="admin.php?delete_form=<?= $form['id']; ?>" onclick="return confirm('Are you sure you want to delete this form?')">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

        
    <!-- Display edit form below the list -->
   <!-- Edit Form Section -->
<?php if (isset($_GET['edit_form']) && $form_id > 0): ?>
    <div class="form-container">
        <h2>Edit Form</h2>
        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="update_form">
            <input type="hidden" name="form_id" value="<?= $form_id; ?>">

            <div class="form-row">
                <label for="form_title">Form Title</label>
                <input type="text" name="form_title" value="<?= htmlspecialchars($forms[array_search($form_id, array_column($forms, 'id'))]['form_title']); ?>" required>
            </div>

            <h3>Fields</h3>
            <div id="edit-fields-container">
                <?php foreach ($current_fields as $index => $field): ?>
                    <div class="field-row">
                    <input type="text" name="field_name[]" value="<?= htmlspecialchars_decode($field['name'], ENT_QUOTES); ?>" placeholder="Field Name" required>

                        <select name="field_type[]">
                            <option value="text" <?= $field['type'] === 'text' ? 'selected' : ''; ?>>Text</option>
                            <option value="email" <?= $field['type'] === 'email' ? 'selected' : ''; ?>>Email</option>
                            <option value="number" <?= $field['type'] === 'number' ? 'selected' : ''; ?>>Number</option>
                            <option value="date" <?= $field['type'] === 'date' ? 'selected' : ''; ?>>Date</option>
                            <option value="textarea" <?= $field['type'] === 'textarea' ? 'selected' : ''; ?>>Textarea</option>
                        </select>
                        <input type="checkbox" name="field_required[]" <?= $field['required'] ? 'checked' : ''; ?>> Required
                        <button type="button" class="remove-btn" onclick="removeField(this)">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add-edit-field-btn">Add New Field</button>
            <button type="submit">Update Form</button>
        </form>
    </div>
<?php endif; ?>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Toggle the visibility of the form container
    document.getElementById('createFormBtn').addEventListener('click', function (event) {
        event.preventDefault();
        const addForm = document.getElementById('addForm');
        if (addForm) {
            addForm.style.display = addForm.style.display === 'none' ? 'block' : 'none';
        } else {
            console.error("Add form container not found!");
        }
    });

    // Function to add new fields
    function addField() {
        const fieldsContainer = document.getElementById('fields-container');
        const newField = document.createElement('div');
        newField.classList.add('field-row');
        newField.innerHTML = `
            <input type="text" name="field_name[]" placeholder="Field Name" required>
            <select name="field_type[]">
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="number">Number</option>
                <option value="date">Date</option>
                <option value="textarea">Textarea</option>
            </select>
            <button type="button" class="remove-btn" onclick="removeField(this)">Remove</button>
        `;
        fieldsContainer.appendChild(newField);
    }

    // Function to remove a field
    function removeField(button) {
        button.closest('.field-row').remove();
    }

    // Attach listener for adding fields
    document.getElementById('add-edit-field-btn')?.addEventListener('click', function () {
        addField();
    });
});


 // Function to dynamically add new edit fields
 function addEditField() {
        const editFieldsContainer = document.getElementById('edit-fields-container');
        const newField = document.createElement('div');
        newField.classList.add('field-row');
        newField.innerHTML = `
            <input type="text" name="field_name[]" placeholder="Field Name" required>
            <select name="field_type[]">
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="number">Number</option>
                <option value="date">Date</option>
                <option value="textarea">Textarea</option>
            </select>
            <input type="checkbox" name="field_required[]"> Required
            <button type="button" class="remove-btn" onclick="removeField(this)">Remove</button>
        `;
        editFieldsContainer.appendChild(newField);
    }

    // Function to remove a field
    function removeField(button) {
        button.closest('.field-row').remove();
    }

    // Attach event listener for adding new fields
    document.getElementById('add-edit-field-btn')?.addEventListener('click', function () {
        addEditField();
    });

</script>
</body>
</html>

