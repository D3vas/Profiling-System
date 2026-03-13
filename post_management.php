<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:admin_login.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

$data = mysqli_connect($host, $user, $password, $db);

if (!$data) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission for creating a new post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    $description = mysqli_real_escape_string($data, $_POST['description']);
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = "uploads/" . $image_name;

        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            echo "Error uploading image.";
            exit();
        }
    }

    // Insert post into the database
    $sql = "INSERT INTO posts (description, image) VALUES ('$description', '$image_path')";
    if (mysqli_query($data, $sql)) {
        echo "Post created successfully.";
    } else {
        echo "Error creating post: " . mysqli_error($data);
    }
}

// Handle update of posts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_post'])) {
    if (!empty($_POST['post_id'])) {
        $post_id = mysqli_real_escape_string($data, $_POST['post_id']);
        $description = mysqli_real_escape_string($data, $_POST['description']);
        
        // Handle image upload
        $image_path = $_POST['current_image'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = basename($_FILES['image']['name']);
            $image_path = "uploads/" . $image_name;

            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                echo "Error uploading image.";
                exit();
            }
        }

        $sql = "UPDATE posts SET description='$description', image='$image_path' WHERE id='$post_id'";
        if (mysqli_query($data, $sql)) {
            echo "Post updated successfully.";
        } else {
            echo "Error updating post: " . mysqli_error($data);
        }
    } else {
        echo "Error: Missing post ID.";
    }
}

// Handle deletion of posts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    if (!empty($_POST['post_id'])) {
        $post_id = mysqli_real_escape_string($data, $_POST['post_id']);
        $sql = "DELETE FROM posts WHERE id='$post_id'";
        if (mysqli_query($data, $sql)) {
            echo "Post deleted successfully.";
        } else {
            echo "Error deleting post: " . mysqli_error($data);
        }
    } else {
        echo "Error: Missing post ID.";
    }
}

// Retrieve all posts
$sql = "SELECT * FROM posts ORDER BY id DESC";
$result = mysqli_query($data, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            display: flex;
            height: 100vh;
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

/* Content */
.content {
    margin-left: 260px; /* Adjust this value for sidebar spacing */
    padding: 20px;      /* Adjust padding for inner spacing */
    width: calc(50% - 260px); /* Adjust width to match the new margin */
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    flex-grow: 1;
}


        .form-group {
            margin-bottom: 20px;
        }

        .post-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .post {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .post:hover {
            transform: translateY(-5px);
        }

        .post img {
            width: 100%;
            height: auto;
            display: block;
        }

        .post .description {
            padding: 15px;
            font-size: 16px;
            color: #333;
        }

        .post .actions {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .post .actions button,
        .post .actions a {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .post .actions button:hover,
        .post .actions a:hover {
            background-color: #16a085;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }
        
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Manage Post</h2>
    <ul>
    <a href="adminhome.php"  class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin.php" ><i class="fas fa-cogs"></i> Manage Form</a>
    <a href="view_student.php"><i class="fas fa-users"></i> Manage Students</a>
    <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post</a>
    <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </ul>
</div>

<div class="content">
    <h1>Create or Update a Post</h1>

    <!-- Post Form -->
    <?php
    $post_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
    $post_description = '';
    $post_image = '';

    if ($post_id) {
        // Retrieve the post to edit
        $sql_edit = "SELECT * FROM posts WHERE id='$post_id'";
        $result_edit = mysqli_query($data, $sql_edit);
        $post = mysqli_fetch_assoc($result_edit);
        $post_description = $post['description'];
        $post_image = $post['image'];
    }
    ?>

    <form action="post_management.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <div class="form-group">
            <label for="description">Post Description</label>
            <textarea name="description" class="form-control" id="description" rows="3" required><?= $post_description ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image (Optional)</label>
            <input type="file" name="image" class="form-control" id="image">
            <?php if ($post_image): ?>
                <img src="<?= $post_image ?>" alt="Post Image" width="100px">
            <?php endif; ?>
        </div>
        <?php if ($post_id): ?>
            <button type="submit" name="update_post" class="btn btn-warning">
                <i class="fas fa-edit"></i> Update Post
            </button>
        <?php else: ?>
            <button type="submit" name="create_post" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Create Post
            </button>
        <?php endif; ?>
    </form>

    <hr>
<!-- Display Posts -->
<h2>Manage Posts</h2>
<div class="post-grid">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($post = mysqli_fetch_assoc($result)) {
            echo "<div class='post'>";
            // Only display the image if it's provided
            if (!empty($post['image'])) {
                echo "<img src='" . htmlspecialchars($post['image']) . "' alt='Post Image'>";
            }
            // Always display the description
            echo "<div class='description'>" . htmlspecialchars($post['description']) . "</div>";
            echo "<div class='actions'>
                    <form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='post_id' value='" . $post['id'] . "'>
                        <button type='submit' name='delete_post' class='btn btn-danger'>
                            <i class='fas fa-trash'></i> Delete
                        </button>
                    </form>
                    <a href='post_management.php?edit_id=" . $post['id'] . "' class='btn btn-primary'>
                        <i class='fas fa-pencil-alt'></i> Edit
                    </a>
                </div>";
            echo "</div>";
        }
    } else {
        echo "<p>No posts found.</p>";
    }
    ?>
</div>
</div>


</body>
</html>
