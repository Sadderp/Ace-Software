<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;
    
    $username = $_GET['username'];
    $token = $_GET['token'];
    $evdate = $_GET['evdate'];
    $evend_date = $_GET['evend_date'];
    $userID = $_GET['userID'];

    //Checks what events you're invited to
    $sel = "SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID
    WHERE calendar_event.userID=? AND calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID AND ((date>? OR date<?) OR (end_date>? OR end_date<?))";

    $stamt = $conn->prepare($sel);
    $stamt->bind_param("ssssi", $evdate, $evend_date, $evdate, $evend_date, $userID);
    $stamt->execute();
    $result = $stamt->get_result();

    //Checks what events you have made
    $sql = "SELECT * FROM calendar_event WHERE userID=? AND userID=? AND ((date>? OR date<?) OR (end_date>? OR end_date<?))";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("ssssi", $evdate, $evend_date, $evdate, $evend_date, $userID);
    $stmt->execute();
    $result2 = $stmt->get_result();

    
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