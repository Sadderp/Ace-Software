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
$img_id = get_if_set('img_id');

//==============================
//    Requirements
//==============================

if(!$content_id or !$user_id or !$token){
    output_error("Missing input(s) - required: 'content_id', 'user_id' and 'token'");
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!check_end_user($user_id,get_blog_from_content($content_id))) {
    output_error($permission_error);
}

//==============================
//    Check for end user
//==============================

$sql = "SELECT serviceID FROM content WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $content_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    output_error("Content does not exist");
}

$blog_id = mysqli_fetch_assoc($result)['serviceID'];

if(!check_end_user($user_id,$blog_id)) {
    output_error($permission_error);
}

//==============================
//    Delete content and all connected images
//==============================

if($content_id && !$img_id){

    $stmt = $conn->prepare("DELETE FROM content WHERE ID = ?");
    $stmt->bind_param("i",$content_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM img WHERE contentID = ?");
    $stmt->bind_param("i",$content_id);
    $stmt->execute();

    // Output
    $output = ["text"=>"Content was deleted successfully","content_id"=>$content_id];
    output_ok($output);
}

//==============================
//    Delete image from content
//==============================

else if($img_id && $content_id) {
    $stmt = $conn->prepare("DELETE FROM img WHERE ID = ?");
    $stmt->bind_param("i",$img_id);
    $stmt->execute();

    if($stmt->affected_rows == 0){
        output_error("Failed to delete image");
    }

    // Output
    $output = ["text"=>"Image was deleted successfully!","image_id"=>$img_id];
    output_ok($output);
}
?>

