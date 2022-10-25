<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.2";
    $ok = "OK";
    $error = "Error";

    $username = $_GET['username'];
    $token = $_GET['token'];
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
        echo json_encode("Version: "=>$version, "Type: "=>$ok, "Data: "=>"Invite created");
      } else {
        echo json_encode("Version: "=>$version, "Type: "=>$error, "Data: "=>"Uh oh");
    };
?>