<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

$content = get_if_set('content');
$content_id = get_if_set('content_id');
$blog_id = get_if_set('blog_id');
$img_url = get_if_set('img_url');

$user_id = get_if_set('user_id');
$token = get_if_set('token');

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}

if(!is_numeric($blog_id) || !is_numeric($content_id)){
    output_error("The ID must be numeric!");
}
//==================================================
// content table
//==================================================
if(!$blog_id && !$user_id){
    output_error("The URL is empty!");
}
if($content){
    $stmt = $conn->prepare("SELECT * FROM end_user INNER JOIN service ON end_user.serviceID = service.ID WHERE service.type = 'blog' AND end_user.userID = ? AND end_user.serviceID = ?");
    $stmt->bind_param("ii",$user_id,$blog_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        output_error("You are not connected to a blog!");
    }

    $stmt = $conn->prepare("INSERT INTO content (contents, serviceID, userID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii",$content, $blog_id, $user_id);
    $stmt->execute(); 

    $content_id = $conn->insert_id;
}

//==================================================
// img table
//==================================================
if($img_url && !$content_id){
    output_error("You cannot create this image since it is not connected to any content!");
}

if($img_url && $content_id){
    $stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON service.ID = content.serviceID WHERE service.type = 'blog' AND content.ID=?");
    $stmt->bind_param("i",$content_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        output_error("This content does not exist!");
    }

    $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
    $stmt->bind_param("is", $content_id, $img_url);
    $stmt->execute();
}
output_ok("New content added!");
?>