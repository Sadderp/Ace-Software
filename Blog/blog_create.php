<?php
require_once('../db.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['title']) && !empty($_GET['user']) && !empty($_GET['password'])){
    $title = $_GET['title'];
    $user = $_GET['user'];
    $password = $_GET['password'];
    

    $sql = "SELECT user.ID,user.username,user.password FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY username= '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
                $userID = $row['ID'];
                
                if(password_verify($password, $row['password'])) {
                    $lastID = mysqli_insert_id($conn);
                    $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
                    $sql2= "INSERT INTO end_user(userID,serviceID) VALUES (?,?)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s",$title); 
                    $stmt->execute();

                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ii",$userID,$lastID); 
                    $stmt2->execute();
                
                    $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Blog created successfully'];
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

