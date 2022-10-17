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

    $sel = "SELECT * FROM calendar_event";
    $selfraga = $db->query($sel) or die("Could not search");
    $print = mysqli_fetch_all($selfraga, MYSQLI_ASSOC);
    echo json_encode($print);
?>