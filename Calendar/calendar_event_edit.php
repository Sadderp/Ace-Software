<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");



    //==================================================
    //      Get variables
    //==================================================
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');
    
    $event_id = get_if_set('event_id');
    $date  = get_if_set('date');
    $end_date  = get_if_set('end_date');
    $title  = get_if_set('title');
    $description = get_if_set('description');

    if(!$user_id || !$token || !$event_id){
        output_error("You need to fill in user_id, token and event_id");
    }

    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }


    
    //===============================
    //    Updates the event
    //===============================
    $stmt = $conn->prepare("SELECT * FROM calendar_event WHERE userID=? AND ID=?");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 0){
        output_error("You do not have an event with this ID");
    }

    if(!$date && !$end_date && !$title && !$description){
        output_error("Please write what you want to edit (Title, Date, End date, Description)");
    }

    if($date){
        $stmt3 = $conn->prepare("UPDATE calendar_event SET date=? WHERE ID=?");
        $stmt3->bind_param("si", $date, $event_id);
        $stmt3->execute();

        $json_result[] = "Date updated";
    }
    if($end_date){
        $stmt4 = $conn->prepare("UPDATE calendar_event SET end_date=? WHERE ID=?");
        $stmt4->bind_param("si", $end_date, $event_id);
        $stmt4->execute();

        $json_result[] = "End date updated";
    }
    if($title){
        $stmt5 = $conn->prepare("UPDATE calendar_event SET title=? WHERE ID=?");
        $stmt5->bind_param("si", $title, $event_id);
        $stmt5->execute();

        $json_result[] = "Title updated";
    }
    if($description){
        $stmt6 = $conn->prepare("UPDATE calendar_event SET description=? WHERE ID=?");
        $stmt6->bind_param("si", $description, $event_id);
        $stmt6->execute();

        $json_result[] = "Description updated";
    }
    output_ok($json_result);
?>