<?php
require_once('../db.php');


$sql = "DELETE FROM service WHERE title = ? AND type = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$title,$type); 

$title = $_GET['title'];
$type = $_GET['type'];
$stmt->execute();

echo "New blog deleted successfully";

$stmt->close();
?>