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



    //==================================================
    //      Requirements
    //==================================================
    if(!$user_id || !$token || !$event_id){
        output_error("You need to fill in user_id, token, event_id");
    }
    if(!$date && !$end_date && !$title && !$description){
        output_error("Please write what you want to edit (date, end_date, title, description)");
    }
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }
    if(!is_numeric($event_id) || !is_numeric($date) || !is_numeric($end_date)) {
        output_error("Date or end date must be numerical");
    }
    if(strlen($date) >= 15 || strlen($end_date) >= 15) {
        output_error("Date or end date is formatted wrong");
    }

    if(check_admin($user_id)){
        die(output_ok("Admins do not have access to the calendar"));
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

    if($date){
        $stmt = $conn->prepare("UPDATE calendar_event SET date=? WHERE ID=?");
        $stmt->bind_param("si", $date, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        output_ok("Date edited");
    }
    if($end_date){
        $stmt = $conn->prepare("UPDATE calendar_event SET end_date=? WHERE ID=?");
        $stmt->bind_param("si", $end_date, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        output_ok("End date edited");
    }
    if($title){
        $stmt = $conn->prepare("UPDATE calendar_event SET title=? WHERE ID=?");
        $stmt->bind_param("si", $title, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        output_ok("Title edited");
    }
    if($description){
        $stmt = $conn->prepare("UPDATE calendar_event SET description=? WHERE ID=?");
        $stmt->bind_param("si", $description, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        output_ok("Description edited");
    }
?>