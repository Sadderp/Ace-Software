<?php
    require_once('../db.php');

    $db = $conn;
    
    $date = $_GET['date'];
    $end_date = $_GET['end_date'];

    $sel = "SELECT * FROM calendar_event WHERE (date>? AND end_date<?) OR (date<? OR end_date>?) OR (date<? AND end_date>?)";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("ss", $date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // || $_GET['end_date']
    if ($_GET['date'] == NULL ){
        $sel = "SELECT * FROM calendar_event";
        $selfraga = $db->query($sel);
        if ($selfraga->num_rows > 0) {
            while($row = $selfraga->fetch_assoc()) {
                $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "end_date "=>$row["end_date"], "Title "=>$row["title"], "description "=>$row["description"]);
                echo json_encode($search);
                }
        }
    }
    else if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "end_date "=>$row["end_date"], "Title "=>$row["title"], "description "=>$row["description"]);
            echo json_encode($search);
            }
    } else {
        echo json_encode("0 results");
    }
?>