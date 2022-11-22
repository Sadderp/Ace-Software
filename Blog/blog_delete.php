<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

//==============================
//    Get variables
//==============================

$blog_id = get_if_set('blog_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

//==============================
//    Requirements
//==============================

if(!$blog_id or !$user_id or !$token) {
    output_error("Missing input(s) - expected: 'blog_id', 'user_id' and 'token'");
}

if(!is_numeric($blog_id)){
    output_error("The ID must be numeric!");
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!verify_service_type($blog_id,'blog')) {
    output_error("This is not a blog!");
}

if(!check_end_user($user_id,$blog_id)) {
    output_error($permission_error);
}

//==============================
//    Delete blog
//==============================

$stmt = $conn->prepare("DELETE FROM service WHERE ID=? AND type='blog'");
$stmt->bind_param("i",$blog_id); 
$stmt->execute();

if($stmt->affected_rows == 0) {
    output_error("Failed to delete blog");
}

$stmt = $conn->prepare("DELETE FROM end_user WHERE serviceID = ? AND userID = ?");
$stmt->bind_param("ii", $blog_id, $user_id); 
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM img INNER JOIN content ON img.contentID = content.ID WHERE serviceID = ?");
$stmt->bind_param("i", $blog_id); 
$stmt->execute();
$result = $stmt->get_result();

$stmt = $conn->prepare("DELETE FROM content WHERE serviceID = ? AND userID = ?");
$stmt->bind_param("ii", $blog_id, $user_id); 
$stmt->execute();

if($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stmt = $conn->prepare("DELETE FROM img WHERE contentID = ?");
        $stmt->bind_param("i", $row['contentID']);
        $stmt->execute();
    }
}

// Output
$output = ["text"=>"Blog was deleted successfully!","id"=>$blog_id];
output_ok($output);         
?>