<?php
require_once('../db.php');
session_start();


$id = $_GET['ID'];



$sql = "DELETE FROM service WHERE ID = ? AND type = 'blog'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 1){
    echo json_encode("New blog deleted successfully");
}
else{
    echo json_encode("This is not a blog!");
}

$stmt->close();
?>