<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');


$blogID = get_if_set('blogID');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

if(!$blogID && !$user_id && !$token) {
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}

$stmt = $conn->prepare("SELECT * FROM end_user WHERE userID=? AND serviceID=?");
$stmt->bind_param("ii", $user_id, $blogID); 
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 0) {
    $stmt = $conn->prepare("DELETE FROM service WHERE ID=? AND type='blog'");
    $stmt->bind_param("i",$blogID); 
    $stmt->execute();

    if($stmt->affected_rows == 1){
        $stmt = $conn->prepare("DELETE FROM end_user WHERE serviceID = ? AND userID = ?");
        $stmt->bind_param("ii", $blogID, $user_id); 
        $stmt->execute();
    
        $stmt = $conn->prepare("DELETE FROM content WHERE serviceID = ? AND userID = ?");
        $stmt->bind_param("ii", $blogID, $user_id); 
        $stmt->execute();
    
        output_ok("Blog was deleted successfully!");
    }
    else{
        output_error("This is not a blog!");
    }
}
else {
    output_error("This is not your blog!");
}
              
?>