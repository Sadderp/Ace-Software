<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";

if (!empty($_GET['name'])) {

    if ($_GET['name'] == $_SESSION['username']) {
        if (!empty($_GET['ID']) && !empty($_GET['title'])){
            $sql = "UPDATE service SET title = ?, WHERE ID = ? AND type = 'blog')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si",$title,$ID); 
        
            $title = $_GET['title'];
            $title = $_GET['ID'];
            $stmt->execute();
        
            if($stmt->affected_rows == 1){
                $json_array = ["Version: "=>$version,"Type: "=>$ok,"Data"=>"Blog edited successfully"];
                echo json_encode($json_array);
            }
            else{
                $json_array = ["Version: "=>$version,"Type: "=>$error,"Data"=>"This is not a blog!"];
                echo json_encode($json_array);
            }
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