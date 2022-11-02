<?php
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");

//==================================================
//      Get variables
//==================================================
$user_id = get_if_set('userID');
$token = get_if_set('token');

$date = get_if_set('date');
$end_date = get_if_set('end_date');
$title = get_if_set('title');
$description = get_if_set('description');
//==================================================
//      Requirements
//==================================================
if(!verify_token($user_id,$token)) {
    output_error("Token is invalid or expired");
}

//==================================================
//      Finds the user
//==================================================
$stmt = $conn->prepare("SELECT * FROM user WHERE ID=? AND token=?");
$stmt->bind_param("is", $userID, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result3->fetch_assoc()) {
        $userID = $row['ID'];
        }
}else {
    output_error("No user");
}

//==================================================
//      Checks if start date is set before end date
//==================================================
if($date > $end_date){
    output_error("You can not put the end date before the start date");
}

$stmt = $conn->prepare("INSERT INTO calendar_event(userID, date, end_date, title, description) VALUES (?,?,?,?,?)");
$stmt->bind_param("issss", $userID, $date, $end_date, $title, $description);
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