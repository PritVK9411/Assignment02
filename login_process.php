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
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

$username = trim($_POST["username"] ?? "");
$passwordInput = ($_POST["password"] ?? "");

if (empty($username) || empty($passwordInput)) {

    $_SESSION["login_error"] = 
        "Please enter both username and password.";

        header("Location: login.php");
        exit();
}

$sql = 
    "SELECT id, username, password
    FROM users
    WHERE username = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    $_SESSION["login_error"] = 
        "System error. Please try again later.";

        header("Location: login.php");
        exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    if (
        password_verify(
            $passwordInput,
            $row["password"]
        )
    ) {
        session_regenerate_id(true);

        $_SESSION["user_id"] =
            $row["id"];

        $_SESSION["username"] =
            $row["username"];

        header("Location: manage.php");
        exit();
    }
}
?>