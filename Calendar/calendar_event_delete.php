<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    
    //===============================
    //    Checks user_id and token
    //===============================
    if(!empty($_GET['user_id'])&& !empty($_GET['token'])){
        $user_id = $_GET['user_id'];
        $token = $_GET['token'];
    }else{
        output_ok("You need to log in")
    }
    
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    //===============================
    //    Prepared statements
    //===============================

    $sql = "DELETE FROM calendar_event WHERE ID=? AND userID=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ID, $user_id);

    $sql2 = "DELETE FROM calendar_invite WHERE eventID=?";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $ID);

    $sql3 = "DELETE FROM calendar_invite WHERE eventID=? AND userID=?";

    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("ii", $ID, $user_id);

    //===============================
    // Deletes invites and/or events
    //===============================

    $stmt2->execute();
    if($stmt2->affected_rows == 0){
        echo("Couldn't delete invite");
    }
    $stmt2->close();    
    $stmt->execute();
    
    if ($stmt->affected_rows == 1){
        output_ok("event removed");
    }else if ($stmt->affected_rows == 0){
        $stmt3->execute();
        if ($stmt3->affected_rows == 1){
            output_ok("Invite removed");
        }else{
            output_ok("You can't delete someone else's event");
        } 
        $stmt3->close();
    }
    $stmt->close();
?>