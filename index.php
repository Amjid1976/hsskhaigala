<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if config.php exists
if (!file_exists("config.php")) {
    die("Error: config.php file not found!");
}

require_once "config.php";

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Debug login attempt
    error_log("Login attempt for username: " . $username);

    // Use plain text comparison for now to debug
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        $role = $row['role'];
        
        // Debug session
        error_log("Session variables set:");
        error_log("ID: " . $_SESSION['id']);
        error_log("Username: " . $_SESSION['username']);
        error_log("Role: " . $_SESSION['role']);

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Redirect based on role
        if ($role == "teacher") {
            error_log("Redirecting to: Teacher/teacher.php");
            header("Location: Teacher/teacher.php");
            exit();
        } elseif ($role == "admin") {
            error_log("Redirecting to: admin.php");
            header("Location: admin.php");
            exit();
        } elseif ($role == "parent") {
            error_log("Redirecting to: parent.php");
            header("Location: parent.php");
            exit();
        } elseif ($role == "student") {
            error_log("Redirecting to: student.php");
            header("Location: student.php");
            exit();
        }
    } else {
        $error = "Invalid username or password!";
        error_log("Login failed for username: " . $username);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            margin-top: 100px;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1.5rem;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">School Management System</h3>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>