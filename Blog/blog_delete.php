<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";

$blogID = get_if_set('blogID');
$user_id = get_if_set('userID');
$token = get_if_set('token');

if(!$blogID && !$user_id && !$token) {
    error_message("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    error_message("access denied");
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
    
        $json_array = ["Version: "=>$version,"Status: "=>$ok,"Data: "=>'Blog was deleted successfully!'];
        echo json_encode($json_array);
        die();
    }
    else{
        error_message("This is not a blog!");
    }
}
else {
    error_message("This is not your blog!");
}


                
?>