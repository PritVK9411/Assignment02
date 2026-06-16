<?php

session_start();

require_once("settings.php");

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

$username = trim($_POST["username"]);
$passwordInput = trim($_POST["password"]);

$sql = "SELECT * FROM users WHERE username = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    // Plain text password version
    if ($passwordInput === $row["password"]) {

        $_SESSION["username"] = $row["username"];

        header("Location: manage.php");
        exit();
    }
}

echo "<h2>Invalid username or password.</h2>";
echo "<a href='login.php'>Try Again</a>";

mysqli_close($conn);

?>