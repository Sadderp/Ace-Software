<?php
    // Creates connection
    require_once("../db.php");

    // Gets user data
   
    

    // Prepares statement
    $stmt = $conn->prepare("INSERT INTO service (title, type) VALUES (?, ?)");
    $stmt->bind_param("ss", $wiki_name, $type_wiki); 
    
    $stmt2 = $conn->prepare("INSERT INTO end_user (userID, serviceID) VALUES (?, ?)");
    $stmt2->bind_param("ii", $userid, $wikiid); 
    
    $stmt3 = $conn->prepare("SELECT ID FROM user WHERE username = ? AND password = ?");
    $stmt3->bind_param("ss", $user, $pass);  

    $stmt4 = $conn->prepare("SELECT ID FROM service WHERE title = ?");
    $stmt4->bind_param("s", $wiki_name);  


    // Creates wiki variables
    $wiki_name = $_GET['wiki_name'];
    $type_wiki = 'wiki';

    $json_ok = 'OK';
    $json_err = 'Error';

    //If statement to add the wiki to the database and show name after it gets added
    if(!empty($_GET['wiki_name'])) {
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            echo "Titel: ", json_encode($wiki_name); 
            echo "<br>";
            echo "Typ: ", json_encode($json_ok);       
        } else {
            echo "Typ: ", json_encode($json_err . $stmt . $conn->error);
        }

        $user = "spookiebruh";
        $pass = "hurrdurr1";

        $stmt3->execute();
        $resultuid = $stmt3->get_result();
        
        if($resultuid->num_rows == 1){
            $userid = $resultuid->fetch_array();
        }

        $stmt4->execute();
        $resultsid = $stmt4->get_result();

        if($resultsid->num_rows == 1){
            $wikiid = $resultsid->fetch_assoc();
        }
        
        $stmt2->execute();
        

    }
    



    $stmt3->close();
    $stmt4->close();
    $stmt->close();
    $stmt2->close();
    $conn->close();
?>