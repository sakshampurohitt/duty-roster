<?php
session_start();
require_once 'config/config.php'; // Adjust this to your database connection script

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Insert operation
    if ($action == 'insert') {
        $deptid = $_POST['departmentid'];
        $empid = $_POST['empid'];
        $date = $_POST['date'];
        $shift_start = $_POST['shift_start'];
        $shift_end = $_POST['shift_end'];
        $present = $_POST['present'];

        // Validate and sanitize inputs (implement your validation logic)
        // Example: Basic validation for required fields
        if (empty($deptid) || empty($empid) || empty($date) || empty($shift_start) || empty($shift_end) || empty($present)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: add_schedule.php");
            exit();
        }

        // Check if deptid exists in department table
        $check_dept_query = "SELECT departmentid FROM department WHERE departmentid = ?";
        $stmt_dept = $conn->prepare($check_dept_query);
        $stmt_dept->bind_param("i", $deptid);
        $stmt_dept->execute();
        $stmt_dept->store_result();
        if ($stmt_dept->num_rows == 0) {
            $_SESSION['error'] = "Department with ID $deptid does not exist.";
            header("Location: add_schedule.php"); // Redirect back to form
            exit();
        }
        $stmt_dept->close();

        // Check if empid exists in employee table
        $check_emp_query = "SELECT empid FROM employee WHERE empid = ?";
        $stmt_emp = $conn->prepare($check_emp_query);
        $stmt_emp->bind_param("i", $empid);
        $stmt_emp->execute();
        $stmt_emp->store_result();
        if ($stmt_emp->num_rows == 0) {
            $_SESSION['error'] = "Employee with ID $empid does not exist.";
            header("Location: add_schedule.php"); // Redirect back to form
            exit();
        }
        $stmt_emp->close();

        // Insert into schedule table
        $insert_query = "INSERT INTO schedule (departmentid, empid, date, shift_start, shift_end, present) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("iissss", $deptid, $empid, $date, $shift_start, $shift_end, $present);

        if ($stmt_insert->execute()) {
            $_SESSION['success'] = "Schedule added successfully.";
        } else {
            $_SESSION['error'] = "Error: " . htmlspecialchars($stmt_insert->error);
        }
        $stmt_insert->close();

        // Redirect after processing
        header("Location: add_schedule.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <!-- Navigation buttons -->
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <button onclick="location.href='monthlydeptstat.php'">Monthly Department Status</button>
    </div>

    <div class="container">
        <h2>Add Schedule</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form id="add_schedule_form" method="post" action="add_schedule.php">
            <input type="hidden" name="action" value="insert">
            <label for="departmentid">Department ID:</label>
            <input type="number" id="departmentid" name="departmentid" required>
            <label for="empid">Employee ID:</label>
            <input type="number" id="empid" name="empid" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <label for="shift_start">Shift Start:</label>
            <input type="time" id="shift_start" name="shift_start" required>
            <label for="shift_end">Shift End:</label>
            <input type="time" id="shift_end" name="shift_end" required>
            <label for="present">Present:</label>
            <select id="present" name="present" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
            <button type="submit">Add Schedule</button>
        </form>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
