<?php

    /**
     * create_wiki.php
     * 
     * Create a new Wiki service
     */

    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

    //==============================
    //    Prepared statements
    //==============================

    $stmt = $conn->prepare("INSERT INTO service (title, type, visibility) VALUES (?, 'wiki', ?)");
    $stmt->bind_param("ss", $wiki_name, $visibility); 

    //==============================
    //    Get variables
    //==============================

    $wiki_name = get_if_set('wiki_name');
    $visibility = get_if_set('visibility');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$wiki_name or !$user_id or !$visibility or !$token) {
        output_error("Missing input - expected: 'wiki_name', 'visibility', 'user_id' and 'token'");
    }

    // user_id input must be numeric
    if(!is_numeric($user_id)) {
        output_error($num_error);
    }
    
    // Visibility must be either private or public
    if($visibility != 'public' and $visibility != 'private') {
        output_error("'visibility' must be set to either 'private' or 'public'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error($token_error);
    }

    // User must be admin
    if(!check_admin($user_id)) {
        output_error("You must be an admin to create a wiki. If you think this is dumb, please file a complaint to The Provider.");
    }

    //==============================
    //    Creates wiki in service table
    //==============================

    $stmt->execute();

    if($stmt->affected_rows == 0) {
        output_error("Failed to add to database");
    }
    // Get wiki ID
    $wiki_id = $stmt->insert_id;

    // Output
    $output = ["text"=>"Successfully created wiki","id"=>$wiki_id,"name"=>$wiki_name];
    output_ok($output);
?>