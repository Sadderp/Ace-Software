<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    $version = "0.0.3";
    
    //==============================
    //    Prepared statements
    //==============================

    $stmt_create = $conn->prepare("INSERT INTO service (title, type, visibility) VALUES (?, 'wiki', ?)");
    $stmt_create->bind_param("ss", $wiki_name, $visibility); 

    $stmt_end_user = $conn->prepare("INSERT INTO end_user (userID, serviceID) VALUES (?, ?)");
    $stmt_end_user->bind_param("ii", $user_id, $wiki_id);

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

    // Make sure all variables are set;
    if(!$wiki_name or !$user_id or !$visibility or !$token) {
        output_error("Missing input - expected: 'wiki_name', 'visibility', 'user_id' and 'token'");
    }
    
    // Make sure visibility is either private or public
    if($visibility != 'public' and $visibility != 'private') {
        output_error("'visibility' must be set to either 'private' or 'public'");
    }

    // Check token validity
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
    }

    // Check if user is admin
    if(!check_admin($user_id)) {
        output_error("You must be an admin to create a wiki. If you think this is dumb, please file a complaint to The Provider.");
    }

    //==============================
    //    Creates wiki in service table
    //==============================

    $stmt_create->execute();

    if($stmt_create->affected_rows == 0) {
        output_error("Failed to add to database");
    }

    // Get wiki ID
    $wiki_id = $stmt_create->insert_id;

    //==============================
    //     Set end user
    //==============================
    $stmt_end_user->execute();

    if($stmt_end_user->affected_rows == 0) {
        output_error("Failed to set end user");
    }

    // JSON
    output_ok($wiki_name);

    // Close statements
    $stmt_create->close();
    $stmt_end_user->close();
?>