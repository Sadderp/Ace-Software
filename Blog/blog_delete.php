<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if(!empty($_GET['ID']) && !empty($_GET['token'])) {
    $ID = $_GET['ID'];

    $sql = "SELECT * FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY token='".$token"'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['token'] == $_GET['token']){
                if($row['ID'] == $row['userID']){
                    $sql = "DELETE FROM service WHERE ID = ? AND type = 'blog'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i",$ID); 
                    $stmt->execute();

                    
                    if($stmt->affected_rows == 1){
                        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Blog was deleted successfully!'];
                        echo json_encode($json_array);
                    }
                    else{
                        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This is not a blog!'];
                        echo json_encode($json_array);
                    } 
                }
                else{
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'You cannot delete this blog (since its not yours you freaking dumbass!'];
                    echo json_encode($json_array);
                }
            }
        } else{
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }
    }

}
else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>