<?php
require 'settings.php';
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

    <nav>
        <ul>
           <?php include("includes/nav.inc"); ?>
        </ul>   
    </nav>

    <main>
    <section>
        <h2>About Our Organisation</h2>
        <p>The Buddiesss is a non-profit organisation which links proficient volunteers whose projects enhance communities by means of technology.</p>
    </section>

    <section>
        <h2>Our Mission</h2>
        <p>We will enhance digital infrastructure of charities and social programs through establishing volunteer technology teams.</p>
    </section>

    <figure>
        <img src="Images/Volunteer.jpeg" alt="Volunteers working together">
        <figcaption>Our volunteers building impactful technology</figcaption>
    </figure>


    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM about ORDER BY id");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($members)) {
            echo '<section>';
            echo '<h2>Team Contributions</h2>';
            echo '<ul>';
            foreach ($members as $member) {
                echo '<li><strong>' . htmlspecialchars($member['member_name']) . '</strong><br>';
                echo 'Project 1: ' . htmlspecialchars($member['project1_details']) . '<br>';
                echo 'Project 2: ' . htmlspecialchars($member['project2_details']);
                echo '</li>';
            }
            echo '</ul>';
            echo '</section>';
        } else {
            echo '<section><p><em>No team contributions have been added yet.</em></p></section>';
        }
    } catch (PDOException $e) {
        
        echo '<section><p><em>Contributions table not set up yet. Please create the "about" table in phpMyAdmin.</em></p></section>';
    }
    ?>
</main>


<footer>
    <?php include("includes/footer.inc"); ?>
</footer>

    </body>
</html>