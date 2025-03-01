<?php
// Database connection
require_once "config.php";

// Check if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure ID is an integer

    // Prepare Delete Query
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Redirect after successful deletion
            header("Location: manage_users.php?message=User+deleted+successfully");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Failed to prepare statement!";
    }
} else {
    echo "Invalid request!";
}
?>
