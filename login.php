<?php
session_start();

if (isset($_SESSION["username"])) {
    header("Location: manage.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>HR Login</title>
</head>
<body>

<h1>HR Manager Login</h1>

<form action="login_process.php" method="post">

    <p>
        <label>Username:</label>
        <input type="text" name="username" required>
    </p>

    <p>
        <label>Password:</label>
        <input type="password" name="password" required>
    </p>

    <p>
        <input type="submit" value="Login">
    </p>

</form>

</body>
</html>