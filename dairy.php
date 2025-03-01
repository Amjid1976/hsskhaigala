<?php
require_once "config.php";

if (!$conn) {
    die("Database connection failed!");
}

$query = "SELECT DISTINCT class_name FROM classes";
$classes = $conn->query($query);

$homework_data = [];
$class_name = '';
$date = date('Y-m-d');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_name'], $_POST['date'])) {
    $class_name = $_POST['class_name'];
    $date = $_POST['date'];
    
    $query = "SELECT h.id, s.subject_name, u.full_name AS teacher_name, h.description, h.due_date 
              FROM homework h
              JOIN subjects s ON h.subject_id = s.id
              JOIN users u ON h.teacher_id = u.id
              JOIN classes c ON h.class_id = c.id
              WHERE c.class_name = ? AND h.due_date = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss", $class_name, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $homework_data[] = $row;
        }
        $stmt->close();
    } else {
        die("Query preparation failed: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
        }
        table {
            background: white;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
        }
        .form-group {
            flex: 1;
            margin-right: 10px;
        }
        .form-group:last-child {
            margin-right: 0;
        }
        .table th:first-child, .table th:last-child {
            width: 20%;
        }
        .table th:nth-child(2) {
            width: 20%;
        }
        .table th:nth-child(3) {
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Homework Report</h2>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Select Class:</label>
                    <select name="class_name" class="form-control" required>
                        <option value="">Select a Class</option>
                        <?php while ($row = $classes->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($row['class_name']); ?>" <?php echo ($row['class_name'] == $class_name) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['class_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Date:</label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">View Homework</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
        </form>
        
        <?php if (!empty($homework_data)) { ?>
            <h3 class="mt-4">Class: <?php echo htmlspecialchars($class_name); ?></h3>
            <table class="table table-bordered mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Description</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($homework_data as $hw) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hw['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($hw['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($hw['description']); ?></td>
                            <td><?php echo htmlspecialchars($hw['due_date']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
            <p class='text-danger mt-3'>No homework found for this class and date.</p>
        <?php } ?>
    </div>
</body>
</html>
