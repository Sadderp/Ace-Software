<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if(!empty($_GET['ID']) && !empty($_GET['serviceID']) && !empty($_GET['user']) && !empty($_GET['token']) )  {
    $ID = $_GET['ID'];
    $user = $_GET['user'];
    $token = $_GET['token'];
    $serviceID = $_GET['serviceID'];

    $sql = "SELECT username, token FROM user WHERE BINARY username = ? AND token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$user,$token); 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['token'] == $_GET['token']){
                    if($row['username'] == $_GET['user']){
        
                            $sql = "DELETE FROM content WHERE ID = ? AND serviceID = ? ";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii",$ID, $serviceID); 
                            $stmt->execute();

                            if($stmt->affected_rows == 1){
                                $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Content was deleted successfully!'];
                                echo json_encode($json_array);
                                die();
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
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>