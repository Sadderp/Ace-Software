<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');



//==================================================
// Edit the text you've got in a blog post
//==================================================
$content_id = get_if_set('content_id');
$content = get_if_set('content');
$user_id = get_if_set('user_id');
$token = get_if_set('token');
$img_id = get_if_set('img_id');
$img_url = get_if_set('img_url');

if(!$content_id && !$user_id && !$token){
    output_error("The URL is empty!");
}

if(!verify_token($user_id,$token)) {
    output_error("Access denied");
}

$stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID WHERE service.type = 'blog' AND content.userID=? AND content.ID=?");
$stmt->bind_param("ii", $user_id, $content_id);
$stmt->execute();
$result = $stmt->get_result();

if($content && !$img_id && !$img_url){
    if($result->num_rows == 1) {
        $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
        $stmt->bind_param("si", $content, $content_id);
        $stmt->execute();

        output_ok("Content was edited successfully");
    }
    else{
        output_error("This content is not in the right blog or you signed in as the wrong person");
    }
}



//==================================================
// Edit an image in a blog post
//==================================================
else if($img_id && $img_url && !$content){
    if($result->num_rows == 1) {
        $stmt = $conn->prepare("UPDATE img SET img_url = ? WHERE ID = ?");
        $stmt->bind_param("ss",$img_url,$img_id); 
        $stmt->execute();

        output_ok("Image was edited successfully");
    }else{
        output_error("You cannot edit this content since it is not your blog!");
    }
}



else if($content && $img_id && $img_url){
    $stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID 
                                                  INNER JOIN img ON content.ID = img.contentID WHERE service.type = 'blog' AND content.userID=? AND content.ID=? AND img.ID=?");
    $stmt->bind_param("iii", $user_id, $content_id, $img_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 1) {
        $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
        $stmt->bind_param("si", $content, $content_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE img SET img_url = ? WHERE ID = ?");
        $stmt->bind_param("ss",$img_url,$img_id); 
        $stmt->execute();
        output_ok("Content was edited successfully");
    }else{
        output_error("Oops! something went wrong with either the content or the image");
    }
}



else{
    output_error("You have logged in, but you have not requested anything!");
}
?>