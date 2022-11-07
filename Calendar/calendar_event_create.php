<?php
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");

//==================================================
//      Get variables
//==================================================
$user_id = get_if_set('user_id');
$token = get_if_set('token');

$date = get_if_set('date');
$end_date = get_if_set('end_date');
$title = get_if_set('title');
$description = get_if_set('description');

if(!is_numeric($date) || !is_numeric($end_date)) {
    output_error("Date or end date must be numerical");
}
if(strlen($date) >= 15 || strlen($end_date) >= 15) {
    output_error("Date or end date is formatted wrong");
}

//==================================================
//      Requirements
//==================================================
if(!verify_token($user_id,$token)) {
    output_error("Token is invalid or expired");
}

//==================================================
//      Checks if start date is set before end date
//==================================================
if($date > $end_date){
    output_error("You can not put the end date before the start date");
}

$stmt = $conn->prepare("INSERT INTO calendar_event(userID, date, end_date, title, description) VALUES (?,?,?,?,?)");
$stmt->bind_param("issss", $user_id, $date, $end_date, $title, $description);
$stmt->execute();

//==================================================
//      Skapar ett event om den kan
//==================================================
if ($stmt->affected_rows === 1) {
    output_ok("Event created");
} else {
    output_error("Could not create an event");
}   
?>