<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

$title = get_if_set('title');
$user_id = get_if_set('user_id');
$token = get_if_set('token');


if(!$title && !$user_id && !$token){
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}



//==================================================
// Checks if you all ready have a blog
//==================================================
$stmt = $conn->prepare("SELECT user.ID, user.username, user.token, end_user.userID, end_user.serviceID, service.ID, service.type FROM user INNER JOIN end_user ON user.ID = end_user.userID 
                                                                                                                                           INNER JOIN service ON end_user.serviceID = service.ID WHERE BINARY user.ID = ? AND token = ? AND type = 'blog'");
$stmt->bind_param("is",$user_id,$token); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    output_error("You already have a blog!");
}

//==================================================
// Makes a blog if you do not have one already
//==================================================
$stmt = $conn->prepare("INSERT INTO service(title,type) VALUES (?,'blog')");
$stmt->bind_param("s",$title); 
$stmt->execute();

$lastID = $conn->insert_id; 

$stmt = $conn->prepare("INSERT INTO end_user(userID,serviceID) VALUES (?,?)");
$stmt->bind_param("ii",$user_id,$lastID); 
$stmt->execute();

output_ok("Blog was created successfully");
?>

