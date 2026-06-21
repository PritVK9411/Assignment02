<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "thebuddiesss"; 

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully!";

// Query the 'users' table (or 'jobs', 'about', 'oei')
$result = $mysqli->query("SELECT * FROM users");

while ($row = $result->fetch_assoc()) {

    echo $row['name'] . "<br>";
}

$mysqli->close();

?>