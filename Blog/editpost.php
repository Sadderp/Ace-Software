<?php
require_once('../db.php');

if (!empty($_GET('HTML_element')) && !empty($_GET('contents')) && !empty($_GET('ID'))){
    $sql = "UPDATE content SET HTML_element = ? AND contents = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss",$html,$content,$id); 

    $html = $_GET['HTML_element'];
    $content = $_GET['contents'];
    $id = $_GET['ID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}



if (!empty($_GET('title'))){
    $sql = "UPDATE blog_post SET title = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$title,$id); 

    $title = $_GET['title'];
    $id = $_GET['ID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}



if (!empty($_GET('Title'))){
    $sql = "UPDATE service SET title = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$title,$id); 

    $title = $_GET['Title'];
    $id = $_GET['ID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}



if (!empty($_GET('img'))){
    $sql = "UPDATE img SET img_url = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$title,$id); 

    $img = $_GET['img'];
    $id = $_GET['ID'];
    $stmt->execute();

    echo json_encode("Old blog edited successfully");
}
?>