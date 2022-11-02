<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    $version = "0.10.3";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    //===============================
    //    Prepared statements
    //===============================
    $sql = "SELECT * FROM user WHERE ID=? AND token=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $userID, $token);

    $sql2 = "DELETE FROM calendar_event WHERE ID=? AND userID=?";
    
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ii", $ID, $userID);

    $sql3 = "SELECT * FROM calendar_invite WHERE eventID=?";
    
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("i", $ID);

    $sql4 = "DELETE FROM calendar_invite WHERE eventID=?";

    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("i", $ID);

    $sql5 = "DELETE FROM calendar_invite WHERE eventID=? AND userID=?";

    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("ii", $ID, $userID);

    //===============================
    //    Checks userID and token
    //===============================
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

    $stmt->execute();

    if ($stmt->num_rows == 1) {
        while($row = $stmt->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"Please log in"];
        echo json_encode($json_result);
    }
    $stmt->close();

    $stmt3->execute();

    if($stmt3->affected_rows >= 1){
        $stmt4->execute();
        $stmt4->close();
    }
    $stmt3->close();
    $stmt2->execute();
    
    if ($stmt2->affected_rows == 1){
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"event removed"];
        echo json_encode($json_result);
    }else if ($stmt2->affected_rows == 0){
        $stmt5->execute();
        if ($stmt5->affected_rows == 1){
            $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"invite removed"];
            echo json_encode($json_result);
        }else{
            $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You can't delete someone else's event"];
            echo json_encode($json_result);
        } 
        $stmt5->close();
    }
    $stmt2->close();
?>