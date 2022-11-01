<?php
    require_once("../db.php");
    require_once("../verify_token.php");
    $version = "0.0.2";
    $ok = "OK";
    $error = "Error";
    $data = [];

    if(!empty($_GET['userID']) && !empty($_GET['token'])){
        $userID = $_GET['userID'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }

    verify_token($userID, $token);


    $sql = "SELECT * FROM user WHERE ID=? AND token=?";

    $stmt = $conn->prepare($sql2);
    $stmt->bind_param("is", $userID, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        echo json_encode("No user");
    }

    if(!empty($_GET['date'])){
        $date  = $_GET['date'];

        $sql = "UPDATE calendar_event SET date=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        echo("date");

        $json_result = ["Date updated"];
        array_push($data, $json_result);
    }
    if(!empty($_GET['end_date'])){
        $date  = $_GET['end_date'];

        $sql = "UPDATE calendar_event SET end_date=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        echo("end_date");

        $json_result = ["End date updated"];
        array_push($data, $json_result);
    }
    if(!empty($_GET['title'])){
        $date  = $_GET['title'];

        $sql = "UPDATE calendar_event SET title=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $title);
        $stmt->execute();
        $result = $stmt->get_result();

        echo("title");

        $json_result = ["Title updated"];
        array_push($data, $json_result);
    }
    if(!empty($_GET['description'])){
        $date  = $_GET['description'];

        $sql = "UPDATE calendar_event SET description=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $description);
        $stmt->execute();
        $result = $stmt->get_result();

        echo("desck");

        $json_result = ["Description updated"];
        array_push($data, $json_result);
    }else{
        $json_result = ["uh oh"];
        array_push($data, $json_result);
    }
    $result = ["Version"=>$version, "Status"=>$error, "Data"=>$data]
?>