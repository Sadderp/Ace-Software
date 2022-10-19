<?php
require_once('../db.php');


$sql = "DELETE FROM service WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id); 

$id = $_GET['ID'];
$stmt->execute();

echo json_encode("New blog deleted successfully");

$stmt->close();
?>