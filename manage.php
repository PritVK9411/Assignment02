<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once("settings.php");

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";
$result = false;

function clean_output($text) {
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

function display_eoi_table($result) {
    if (!$result || mysqli_num_rows($result) == 0) {
        echo "<p>No EOI records found.</p>";
        return;
    }

    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>EOI Number</th>";
    echo "<th>Job Reference</th>";
    echo "<th>First Name</th>";
    echo "<th>Last Name</th>";
    echo "<th>Email</th>";
    echo "<th>Phone</th>";
    echo "<th>Status</th>";
    echo "<th>Submitted At</th>";
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . clean_output($row["EOInumber"]) . "</td>";
        echo "<td>" . clean_output($row["job_reference"]) . "</td>";
        echo "<td>" . clean_output($row["first_name"]) . "</td>";
        echo "<td>" . clean_output($row["last_name"]) . "</td>";
        echo "<td>" . clean_output($row["email"]) . "</td>";
        echo "<td>" . clean_output($row["phone"]) . "</td>";
        echo "<td>" . clean_output($row["status"]) . "</td>";
        echo "<td>" . clean_output($row["submitted_at"]) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["list_all"])) {
        $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at FROM eoi";
        $result = mysqli_query($conn, $sql);
    }

    if (isset($_POST["search_job"])) {
        $job_ref = trim($_POST["job_ref"]);

        $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at 
                FROM eoi 
                WHERE job_reference = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $job_ref);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (isset($_POST["search_name"])) {
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);

        if ($first_name != "" && $last_name != "") {
            $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at 
                    FROM eoi 
                    WHERE first_name LIKE ? AND last_name LIKE ?";

            $first_search = "%" . $first_name . "%";
            $last_search = "%" . $last_name . "%";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $first_search, $last_search);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

        } elseif ($first_name != "") {
            $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at 
                    FROM eoi 
                    WHERE first_name LIKE ?";

            $first_search = "%" . $first_name . "%";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $first_search);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

        } elseif ($last_name != "") {
            $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at 
                    FROM eoi 
                    WHERE last_name LIKE ?";

            $last_search = "%" . $last_name . "%";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $last_search);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

        } else {
            $message = "Please enter first name, last name, or both.";
        }
    }

    if (isset($_POST["delete_eoi"])) {
        $delete_job_ref = trim($_POST["delete_job_ref"]);

        $sql = "DELETE FROM eoi WHERE job_reference = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $delete_job_ref);
        mysqli_stmt_execute($stmt);

        $message = "EOI records for job reference " . clean_output($delete_job_ref) . " have been deleted.";

        $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at FROM eoi";
        $result = mysqli_query($conn, $sql);
    }

    if (isset($_POST["update_status"])) {
        $eoi_number = trim($_POST["eoi_number"]);
        $status = trim($_POST["status"]);

        if ($status == "New" || $status == "Current" || $status == "Final") {
            $sql = "UPDATE eoi SET status = ? WHERE EOInumber = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $status, $eoi_number);
            mysqli_stmt_execute($stmt);

            $message = "EOI status has been updated.";

            $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at FROM eoi";
            $result = mysqli_query($conn, $sql);
        } else {
            $message = "Invalid status selected.";
        }
    }

    if (isset($_POST["sort_results"])) {
        $sort_field = $_POST["sort_field"];

        $allowed_fields = array(
            "EOInumber",
            "job_reference",
            "first_name",
            "last_name",
            "status",
            "submitted_at"
        );

        if (!in_array($sort_field, $allowed_fields)) {
            $sort_field = "EOInumber";
        }

        $sql = "SELECT EOInumber, job_reference, first_name, last_name, email, phone, status, submitted_at 
                FROM eoi 
                ORDER BY $sort_field ASC";

        $result = mysqli_query($conn, $sql);
    }
}
?>

<?php include("includes/header.inc"); ?>
<?php include("includes/nav.inc"); ?>

<main>
    <h1>HR Management Page</h1>

    <p>This page allows the HR manager to view, search, delete, update, and sort EOI records.</p>

    <?php
    if ($message != "") {
        echo "<p><strong>" . clean_output($message) . "</strong></p>";
    }
    ?>

    <section>
        <h2>List All EOIs</h2>
        <form method="post" action="manage.php">
            <button type="submit" name="list_all">View All EOIs</button>
        </form>
    </section>

    <section>
        <h2>Search by Job Reference</h2>
        <form method="post" action="manage.php">
            <label for="job_ref">Job Reference:</label>
            <input type="text" id="job_ref" name="job_ref" maxlength="5" required>

            <button type="submit" name="search_job">Search</button>
        </form>
    </section>

    <section>
        <h2>Search by Applicant Name</h2>
        <form method="post" action="manage.php">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name">

            <button type="submit" name="search_name">Search</button>
        </form>
    </section>

    <section>
        <h2>Delete EOIs by Job Reference</h2>
        <form method="post" action="manage.php">
            <label for="delete_job_ref">Job Reference:</label>
            <input type="text" id="delete_job_ref" name="delete_job_ref" maxlength="5" required>

            <button type="submit" name="delete_eoi">Delete</button>
        </form>
    </section>

    <section>
        <h2>Change EOI Status</h2>
        <form method="post" action="manage.php">
            <label for="eoi_number">EOI Number:</label>
            <input type="number" id="eoi_number" name="eoi_number" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="New">New</option>
                <option value="Current">Current</option>
                <option value="Final">Final</option>
            </select>

            <button type="submit" name="update_status">Update Status</button>
        </form>
    </section>

    <section>
        <h2>Sort Results</h2>
        <form method="post" action="manage.php">
            <label for="sort_field">Sort By:</label>
            <select id="sort_field" name="sort_field">
                <option value="EOInumber">EOI Number</option>
                <option value="job_reference">Job Reference</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="status">Status</option>
                <option value="submitted_at">Submitted Date</option>
            </select>

            <button type="submit" name="sort_results">Sort</button>
        </form>
    </section>

    <section>
        <h2>EOI Results</h2>

        <?php
        if ($result) {
            display_eoi_table($result);
        } else {
            echo "<p>Select an option above to view EOI records.</p>";
        }
        ?>
    </section>

</main>

<?php include("includes/footer.inc"); ?>

<?php
mysqli_close($conn);
?>