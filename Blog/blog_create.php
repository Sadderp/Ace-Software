<?php
require_once('../db.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['title'])) {
    $title = $_GET['title'];

    $username = $_SESSION['user_username'];
    $password = $_SESSION['password'];
    $sql = "SELECT username,password FROM user INNER JOIN end_user ON user.ID = end_user.userID WHERE BINARY username='".$username."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password'])) {
                if($username)
                $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s",$title); 
                $stmt->execute();
            
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

