<?php
$host = 'localhost';
$dbname = 'your_database_name'; // change this
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM jobs");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jobs - A non-profit tech organisation</title>
    <link href="jobs.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Join The Buddiesss Team</h1>
        <p>We are a non-profit organisation building our technology capacity with volunteers.</p>
    </header>

    <nav>
        <ul>
            <a href="index.html">Home</a>
            <a href="jobs.php">Jobs</a>
            <a href="apply.php">Apply</a>
            <a href="about.html">About</a>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Job Listings</h2>

            <?php foreach ($jobs as $job): ?>
                <div class="job-listing">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                    <h5>(Reference number: <?= htmlspecialchars($job['reference_number']) ?>)</h5>

                    <p><strong>Description:</strong></p>
                    <p><?= htmlspecialchars($job['description']) ?></p>

                    <p><strong>Reporting Line:</strong></p>
                    <p><?= htmlspecialchars($job['reporting_line']) ?></p>

                    <p><strong>Salary:</strong></p>
                    <p><?= htmlspecialchars($job['salary']) ?></p>

                    <p><strong>Key Responsibilities:</strong></p>
                    <ul>
                        <?php foreach (explode('|', $job['responsibilities']) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p><strong>Requirements:</strong></p>
                    <ol>
                        <li>Essential requirements:
                            <ul>
                                <?php foreach (explode('|', $job['essential_requirements']) as $item): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li>Preferable requirements:
                            <ul>
                                <?php foreach (explode('|', $job['preferable_requirements']) as $item): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ol>
                </div>
                <hr>
            <?php endforeach; ?>
        </section>

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
</body>
</html>