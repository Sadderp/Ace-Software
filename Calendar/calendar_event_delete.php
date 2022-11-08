<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    


    //==================================================
    //      Get variables
    //==================================================
    $event_id = get_if_set('event_id');

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');



    //==================================================
    //      Requirements
    //==================================================
    if(!$event_id || !$user_id || !$token){
        output_ok("You need fill in event_id, user_id and token");
    }
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }
    if(check_admin($user_id)){
        die(output_ok("Admins do not have access to the calendar"));
    }
    if(!is_numeric($event_id)) {
        output_error("event_id must be numerical");
    }



    //==================================================
    //      Deletes invites and/or events
    //==================================================
    $stmt = $conn->prepare("DELETE FROM calendar_event WHERE ID=? AND userID=?");
    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($stmt->affected_rows == 1){
        $stmt = $conn->prepare("DELETE FROM calendar_invite WHERE eventID=?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        die(output_ok('Status'=>'Remove', 'Type'=>'Event'));
    }
    
    $stmt = $conn->prepare("DELETE FROM calendar_invite WHERE eventID=? AND userID=?");
    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    if ($stmt->affected_rows == 1){
        die(output_ok('Status'=>'Remove', 'Type'=>'Invite'));
    }

    output_ok("You can't delete an event/invite that doesn't exist");
?>