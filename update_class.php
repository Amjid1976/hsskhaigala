<?php
// Database connection
require_once "config.php";

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch class data
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();

    if (!$class) {
        die("Class not found!");
    }
} else {
    die("Invalid request!");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $teacher_name = $_POST['teacher_name'];
    $section = $_POST['section'];

    $update_sql = "UPDATE classes SET class_name = ?, teacher_name = ?, section = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $class_name, $teacher_name, $section, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Class updated successfully!'); window.location='manage_classes.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Class</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3">Update Class</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Class Name</label>
                <input type="text" name="class_name" class="form-control" value="<?php echo $class['class_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teacher Name</label>
                <input type="text" name="teacher_name" class="form-control" value="<?php echo $class['teacher_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Section</label>
                <input type="text" name="section" class="form-control" value="<?php echo $class['section']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="manage_classes.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
