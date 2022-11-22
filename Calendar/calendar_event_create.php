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



//==================================================
//      Requirements
//==================================================
if(!$user_id && !$token && !$date && !$end_date && !$title && !$description) {
    output_error("You need to fill in user_id, token, date, end_date, title & description");
}
if(!verify_token($user_id,$token)) {
    output_error("Token is invalid or expired");
}
if(check_admin($user_id)){
    die(output_ok("Admins do not have access to the calendar"));
}
if(!is_numeric($date) || !is_numeric($end_date)) {
    output_error("Date or end date must be numerical");
}
if(strlen($date) >= 20 || strlen($end_date) >= 20) {
    output_error("Date or end date is formatted wrong");
}
if($date > $end_date){ // if end date is less then start date
    output_error("You can not put the end date before the start date");
}



//==================================================
//      Skapar ett event om den kan
//==================================================
$stmt = $conn->prepare("INSERT INTO calendar_event(userID, date, end_date, title, description) VALUES (?,?,?,?,?)");
$stmt->bind_param("issss", $user_id, $date, $end_date, $title, $description);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    $data[] = ['Status'=>'Created', 'ID'=>$conn->insert_id];
    output_ok($data);
} else {
    output_error("Could not create an event");
}   
?>