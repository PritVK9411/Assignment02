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
    
</main>


<footer>
    <?php include("includes/footer.inc"); ?>
</footer>

    </body>
</html>