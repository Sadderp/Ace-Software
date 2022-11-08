<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
require_once('blog_utility.php');

//==============================
//    Get variables
//==============================

// Required
$content_id = get_if_set('content_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

// Optional
$content = get_if_set('content');
$img_id = get_if_set('img_id');
$img_url = get_if_set('img_url');

// Blog ID
$blog_id = get_blog_from_content($content_id);

//==============================
//    Requirements
//==============================

if(!$content_id or !$user_id or !$token){
    output_error("Missing input(s) - required: 'content_id', 'user_id' and 'token'");
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!$blog_id) {
    output_error("Invalid content");
}

if(!check_end_user($user_id,$blog_id)) {
    output_error($permission_error);
}

if(!$content and !$img_id and !$img_url){
    output_error("You have logged in, but you have not requested any edits!");
}

//==============================
//    Edit post content
//==============================

if($content) {
    $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
    $stmt->bind_param("si", $content, $content_id);
    $stmt->execute();
    $output = ["text"=>"Content was edited successfully","content_id"=>$content_id];
}

//==================================================
//      Edit an image in a blog post
//==================================================
if($img_id && $img_url){

    $stmt = $conn->prepare("UPDATE img SET img_url = ? WHERE ID = ?");
    $stmt->bind_param("ss",$img_url,$img_id); 
    $stmt->execute();

    // Output
    $output = ["text"=>"Content was edited successfully","content_id"=>$content_id,"image_id"=>$img_id];
}

output_ok($output);

?>