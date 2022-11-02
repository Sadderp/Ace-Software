<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");
    
    $title = get_if_set('title');

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    if(!$user_id && !$token){
        output_ok("You need fill in user_id and token")
    }
    
    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    //===============================
    //    Prepared statements
    //===============================
    $sql = "SELECT * FROM user WHERE ID=? AND token=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $token);

    $sql2 = "SELECT * FROM calendar_event INNER JOIN calendar_invite ON calendar_event.userID!=calendar_invite.userID 
    WHERE calendar_invite.userID=? AND calendar_event.ID=calendar_invite.eventID";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $user_id);

    $sql3 = "SELECT * FROM calendar_event WHERE userID=? AND title LIKE ?";

    $stmt3 = $conn->prepare($sql3);
    $title = "%".$title."%";
    $stmt3->bind_param("is", $user_id, $title);


    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $user_id = $row['ID'];
            }
    }else {
        output_error("User does not exist");
    }
   
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $stmt3->execute();
    $result3 = $stmt3->get_result();

    //===============================
    //    Lists your own events
    //===============================
    if($result3->num_rows == 0){
        die(output_ok("You have not created any events yet"));
    }
    if ($result3->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $json_result[] = "ID: ".$row["ID"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"];
            }
    }
    //===============================
    //    Lists invites to events
    //===============================
    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $json_result[] = "Invited to:"." ID: ".$row["eventID"]." by: "." user_id: ".$row["user_id"]. " Date: ".$row["date"]. " End_date: ".$row["end_date"]. " Title: ".$row["title"]. " Description: ".$row["description"];
            }
    }
    output_ok($json_result);
?>