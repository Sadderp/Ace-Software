<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    if(!empty($_GET['userID'])&& !empty($_GET['token'])){
        $userID = $_GET['userID'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }

    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    $sql2 = "SELECT * FROM user WHERE ID=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("ss", $userID, $token);
    $statement->execute();
    $result2 = $statement->get_result();

    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        $result = ["Version"=>$version, "Status"=>$ok, "Data"=>"Please log in"];
        echo json_encode($result);
    }

    $del = "DELETE FROM calendar_event WHERE ID=?";
    
    //prepared statement
    $stmt = $conn->prepare($del);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $delete = ["Version"=>$version, "Status"=>$ok, "Data"=>"event removed"];
    echo json_encode($delete);
?>