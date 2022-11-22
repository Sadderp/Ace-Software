<?php
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");



//==================================================
//      Get variables
//==================================================
$user_id = get_if_set('user_id');
$token = get_if_set('token');

$invuser_id = get_if_set('invuser_id');
$event_ID = get_if_set('event_id');



//==================================================
//      Requirements
//==================================================
if(!$user_id || !$token || !$invuser_id || !$event_ID) {
    output_error("You need to fill in user_id, token, invuser_id and event_id");
}
if(!verify_token($user_id,$token)) {
    output_error("Token is invalid or expired");
}
if($user_id == $invuser_id){
    die(output_ok("You can't invite yourself to an event"));
}
$stmt = $conn->prepare("SELECT * FROM user WHERE ID=?");
$stmt->bind_param("i", $invuser_id);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0) {
    output_error("The invited user do not exist");
}

if(check_admin($user_id) || check_admin($invuser_id)){
    die(output_ok("Admins do not have access to the calendar"));
}



//==================================================
//      Checks if the user owns the event
//==================================================

$stmt = $conn->prepare("SELECT * FROM calendar_event WHERE userID=? AND ID=?");
$stmt->bind_param("ii", $user_id, $event_ID);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0){
    die(output_ok("You can't invite someone to an event you haven't created"));
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
        $data[] = ['Status'=>$conn->'Created', 'User ID'=>$invuser_id, 'Event ID'=>$event_ID];
        die(output_ok($data));
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

$data[] = ['Status'=>'Removed', 'User ID'=>$invuser_id, 'Event ID'=>$event_ID];
die(output_ok($data));
?>