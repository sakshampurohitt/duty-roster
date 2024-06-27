<?php
session_start();
require_once 'config/config.php';

// Ensure the user is logged in and is an employee
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: login.php");
    exit();
}

// Fetch employee ID from the session
$empid = $_SESSION['user_id'];

// Calculate the date 6 months ago
$six_months_ago = date('Y-m-d', strtotime('-6 months'));

// Fetch attendance data for the past 6 months
$sql = "SELECT date, shift_start, shift_end, present 
        FROM schedule 
        WHERE empid = ? AND date >= ? 
        ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $empid, $six_months_ago);
$stmt->execute();
$result = $stmt->get_result();

$attendance = [
    'present' => [],
    'absent' => []
];

while ($row = $result->fetch_assoc()) {
    if ($row['present'] == 'yes') {
        $attendance['present'][] = $row;
    } else {
        $attendance['absent'][] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Monthly Attendance</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='empdash.php'">Employee Dashboard</button>
        <button onclick="location.href='logout.php'">Logout</button>
    </div>

    <div class="container">
        <h2>Monthly Attendance for the Past 6 Months</h2>
        
        <h3>Present Days</h3>
        <?php if (!empty($attendance['present'])) { ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                </tr>
                <?php foreach ($attendance['present'] as $day) { ?>
                    <tr>
                        <td><?php echo $day['date']; ?></td>
                        <td><?php echo $day['shift_start']; ?></td>
                        <td><?php echo $day['shift_end']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No present days recorded in the past 6 months.</p>
        <?php } ?>

        <h3>Absent Days</h3>
        <?php if (!empty($attendance['absent'])) { ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                </tr>
                <?php foreach ($attendance['absent'] as $day) { ?>
                    <tr>
                        <td><?php echo $day['date']; ?></td>
                        <td><?php echo $day['shift_start']; ?></td>
                        <td><?php echo $day['shift_end']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No absent days recorded in the past 6 months.</p>
        <?php } ?>
    </div>
</body>
</html>
