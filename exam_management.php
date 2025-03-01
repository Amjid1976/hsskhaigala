<?php
require_once "config.php";

if (!$conn) {
    die("Database connection failed!");
}

// Fetch class list
$class_query = "SELECT DISTINCT class_name FROM classes";
$classes = $conn->query($class_query);

// Fetch exam types
$type_query = "SELECT DISTINCT type_name FROM result_types";
$type_result = $conn->query($type_query);

$exam_data = [];
$class_name = '';
$selected_type = ''; // Renamed to avoid conflict
$date = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_name'], $_POST['type_name'], $_POST['date'])) {
    $class_name = $_POST['class_name'];
    $selected_type = $_POST['type_name'];
    $date = $_POST['date'];

    $query = "SELECT r.roll_no, s.full_name AS student_name, r.total_marks, r.marks_obtained 
              FROM results r
              JOIN students s ON r.roll_no = s.roll_no
              JOIN classes c ON s.class_name = c.class_name
              WHERE c.class_name = ? AND r.type_name = ? AND r.date_of_exam = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sss", $class_name, $selected_type, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $row['percentage'] = ($row['total_marks'] > 0) ? ($row['marks_obtained'] / $row['total_marks']) * 100 : 0;
            $row['grade'] = calculateGrade($row['percentage']);
            $exam_data[] = $row;
        }
        $stmt->close();
    } else {
        die("Query preparation failed: " . $conn->error);
    }
}

function calculateGrade($percentage) {
    if ($percentage >= 90) return 'A+';
    elseif ($percentage >= 80) return 'A';
    elseif ($percentage >= 70) return 'B';
    elseif ($percentage >= 60) return 'C';
    elseif ($percentage >= 50) return 'D';
    elseif ($percentage >= 40) return 'E';
    else return 'F';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container {
            max-width: 900px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 { color: #007bff; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Exam Results</h2>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Select Class:</label>
                    <select name="class_name" class="form-control" required>
                        <option value="">Select a Class</option>
                        <?php while ($row = $classes->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($row['class_name']); ?>" <?= ($row['class_name'] == $class_name) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($row['class_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Exam Type:</label>
                    <select name="type_name" class="form-control" required>
                        <option value="">Select Exam Type</option>
                        <?php while ($row = $type_result->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($row['type_name']); ?>" <?= ($row['type_name'] == $selected_type) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($row['type_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Date:</label>
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">View Results</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
        </form>
        
        <?php if (!empty($exam_data)) { ?>
            <h3 class="mt-4">Class: <?= htmlspecialchars($class_name); ?></h3>
            <table class="table table-bordered mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Total Marks</th>
                        <th>Obtained Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exam_data as $exam) { ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['roll_no']); ?></td>
                            <td><?= htmlspecialchars($exam['student_name']); ?></td>
                            <td><?= htmlspecialchars($exam['total_marks']); ?></td>
                            <td><?= htmlspecialchars($exam['marks_obtained']); ?></td>
                            <td><?= number_format($exam['percentage'], 2) ?>%</td>
                            <td><?= htmlspecialchars($exam['grade']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
            <p class='text-danger mt-3'>No exam results found for this selection.</p>
        <?php } ?>
    </div>
</body>
</html>
