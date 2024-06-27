<?php
session_start();
 require_once 'config/config.php';
//  Database connection parameters
//  $servername = "localhost";
//  $username = "root";
//  $password = "";
//  $dbname = "dutyregister";


// // Establish database connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Insert operation
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
    
        // Insert operation
        if ($action == 'insert') {
            $name = $_POST['name'];
            $departmentid = $_POST['departmentid'];
            $contact_no = $_POST['contact_no'];
            $stafftype = $_POST['stafftype'];
            $password = $_POST['password'];
    
            // Prepare SQL statement
            $sql = "INSERT INTO employee (name, departmentid, contact_no, stafftype, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
    
            if ($stmt === false) {
                $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
            } else {
                // Bind parameters
                $stmt->bind_param("sisss", $name, $departmentid, $contact_no, $stafftype, $password);
    
                // Execute statement
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Employee added successfully!";
                } else {
                    $_SESSION['error'] = "Error: " . htmlspecialchars($stmt->error);
                }
    
                // Close statement
                $stmt->close();
            }
        }
    
        // Redirect back to empform.php after processing
        header("Location: empform.php");
        exit();
    }
    

    // Delete operation
    if ($action == 'delete') {
        $empid = $_POST['empid'];

        // Check if employee ID exists before deleting
        $check_sql = "SELECT 1 FROM employee WHERE empid = ?";
        $check_stmt = $conn->prepare($check_sql);

        if ($check_stmt === false) {
            $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
        } else {
            $check_stmt->bind_param("i", $empid);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                // Employee ID exists, proceed with deletion
                $delete_sql = "DELETE FROM employee WHERE empid = ?";
                $delete_stmt = $conn->prepare($delete_sql);

                if ($delete_stmt === false) {
                    $_SESSION['error'] = "Prepare failed: " . htmlspecialchars($conn->error);
                } else {
                    // Bind parameters and execute deletion
                    $delete_stmt->bind_param("i", $empid);
                    if ($delete_stmt->execute()) {
                        $_SESSION['success'] = "Employee deleted successfully!";
                    } else {
                        $_SESSION['error'] = "Error deleting employee: " . htmlspecialchars($delete_stmt->error);
                    }
                    $delete_stmt->close();
                }
            } else {
                // Employee ID does not exist
                $_SESSION['error'] = "Employee with ID $empid does not exist!";
            }

            $check_stmt->close();
        }
    }

    // Redirect back to empform.php after processing
    header("Location: empform.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Form</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='admindash.php'">Admin Dashboard</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <button onclick="location.href='monthlydeptstat.php'">Monthly Department Status</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>

    </div>

    <div class="container">
        <h2>Employee Management</h2>

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

        <h3>Insert New Employee</h3>
        <form method="post" action="empform.php">
            <input type="hidden" name="action" value="insert">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="departmentid">Department ID:</label>
            <input type="number" id="departmentid" name="departmentid" required>
            <label for="contact_no">Contact No:</label>
            <input type="text" id="contact_no" name="contact_no">
            <label for="stafftype">Staff Type:</label>
            <select id="stafftype" name="stafftype" required>
                <option value="class1">Class 1</option>
                <option value="class2">Class 2</option>
                <option value="class3">Class 3</option>
                <option value="class4">Class 4</option>
                <option value="class5">Class 5</option>
            </select>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Insert Employee</button>
        </form>

        <h3>Delete Employee</h3>
        <form method="post" action="empform.php">
            <input type="hidden" name="action" value="delete">
            <label for="empid">Employee ID:</label>
            <input type="number" id="empid" name="empid" required>
            <button type="submit">Delete Employee</button>
        </form>
    </div>
</body>
</html>
