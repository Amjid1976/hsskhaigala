<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit'])) {
    $roll_no = trim($_POST['roll_no']);
    $full_name = trim($_POST['full_name']);
    $class_name = trim($_POST['class_name']);
    
    if(empty($roll_no) || empty($full_name) || empty($class_name)) {
        echo "<div style='color: red; margin: 10px 0;'>Please fill all fields</div>";
    } else {
        // First check if student exists in the same class
        $check_query = "SELECT * FROM students WHERE roll_no = '$roll_no' AND class_name = '$class_name'";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) > 0) {
            echo "<div style='color: red; margin: 10px 0;'>Error: Roll number $roll_no already exists in class $class_name</div>";
        } else {
            $query = "INSERT INTO students (roll_no, full_name, class_name) VALUES ('$roll_no', '$full_name', '$class_name')";
            $result = mysqli_query($conn, $query);
            
            if($result) {
                echo "<div style='color: green; margin: 10px 0;'>Student added successfully!</div>";
                // Clear form data
                $roll_no = $full_name = $class_name = '';
            } else {
                echo "<div style='color: red; margin: 10px 0;'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}

if(isset($_POST['cancel'])) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Student</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .form-container { max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .btn-submit { background-color: #4CAF50; color: white; border: none; }
        .btn-cancel { background-color: #f44336; color: white; border: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Student</h2>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Roll Number:</label>
                <input type="text" name="roll_no" value="<?php echo isset($roll_no) ? htmlspecialchars($roll_no) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Class:</label>
                <select name="class_name" required>
                    <option value="">Select Class</option>
                    <?php
                    $query = "SELECT class_name FROM classes ORDER BY class_name";
                    $result = mysqli_query($conn, $query);
                    
                    if($result) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $selected = (isset($class_name) && $class_name == $row['class_name']) ? 'selected' : '';
                            echo "<option value='" . $row['class_name'] . "' $selected>" . $row['class_name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Error loading classes: " . mysqli_error($conn) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <input type="submit" name="submit" value="Add Student" class="btn btn-submit">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-cancel" formnovalidate>
        </form>
    </div>
</body>
</html>
