<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    require_once("../utility.php");

    if(!empty($_GET['user_id']) && !empty($_GET['token'])){
        $user_id = $_GET['user_id'];
        $token = $_GET['token'];
    }else{
        output_error("You need to log in");
    }

    if(!verify_token($user_id, $token)){
        output_error("Token is invalid or expired");
    }

    //===============================
    //    Prepared statements
    //===============================
    $sql = "SELECT * FROM user WHERE ID=? AND token=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $token);

    $sql2 = "SELECT * FROM calendar_event WHERE userID=? AND ID=?";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ii", $user_id, $eventID);

    $sql3 = "UPDATE calendar_event SET date=? WHERE ID=?";

    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("si", $date, $eventID);

    $sql4 = "UPDATE calendar_event SET end_date=? WHERE ID=?";

    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("si", $end_date, $eventID);

    $sql5 = "UPDATE calendar_event SET title=? WHERE ID=?";

    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("si", $title, $eventID);

    $sql6 = "UPDATE calendar_event SET description=? WHERE ID=?";

    $stmt6 = $conn->prepare($sql6);
    $stmt6->bind_param("si", $description, $eventID);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $user_id = $row['ID'];
            }
    }else {
        output_error("No user");
    }

    if(!empty($_GET['ID'])){
        $eventID = $_GET['ID'];
    }
    
    //===============================
    //    Updates the event
    //===============================
    $stmt2->execute();
    $result = $stmt2->get_result();
    if($stmt2->affected_rows == 1){
        if(!empty($_GET['date'])){
            $date  = $_GET['date'];
            $stmt3->execute();

            $json_result = ("Date updated");
            array_push($data, $json_result);
        }
        if(!empty($_GET['end_date'])){
            $end_date  = $_GET['end_date'];
            $stmt4->execute();

            $json_result = ("End date updated");
            array_push($data, $json_result);
        }
        if(!empty($_GET['title'])){
            $title  = $_GET['title'];
            $stmt5->execute();

            $json_result = ("Title updated");
            array_push($data, $json_result);
        }
        if(!empty($_GET['description'])){
            $description = $_GET['description'];
            $stmt6->execute();

            $json_result = ("Description updated");
            array_push($data, $json_result);
        }else{
            output_ok("Please write what you want to edit");
        }
    }
    output_ok($data);
?>