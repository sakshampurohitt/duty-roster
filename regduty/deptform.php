<?php
session_start();
require_once 'config/config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Insert operation
    if ($action == 'insert') {
        $departmentname = $_POST['departmentname'];

        // Prepare SQL statement
        $sql = "INSERT INTO department (departmentname) VALUES (?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
        } else {
            // Bind parameters
            $stmt->bind_param("s", $departmentname);

            // Execute statement
            if ($stmt->execute()) {
                $_SESSION['success'] = "Department added successfully!";
            } else {
                $_SESSION['error'] = "Error: " . htmlspecialchars($stmt->error);
            }

            // Close statement
            $stmt->close();
        }
    }

    // Delete operation
    if ($action == 'delete') {
        $departmentid = $_POST['departmentid'];

        // Check if department ID exists before deleting
        $check_sql = "SELECT 1 FROM department WHERE departmentid = ?";
        $check_stmt = $conn->prepare($check_sql);

        if ($check_stmt === false) {
            $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
        } else {
            $check_stmt->bind_param("i", $departmentid);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                // Department ID exists, proceed with deletion
                $delete_sql = "DELETE FROM department WHERE departmentid = ?";
                $delete_stmt = $conn->prepare($delete_sql);

                if ($delete_stmt === false) {
                    $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
                } else {
                    // Bind parameters and execute deletion
                    $delete_stmt->bind_param("i", $departmentid);
                    if ($delete_stmt->execute()) {
                        $_SESSION['success'] = "Department and associated employees deleted successfully!";
                    } else {
                        $_SESSION['error'] = "Error deleting department: " . htmlspecialchars($delete_stmt->error);
                    }
                    $delete_stmt->close();
                }
            } else {
                // Department ID does not exist
                $_SESSION['error'] = "Department with ID $departmentid does not exist!";
            }

            $check_stmt->close();
        }
    }

    // Redirect back to deptform.php after processing
    header("Location: deptform.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="assets/script.js" defer></script>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <button onclick="location.href='monthlydeptstat.php'">Monthly Department Status</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
    </div>

    <div class="container">
        <h2>Department Management</h2>

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

        <h3>Insert New Department</h3>
        <form method="post" action="deptform.php" id="insert_department_form">
            <input type="hidden" name="action" value="insert">
            <label for="departmentname">Department Name:</label>
            <input type="text" id="departmentname" name="departmentname" required>
            <button type="submit">Insert Department</button>
        </form>

        <h3>Delete Department</h3>
        <form method="post" action="deptform.php" id="delete_department_form">
            <input type="hidden" name="action" value="delete">
            <label for="departmentid">Department ID:</label>
            <input type="number" id="departmentid" name="departmentid" required>
            <button type="button" onclick="confirmDepartmentDeletion()">Delete Department</button>
        </form>
    </div>
</body>
</html>
