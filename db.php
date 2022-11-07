<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the_provider_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Error - Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  die("Error: 'POST'-requests are not supported for our services.");
}
?>