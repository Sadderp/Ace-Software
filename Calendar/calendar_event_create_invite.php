<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    $version = "0.0.2";
    $ok = "OK";
    $error = "Error";

    if(!empty($_GET['userID']) && !empty($_GET['token'])){
        $userID = $_GET['userID'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }

    verify_token($userID, $token);


    $sql2 = "SELECT * FROM user WHERE ID=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("ss", $userID, $token);
    $statement->execute();
    $result3 = $statement->get_result();

    if ($result3->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        echo json_encode("No user");
    }

    if(!empty($_GET['invuserID'])){
        $invuserID = $_GET['invuserID'];
    };

    if(!empty($_GET['eventID'])){
        $eventID = $_GET['eventID'];
    };

    $sql = "INSERT INTO calendar_invite(userID, eventID) VALUES (?,?)";

    //prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $invuserID, $eventID);
    $stmt->execute();
    $result = $stmt->get_result();
    if($userID == $invuserID){
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You can't invite yourself to an event"];
        echo json_encode($json_result);
    }else{   
        if ($stmt->affected_rows === 1) {
            $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"invite created"];
            echo json_encode($json_result);
        } else {
            $json_result = ["Version"=>$version, "Status"=>$error, "Data"=>"uh oh"];
            echo json_encode($json_result);
        };
    }
?>