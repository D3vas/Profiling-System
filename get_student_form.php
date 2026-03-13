<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = intval($_GET['id']);
} else {
    echo json_encode(["error" => "Invalid student ID"]);
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$db = "profiling_system";

$data = mysqli_connect($host, $user, $password, $db);
if (!$data) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch student profile
$student_query = "SELECT profile_picture FROM register WHERE id = $student_id";
$student_result = mysqli_query($data, $student_query);

$default_image = "path/to/default-profile.png"; 

if ($student_result && mysqli_num_rows($student_result) > 0) {
    $student_info = mysqli_fetch_assoc($student_result);
    $profile_picture = !empty($student_info['profile_picture']) ? $student_info['profile_picture'] : $default_image;
} else {
    $profile_picture = $default_image;
}

// Fetch form responses
$form_sql = "
    SELECT f.form_title, fr.response_data 
    FROM form_responses fr 
    JOIN forms f ON fr.form_id = f.id 
    WHERE fr.student_id = $student_id
";
$form_result = mysqli_query($data, $form_sql);

if ($form_result && mysqli_num_rows($form_result) > 0) {
    $form_data = mysqli_fetch_assoc($form_result);
    $response_data = json_decode($form_data['response_data'], true);
    
    // Prepare data to return
    $response = [
        'formTitle' => $form_data['form_title'],
        'profilePicture' => $profile_picture,
        'formResponses' => $response_data
    ];

    echo json_encode($response);
} else {
    echo json_encode(["error" => "No form responses found for this student."]);
}
?>
