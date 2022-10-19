<?php
require_once('../db.php');


$sql = "INSERT INTO service(title,type) VALUES (?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$title,$type); 

$title = $_GET['title'];
$type = $_GET['type'];
$stmt->execute();

echo "New blog created successfully";

$stmt->close();
?>

