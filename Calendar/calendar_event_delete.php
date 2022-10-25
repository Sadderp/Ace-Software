<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.8";
    $ok = "OK";
    $error = "Error";

    $db = $conn;

    if(!empty($_GET['username'])&& !empty($_GET['token'])){
        $username = $_GET['username'];
        $token = $_GET['token'];
    }else{
        echo json_encode(["Version: "=>$version, "Type: "=>$error, "Data: "=>"You need to log in"]);
    }

    if(!empty($_GET['ID'])){
        $ID = $_GET['ID'];
    }

    $sql2 = "SELECT * FROM user WHERE username=? AND token=?";

    $statement = $conn->prepare($sql2);
    $statement->bind_param("ss", $username, $token);
    $statement->execute();
    $result3 = $statement->get_result();

    if ($result->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $userID = $row['ID'];
            }
    }else {
        echo json_encode("Don't try to delete someone else's event");
    }

    $del = "DELETE FROM calendar_event WHERE ID=?";
    
    //prepared statement
    $stmt = $conn->prepare($del);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $delete = ["Version: "=>$version, "Status: "=>$error, "Data: "=>"event removed"];
    echo json_encode($json_result);
?>