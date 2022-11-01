<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";


$title = get_if_set('title');
$blogID = get_if_set('blogID');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

if(!$blogID && !$title && !$user_id && !$token){
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("Access denied");
}

$stmt = $conn->prepare("SELECT * FROM end_user WHERE userID=? AND serviceID=?");
$stmt->bind_param("ii", $user_id, $blogID); 
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE service SET title = ? WHERE ID = ? AND type = 'blog'");
    $stmt->bind_param("si",$title,$blogID); 
    $stmt->execute();

    if($stmt->affected_rows == 1){
        output_ok("Blog edited successfully");
    }
    else{
        output_error("This is not a blog!");
    }
}
else{
    output_error("Blog was not found!");
}

?>