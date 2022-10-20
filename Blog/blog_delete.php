<?php
require_once('../db.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";




if (!empty($_GET['ID'])){
    $ID = $_GET['ID'];    
    $sql = "DELETE FROM service WHERE ID = ? AND type = 'blog'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 1){
        $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data: "=>'New blog deleted successfully'];
        echo json_encode($json_array);
    }
    else{
        $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'This is not a blog!'];
        echo json_encode($json_array);
    }
}
else{
    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'The URL is empty!'];
    echo json_encode($json_array);
}
?>