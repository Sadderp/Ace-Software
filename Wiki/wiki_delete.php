<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    $version = "0.0.2";
    
    //==============================
    //     Prepared statements
    //==============================

    $stmt = $conn->prepare("DELETE FROM service WHERE ID = ?");
    $stmt->bind_param("i", $wiki_id);
 
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

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
    }

    // User must be admin
    if(!check_admin($user_id)) {
        output_error("You must be an admin to delete a wiki.");
    }

    // Page must be a wiki
    if(!verify_service_type($wiki_id,'wiki')) {
        output_error("Not a wiki");
    }
    
    //=====================================
    //  Deletes from service and end_user
    //=====================================

    $stmt->execute();

    if ($stmt->affected_rows == 0) {
        output_error("Failed to delete");
    }

    output_ok("Successfully deleted Wiki (ID " . $wiki_id . ")");      

    $stmt->close();
?>