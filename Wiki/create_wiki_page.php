<?php
require_once("../db.php");

if(isset($_GET['wiki_id'])) {
    $wiki_id = $_GET['wiki_id'];
}

if(isset($_GET['page_title'])) {
    $page_title = $_GET['page_title'];
}

$sql = "INSERT INTO wiki_page (serviceID, title) VALUES (?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is",$wiki_id,$page_title);
$stmt->execute();

if($stmt->affected_rows === 1) {
    echo json_encode("success");
} else {
    echo json_encode("error");
}
?>