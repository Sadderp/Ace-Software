<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

$blog_id = get_if_set('blog_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

if(!$blog_id && !$user_id && !$token) {
    output_error("The URL is empty!");
}

if(!is_numeric($blog_id)){
    output_error("The ID must be numeric!");
}

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}

$stmt = $conn->prepare("SELECT * FROM end_user WHERE userID=? AND serviceID=?");
$stmt->bind_param("ii", $user_id, $blog_id); 
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 0) {
    $stmt = $conn->prepare("DELETE FROM service WHERE ID=? AND type='blog'");
    $stmt->bind_param("i",$blog_id); 
    $stmt->execute();

    if($stmt->affected_rows == 1){
        $stmt = $conn->prepare("DELETE FROM end_user WHERE serviceID = ? AND userID = ?");
        $stmt->bind_param("ii", $blog_id, $user_id); 
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM img INNER JOIN content ON img.contentID = content.ID WHERE serviceID = ?");
        $stmt->bind_param("i", $blog_id); 
        $stmt->execute();
        $result = $stmt->get_result();

        $stmt = $conn->prepare("DELETE FROM content WHERE serviceID = ?");
        $stmt->bind_param("i", $blog_id); 
        $stmt->execute();

        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stmt = $conn->prepare("DELETE FROM img WHERE contentID = ?");
                $stmt->bind_param("i", $row['contentID']);
                $stmt->execute();
            }
        }
    
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