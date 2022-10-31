<?php
require_once('../db.php');

require_once('../verify_token.php');
require_once('../utility.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";

$contents = get_if_set('contents');
$serviceID = get_if_set('serviceID');
$user_id = get_if_set('user_id');
$token = get_if_set('token');
$img_url = get_if_set('img_url');

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}



//==================================================
// content table
//==================================================
if(!$contents && !$serviceID && !$user_id){
    output_error("The URL is empty!");
}

$stmt = $conn->prepare("SELECT * FROM end_user INNER JOIN service ON end_user.serviceID = service.ID WHERE service.type = 'blog' AND end_user.userID = ? AND end_user.serviceID = ?");
$stmt->bind_param("ii",$user_id,$serviceID); 
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    output_error("You are not connected to a blog!");
}

$stmt = $conn->prepare("INSERT INTO content (contents, serviceID, userID) VALUES (?, ?, ?)");
$stmt->bind_param("sii",$contents, $serviceID, $user_id);
$stmt->execute();

$contentID = $conn->insert_id;

output_ok("New content added!");



//==================================================
// img table
//==================================================
if($contents && $serviceID && $user_id && $img_url){
    if($result->num_rows == 1) {
        $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
        $stmt->bind_param("is", $contentID, $img_url);
        $stmt->execute();
        
        output_ok("contentID:$contentID img url:$img_url");
    }else{
        output_error("You cannot edit this content since it is not your blog!");
    }
}
?>