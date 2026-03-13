<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'profiling_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token.";
        header("Location: index.php");
        exit();
    }

    $query = "SELECT name FROM form_fields";
    $result = $conn->query($query);
    if (!$result || $result->num_rows === 0) {
        $_SESSION['error'] = "Form fields not configured.";
        header("Location: index.php");
        exit();
    }

    $fields = [];
    $placeholders = [];
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $field_name = $row['name'];
        if (isset($_POST[$field_name])) {
            $fields[] = "`" . $field_name . "`"; // Wrap column names with backticks
            $placeholders[] = "?";
            $values[] = htmlspecialchars(trim($_POST[$field_name]));
        }
    }

    file_put_contents('debug_log.txt', "Fields: " . print_r($fields, true) . "\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "Values: " . print_r($values, true) . "\n", FILE_APPEND);

    if (!empty($fields)) {
        $columns = implode(", ", $fields);
        $placeholders_str = implode(", ", $placeholders);
        $stmt = $conn->prepare("INSERT INTO admission ($columns) VALUES ($placeholders_str)");

        if (!$stmt) {
            die("SQL Preparation Error: " . $conn->error);
        }

        $stmt->bind_param(str_repeat("s", count($values)), ...$values);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Form submitted successfully.";
        } else {
            $_SESSION['error'] = "SQL Error: " . $conn->error;
        }

        $stmt->close();
    }

    $conn->close();
    header("Location: index.php");
    exit();
}
?>
