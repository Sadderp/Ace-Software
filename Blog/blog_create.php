<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

//==============================
//    Get variables
//==============================

$title = get_if_set('title');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

//==============================
//    Requirements
//==============================

if(!$title or !$user_id or !$token){
    output_error("Missing input(s) - expected: 'title', 'user_id' and 'token'");
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

//==============================
//      Checks if you all ready have a blog
//==============================

$sql = "SELECT ID from end_user WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    output_error("You already have a blog!");
}

//==================================================
//      Makes a blog if you do not have one already
//==================================================

$stmt = $conn->prepare("INSERT INTO service(title,type) VALUES (?,'blog')");
$stmt->bind_param("s",$title); 
$stmt->execute();

if($stmt->affected_rows == 0) {
    output_error("Failed to create blog");
}

$last_id = $conn->insert_id; 

$stmt = $conn->prepare("INSERT INTO end_user(userID,serviceID) VALUES (?,?)");
$stmt->bind_param("ii",$user_id,$last_id); 
$stmt->execute();

// Output
$output = ["text"=>"Blog was created successfully","id"=>$last_id];
output_ok($output);
?>

