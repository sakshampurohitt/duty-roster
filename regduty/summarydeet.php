<?php
session_start();
require_once 'config/config.php';

// Check if date parameter is set
if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Fetch detailed employee information for the selected date
    $sql = "SELECT s.empid, e.name, d.departmentname, e.stafftype 
            FROM schedule s
            INNER JOIN employee e ON s.empid = e.empid
            INNER JOIN department d ON s.departmentid = d.departmentid
            WHERE s.date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $_SESSION['error'] = "Date parameter not found!";
    header("Location: summaryduty.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details Summary</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
        <button onclick="location.href='summaryduty.php'">Back</button>
    </div>

    <div class="container">
        <h2>Employee Details Summary for Date: <?php echo htmlspecialchars($date); ?></h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <?php if (isset($details) && !empty($details)) { ?>
            <table>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Staff Type</th>
                </tr>
                <?php foreach ($details as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['empid']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['departmentname']); ?></td>
                        <td><?php echo htmlspecialchars($row['stafftype']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No employee details found for this date.</p>
        <?php } ?>
    </div>
</body>
</html>
