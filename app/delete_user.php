<?php
// delete_user.php
require_once('db_config.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the delete query
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the main list after deleting
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting: " . $conn->error;
    }
} else {
    header("Location: index.php");
    exit();
}
?>
