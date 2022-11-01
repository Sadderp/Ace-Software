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
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }
    
    verify_token($userID, $token);

    $sql2 = "SELECT * FROM user WHERE ID=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("is", $userID, $token);
    $statement->execute();
    $result3 = $statement->get_result();

    if ($result3->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        echo json_encode("No user");
    }

    //Check info about the event
    if(!empty($_GET['date'])){
        $date = $_GET['date'];
    };

    if(!empty($_GET['end_date'])){
        $end_date = $_GET['end_date'];
    };

    if(!empty($_GET['title'])){
        $title = $_GET['title'];
    };

    if(!empty($_GET['description'])){
        $description = $_GET['description'];
    };
    
    $sql = "INSERT INTO calendar_event(userID, date, end_date, title, description) VALUES (?,?,?,?,?)";

    if($date > $end_date){
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You can not put the end date before the start date"];
        echo json_encode($json_result);
        die();
    }

    //prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $userID, $date, $end_date, $title, $description);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"event created"];
        echo json_encode($json_result);
    } else {
        $json_result = ["Version"=>$version, "Status"=>$error, "Data"=>"uh oh"];
        echo json_encode($json_result);
    }   
?>