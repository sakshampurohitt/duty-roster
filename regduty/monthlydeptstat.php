<?php
session_start();
require_once 'config/config.php';

// Function to calculate total present and absent days for a given employee within a month
function calculateEmployeeAttendance($empid, $year, $month, $conn) {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deptid = $_POST['deptid'];
    $year = date("Y");
    $month = date("m");

    // Fetch department name
    $dept_name_sql = "SELECT departmentname FROM department WHERE departmentid = ?";
    $dept_stmt = $conn->prepare($dept_name_sql);
    $dept_stmt->bind_param("i", $deptid);
    $dept_stmt->execute();
    $dept_result = $dept_stmt->get_result();

    if ($dept_result->num_rows > 0) {
        $dept_row = $dept_result->fetch_assoc();
        $departmentname = $dept_row['departmentname'];

        // Fetch all employees of the department
        $emp_sql = "SELECT empid, name FROM employee WHERE departmentid = ?";
        $emp_stmt = $conn->prepare($emp_sql);
        $emp_stmt->bind_param("i", $deptid);
        $emp_stmt->execute();
        $emp_result = $emp_stmt->get_result();

        $department_attendance = [];

        while ($emp_row = $emp_result->fetch_assoc()) {
            $empid = $emp_row['empid'];
            $emp_name = $emp_row['name'];

            // Calculate attendance for each employee
            $attendance = calculateEmployeeAttendance($empid, $year, $month, $conn);

            // Store employee details along with attendance
            $department_attendance[] = [
                'empid' => $empid,
                'name' => $emp_name,
                'attendance' => $attendance
            ];
        }

    } else {
        $_SESSION['error'] = "Department not found!";
        header("Location: monthlydeptstat.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Department Status</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        /* Additional CSS for making rows clickable */
        .clickable-row {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
        <a href="logout.php" id="logout_link">Logout</a>
    </div>

    <div class="container">
        <h2>Monthly Department Status</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="post" action="monthlydeptstat.php">
            <label for="deptid">Select Department:</label>
            <select id="deptid" name="deptid" required>
                <?php
                // Fetch all departments for dropdown
                $dept_list_sql = "SELECT departmentid, departmentname FROM department";
                $dept_list_result = $conn->query($dept_list_sql);

                if ($dept_list_result->num_rows > 0) {
                    while ($dept_row = $dept_list_result->fetch_assoc()) {
                        $selected = ($deptid == $dept_row['departmentid']) ? "selected" : "";
                        echo "<option value='" . $dept_row['departmentid'] . "' $selected>" . $dept_row['departmentname'] . "</option>";
                    }
                }
                ?>
            </select>
            <button type="submit">View Monthly Status</button>
        </form>

        <?php if (isset($department_attendance) && !empty($department_attendance)) { ?>
            <h3>Monthly Status for Department: <?php echo $departmentname; ?></h3>
            <table>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Total Present Days</th>
                    <th>Total Absent Days</th>
                </tr>
                <?php foreach ($department_attendance as $employee) { ?>
                    <tr class="clickable-row" data-href="summarydeet.php?empid=<?php echo $employee['empid']; ?>">
                        <td><?php echo $employee['empid']; ?></td>
                        <td><?php echo $employee['name']; ?></td>
                        <td><?php echo $employee['attendance']['total_present']; ?></td>
                        <td><?php echo $employee['attendance']['total_absent']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

    <script>
        // JavaScript to make the table rows clickable
        document.addEventListener("DOMContentLoaded", function() {
            var rows = document.querySelectorAll(".clickable-row");
            rows.forEach(function(row) {
                row.addEventListener("click", function() {
                    var href = row.getAttribute("data-href");
                    if (href) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>
</body>
</html>
