<?php
require_once('../db.php');



//==================================================
// blog_post tabell
//==================================================
    if (!empty($_GET['title']) && (!empty($_GET['serviceID'])) && (!empty($_GET['userID'])))  {
        $title = $_GET['title'];
        $serviceID = $_GET['serviceID'];
        $userID = $_GET['userID'];

        $stmt = $conn->prepare("INSERT INTO blog_post (serviceID, userID, title) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $serviceID, $userID, $title);
        $result = $stmt->get_result();
        $stmt->execute();
        echo $result;
        
        $post = array("serviceID"=>$serviceID, "userID"=>$userID, "title"=>$title);
        echo json_encode($post);
    }



//==================================================
// content tabell
//==================================================
    if (!empty($_GET['postID']) && (!empty($_GET['contents'])) && (!empty($_GET['HTMLelement'])) && (!empty($_GET['imgID'])))  {
        $postID = $_GET['postID'];
        $HTMLelement = $_GET['HTMLelement'];
        $contents = $_GET['contents'];
        $imgID = $_GET['imgID'];

        $stmt = $conn->prepare("INSERT INTO content (postID, HTML_element, contents, imgID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $postID, $HTMLelement, $contents, $imgID);
        $result = $stmt->get_result();
        $stmt->execute();
        echo $result;
        
        $post = array("postID"=>$postID, "HTMLelement"=>$HTMLelement, "contents"=>$contents, "imgID"=>$imgID);
        echo json_encode($post);
    }



//==================================================
// img tabell
//==================================================
    if (!empty($_GET['contentID']) && (!empty($_GET['img_url']))) {
        $contentID = $_GET['contentID'];
        $img_url = $_GET['img_url'];

        $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
        $stmt->bind_param("is", $contentID, $img_url);
        $result = $stmt->get_result();
        $stmt->execute();
        echo $result;

        $post = array("contentID"=>$contentID, "img_url"=>$img_url);
        echo json_encode($post);
    }

?>