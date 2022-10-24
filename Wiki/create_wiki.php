<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.1";
    
    //==============================
    //    Prepared statements
    //==============================
    $stmt = $conn->prepare("INSERT INTO service (title, type) VALUES (?, ?)");
    $stmt->bind_param("ss", $wiki_name, $type_wiki); 

    $stmt2 = $conn->prepare("SELECT ID FROM user WHERE username = ? AND password = ?");
    $stmt2->bind_param("ss", $user, $pass);  
    
    $stmt3 = $conn->prepare("SELECT ID FROM service WHERE title = ?");
    $stmt3->bind_param("s", $wiki_name);  

    $stmt4 = $conn->prepare("INSERT INTO end_user (userID, serviceID) VALUES (?, ?)");


    //==============================
    //    Creating variables
    //==============================
    $wiki_name = $_GET['wiki_name'];
    $type_wiki = 'wiki';

    //==============================
    // Creates wiki in service table
    //==============================
    if(!empty($_GET['wiki_name'])) {
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            $status = "OK";
            $json_result = ["Version: "=>$version, "Status: "=>$status, "Data: "=>$wiki_name];
            echo json_encode($json_result);        
        } else {
            echo "Error: " . $stmt . "<br>" . $conn->error;
        }

        $user = "spookiebruh";
        $pass = "hurrdurr1";

        $stmt2->execute();
        $resultuid = $stmt2->get_result();
        
        if($resultuid->num_rows == 1){
            $user_arr = $resultuid->fetch_assoc();
            $user_id = $user_arr['ID'];
        }

        $stmt3->execute();
        $resultsid = $stmt3->get_result();

        if($resultsid->num_rows == 1){
            $wiki_arr = $resultsid->fetch_assoc();
            $wiki_id = $wiki_arr['ID'];
        }
        
        $stmt4->bind_param("ii", $user_id, $wiki_id); 
        $stmt4->execute();
        

    }
    



    $stmt3->close();
    $stmt4->close();
    $stmt->close();
    $stmt2->close();
    $conn->close();
?>