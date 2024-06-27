<?php
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departmentname = $_POST['departmentname'];

    // Fetch department ID
    $dept_sql = "SELECT departmentid FROM department WHERE departmentname = ?";
    $dept_stmt = $conn->prepare($dept_sql);
    $dept_stmt->bind_param("s", $departmentname);
    $dept_stmt->execute();
    $dept_stmt->store_result();

    if ($dept_stmt->num_rows > 0) {
        $dept_stmt->bind_result($departmentid);
        $dept_stmt->fetch();

        // Get current date
        $current_date = date('Y-m-d');
        
        // Fetch schedule data for the last 4 days including today
        $schedule_sql = "SELECT date, empid, shift_start, shift_end, present 
                         FROM schedule 
                         WHERE departmentid = ? AND date BETWEEN DATE_SUB(?, INTERVAL 2 DAY) AND DATE_ADD(?, INTERVAL 2 DAY) 
                         ORDER BY date, shift_start";
        $schedule_stmt = $conn->prepare($schedule_sql);
        $schedule_stmt->bind_param("iss", $departmentid, $current_date, $current_date);
        $schedule_stmt->execute();
        $result = $schedule_stmt->get_result();

        $schedule_data = [];
        while ($row = $result->fetch_assoc()) {
            $schedule_data[$row['date']][] = $row;
        }

        $dept_stmt->close();
        $schedule_stmt->close();
    } else {
        $_SESSION['error'] = "Department not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary of Duty Register</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
    </div>

    <div class="container">
        <h2>Summary of Duty Register</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="post" action="summaryduty.php">
            <label for="departmentname">Department Name:</label>
            <input type="text" id="departmentname" name="departmentname" required>
            <button type="submit">View Summary</button>
        </form>

        <?php if (isset($schedule_data) && !empty($schedule_data)) { ?>
            <h3>Schedule Summary</h3>
            <?php foreach ($schedule_data as $date => $data) { ?>
                <h4><?php echo $date; ?></h4>
                <table>
                    <tr>
                        <th>Employee ID</th>
                        <th>Shift Start</th>
                        <th>Shift End</th>
                        <th>Present</th>
                        <th>Details</th>
                    </tr>
                    <?php 
                    $total_present = 0;
                    foreach ($data as $row) { 
                        if ($row['present'] == 'yes') {
                            $total_present++;
                        }
                    ?>
                        <tr>
                            <td><?php echo $row['empid']; ?></td>
                            <td><?php echo $row['shift_start']; ?></td>
                            <td><?php echo $row['shift_end']; ?></td>
                            <td><?php echo $row['present']; ?></td>
                            <td><a href="summarydeet.php?date=<?php echo $date; ?>">View Details</a></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4"><strong>Total Present:</strong></td>
                        <td><strong><?php echo $total_present; ?></strong></td>
                    </tr>
                </table>
                <hr>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>
