<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    if(!empty($_GET['userID'])&& !empty($_GET['token'])){
        $userID = $_GET['userID'];
        $token = $_GET['token'];
    }else{
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You need to log in"];
        echo json_encode($json_result);
    }
    
    verify_token($userID, $token);


    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    $sql2 = "SELECT * FROM user WHERE ID=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("ss", $userID, $token);
    $statement->execute();
    $result2 = $statement->get_result();

    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"Please log in"];
        echo json_encode($json_result);
    }

    $del = "DELETE FROM calendar_event WHERE ID=? AND userID=?";
    
    //prepared statement
    $stmt = $conn->prepare($del);
    $stmt->bind_param("ii", $ID, $userID);
    $stmt->execute();

    if ($stmt->affected_rows == 1){
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"event removed"];
        echo json_encode($json_result);
    }else if ($stmt->affected_rows == 0){
        $sql = "DELETE FROM calendar_invite WHERE eventID=? AND userID=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $ID, $userID);
        $stmt->execute();

        if ($stmt->affected_rows == 1){
            $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"invite removed"];
            echo json_encode($json_result);
        }else{
            $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You can't delete someone else's event"];
            echo json_encode($json_result);
        } 
    }
?>