<?php
    require_once('../db.php');

    if(!empty($_GET['userID'])){
        $userID = $_GET['userID'];
    };

    if(!empty($_GET['eventID'])){
        $eventID = $_GET['eventID'];
    };

    $sql = "INSERT INTO calendar_invite(userID, eventID) VALUES (?,?)";

    //prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userID, $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($stmt->affected_rows === 1) {
        echo json_encode("Invite created");
      } else {
        echo json_encode("Uh oh");
    };
?>