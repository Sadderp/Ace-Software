<?php
require_once('../db.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";


//==================================================
// Edit the text you've got in a blog post
//==================================================
if (!empty($_GET['contents']) && !empty($_GET['conID'])){
    $content = $_GET['contents'];
    $conID = $_GET['conID'];

    $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
    $stmt->bind_param("si",$content,$conID); 
    $stmt->execute();

    if (mysqli_affected_rows($conn) > 0) {
            $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Old content edited successfully'];
    }
    else {
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This is not connected to a blog!'];
    }
    
    echo json_encode($json_array);
}



//==================================================
// Edit the name of a specific blog
//==================================================
else if (!empty($_GET['Title']) && !empty($_GET['servID'])){
    $sql = "UPDATE service SET title = ? WHERE ID = ? AND type = 'blog'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$title,$servID); 

    $title = $_GET['Title'];
    $servID = $_GET['servID'];
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 1){
        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data"=>"Old blog edited successfully"];
        echo json_encode($json_array);
    }
    else{
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data"=>"This is not a blog!"];
        echo json_encode($json_array);
    }
}



//==================================================
// Edit an image in a blog post
//==================================================
else if (!empty($_GET['img']) && !empty($_GET['imgID'])){
    $sql = "UPDATE img INNER JOIN content ON img.contentID = content.ID SET img.img_url = ? WHERE img.ID = ? AND content.pageID = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$img,$imgID); 

    $img = $_GET['img'];
    $imgID = $_GET['imgID'];
    $stmt->execute();

    if (mysqli_affected_rows($conn) > 0) {
        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Old image edited successfully'];
    }
    else {
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This is not connected to a blog!'];
    }

echo json_encode($json_array);
}





else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data"=>"The URL is empty!"];
    echo json_encode($json_array);
}
?>