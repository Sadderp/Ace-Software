<?php
    require_once('../db.php');

    $db = $conn;

    if(!empty($_GET['serviceID'])){
        $serviceID = $_GET['serviceID'];
    };

    if(!empty($_GET['date'])){
        $date = $_GET['date'];
    };

    if(!empty($_GET['End_date'])){
        $End_date = $_GET['End_date'];
    };

    if(!empty($_GET['title'])){
        $title = $_GET['title'];
    };

    if(!empty($_GET['description'])){
        $description = $_GET['description'];
    };

    
    $sql = "INSERT INTO calendar_event(serviceID, date, End_date, title, description) VALUES (?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $serviceID, $date, $End_date, $title, $description);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        echo json_encode("Event created");
      } else {
        echo json_encode("Uh oh");
    };
?>