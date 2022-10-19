<?php
    require_once('../db.php');

    $db = $conn;
    
    $title = $_GET['title'];
    

    $sel = "SELECT * FROM calendar_event WHERE title=?";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($_GET['title'] == NULL){
        $sel = "SELECT * FROM calendar_event";
        $selfraga = $db->query($sel);
        if ($selfraga->num_rows > 0) {
            while($row = $selfraga->fetch_assoc()) {
                $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "End_date "=>$row["End_date"], "Title "=>$row["title"], "description "=>$row["description"]);
                echo json_encode($search);
                }
        } else {
            echo json_encode("0 results");
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