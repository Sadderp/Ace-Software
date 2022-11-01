<?php
require_once('../db.php');
require_once('../verify_token.php');

        
$blog_id = get_if_set('blog_id');

if(!$blog_id){
    output_error("The URL is empty!");
}
    
$stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID INNER JOIN img ON content.imgID = img.ID WHERE content.serviceID = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

$data = "blog name:{$row['title']} content:{$row['contents']} img url:{$row['img_url']}"; 
output_ok($data);

?>