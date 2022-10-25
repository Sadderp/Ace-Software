<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['title']) && !empty($_GET['token'])){
    $title = $_GET['title'];
    $token = $_GET['token'];
    

    $sql = "SELECT * FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY user.token= '$token'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {

                $userID = $row['ID'];
                
                if($row['token'] == $_GET['token']) {
                    $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s",$title); 
                    $stmt->execute();
                    printf($lastID = $conn->insert_id); 


                    $sql2= "INSERT INTO end_user(userID,serviceID) VALUES (?,?)";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ii",$userID,$lastID); 
                    $stmt2->execute();
                
                    $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Blog was created successfully'];
                    echo json_encode($json_array);
                }
                else {
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
                    echo json_encode($json_array);
                }

        }
    } else {
        
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
        echo json_encode($json_array);
    }
} else{
    
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>

