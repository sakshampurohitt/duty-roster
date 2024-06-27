<?php
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userType = $_POST['userType'];
    $id = $_POST['id'];
    $password = $_POST['password'];

    if ($userType == 'employee') {
        $sql = "SELECT * FROM employee WHERE empid = ? AND password = ?";
    } else {
        $sql = "SELECT * FROM admin WHERE adminid = ? AND password = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_type'] = $userType;
        if ($userType == 'employee') {
            header("Location: empdash.php");
        } else {
            header("Location: admindash.php");
        }
        exit();
    } else {
        $error = "Invalid ID or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h2 id="login-title">Employee Login</h2>
        <form method="post" action="login.php">
            <input type="hidden" name="userType" id="userType" value="employee">
            <label for="id">ID:</label>
            <input type="text" id="id" name="id" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <button id="toggleButton">Login as Admin</button>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>
