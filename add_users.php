<?php
require_once 'auth.php';
require_once 'config.php';

// Ensure only admin can access this page
requireRole('admin');
checkSessionTimeout();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // No hashing
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);


    // Check if username already exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        $message = "Username already exists!";
        $message_class = "error";
    } else {
        $sql = "INSERT INTO users (username, password, role, full_name, email, phone,address) VALUES ('$username', '$password', '$role','$full_name', '$email', '$phone', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            header('Location: manage_users.php');
            //$message = "User added successfully!";
            $message_class = "success";
        } else {
            $message = "Error: " . $conn->error;
            $message_class = "error";
        }
    }
}

// Fetch existing users
$users_query = "SELECT id, username, role, email FROM users ORDER BY id DESC";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PSchool</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            padding: 10px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto 30px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 0px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
        a{
            text-decoration: none;
            display: inline-block;
            color: black;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Management</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                 <button class="mybutton"><a href="manage_users.php" class="mybutton">Update Users</a></button>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="username">Full name:</label>
                    <input type="text" id="username" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="username">Phone:</label>
                    <input type="text" id="username" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="username">Address:</label>
                    <input type="text" id="username" name="address" required>
                </div>
                <button type="submit">Add User</button>
                <button type="cancel" class="btn btn-danger"><a href="admin.php">Cancel</a></button>
            </form>
        </div>
            
                
    </div>
</body>
</html>
