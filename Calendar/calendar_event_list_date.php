<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    
    $date = get_if_set('date');
    $end_date = get_if_set('end_date');
    if(strlen($date) >= 15 || strlen($end_date) >= 15) {
        output_error("Date or end date is formatted wrong");
    }

    $json_result = [];

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    if(!$user_id || !$token || !$date || !$end_date){
        output_error("You need to fill in user_id, token, date and end_date");
    }

    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    //===============================
    //    Prepared statements
    //===============================
    $stmt = $conn->prepare("SELECT * FROM user WHERE ID=? AND token=?");
    $stmt->bind_param("is", $user_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $user_id = $row['ID'];
        }
    }else {
        output_error("No user");
    }

    //===============================
    //    Lists your own events
    //===============================
    $stmt = $conn->prepare("SELECT * FROM calendar_event WHERE userID=? AND (end_date >= ? AND date <= ?)");
    $stmt->bind_param("iss", $user_id, $date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        $json_result[] = "Could not find any events";
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($json_result,$row);
        }
    }

    //===============================
    //    Lists invites to events
    //===============================
    $stmt = $conn->prepare("SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID AND (end_date >= ? AND date <= ?)");
    $stmt->bind_param("iss",$user_id, $date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($json_result,$row);
        }
    }

    //===============================
    //    Output
    //===============================
    output_ok($json_result);
?>