<?php
// Database connection
require_once "config.php";

// Fetch users data
$sql = "SELECT id, subject_name FROM subjects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subject Table</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let subject_name = row.cells[1].textContent.toLowerCase();

                if (class_name.includes(input) ) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            margin-top: 20px;
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-group {
            display: flex;
            justify-content: center;
        }
        .btn {
            margin: 2px;
        }
        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3 text-center">Subject Management</h2>
        <div class="top-buttons">
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Search" onkeyup="searchTable()">
            <div>
                <a href="add_subject.php" class="btn btn-success">Add Subject</a>
                <a href="admin.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['subject_name']; ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="update_subject.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                                <a href="delete_subject.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
