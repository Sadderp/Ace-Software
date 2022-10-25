<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;
    
    $username = $_GET['username'];
    $token = $_GET['token'];
    /*$title = $_GET['title']*/;
    $userID = $_GET['userID'];
    $username = $_GET['username'];
    $token = $_GET['token'];
    
    //Checks what events you're invited to
    $sel = "SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID 
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("i", $userID/*, $title)*/);
    $stmt->execute();
    $result2 = $stmt->get_result();

    //Checks what events you have made
    $sql = "SELECT * FROM calendar_event WHERE userID=?";

    $stamt = $conn->prepare($sql);
    $stamt->bind_param("i", $userID/*, $title)*/);
    $stamt->execute();
    $result = $stamt->get_result();
 
    // Your own event
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = ["Version: "=>$version, "Type: "=>$ok, "Data: "=>" ID: ".$row["ID"]. " date: ".$row["date"]. " end_date: ".$row["end_date"]. " Title: ".$row["title"]. " description: ".$row["description"]];
            echo json_encode($search);
            }
    }else {
        echo json_encode("0 results");
    }

    // Invited to
    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $search = ["Version: "=>$version, "Type: "=>$ok, "Data: "=> " Invited to: "."ID: ".$row["eventID"]. " date: ".$row["date"]. " end_date: ".$row["end_date"]. " Title: ".$row["title"]. " description: ".$row["description"]];
            echo json_encode($search);
            }
    }
?>