<?php
require_once('../db.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['name'])) {

    if ($_GET['name'] == $_SESSION['username']) {
        if (!empty($_GET['title'])){
            $sql = "INSERT INTO service(title,type) VALUES (?,'blog')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s",$title); 
        
            $title = $_GET['title'];
            $stmt->execute();
        
            $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'Blog created successfully'];
            echo json_encode($json_array);
        }

    }
    else {
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
        echo json_encode($json_array);

    }

}


else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>

