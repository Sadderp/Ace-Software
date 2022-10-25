<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    $username = $_GET['username'];
    $token = $_GET['token'];

    //kollar info om eventet
    if(!empty($_GET['date'])){
        $date = $_GET['date'];
    };

    if(!empty($_GET['end_date'])){
        $end_date = $_GET['end_date'];
    };

    if(!empty($_GET['title'])){
        $title = $_GET['title'];
    };

    if(!empty($_GET['description'])){
        $description = $_GET['description'];
    };

    
    $sql = "INSERT INTO calendar_event(date, end_date, title, description) VALUES (?,?,?,?)";

    //prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $date, $end_date, $title, $description);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        echo json_encode("Version: "=>$version, "Type: "=>$ok, "Data: "=>"Event created");
      } else {
        echo json_encode("Version: "=>$version, "Type: "=>$error, "Data: "=>"Uh oh");
    };
?>