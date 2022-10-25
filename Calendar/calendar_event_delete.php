<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    $username = $_GET['username'];
    $token = $_GET['token'];
    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    $del = "DELETE FROM calendar_event WHERE ID=?";
    
    //prepared statement
    $stmt = $conn->prepare($del);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode("Version: "=>$version, "Type: "=>$ok, "Data: "=>"Borta?");
?>