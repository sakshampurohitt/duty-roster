<?php
session_start();
require_once 'config/config.php';

// Function to calculate total present and absent days for a given month
function calculateAttendance($empid, $year, $month, $conn) {
    // Calculate start and end dates for the given month
    $start_date = date("Y-m-01", strtotime("$year-$month-01"));
    $end_date = date("Y-m-t", strtotime("$year-$month-01"));

    // Fetch attendance data for the employee within the given month
    $sql = "SELECT present, DATE(date) AS day, shift_start, shift_end
            FROM schedule
            WHERE empid = ? AND date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $empid, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendance = [
        'total_present' => 0,
        'total_absent' => 0,
        'details' => []
    ];

    while ($row = $result->fetch_assoc()) {
        if ($row['present'] == 'yes') {
            $attendance['total_present']++;
        } else {
            $attendance['total_absent']++;
        }

        // Collect shift details for each day
        $attendance['details'][] = [
            'date' => $row['day'],
            'shift_start' => $row['shift_start'],
            'shift_end' => $row['shift_end'],
            'present' => $row['present']
        ];
    }

    $stmt->close();

    return $attendance;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empid = $_POST['empid'];

    // Fetch employee details
    $emp_sql = "SELECT name FROM employee WHERE empid = ?";
    $emp_stmt = $conn->prepare($emp_sql);
    $emp_stmt->bind_param("i", $empid);
    $emp_stmt->execute();
    $emp_stmt->bind_result($emp_name);
    $emp_stmt->fetch();
    $emp_stmt->close();

    if (!empty($emp_name)) {
        // Get current month and year
        $year = date("Y");
        $month = date("m");

        // Calculate attendance for the employee for the current month
        $attendance = calculateAttendance($empid, $year, $month, $conn);
    } else {
        $_SESSION['error'] = "Employee not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Employee Status</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlydeptstat.php'">Monthly Department Status</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
        <a href="logout.php" id="logout_link">Logout</a>
    </div>

    <div class="container">
        <h2>Monthly Employee Status</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="empid">Enter Employee ID:</label>
            <input type="number" id="empid" name="empid" required>
            <button type="submit">View Status</button>
        </form>

        <?php if (isset($attendance)) { ?>
            <h3>Employee: <?php echo htmlspecialchars($emp_name); ?></h3>
            <h4>Monthly Report for <?php echo date("F Y"); ?></h4>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                    <th>Present</th>
                </tr>
                <?php foreach ($attendance['details'] as $day) { ?>
                    <tr>
                        <td><?php echo $day['date']; ?></td>
                        <td><?php echo $day['shift_start']; ?></td>
                        <td><?php echo $day['shift_end']; ?></td>
                        <td><?php echo ucfirst($day['present']); ?></td>
                    </tr>
                <?php } ?>
            </table>
            <p>Total Present Days: <?php echo $attendance['total_present']; ?></p>
            <p>Total Absent Days: <?php echo $attendance['total_absent']; ?></p>
        <?php } ?>
    </div>
</body>
</html>
