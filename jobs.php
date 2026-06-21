<?php
require 'settings.php';

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}

$sql = "SELECT * FROM jobs";
$result = mysqli_query($conn, $sql);

$jobs = [];

if ($result) {
    $jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Jobs - A non-profit tech organisation</title>
        <link href="styles.css" rel="stylesheet">
    </head>

    <body>
        <?php include 'header.inc'; ?>
        <?php include 'nav.inc'; ?>
        

        <main>
            <section>
                <h2>Job Listings</h2>
            </section>

            <?php foreach ($jobs as $job): ?>
            <section>
                <h3><?= htmlspecialchars($job['title']) ?></h3>
                <h5>(Reference number: <?= htmlspecialchars($job['reference_number']) ?>)</h5>
                <p><strong>Description:</strong></p>
                <p><?= htmlspecialchars($job['description']) ?></p>
                <p><strong>Reporting Line:</strong></p>
                <p><?= htmlspecialchars($job['reporting_line']) ?></p>
                <p><strong>Salary:</strong></p>
                <p><?= htmlspecialchars($job['salary']) ?></p>
                <p><strong>Key responsibilities:</strong></p>
                <ul>
                    <?php foreach (explode('|', $job['responsibilities']) as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Requirements:</strong></p>
                <ol>
                    <li>Essential requirements:</li>
                    <ul>
                        <?php foreach (explode('|', $job['essential_requirements']) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <li>Preferable requirements:</li>
                    <ul>
                        <?php foreach (explode('|', $job['preferable_requirements']) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </ol>
            </section>
            <?php endforeach; ?>

            <aside>
                <p><strong>Why volunteer with us?</strong></p>
                <ul>
                    <li>Gain real-world skills with us</li>
                    <li>Develop technical and soft skills</li>
                    <li>Work on meaningful community projects</li>
                    <li>Collaborate with a diverse team</li>
                </ul>
            </aside>

        </main>

        <?php include 'footer.inc'; ?>
    </body>
</html>