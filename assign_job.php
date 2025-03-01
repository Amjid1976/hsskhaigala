<?php
require_once "config.php";
session_start();

// Fetch teachers and classes for dropdowns
$teachers = $conn->query("SELECT id, full_name FROM users WHERE role='teacher' ORDER BY full_name ASC");
$classes = $conn->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
$subjects = $conn->query("SELECT id, subject_name FROM subjects ORDER BY subject_name ASC");

// Handle form submission
if(isset($_POST['submit'])) {
    $teacher_id = $_POST['teacher_id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];

    // Get the names
    $get_teacher = $conn->query("SELECT full_name FROM users WHERE id = '$teacher_id'");
    $get_class = $conn->query("SELECT class_name FROM classes WHERE id = '$class_id'");
    $get_subject = $conn->query("SELECT subject_name FROM subjects WHERE id = '$subject_id'");

    $teacher_name = $get_teacher->fetch_assoc()['full_name'];
    $class_name = $get_class->fetch_assoc()['class_name'];
    $subject_name = $get_subject->fetch_assoc()['subject_name'];

    // Check if assignment already exists
    $check = $conn->query("SELECT * FROM assignments1 WHERE teacher_id = '$teacher_id' AND class_id = '$class_id' AND subject_id = '$subject_id'");

    if($check->num_rows > 0) {
        echo "<script>alert('This assignment already exists!');</script>";
    } else {
        // Insert the assignment
        $sql = "INSERT INTO assignments1 (teacher_id, class_id, subject_id, teacher_name, class_name, subject_name) 
                VALUES ('$teacher_id', '$class_id', '$subject_id', '$teacher_name', '$class_name', '$subject_name')";

        if($conn->query($sql)) {
            echo "<script>alert('Job assigned successfully!'); window.location='assign_job.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Job</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Assign Job to Teacher</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Select Teacher:</label>
                        <select class="form-control" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            <?php while($row = $teachers->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['full_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Class:</label>
                        <select class="form-control" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php while($row = $classes->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['class_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Subject:</label>
                        <select class="form-control" name="subject_id" required>
                            <option value="">Select Subject</option>
                            <?php while($row = $subjects->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['subject_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="submit" name="submit" value="Assign Job" class="btn btn-primary">
                        <a href="admin.php" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
