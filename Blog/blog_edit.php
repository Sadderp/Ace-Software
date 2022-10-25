<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";


if(!empty($_GET['ID']) && !empty($_GET['title']) && !empty($_GET['user']) && !empty($_GET['token'])) {
    $title = $_GET['title'];
    $ID = $_GET['ID'];
    $user = $_GET['user'];
    $token = $_GET['token'];

    $sql = "SELECT user.ID AS uID, user.token, end_user.userID, end_user.serviceID FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY user.username = ? AND user.token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$user,$token); 
    $stmt->execute();
    $stmt->close();
    $result = $stmt->get_result();
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['token'] == $_GET['token']){
                    if($row['uID'] == $_GET['user']){
                        if($row['userID'] == $row['uID']){
                            $sql = "UPDATE service SET title = ? WHERE ID = ? AND type = 'blog'";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("si",$title,$ID); 
                            $stmt->execute();
                        
                            if($stmt->affected_rows == 1){
                                $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data"=>"Blog edited successfully"];
                                echo json_encode($json_array);
                            }
                            else{
                                $json_array = ["Version: "=>$version,"Type: "=>$error,"Data"=>"This is not a blog!"];
                                echo json_encode($json_array);
                            }
                        }else{
                            echo "hej";
                            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'You cannot delete this blog since its not yours!'];
                            echo json_encode($json_array);
                        }
                }else{
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
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
else{
$json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
echo json_encode($json_array);
}
?>