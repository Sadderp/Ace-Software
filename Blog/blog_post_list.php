<?php
require_once('../db.php');
require_once('../verify_token.php');
require_once('../utility.php');

$blog_id = get_if_set('blog_id');

//==================================================
// Shows all the post in a blog
//==================================================

if(!$blog_id){
    output_error("Missing input(s) - required: 'blog_id'");
}


$stmt = $conn->prepare("SELECT * FROM service WHERE type = 'blog' AND ID = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    output_error('Blog could not be found!');
}

$data = [];

$stmt = $conn->prepare("SELECT * FROM content WHERE serviceID = ?");
$stmt->bind_param("i", $blog_id);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    output_ok("Blog has no content");
    die();
}

while ($row = $result->fetch_assoc()) {
    $stmt = $conn->prepare("SELECT * FROM img INNER JOIN content ON content.ID = img.contentID WHERE content.serviceID = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $c = ['content'=>$row['contents'],'images'=>[]];

    if ($result->num_rows > 0) {
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = ['image_url'=>$row['img_url']];
        }
        $c['images'] = $images;
    } 

    $data[] = $c;
}

// Output
$output = ["id"=>$blog_id,"blog_data"=>$data];
output_ok($output);

?>