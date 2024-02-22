<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "e_commerce";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
