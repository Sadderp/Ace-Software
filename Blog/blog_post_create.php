<?php
require_once('../db.php');

require_once('../verify_token.php');
require_once('../utility.php');


$contents = get_if_set('contents');
$service_id = get_if_set('service_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');
$img_url = get_if_set('img_url');


if(!verify_token($user_id,$token)) {
    output_error("Access denied");
}

//==================================================
// content table
//==================================================
if(!$contents && !$service_id && !$user_id){
    output_error("The URL is empty!");
}

$stmt = $conn->prepare("SELECT * FROM end_user INNER JOIN service ON end_user.serviceID = service.ID WHERE service.type = 'blog' AND end_user.userID = ? AND end_user.serviceID = ?");
$stmt->bind_param("ii",$user_id,$service_id); 
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    output_error("You are not connected to a blog!");
}

$stmt = $conn->prepare("INSERT INTO content (contents, serviceID, userID) VALUES (?, ?, ?)");
$stmt->bind_param("sii",$contents, $service_id, $user_id);
$stmt->execute();

$content_id = $conn->insert_id;

if($img_url && !$contents){
    output_error("You cannot create a new image without a post!"); 
}
else{
    $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
    $stmt->bind_param("is", $content_id, $img_url);
    $stmt->execute();
}
output_ok("New content added!");
?>