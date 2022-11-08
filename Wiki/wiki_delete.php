<?php

    /**
     * wiki_delete.php
     * 
     * Permanently delete a wiki service
     */

    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

    //==============================
    //     Prepared statements
    //==============================

    $stmt1 = $conn->prepare("SELECT ID FROM wiki_page WHERE serviceID = ?");
    $stmt1->bind_param("i", $wiki_id);

    $stmt2 = $conn->prepare("DELETE FROM content WHERE pageID = ?");
    $stmt2->bind_param("i", $wiki_pID);

    $stmt3 = $conn->prepare("DELETE FROM wiki_page_version WHERE pageID = ?");
    $stmt3->bind_param("i", $wiki_pID);

    $stmt4 = $conn->prepare("DELETE FROM wiki_page WHERE serviceID = ?");
    $stmt4->bind_param("i", $wiki_id);

    $stmt5 = $conn->prepare("DELETE FROM service WHERE ID = ? AND type = 'wiki'");
    $stmt5->bind_param("i", $wiki_id);

    $stmt6 = $conn->prepare("DELETE FROM end_user WHERE serviceID = ?");
    $stmt6->bind_param("i", $wiki_id);
 
    //==============================
    //      Get variables
    //==============================

    $wiki_id = get_if_set('wiki_id');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //      Requirements
    //==============================

    // All input variables must be set
    if(!$wiki_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'wiki_id', 'user_id' and 'token'");
    }

    // wiki_id and user_id must be numeric
    if(!is_numeric($wiki_id) or !is_numeric($user_id)) {
        output_error($num_error);
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error($token_error);
    }

    // User must not be banned
    if(check_ban($user_id)) {
        output_error($ban_error);
    }

    // User must be admin or manager
    if(!check_admin($user_id) and !check_manager($user_id)) {
        output_error($permission_error);
    }

    // Service must be a wiki
    if(!verify_service_type($wiki_id,'wiki')) {
        output_error("Not a wiki");
    }
    
    //=====================================
    //  Deletes from service and end_user
    //=====================================

    $stmt1->execute();

    if ($stmt1->affected_rows == 0) {
        error_message("Failed to fetch pageID");
    }

    $result_pID = $stmt1->get_result();
        
    if($result_pID->num_rows == 1){
        $pID_arr = $result_pID->fetch_assoc();
        $wiki_pID = $pID_arr['ID'];
    }
    $stmt1->close();

    $stmt2->execute();

    if ($stmt2->affected_rows == 0) {
        error_message("Failed to delete content");
    }

    $stmt3->execute();

    if ($stmt3->affected_rows == 0) {
        error_message("Failed to delete wiki page versions");
    }

    $stmt4->execute();

    if ($stmt4->affected_rows == 0) {
        error_message("Failed to delete wiki page");
    }

    $stmt5->execute();

    if ($stmt5->affected_rows == 0) {
        error_message("Failed to delete wiki");
    }

    $stmt6->execute();


    if ($stmt6->affected_rows == 0) {
        error_message("Failed to delete end user");

    }

    $output = ["text"=>"Successfully deleted wiki","id"=>$wiki_id];
    output_ok($output);      
?>