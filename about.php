<?php
mysqli_report(MYSQLI_REPORT_OFF);

require_once("settings.php");

$db_host = $host ?? "localhost";
$db_user = $user ?? "root";
$db_pass = $password ?? $pwd ?? "";
$db_name = $database ?? $sql_db ?? "";

$conn = false;
$contributions = array();

if ($db_name != "") {
    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if ($conn) {
        $sql = "SELECT member_name, project1_contribution, project2_contribution FROM about";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $contributions[$row["member_name"]] = $row;
            }
        }
    }
}

function clean_output($text) {
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

function show_contribution($contributions, $member_name) {
    if (isset($contributions[$member_name])) {
        echo "<dd><strong>Project 1 Contribution:</strong> " . clean_output($contributions[$member_name]["project1_contribution"]) . "</dd>";
        echo "<dd><strong>Project 2 Contribution:</strong> " . clean_output($contributions[$member_name]["project2_contribution"]) . "</dd>";
    } else {
        echo "<dd><strong>Contribution:</strong> Contribution data is not available yet.</dd>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Buddiesss</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>

<?php include("includes/header.inc"); ?>
<?php include("includes/nav.inc"); ?>

<main>

<h1>The Buddiesss - Team profile</h1>



<h2>Group Information</h2>

<section>

<ul>
    <li>
        Organization Details
        <ul>
            <li>Organization Name: The Buddiesss</li>
            <li>Purpose: To provide volunteering and job application opportunities through an online system.</li>
        </ul>
    </li>

    <li>
        Team Details
        <ul>
            <li>Team Name: The Buddiesss</li>
            <li>Subject: COS10026 Web Technology Project</li>
            <li>Project: Applied Web Project Part 2</li>
        </ul>
    </li>

    <li>
        Class Day and Time
        <ul>
            <li>
                COS10004 Computer Systems
                <ul>
                    <li>Lecture: Monday, 12:00 PM - 2:00 PM</li>
                    <li>Practical: Wednesday, 10:00 AM - 12:00 PM</li>
                </ul>
            </li>

            <li>
                COS10009 Introduction to Programming
                <ul>
                    <li>Lecture: Monday, 4:00 PM - 6:00 PM</li>
                    <li>Practical: Tuesday, 12:00 PM - 2:00 PM</li>
                </ul>
            </li>

            <li>
                COS10026 Web Technology Project
                <ul>
                    <li>Lecture: Tuesday, 2:00 PM - 4:00 PM</li>
                    <li>Practical: Wednesday, 8:00 AM - 10:00 AM</li>
                </ul>
            </li>

            <li>
                MAT2208 Mathematics for Computing
                <ul>
                    <li>Lecture: Tuesday, 8:00 AM - 10:00 AM</li>
                    <li>Lecture & Tutorial: Friday, 8:00 AM - 10:00 AM</li>
                </ul>
            </li>

            <li>
                TNE10006 Networks and Switching
                <ul>
                    <li>Practical: Thursday, 3:00 PM - 6:00 PM</li>
                    <li>Lecture: Friday, 11:00 AM - 1:00 PM</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

</section>

<h2>Team Members</h2>

<section>
    <dl>
        <dt>Prit Vinesh Kumar</dt>
        <?php show_contribution($contributions, "Prit Vinesh Kumar"); ?>
        <dd>Quote: "If you're smart, why aren't you rich?"</dd>
        <dd>Favourite language: English</dd>
        <dd>Translation: "Hello, my name is Prit." = "Bonjour, je m'appelle Prit."</dd>
    </dl>
</section>

<section>
    <dl>
        <dt>Thanish Thevan</dt>
        <?php show_contribution($contributions, "Thanish Thevan"); ?>
        <dd>Quote: "Your intentions define your life."</dd>
        <dd>Favourite language: Tamil</dd>
        <dd>Translation: "En peru Thanish" = "My name is Thanish"</dd>
    </dl>
</section>

<section>
    <dl>
        <dt>Muhammad Ishaq Shoukat</dt>
        <?php show_contribution($contributions, "Muhammad Ishaq Shoukat"); ?>
        <dd>Quote: "The devil can't make hell look beautiful, so he makes the path to it beautiful."</dd>
        <dd>Favourite language: Arabic</dd>
        <dd>Translation: "الوقت كالسيف إن لم تقطعه قطعك" = "Time is like a sword. If you don't cut it, it cuts you."</dd>
    </dl>
</section>

<section>
    <dl>
        <dt>Maithini Sundaram</dt>
        <?php show_contribution($contributions, "Maithini Sundaram"); ?>
        <dd>Quote: "Growth is growth, no matter how small."</dd>
        <dd>Favourite language: French</dd>
        <dd>Translation: "Hello" = "Bonjour"</dd>
    </dl>
</section>

<h2>Group Photo</h2>

<figure>
    <img src="Images/Group_Photo.jpeg" alt="Our Team" width="300">
    <figcaption>Our Team "The Buddiesss"</figcaption>
</figure>

<h2>Fun Facts</h2>

<table>
    <caption>Fun Facts About Our Team</caption>

    <thead>
        <tr>
            <th>Name</th>
            <th>Dream Job</th>
            <th>Snack</th>
            <th>Hometown</th>
            <th>Weird Habit</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Prit Vinesh Kumar</td>
            <td>Pilot</td>
            <td>Coffee and Biscuits</td>
            <td>Melaka</td>
            <td>I bite my nails</td>
        </tr>

        <tr>
            <td>Thanish Thevan</td>
            <td>Engineer</td>
            <td>Masala Tea</td>
            <td>Petaling Jaya</td>
            <td>I sometimes talk to myself</td>
        </tr>

        <tr>
            <td>Muhammad Ishaq Shoukat</td>
            <td>GPT wrapper founder</td>
            <td>Raw Honey</td>
            <td>Jeddah, Saudi Arabia</td>
            <td>I overthink a lot</td>
        </tr>

        <tr>
            <td>Maithini Sundaram</td>
            <td>Digital Forensic Analyst</td>
            <td>Coffee</td>
            <td>Klang</td>
            <td>Smelling books</td>
        </tr>
    </tbody>
</table>

</main>

<?php include("includes/footer.inc"); ?>

</body>
</html>

<?php
if ($conn) {
    mysqli_close($conn);
}
?>