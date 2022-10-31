<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
$version = "0.1.1";
$ok = "OK";
$error = "Error";



//==================================================
// Edit the text you've got in a blog post
//==================================================
$contentID = get_if_set('contentID');
$content = get_if_set('contents');
$user_id = get_if_set('user_id');
$token = get_if_set('token');
$imgID = get_if_set('imgID');
$img_url = get_if_set('img_url');

if(!$contentID && !$contents && !$user_id && !$token){
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("Access denied");
}

$stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID WHERE service.type = 'blog' AND content.userID=? AND content.ID=?");
$stmt->bind_param("ii", $user_id, $contentID);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 1) {
    $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
    $stmt->bind_param("si", $content, $contentID);
    $stmt->execute();

    output_ok("Content was edited successfully");
}
else{
    output_error("This content is not in the right blog or you signed in as the wrong person");
}



//==================================================
// Edit an image in a blog post
//==================================================
if($imgID && $img_url){
    if($result->num_rows == 1) {
        $stmt = $conn->prepare("UPDATE img SET img_url = ? WHERE ID = ?");
        $stmt->bind_param("ss",$img_url,$imgID); 
        $stmt->execute();

        output_ok("Image was edited successfully");
    }else{
        output_error("You cannot edit this content since it is not your blog!");
    }
}
?>