<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";
    $data = [];

    $db = $conn;
    
    if(!empty($_GET['userID'])&& !empty($_GET['token'])){
        $userID = $_GET['userID'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }
    
    verify_token($userID, $token);

    
    if(!empty($_GET['title'])){
        $title = $_GET['title'];
    };

    //checks your own events
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

    print_r($_GET['userID']);
    
    //Checks what events you're invited to
    $sel = "SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID 
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result2 = $stmt->get_result();

    //Checks what events you have made
    $sql = "SELECT * FROM calendar_event WHERE userID=? AND title=?";

    $stamt = $conn->prepare($sql);
    $stamt->bind_param("is", $userID, $title);
    $stamt->execute();
    $result = $stamt->get_result();

    // Your own event
    if($result->num_rows == 0){
        $json_result = ["Version"=>$version, "Status"=>$ok, "Data"=>"You have not created any events yet"];
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $json_result = ["ID: ".$row["ID"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"]];
            array_push($data,$json_result);
            }
    }

    // Invited to
    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $json_result = ["Invited to:"." ID: ".$row["eventID"]." by: "." userID ".$row["userID"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"]];
            array_push($data,$json_result);
            }
    }
    $resultat = ["Version"=>$version, "Status"=>$ok, "Data"=>$data];
    echo json_encode($resultat);
?>