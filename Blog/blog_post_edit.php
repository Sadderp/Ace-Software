<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";


//==================================================
// Edit the text you've got in a blog post
//==================================================
if (!empty($_GET['contents']) && !empty($_GET['contentID']) && !empty($_GET['user']) && !empty($_GET['token']) ){
    $content = $_GET['contents'];
    $contentID = $_GET['contentID'];
    $user = $_GET['user'];
    $token = $_GET['token'];

    $sql = "SELECT * FROM user WHERE BINARY username = ? AND token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$user,$token); 
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['token'] == $_GET['token']){
                if($row['username'] == $_GET['user']){
                    if ($stmt->affected_rows == 1) {
                        $stmt = $conn->prepare("UPDATE content SET contents = ? WHERE ID = ? AND pageID = 0");
                        $stmt->bind_param("si",$content,$contentID); 
                        $stmt->execute();
                        $stmt->close();
                        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Content was edited successfully'];
                        echo json_encode($json_array);
                    }
                    else{
                        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This content is not in the right blog!'];
                        echo json_encode($json_array);
                    } 
                }else{
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'You cannot delete this content since it is not your blog!'];
                    echo json_encode($json_array);
                }
        }else{
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }
    }
}else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
    echo json_encode($json_array);
}

}


//==================================================
// Edit an image in a blog post
//==================================================
else if (!empty($_GET['img_url']) && !empty($_GET['imgID']) && !empty($_GET['user']) && !empty($_GET['token'])){
    $img_url = $_GET['img_url'];
    $imgID = $_GET['imgID'];
    $user = $_GET['user'];
    $token = $_GET['token'];

    $sql = "SELECT * FROM user WHERE BINARY username = ? AND token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$user,$token); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['token'] == $_GET['token']){
                if($row['username'] == $_GET['user']){
                    if($stmt->affected_rows == 1){
                        $sql = "UPDATE img SET img_url = ? WHERE ID = ? ";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss",$img_url,$imgID); 
                        $stmt->execute();
                        $stmt->close();
                        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Old image edited successfully'];
                        echo json_encode($json_array);
                        die();
                    }
                    else{
                        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This image is not in the right blog!'];
                        echo json_encode($json_array);
                    }
                }else{
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'You cannot delete this content since it is not your blog!'];
                    echo json_encode($json_array);
                }
        }else{
            echo "hej";
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }
    }
}else{
    echo "hejsan";
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
    echo json_encode($json_array);
}

}
else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data"=>"The URL is empty!"];
    echo json_encode($json_array);
}
?>