<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    
    //===============================
    //    Checks user_id and token
    //===============================
    $event_id = get_if_set('event_id');

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');
    
    if(!$user_id && !$token){
        output_ok("You need fill in user_id and token");
    }
    
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    //===============================
    //    Prepared statements
    //===============================
    $stmt = $conn->prepare("DELETE FROM calendar_event WHERE ID=? AND userID=?");
    $stmt->bind_param("ii", $event_id, $user_id);

    $stmt2 = $conn->prepare("DELETE FROM calendar_invite WHERE eventID=?");
    $stmt2->bind_param("i", $event_id);

    $stmt3 = $conn->prepare("DELETE FROM calendar_invite WHERE eventID=? AND userID=?");
    $stmt3->bind_param("ii", $event_id, $user_id);

    //===============================
    // Deletes invites and/or events
    //===============================
    $stmt2->execute();
    if($stmt2->affected_rows == 0){
        output_error("Couldn't delete invite");
    }
    $stmt2->close();    
    $stmt->execute();
    
    if ($stmt->affected_rows == 1){
        output_ok("event removed");
    }else if ($stmt->affected_rows == 0){
        $stmt3->execute();
        if ($stmt3->affected_rows == 1){
            output_ok("Invite removed");
        }else{
            output_ok("You can't delete an event that doesn't exist");
        } 
        $stmt3->close();
    }
    $stmt->close();
?>