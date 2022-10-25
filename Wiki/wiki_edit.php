<?php
    require_once("../db.php");
    require_once("../token.php");
    $version ="1.0.1";
    //==============================
    //    Prepared statements
    //==============================

    $stmt = $conn->prepare("SELECT ID FROM service WHERE title = ? AND type = 'wiki'");
    $stmt->bind_param("s", $wiki_title);

    $stmt2 = $conn->prepare("UPDATE service SET title = ? WHERE ID = ?");
    $stmt2->bind_param("si", $new_wiki_title, $wiki_id);
    
    //==============================
    //          Variables
    //==============================
    $wiki_title = $_GET['wiki_title'];
    $new_wiki_title = $_GET['new_wiki_title'];

    //==============================
    //  Running statements to edit
    //==============================
    if(!empty($_GET['wiki_title'])) {

        
        $stmt->execute();
        $result_wid = $stmt->get_result();
        
        if($result_wid->num_rows == 1){
            $wiki_arr = $result_wid->fetch_assoc();
            $wiki_id = $wiki_arr['ID'];
        }
        $stmt->close();

        $stmt2->execute();
        if ($stmt2->affected_rows == 1) {
            $status = "OK";
            $json_result = ["Version "=>$version, "Status "=>$status, "Data "=>$new_wiki_title];
            echo json_encode($json_result);        
        } else {
            echo "Error: " . $stmt2 . "<br>" . $conn->error;
        }

        $stmt2->close();

    }
    $conn->close();
?>