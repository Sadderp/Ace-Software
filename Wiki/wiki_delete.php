<?php
    require_once("../db.php");
    require_once("../token.php");
    $version = "0.0.1";
    
    //==============================
    //     Prepared statements
    //==============================
    $stmt = $conn->prepare("SELECT ID FROM service WHERE title = ?");

    $stmt2 = $conn->prepare("DELETE FROM service WHERE title = ? AND type = ?");
    $stmt2->bind_param("ss", $wiki_title, $wiki_type);
    
    $stmt3 = $conn->prepare("DELETE FROM end_user WHERE serviceID = ?");

    //==============================
    //          Variables
    //==============================
    $wiki_title = $_GET['wiki_title'];
    $wiki_type = "wiki";

    
    //=====================================
    //  Deletes from service and end_user
    //=====================================
    if(!empty($_GET['wiki_title'])) {


        $stmt->bind_param("s", $wiki_title);
        $stmt->execute();
        $result_wid = $stmt->get_result();
        
        if($result_wid->num_rows == 1){
            $wiki_arr = $result_wid->fetch_assoc();
            $wiki_id = $wiki_arr['ID'];
        }
        
        $stmt3->bind_param("i", $wiki_id); 
        $stmt3->execute();

        $stmt2->execute();
        if ($stmt2->affected_rows == 1) {
            $status = "OK";
            $json_result = ["Version "=>$version, "Status "=>$status, "Data "=>$wiki_title];
            echo json_encode($json_result);        
        } else {
            echo "Error: " . $stmt2 . "<br>" . $conn->error;
        }



    }


    $stmt->close();
    $stmt2->close();
    $stmt3->close();
?>