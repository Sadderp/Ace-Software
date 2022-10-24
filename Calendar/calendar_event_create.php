<?php
    require_once('../db.php');

    $db = $conn;

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

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $date, $end_date, $title, $description);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        echo json_encode("Event created");
      } else {
        echo json_encode("Uh oh");
    };
?>