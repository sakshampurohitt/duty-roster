<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: login.php");
    exit();
}

// Fetch employee information from the session
$empid = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="navbar">
        <h2>Employee Dashboard</h2>
    </div>

    <div class="container">
        <button onclick="location.href='check_schedule.php'">Check Schedule</button>
        <button onclick="location.href='checkattend.php?empid=<?php echo $empid; ?>'">Check Attendance</button>
        <button onclick="location.href='logout.php'">Logout</button>
    </div>
</body>
</html>
