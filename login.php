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
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="styles.css" rel="stylesheet">   <!-- ✅ Put it here -->
</head>
<body>

<?php include("includes/header.inc"); ?>
<?php include("includes/nav.inc"); ?>

<main>
    <section class="login-container">

    <h1>Login</h1>

    <?php
    if (isset($_SESSION["login_error"])) {
        echo '<p class="error-message">'
            . htmlspecialchars($SESSION["login_error"])
            . '</p>';

        unset($_SESSION['login_error']);
    }
    ?>

    <form action="login_process.php" method="post">

        <div>
            <label for="username">Username</label>
            <br>
            <input
                type="text"
                id="username"
                name="username"
                required>
        </div>

        <br>

        <div>
            <label for="password">Password</label>
            <br>
            <input
                type="password"
                id="password"
                name="password"
                required>
        </div>

        <br>

        <input type="submit" value="Login">
    </form>
    </section>
</main>

<?php include("includes/footer.inc"); ?>

