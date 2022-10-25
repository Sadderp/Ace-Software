<?php
    require_once('../db.php');

    $db = $conn;

    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    $del = "DELETE FROM calendar_event WHERE ID=?";
    
    //prepared statement
    $stmt = $conn->prepare($del);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode("Borta?");
?>