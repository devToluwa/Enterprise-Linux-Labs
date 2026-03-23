<?php
// add_user.php
require_once('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['username'])) {
    
    // Grab both name and role from the form
    $name = $_POST['username'];
    $role = $_POST['role']; 

    $stmt = $conn->prepare("INSERT INTO users (name, role) VALUES (?, ?)");
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // "ss" = two strings
    $stmt->bind_param("ss", $name, $role);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Insert failed: " . $stmt->error;
    }
}
?>
