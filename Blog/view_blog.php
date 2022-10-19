<?php
require_once('../db.php');
$sql = "SELECT * FROM service WHERE type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$type); 

$type = $_GET['type'];
$stmt->execute();

$array = $stmt->get_result();

if ($array->num_rows > 0){
    while($row = $array->fetch_assoc()){
        $blogs = array("ID: "=>$row["ID"],"Title: "=>$row["title"],"Type: "=>$row["type"]);
        echo json_encode($blogs);
    }
}else{
    echo json_encode("0 results");
}

$stmt->close();
?>