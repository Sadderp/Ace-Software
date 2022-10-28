<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');
$version = "0.1.1";
$ok = "OK";
$error = "Error";


$contentID = get_if_set('contentID');
$imgID = get_if_set('imgID');
$user_id = get_if_set('user_id');
$token = get_if_set('token');

if(!$blogID && !$user_id && !$serviceID && !$token){
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}

$stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID WHERE service.type = 'blog' AND content.userID=? AND content.ID=?");
$stmt->bind_param("ii", $user_id, $contentID);
$stmt->execute();
$result = $stmt->get_result();


if($result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM content WHERE ID = ?");
    $stmt->bind_param("i",$contentID);
    $stmt->execute();

    if($stmt->affected_rows == 1){
        output_ok("Content was deleted successfully!");
    }
    else{
        output_error("This content is not in the right blog!");
    }
}
else{
    output_error("The URL is empty!");
}
?>

