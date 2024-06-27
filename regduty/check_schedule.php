<?php
session_start();
require_once 'config/config.php';

// Fetch schedules for present and future dates, including the logged-in user's schedule
$sql = "SELECT s.date, s.shift_start, s.shift_end, s.present, s.empid, s.departmentid, e.name as emp_name, d.departmentname as dept_name 
        FROM schedule s
        JOIN employee e ON s.empid = e.empid
        JOIN department d ON s.departmentid = d.departmentid
        WHERE s.date >= CURDATE() OR s.empid = ?
        ORDER BY s.date ASC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Schedule</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='empdash.php'">Employee Dashboard</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <a href="logout.php" id="logout_link">Logout</a>
    </div>

    <div class="container">
        <h2>Check Schedule</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Shift Start</th>
                <th>Shift End</th>
                <th>Present</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department ID</th>
                <th>Department Name</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['shift_start'] . "</td>";
                    echo "<td>" . $row['shift_end'] . "</td>";
                    echo "<td>" . $row['present'] . "</td>";
                    echo "<td>" . $row['empid'] . "</td>";
                    echo "<td>" . $row['emp_name'] . "</td>";
                    echo "<td>" . $row['departmentid'] . "</td>";
                    echo "<td>" . $row['dept_name'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No schedules found for present and future dates.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
