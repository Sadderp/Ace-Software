<?php
require_once('../db.php');
require_once('../token.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['title'])&& !empty($_GET['user']) && !empty($_GET['token'])){
    $title = $_GET['title'];
    $user = $_GET['user'];
    $token = $_GET['token'];
    

    $sql = "SELECT user.ID AS Uid, username, token FROM user WHERE BINARY user.username = ? AND user.token=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$user,$token); 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $userID = $row['Uid'];
            if($row['token'] == $_GET['token']) {
                if($row['username'] == $_GET['user']){
                    $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s",$title); 
                    $stmt->execute();
                    $stmt->close();
                    $lastID = $conn->insert_id; 

                    $sql = "SELECT user.ID AS Uid, username, userID, token FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY user.username = ? AND user.token=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss",$user,$token); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    $sql2= "INSERT INTO end_user(userID,serviceID) VALUES (?,?)";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ii",$userID,$lastID); 
                    $stmt2->execute();
                    $json_array = ["Version: "=>$version,"Status: "=>$ok,"Data: "=>'Blog was created successfully'];
                    echo json_encode($json_array);
                    die();
                }else{
                    echo "hej";
                    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
                    echo json_encode($json_array);
                }
            }else{
                echo "hejsan";
                $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
                echo json_encode($json_array);
            }
        }
    } else {
        echo "hello";
        $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
        echo json_encode($json_array);
    }
} else{
    
    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>

