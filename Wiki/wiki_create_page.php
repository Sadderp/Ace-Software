<?php
    require_once("../db.php");
    require_once("../utility.php");
    $version = "0.0.1";

    // ! ! THIS PAGE IS MISSING USER VERIFICATION ! !

    //==============================
    //    Prepared statements
    //==============================
    $stmt = $conn->prepare("INSERT INTO wiki_page (serviceID, title) VALUES (?,?)");
    $stmt->bind_param("is",$wiki_id,$page_title);
    
    //==============================
    //    Get variables
    //==============================
    $wiki_id = get_if_set('wiki_id');
    $page_title = get_if_set('page_title');

    //==============================
    //    Create page and return status
    //==============================

    // Give error message if one or more inputs are blank
    if(!$wiki_id or !$page_title) {
        $result = ["version"=>$version, "status"=>"ERROR", "data"=>"Missing input - expected: 'wiki_id' and 'page_title'"];
        die(json_encode($result));
    }

    $stmt->execute();
    
    // Check if operation is successful
    if($stmt->affected_rows === 1) {
        $result = ["version"=>$version, "status"=>"OK", "data"=>$page_title];
    } else {
        $result = ["version"=>$version, "status"=>"ERROR", "data"=>"Failed to add to database"];
    }
    
    echo json_encode($result);
?>