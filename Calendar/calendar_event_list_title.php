<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    


    //==================================================
    //      Get variables
    //==================================================
    $title = get_if_set('title');
    $title = "%".$title."%";

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');



    //==================================================
    //      Requirements
    //==================================================
    if(!$user_id || !$token){
        output_error("You need to fill in user_id, token");
    }
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }
    if(check_admin($user_id)){
        die(output_ok("Admins do not have access to the calendar"));
    }


    
    //===============================
    //    Lists your own events
    //===============================    
    $stmt = $conn->prepare("SELECT * FROM calendar_event WHERE userID=? AND title LIKE ?");
    $stmt->bind_param("is", $user_id, $title);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 0){
        $json_result[] = "Could not find any events";
    }
    while($row = $result->fetch_assoc()) {
        $json_result[] = [$row];
    }

    //===============================
    //    Lists invites to events
    //===============================
    $stmt = $conn->prepare("SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $json_result[] = [$row];
    }

    //===============================
    //    Output
    //===============================
    output_ok($json_result);
?>