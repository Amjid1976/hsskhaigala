<?php
require_once "config.php";

if (!$conn) {
    die("Database connection failed!");
}

$query = "SELECT DISTINCT class_name FROM classes";
$classes = $conn->query($query);


$teachers = [];
$daily_topics = [];

// Fetch all teachers
$teacher_query = $conn->query("SELECT id, full_name FROM users WHERE role = 'teacher'");
while ($row = $teacher_query->fetch_assoc()) {
    $teachers[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_id']) && !empty($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];
    $selected_date = $_POST['selected_date'] ?? date('Y-m-d'); // Default to today's date if not selected

    $topic_query = $conn->prepare("SELECT dt.id, c.class_name, dt.time_from, dt.time_to, 
                                  TIMEDIFF(dt.time_to, dt.time_from) AS duration, dt.topic 
                                  FROM daily_topic dt 
                                  JOIN classes c ON dt.class_id = c.id 
                                  WHERE dt.teacher_id = ? AND DATE(dt.date) = ?");
    $topic_query->bind_param("is", $teacher_id, $selected_date);
    $topic_query->execute();
    $result = $topic_query->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $daily_topics[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Topic</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Daily Topic</span>
            <a href="admin.php" class="btn btn-danger">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h4>Select Teacher and Date to View Daily Topics</h4>

        <form method="POST">
            <div class="form-group">
                <label for="teacher_id">Select Teacher:</label>
                <select name="teacher_id" id="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher['id']; ?>" <?= (isset($teacher_id) && $teacher_id == $teacher['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($teacher['full_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="selected_date">Select Date:</label>
                <input type="date" name="selected_date" id="selected_date" class="form-control" 
                       value="<?= isset($selected_date) ? $selected_date : date('Y-m-d'); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">View Topics</button>
        </form>

        <?php if (!empty($daily_topics)): ?>
            <div class="mt-4">
                <h5>Daily Topics for <?= htmlspecialchars($selected_date); ?></h5>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Class Name</th>
                            <th>Time From</th>
                            <th>Time To</th>
                            <th>Duration</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_topics as $index => $topic): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($topic['class_name']); ?></td>
                                <td><?= htmlspecialchars($topic['time_from']); ?></td>
                                <td><?= htmlspecialchars($topic['time_to']); ?></td>
                                <td><?= htmlspecialchars($topic['duration']); ?></td>
                                <td><?= htmlspecialchars($topic['topic']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <?php if (isset($_POST['teacher_id'])): ?>
                <div class="alert alert-warning mt-3">No topics found for the selected teacher and date.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
