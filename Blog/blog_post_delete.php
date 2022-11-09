<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');


$content_id = get_if_set('content_id');
$user_id = get_if_set('user_id');
$token = get_if_set('token');
$img_id = get_if_set('img_id');

if(!$user_id && !$token){
    output_error("The URL is empty!");
}

if(!is_numeric($img_id) || !is_numeric($content_id)){
    output_error("The ID must be numeric!");
}

if(!verify_token($user_id,$token)) {
    output_error("access denied");
}

if($content_id && !$img_id){
    $stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID WHERE service.type = 'blog' AND content.userID=? AND content.ID=?");
    $stmt->bind_param("ii", $user_id, $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM content WHERE ID = ?");
        $stmt->bind_param("i",$content_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM img WHERE contentID = ?");
        $stmt->bind_param("i",$content_id);
        $stmt->execute();
        output_ok("Content was deleted successfully");
    }else{
        output_error("Oops! something went wrong with the connection of either the content or the image");
    }
}


else if($img_id && $content_id){
    $stmt = $conn->prepare("SELECT * FROM img INNER JOIN content ON content.ID = img.contentID
                                              INNER JOIN service ON content.serviceID = service.ID WHERE service.type = 'blog' AND img.ID=? AND img.contentID=?");
    $stmt->bind_param("ii", $img_id, $content_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM img WHERE ID = ?");
        $stmt->bind_param("i",$img_id);
        $stmt->execute();

        if($stmt->affected_rows == 1){
        output_ok("Image was deleted successfully!");
        }
        else{
        output_error("This content is not in the right blog!");
        }
    }
    else{
        output_error("You are not connected to a blog!");
    }
}


else if($img_id && !$content_id){
    output_error("You must specify which image you want to delete!");
}



else{
    output_error("Ooops, something went wrong!");
}
?>

