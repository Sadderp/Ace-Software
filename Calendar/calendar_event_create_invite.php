<?php
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");

//==================================================
//      Get variables
//==================================================
$user_id = get_if_set('userID');
$token = get_if_set('token');

$invuser_id = get_if_set('invuserID');
$event_ID = get_if_set('eventID');

//==================================================
//      Requirements
//==================================================
if(!verify_token($user_id,$token)) {
    output_error("Token is invalid or expired");
}
if($user_id == $invuser_id){
    output_ok("You can't invite yourself to an event");
    die();
}

//==================================================
//      If user owns the event
//==================================================
$stmt = $conn->prepare("SELECT * FROM calendar_event WHERE userID=? AND ID=?");
$stmt->bind_param("ii", $user_id, $event_ID);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0){
    output_ok("You can't invite someone to an event you haven't created");
    die();
}

//==================================================
//      Checks if the user is already invited
//==================================================
$stmt = $conn->prepare("SELECT * FROM calendar_invite WHERE userID=? AND eventID=?");
$stmt->bind_param("ii", $invuser_id, $event_ID);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0){
    //==================================================
    //      Creates invite
    //==================================================
    $stmt = $conn->prepare("INSERT INTO calendar_invite(userID, eventID) VALUES (?,?)");
    $stmt->bind_param("ii", $invuser_id, $event_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($stmt->affected_rows == 1) {
        output_ok("Invite created");
        die();
    } else {
        output_error("Can't find any data");
    }
}

//==================================================
//      Removes invite
//==================================================
$stmt = $conn->prepare("DELETE FROM calendar_invite WHERE userID=? and eventID=?");
$stmt->bind_param("ii", $invuser_id, $event_ID);
$stmt->execute();

output_ok("Invite removed");
die();
?>