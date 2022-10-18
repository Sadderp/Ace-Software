<?php
    require_once('../db.php');

    $db = $conn;

    if(!empty($_GET['title'])){
        $title = $_GET['title'];
    }

    $sel = "SELECT * FROM calendar_event WHERE title=?";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($title = '*'){
        $sel = "SELECT * FROM calendar_event";
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "End_date "=>$row["End_date"], "Title "=>$row["title"], "description "=>$row["description"]);
            echo json_encode($search);
            }
    } else {
        echo json_encode("0 results");
    }
?>