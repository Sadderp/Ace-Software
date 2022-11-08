<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

$blog_id = get_if_set('blog_id');



//==================================================
// Shows all the post in a blog
//==================================================

if(!$blog_id){
    output_error("The URL is empty!");
}


$stmt = $conn->prepare("SELECT * FROM service WHERE type = 'blog' AND ID = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = ['Blog name' => $row['title']];
        $stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID WHERE content.serviceID = ?");
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stmt = $conn->prepare("SELECT * FROM img INNER JOIN content ON content.ID = img.contentID WHERE content.serviceID = ?");
                $stmt->bind_param("i", $blog_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if($row['img_url'] == ""){
                            $data[] = ['content' => $row['contents']];
                        }else{
                            $data[] = ['content' => $row['contents'], 'img_url' => $row['img_url']];   // assosiative array with blog title, content and img url.
                        }
                    }
                }
            }
        }
        output_ok($data);
    } 
}
else {
    output_error('Blog_id must be numeric!');
}
?>