<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../token.php");
    $version = "1.0.2";
    
    //==============================
    //    Prepared statements
    //==============================
    $stmt = $conn->prepare("INSERT INTO service (title, type) VALUES (?, ?)");
    $stmt->bind_param("ss", $wiki_name, $type_wiki); 

    $stmt3 = $conn->prepare("SELECT ID FROM service WHERE title = ?");
    $stmt3->bind_param("s", $wiki_name);  

    $stmt4 = $conn->prepare("INSERT INTO end_user (userID, serviceID) VALUES (?, ?)");


    //==============================
    //    Creating variables
    //==============================
    $wiki_name = $_GET['wiki_name'];
    $type_wiki = 'wiki';
    $user_id = $_GET['user_id'];

    //==============================
    // Creates wiki in service table
    //==============================
    if(!empty($_GET['wiki_name'])) {
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            $status = "OK";
            $json_result = ["Version"=>$version, "Status"=>$status, "Data"=>$wiki_name];
            echo json_encode($json_result);        
        } else {
            error_message("Failed to add to database");
        }
        $stmt->close();

        $wiki_name = $_GET['wiki_name'];
        
        $stmt3->execute();
        $resultsid = $stmt3->get_result();

        if($resultsid->num_rows == 1){
            $wiki_arr = $resultsid->fetch_assoc();
            $wiki_id = $wiki_arr['ID'];
        }
        $stmt3->close();
        
        $stmt4->bind_param("ii", $user_id, $wiki_id); 
        $stmt4->execute();
        $stmt4->close();

    }
    
    $conn->close();
?>