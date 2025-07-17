<?php
$servername = "localhost";
$username = "root";       // your MySQL username
$password = "";           // your MySQL password
$database = "users";    // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>