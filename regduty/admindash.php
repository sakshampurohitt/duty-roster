<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <button onclick="location.href='empform.php'">Employee Management</button>
        <button onclick="location.href='deptform.php'">Department Management</button>
        <button onclick="location.href='summaryduty.php'">Summary of Duty Register</button>
        <button onclick="location.href='monthlyempstat.php'">Monthly Employee Status</button>
        <button onclick="location.href='monthlydeptstat.php'">Monthly Department Status</button>
        <button onclick="location.href='add_schedule.php'">Add Schedule</button>
        <a href="logout.php" id="logout_link">Logout</a>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>
