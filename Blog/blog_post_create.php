<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

//==============================
//    Get variables
//==============================

// Required
$blog_id = get_if_set('blog_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

// Optional
$content = get_if_set('content');
$content_id = get_if_set('content_id');
$img_url = get_if_set('img_url');

//==============================
//    Requirements
//==============================

if(!$blog_id or !$user_id or !$token){
    output_error("Missing input(s) - required: 'blog_id', 'user_id' and 'token'");
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!check_end_user($user_id,$blog_id)) {
    output_error($permission_error);
}

//==================================================
//      Add content
//==================================================

if($content){

    $stmt = $conn->prepare("INSERT INTO content (contents, serviceID, userID) VALUES (?, ?, ?)");
    $stmt->bind_param("sii",$content, $blog_id, $user_id);
    $stmt->execute(); 

    $content_id = $conn->insert_id;
}

//==================================================
//      Add image
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

// Output
$output = ["text"=>"Content added successfully","id"=>$content_id];
output_ok($output);
?>