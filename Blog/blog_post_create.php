<?php
require_once('../db.php');
require_once('../token.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";


//==================================================
// content table
//==================================================
if ((!empty($_GET['contents'])) && (!empty($_GET['serviceID'])) && !empty($_GET['userID']) && !empty($_GET['token']))  {    //checks if the if is empty if so "dies". 
                                                                                                                                
    $contents = $_GET['contents'];
    $serviceID = $_GET['serviceID'];
    $userID = $_GET['userID'];
    $token = $_GET['token'];

    if(empty($_GET['imgID'])){
        $imgID = 0;
    }
    else{
        $imgID = $_GET['imgID']; 
    } 
    
    $sql = "SELECT user.ID, user.username, user.token, end_user.userID, end_user.serviceID, service.ID, service.type FROM user INNER JOIN end_user ON user.ID = end_user.userID 
                                                                                                                                INNER JOIN service ON end_user.serviceID = service.ID WHERE BINARY user.ID = ? AND token = ? AND type = 'blog' AND end_user.serviceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss",$userID,$token,$serviceID); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'You are not connected to a blog!'];
        echo json_encode($json_array);
        die();
    }

    while($row = $result->fetch_assoc()) {
        if($row['token'] != $_GET['token']){
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
            die();
        }
        if($row['userID'] != $_GET['userID']){
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'You cannot delete this content since it is not your blog!'];
            echo json_encode($json_array);
            die();
        }

        if($serviceID == $row['serviceID']){
            $stmt = $conn->prepare("INSERT INTO content (contents, imgID, serviceID, userID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii",$contents, $imgID, $serviceID, $userID);
            $stmt->execute();
            $json_array = ["Version: "=>$version,"Status: "=>$ok,"Data: "=>'New content added!'];
            echo json_encode($json_array);
        }
    }
}



//==================================================
// img table
//==================================================
 else if (!empty($_GET['contentID']) && (!empty($_GET['img_url'])) && !empty($_GET['user']) && !empty($_GET['token'])) {
        $contentID = $_GET['contentID'];
        $img_url = $_GET['img_url'];
        $user = $_GET['user'];
        $token = $_GET['token'];


        $sql = "SELECT * FROM user WHERE BINARY username = ? AND token=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss",$user,$token); 
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
            die();
        }
        while($row = $result->fetch_assoc()) {
            if($row['token'] != $_GET['token']){
                $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
                echo json_encode($json_array);
                die();
            }
            if($row['username'] != $_GET['user']){
                $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Insertion failed!'];
                echo json_encode($json_array);
                die();
            }   
            $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
            $stmt->bind_param("is", $contentID, $img_url);
            $stmt->execute();

            $data = "contentID:$contentID img url:$img_url";
            $json_result = ["Version: "=>$version, "Status: "=>"OK", "Data: "=>$data];
            echo json_encode($json_result);
            }
}else{
$json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'The URL is empty!'];
echo json_encode($json_array);
die();
}
?>