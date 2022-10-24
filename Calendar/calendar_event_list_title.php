<?php
    require_once('../db.php');

    $db = $conn;
    
    $title = $_GET['title'];
    $userID = $_GET['userID'];
    

    $sel = "SELECT * FROM calendar_event WHERE userID=? OR title=?";

    $stmt = $conn->prepare($sel);
    $stmt->bind_param("is", $userID, $title);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search = array("ID "=>$row["ID"],"Title "=>$row["title"],"date "=>$row["date"], "end_date "=>$row["end_date"], "Title "=>$row["title"], "description "=>$row["description"]);
            echo json_encode($search);
            }
    }else {
        echo json_encode("0 results");
    }
?>