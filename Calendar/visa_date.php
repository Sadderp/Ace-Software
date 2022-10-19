<?php
    require_once('../db.php');

    $db = $conn;
    
    $date = $_GET['date'];
    $End_date = $_GET['End_date'];

    $sel = "SELECT * FROM calendar_event WHERE (date>? AND End_date<?) OR (date<? OR End_date>?) OR (date<? AND End_date>?)";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("ss", $date, $End_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // || $_GET['End_date']
    if ($_GET['date'] == NULL ){
        $sel = "SELECT * FROM calendar_event";
        $selfraga = $db->query($sel);
        if ($selfraga->num_rows > 0) {
            while($row = $selfraga->fetch_assoc()) {
                $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "End_date "=>$row["End_date"], "Title "=>$row["title"], "description "=>$row["description"]);
                echo json_encode($search);
                }
        }
    }
    else if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "End_date "=>$row["End_date"], "Title "=>$row["title"], "description "=>$row["description"]);
            echo json_encode($search);
            }
    } else {
        echo json_encode("0 results");
    }
?>