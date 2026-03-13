<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Form Data Submitted:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
?>
