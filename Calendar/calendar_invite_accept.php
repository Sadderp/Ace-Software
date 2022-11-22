<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");

    $json_result = [];

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    $ID = get_if_set('ID');
    $accept = get_if_set('accept');

    //==================================================
    //      Requirements
    //==================================================

    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    if(check_admin($user_id)){
        die(output_ok("Admins do not have access to the calendar"));
    }

    //===============================
    //    Prepared statements
    //===============================

    $stmt = $conn->prepare("SELECT * FROM calendar_invite WHERE userID=? AND accepted=0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(!$ID && !$accept){
        if($result->num_rows == 0){
            output_ok("Could not find any invites");
        }
        if ($result->num_rows > 0) {
            $json_result[] = "Pending invites: ";
            while($row = $result->fetch_assoc()) {
                array_push($json_result,$row);
            }
            output_ok($json_result);
        }
    }

    //===============================
    // Accepts/Deletes invites
    //===============================

    if($ID){
        if($accept == "false"){
            $stmt = $conn->prepare("DELETE FROM calendar_invite WHERE ID=? AND accepted=0");
            $stmt->bind_param("i", $ID);
            $stmt->execute();

            output_ok("Invite declined");
        }
        else if($accept == "true"){
            $stmt = $conn->prepare("UPDATE calendar_invite SET accepted=1 WHERE ID=?");
            $stmt->bind_param("i", $ID);
            $stmt->execute();

            output_ok("Invite accepted");
        }
    }
?>