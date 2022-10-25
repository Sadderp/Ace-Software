<?php
    require_once('../db.php');

    $db = $conn;
    
    $evdate = $_GET['evdate'];
    $evend_date = $_GET['evend_date'];
    $userID = $_GET['userID'];

    $sel = "SELECT * FROM calendar_event WHERE (date>? AND date<?) OR (end_date>? AND end_date<?) AND userID=?";

    //prepared statement
    $stmt = $conn->prepare($sel);
    $stmt->bind_param("ssssi", $evdate, $evend_date, $evdate, $evend_date, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
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