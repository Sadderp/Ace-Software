<?php
require_once('../db.php');


$sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$title); 

$title = $_GET['title'];
$stmt->execute();

echo json_encode("New blog created successfully");

$stmt->close();
?>

