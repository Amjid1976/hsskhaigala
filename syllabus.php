<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "config.php";

if (!$conn) {
    die("Database connection failed!");
}


// Fetch all classes
$class_query = $conn->prepare("SELECT id AS class_id, class_name FROM classes");
$class_query->execute();
$classes = $class_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle AJAX request to fetch subjects from course_division
if (isset($_POST['action']) && $_POST['action'] == "get_course_data") {
    $class_id = $_POST['class_id'];
    $query = $conn->prepare("SELECT cd.id, s.subject_name, u.full_name AS teacher_name, cd.description 
                             FROM course_division cd
                             JOIN subjects s ON cd.subject_id = s.id
                             JOIN users u ON cd.teacher_id = u.id
                             WHERE cd.class_id = ?");
    $query->bind_param("i", $class_id);
    $query->execute();
    $result = $query->get_result();

    $course_data = [];
    while ($row = $result->fetch_assoc()) {
        $course_data[] = $row;
    }
    echo json_encode($course_data);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Division</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#class_id").change(function() {
                let classId = $(this).val();
                if (classId === "") {
                    $("#course_table tbody").html("");
                    return;
                }

                $.ajax({
                    url: "", // Same page
                    method: "POST",
                    data: { action: "get_course_data", class_id: classId },
                    dataType: "json",
                    success: function(data) {
                        let tableBody = $("#course_table tbody");
                        tableBody.empty();
                        if (data.length > 0) {
                            data.forEach((item, index) => {
                                tableBody.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.subject_name}</td>
                                        <td>${item.teacher_name}</td>
                                        <td>${item.description}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="4" class="text-center">No data available</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Course Division</span>
            <a href="admin.php" class="btn btn-danger">Back</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h4>Select Class to View Course Division</h4>

        <div class="form-group">
            <label>Select Class</label>
            <select name="class_id" id="class_id" class="form-control">
                <option value="">Select Class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['class_id']; ?>">
                        <?= htmlspecialchars($class['class_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="table table-bordered mt-4" id="course_table">
            <thead class="thead-dark">
                <tr>
                    <th>S.No.</th>
                    <th>Subject Name</th>
                    <th>Teacher Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4" class="text-center">Select a class to view course details</td></tr>
            </tbody>
        </table>
    </div>
</body>
</html>
