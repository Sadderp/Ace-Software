<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

//==============================
//    Get variables
//==============================

$title = get_if_set('title');
$blog_id = get_if_set('blog_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

//==============================
//    Requirements
//==============================

if(!$blog_id or !$title or !$user_id or !$token){
    output_error("Missing input(s) - expected: 'blog_id', 'title', 'user_id' and 'token'");
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
//    Edit blog
//==============================

$stmt = $conn->prepare("UPDATE service SET title = ? WHERE ID = ?");
$stmt->bind_param("si",$title,$blog_id); 
$stmt->execute();

if($stmt->affected_rows == 0){
    output_error("Failed to edit blog");
}

// Output
$output = ["text"=>"Blog edited successfully","id"=>$blog_id];
output_ok($output);
?>