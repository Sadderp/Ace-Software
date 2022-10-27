<?php
require_once('../db.php');
require_once('../token.php');
$version = "1.0.1";
$ok = "OK";
$error = "Error";

if(!empty($_GET['title'])&& !empty($_GET['userID']) && !empty($_GET['token'])){
    $title = $_GET['title'];
    $userID = $_GET['userID'];
    $token = $_GET['token'];
    


    $sql = "SELECT user.ID, user.username, user.token, end_user.userID, end_user.serviceID, service.ID, service.type FROM user INNER JOIN end_user ON user.ID = end_user.userID 
                                                                                                                               INNER JOIN service ON end_user.serviceID = service.ID WHERE BINARY user.ID = ? AND token = ? AND type = 'blog'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is",$userID,$token); 
    $stmt->execute();
    $result = $stmt->get_result();
    print_r($result);
    if ($result->num_rows > 0) {
        $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'You already have a blog!'];
        echo json_encode($json_array);
        die();
    }
    while($row = $result->fetch_assoc()) {
        $userID = $row['ID'];
        if($row['token'] != $_GET['token']) {
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
            die();
        }

        if($row['username'] != $_GET['user']){
            $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }

        $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s",$title); 
        $stmt->execute();
        printf($lastID = $conn->insert_id); 


        $sql2= "INSERT INTO end_user(userID,serviceID) VALUES (?,?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ii",$userID,$lastID); 
        $stmt2->execute();
        $json_array = ["Version: "=>$version,"Status: "=>$ok,"Data: "=>'Blog was created successfully'];
        echo json_encode($json_array);
    }
}
else{
    
    $json_array = ["Version: "=>$version,"Status: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>

