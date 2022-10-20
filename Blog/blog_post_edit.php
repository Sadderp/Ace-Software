<?php
require_once('../db.php');
session_start();


//==================================================
// 
//==================================================
if (!empty($_GET['contents']) && !empty($_GET['conID'])){
    $sql = "UPDATE content SET HTML_element = ?, contents = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss",$html,$content,$id); 

    $html = $_GET['HTML_element'];
    $content = $_GET['contents'];
    $id = $_GET['conID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}



//==================================================
// 
//==================================================
if (!empty($_GET['Title']) && !empty($_GET['servID'])){
    $sql = "UPDATE service SET title = ? WHERE ID = ? AND type = 'blog'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$title,$id); 

    $title = $_GET['Title'];
    $id = $_GET['servID'];
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode("Old blog edited successfully");
}



//==================================================
// 
//==================================================
if (!empty($_GET['img']) && !empty($_GET['imgID'])){
    $sql = "UPDATE img SET img_url = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$img,$id); 

    $img = $_GET['img'];
    $id = $_GET['imgID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}
?>