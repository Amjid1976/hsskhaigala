<?php
require_once 'config.php'; // Ensure database connection

// Fetch teachers from the users table
$teachers_query = "SELECT full_name FROM users WHERE role = 'teacher' ORDER BY full_name ASC";
$teachers_result = $conn->query($teachers_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST['class_name']);
    $teacher_name = trim($_POST['teacher_name']); // Now stores full name
    $section = trim($_POST['section']);

    if (empty($class_name) || empty($teacher_name) || empty($section)) {
        $message = "All fields are required!";
        $message_class = "error";
    } else {
        // Check if class already exists with the same section
        $check_query = "SELECT * FROM classes WHERE class_name = ? AND section = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $class_name, $section);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Class with the same section already exists!";
            $message_class = "error";
        } else {
            // Insert new class with full name
            $insert_query = "INSERT INTO classes (class_name, teacher_name, section) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sss", $class_name, $teacher_name, $section);
            
            if ($stmt->execute()) {
                header('Location: manage_classes.php');
                exit();
            } else {
                $message = "Error: " . $conn->error;
                $message_class = "error";
            }
        }
    }
}

// Fetch existing classes
$classes_query = "SELECT id, class_name, teacher_name, section FROM classes ORDER BY id DESC";
$classes_result = $conn->query($classes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management - PSchool</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .error {
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .success {
            background: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: black;
        }
        .mybutton{
            background-color: gold;
            float: right;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Class Management</h1>   
        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
 <button class="mybutton"><a href="manage_classes.php" class="mybutton">Update Classes</a></button>
        <div class="form-container">
            <form method="POST" action="">
                  
                <div class="mb-3">
                    <label for="class_name" class="form-label">Class Name:</label>
                    <input type="text" id="class_name" name="class_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="teacher_name" class="form-label">Teacher Name:</label>
                    <select id="teacher_name" name="teacher_name" class="form-control" required>
                        <option value="">Select Teacher</option>
                        <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($teacher['full_name']); ?>">
                                <?php echo htmlspecialchars($teacher['full_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section:</label>
                    <input type="text" id="section" name="section" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Class</button>
                <a href="admin.php" class="btn btn-danger w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
