<?php
require_once('../db.php');

    if (!empty($_GET['blog'])){
        $blog = $_GET['blog'];
        $sql = "SELECT * FROM service, blog_post, content, img WHERE ID = $blog";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
                
            while($row = mysqli_fetch_assoc($result)) {
                if ($row['type'] == 'blog') {
                    $age = array("serviceID"=>$row['serviceID'], "blog_post.title"=>$row['title'], "content.contents"=>$row['contents'], "img.img_url"=>$row['img_url']);
                    echo json_encode($age);
                }
            }
        }
        else {
        echo "ingen information hittades";
        }
    }

?>