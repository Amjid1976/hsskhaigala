<?php
require_once "config.php";

if (!$conn) {
    die("Database connection failed!");
}

$query = "SELECT DISTINCT class_name FROM classes";
$classes = $conn->query($query);

$attendance_data = [];
$total_days = 0;
$class_name = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_name'], $_POST['month'], $_POST['year'])) {
    $class_name = $_POST['class_name'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $query = "SELECT roll_no, student_name, DAY(date) as day, status FROM attendance WHERE class_name = ? AND MONTH(date) = ? AND YEAR(date) = ? ORDER BY roll_no ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $class_name, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendance_data[$row['roll_no']]['name'] = $row['student_name'];
        $attendance_data[$row['roll_no']]['attendance'][$row['day']] = substr($row['status'], 0, 1);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Attendance Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: center; padding: 8px; border: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        .sunday { background-color: #ffcccc; }
        td:first-child { width: 8%; }
        td:nth-child(2) { width: 20%; text-align: left; }
        .search-container { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Monthly Attendance Report</h2>
        
        <form method="POST">
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
                <label>Select Month:</label>
                <select name="month" class="form-control" required>
                    <option value="">Select a Month</option>
                    <?php for ($m = 1; $m <= 12; $m++) { ?>
                        <option value="<?php echo $m; ?>" <?php echo (isset($month) && $m == $month) ? 'selected' : ''; ?>><?php echo date("F", mktime(0, 0, 0, $m, 1)); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Year:</label>
                <input type="number" name="year" class="form-control" value="<?php echo isset($year) ? $year : date('Y'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">View Attendance</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>

        </form>
        
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by Roll No or Student Name...">
        </div>

        <?php if (!empty($attendance_data)) { ?>
            <h3 class="mt-4">Class: <?php echo htmlspecialchars($class_name); ?></h3>
            <table class="table table-bordered mt-4" id="attendanceTable">
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <?php for ($d = 1; $d <= $total_days; $d++) { ?>
                            <th class="<?php echo (date('N', strtotime("$year-$month-$d")) == 7) ? 'sunday' : ''; ?>"><?php echo $d; ?></th>
                        <?php } ?>
                        <th>Present Count</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $roll_no => $data) { 
                        $present_count = count(array_filter($data['attendance'], function($status) { return $status == 'P'; }));
                        $attendance_percentage = ($total_days > 0) ? round(($present_count / $total_days) * 100, 2) : 0;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($roll_no); ?></td>
                            <td><?php echo htmlspecialchars($data['name']); ?></td>
                            <?php for ($d = 1; $d <= $total_days; $d++) { ?>
                                <td class="<?php echo (date('N', strtotime("$year-$month-$d")) == 7) ? 'sunday' : ''; ?>">
                                    <?php echo isset($data['attendance'][$d]) ? htmlspecialchars($data['attendance'][$d]) : '-'; ?>
                                </td>
                            <?php } ?>
                            <td><?php echo $present_count; ?></td>
                            <td><?php echo $attendance_percentage . '%'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
            <p class='text-danger mt-3'>No attendance records found for this class and month.</p>
        <?php } ?>
    </div>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toUpperCase();
            let rows = document.querySelectorAll("#attendanceTable tbody tr");
            rows.forEach(row => {
                let rollNo = row.cells[0].textContent.toUpperCase();
                let name = row.cells[1].textContent.toUpperCase();
                if (rollNo.indexOf(filter) > -1 || name.indexOf(filter) > -1) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>
