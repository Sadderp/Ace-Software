<?php

    /**
     * wiki_edit.php
     * 
     * Edit the title of a wiki service
     */

    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

    //==============================
    //    Prepared statements
    //==============================

    $sql = "UPDATE service SET title = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_title, $wiki_id);
    
    //==============================
    //      Get Variables
    //==============================

    $wiki_id = get_if_set('wiki_id');
    $new_title = get_if_set('new_title');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //      Requirements
    //==============================

    // All input variables must be set
    if(!$wiki_id or !$new_title or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'wiki_id', 'new_title', 'user_id' and 'token'");
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
        output_error("Service is not a wiki");
    }

    //==============================
    //  Running statements to edit
    //==============================
        
    $stmt->execute();
    
    if($stmt->affected_rows == 0){
        output_error("Failed to edit wiki");
    }

    // Output
    $output = ["text"=>"Successfully edited wiki title","id"=>$wiki_id];
    output_ok($output);
?>