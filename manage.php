<?php
session_start();

// Login protection will be updated later after login person confirms session name
// Example:
// if (!isset($_SESSION["username"])) {
//     header("Location: login.php");
//     exit();
// }
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage EOIs</title>
</head>
<body>

<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="jobs.php">Jobs</a>
        <a href="apply.php">Apply</a>
        <a href="about.php">About</a>
        <a href="manage.php">Manage</a>
    </nav>
</header>

<main>
    <h1>HR Management Page</h1>

    <h2>List All EOIs</h2>
    <form method="post">
        <button type="submit" name="list_all">View All EOIs</button>
    </form>

    <h2>Search by Job Reference</h2>
    <form method="post">
        <label>Job Reference:</label>
        <input type="text" name="job_ref">
        <button type="submit" name="search_job">Search</button>
    </form>

    <h2>Search by Applicant Name</h2>
    <form method="post">
        <label>First Name:</label>
        <input type="text" name="first_name">

        <label>Last Name:</label>
        <input type="text" name="last_name">

        <button type="submit" name="search_name">Search</button>
    </form>

    <h2>Delete EOIs by Job Reference</h2>
    <form method="post">
        <label>Job Reference:</label>
        <input type="text" name="delete_job_ref">
        <button type="submit" name="delete_eoi">Delete</button>
    </form>

    <h2>Change EOI Status</h2>
    <form method="post">
        <label>EOI Number:</label>
        <input type="number" name="eoi_number">

        <label>Status:</label>
        <select name="status">
            <option value="New">New</option>
            <option value="Current">Current</option>
            <option value="Final">Final</option>
        </select>

        <button type="submit" name="update_status">Update Status</button>
    </form>

    <h2>Sort Results</h2>
    <form method="post">
        <label>Sort By:</label>
        <select name="sort_field">
            <option value="EOInumber">EOI Number</option>
            <option value="job_ref">Job Reference</option>
            <option value="first_name">First Name</option>
            <option value="last_name">Last Name</option>
            <option value="status">Status</option>
        </select>

        <button type="submit" name="sort_results">Sort</button>
    </form>

</main>

<footer>
    <p>Email: <a href="mailto:j26046056@student.newinti.edu.my">info@thebuddiess.com</a></p>
</footer>

</body>
</html>