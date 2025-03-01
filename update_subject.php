<?php
// Database connection
require_once "config.php";

// Check if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch subject data
    $sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();

    if (!$subject) {
        die("Subject not found!");
    }
} else {
    die("Invalid request!");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = trim($_POST['subject_name']);

    if (!empty($subject_name)) {
        $update_sql = "UPDATE subjects SET subject_name = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }
        $stmt->bind_param("si", $subject_name, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Subject updated successfully!'); window.location='manage_subject.php';</script>";
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Subject name cannot be empty!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Subject</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3">Update Subject</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Subject Name</label>
                <input type="text" name="subject_name" class="form-control" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="manage_subject.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
