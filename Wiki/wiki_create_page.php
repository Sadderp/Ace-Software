<?php
    require_once("../db.php");
    require_once("../utility.php");
    $v = "0.0.2";

    // ! ! THIS PAGE IS MISSING USER VERIFICATION ! !

    //==============================
    //    Prepared statements
    //==============================

    // Create wiki page
    $sql = "INSERT INTO wiki_page (serviceID, title) VALUES (?,?)";
    $stmt_new_page = $conn->prepare($sql);
    $stmt_new_page->bind_param("is",$wiki_id,$page_title);

    // Check if user has wiki privileges
    $sql = "SELECT * FROM end_user WHERE userID = ? AND serviceID = ?";
    $stmt_check_perms = $conn->prepare($sql);
    $stmt_check_perms->bind_param("ii",$user_id,$wiki_id);
    
    //==============================
    //    Get variables
    //==============================
    $wiki_id = get_if_set('wiki_id');
    $user_id = get_if_set('user_id');
    $page_title = get_if_set('page_title');

    // Give error message if one or more inputs are blank
    if(!$wiki_id or !$user_id or !$page_title) {
        error_message($v,"Missing input - expected: 'wiki_id', 'user_id' and 'page_title'");
    }

    //==============================
    //    Check user permissions in wiki
    //==============================
    $stmt_check_perms->execute();
    if($stmt_check_perms->get_result()->num_rows == 0) {
        error_message($v,"User does not have permission to edit this page");
    }

    //==============================
    //    Create page
    //==============================
    $stmt_new_page->execute();
    
    // Check if operation is successful
    if($stmt_new_page->affected_rows === 1) {
        $result = ["version"=>$v, "status"=>"OK", "data"=>$page_title];
    } else {
        error_message($v,"Failed to add to database");
    }
    
    echo json_encode($result);

    $stmt_new_page->close();
    $stmt_check_perms->close();
?>