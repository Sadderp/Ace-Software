<?php
require_once('../db.php');
require_once('../token.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";


//==================================================
// content table
//==================================================
    if ((!empty($_GET['contents'])) && (!empty($_GET['serviceID'])) && !empty($_GET['user']) && !empty($_GET['token']))  {    //checks if the if is empty if so "dies". 
                                                                                                                                    
        $contents = $_GET['contents'];
        $serviceID = $_GET['serviceID'];
        $user = $_GET['user'];
        $token = $_GET['token'];
        if(empty($_GET['imgID'])){
            $imgID = 0;
        }
        else{
           $imgID = $_GET['imgID']; 
        } 
        
        $sql = "SELECT user.ID AS Uid, user.username, user.token, end_user.userID, end_user.serviceID, service.ID, service.type FROM user INNER JOIN end_user ON user.ID = end_user.userID
                                   INNER JOIN service ON end_user.serviceID = service.ID WHERE BINARY username = ? AND token=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss",$user,$token); 
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $userID = $row['Uid'];
                if($row['token'] == $_GET['token']){
                    if($row['username'] == $_GET['user']){
                        if($row['type'] == 'blog'){
                            $stmt = $conn->prepare("INSERT INTO content (contents, imgID, serviceID, userID) VALUES (?, ?, ?, ?)");
                            $stmt->bind_param("siii",$contents, $imgID, $serviceID, $userID);
                            $stmt->execute(); 
                            $stmt->close(); 
                            $json_array = ["Version: "=>$version,"Status: "=>$ok,"Data: "=>'New content added!'];
                            echo json_encode($json_array);
                            die();
                        }else{
                            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'This is not a blog!'];
                            echo json_encode($json_array);
                            die();
                        }
                }else{
                    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'You cannot delete this content since it is not your blog!'];
                    echo json_encode($json_array);
                    die();
                }
        }else{
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
            die();
        }
    }
}else{
    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
    echo json_encode($json_array);
    die();
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

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['token'] == $_GET['token']){
                    if($row['username'] == $_GET['user']){
                
                        $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
                        $stmt->bind_param("is", $contentID, $img_url);
                        $stmt->execute();

                        $data = "contentID:$contentID img url:$img_url";
                        $json_result = ["Version: "=>$version, "Status: "=>"OK", "Data: "=>$data];
                        echo json_encode($json_result);
                
                    }else{
                        $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Insertion failed!'];
                        echo json_encode($json_array);
                        die();
                    }
            }else{
                $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
                echo json_encode($json_array);
                die();
            }
        }
    }else{
        $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
        echo json_encode($json_array);
        die();
    }
    }else{
    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
    die();
    }
?>