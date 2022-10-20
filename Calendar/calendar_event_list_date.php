<?php
    require_once('../db.php');

    $db = $conn;
    
    $evdate = $_GET['evdate'];
    $evend_date = $_GET['evend_date'];

    $sel = "SELECT * FROM calendar_event WHERE (date>? AND date<?) OR (end_date>? AND end_date<?)";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("ssss", $evdate, $evend_date, $evdate, $evend_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // || $_GET['end_date']
    if ($_GET['evdate'] == NULL ){
        $sel = "SELECT * FROM calendar_event";
        $selfraga = $db->query($sel);
        if ($selfraga->num_rows > 0) {
            while($row = $selfraga->fetch_assoc()) {
                $search = array("ID "=>$row["ID"], "date "=>$row["date"], "end_date "=>$row["end_date"], "Title "=>$row["title"], "description "=>$row["description"]);
                echo json_encode ($search);
                }
        }
    }
    else if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = array("ID "=>$row["ID"], "date "=>$row["date"], "end_date "=>$row["end_date"], "Title "=>$row["title"], "description "=>$row["description"]);
            echo json_encode($search);
            }
    } else {
        echo json_encode("0 results");
    }
?>