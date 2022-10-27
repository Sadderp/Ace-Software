<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.2";
    $ok = "OK";
    $error = "Error";

    if(!empty($_GET['username'])&& !empty($_GET['token'])){
        $username = $_GET['username'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }

    $sql2 = "SELECT * FROM user WHERE username=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("ss", $username, $token);
    $statement->execute();
    $result3 = $statement->get_result();

    if ($result->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        echo json_encode("No user");
    }

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
        $json_result = ["Version: "=>$version, "Status: "=>$ok, "Data: "=>"invite created"];
        echo json_encode($json_result);
      } else {
        $json_result = ["Version: "=>$version, "Status: "=>$error, "Data: "=>"uh oh"];
        echo json_encode($json_result);
    };
?>