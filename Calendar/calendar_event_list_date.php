<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    
    $evdate = get_if_set('evdate');
    $evend_date = get_if_set('evend_date');

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    if(!$user_id && !$token){
        output_ok("You need fill in user_id and token")
    }

    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    //===============================
    //    Prepared statements
    //===============================
    $sql = "SELECT * FROM user WHERE ID=? AND token=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $token);

    $sql2 = "SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID AND (end_date >= ? AND date <= ?)";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("iss",$user_id, $evdate, $evend_date);

    $sql3 = "SELECT * FROM calendar_event WHERE user_id=? AND (end_date >= ? AND date <= ?)";

    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("iss", $user_id, $evdate, $evend_date);


    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $user_id = $row['ID'];
            }
    }else {
        output_error("No user");
    }

    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $stmt3->execute();
    $result3 = $stmt3->get_result();

    //===============================
    //    Lists your own events
    //===============================
    if($result3->num_rows == 0){
        output_error("You have to write a start and end date");
    }
    if ($result3->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $json_result[] = "ID: ".$row["ID"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"];
            }
    }
    //===============================
    //    Lists invites to events
    //===============================
    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $json_result[] = "Invited to:"." ID: ".$row["eventID"]." by: "." user_id: ".$row["user_id"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"];
            }
    }
    output_ok($json_result);
?>