<?php
require_once 'config.php'; // Ensure database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = trim($_POST['subject_name']);

    if (empty($subject_name)) {
        $message = "All fields are required!";
        $message_subject = "error";
    } else {
        // Check if subject already exists
        $check_query = "SELECT * FROM subjects WHERE subject_name = ?";
        $stmt = $conn->prepare($check_query);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }
        $stmt->bind_param('s', $subject_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Subject already exists!";
            $message_subject = "error";
        } else {
            // Insert new subject
            $stmt->close();
            $insert_query = "INSERT INTO subjects (subject_name) VALUES (?)";
            $stmt = $conn->prepare($insert_query);
            if (!$stmt) {
                die("Query preparation failed: " . $conn->error);
            }
            $stmt->bind_param('s', $subject_name);

            if ($stmt->execute()) {
                header("Location: manage_subject.php");
                exit();
            } else {
                $message = "Error: " . $conn->error;
                $message_subject = "error";
            }
        }
        $stmt->close();
    }
}

// Fetch existing subjects
$subjects_query = "SELECT id, subject_name FROM subjects ORDER BY id DESC";
$subjects_result = $conn->query($subjects_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Management - PSchool</title>
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
        .mybutton {
            background-color: gold;
            float: right;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .mybutton a {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subject Management</h1>   
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_subject; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <button class="mybutton">
            <a href="manage_subject.php">Update Subject</a>
        </button>

        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name:</label>
                    <input type="text" id="subject_name" name="subject_name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Subject</button>
                <a href="admin.php" class="btn btn-danger w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
