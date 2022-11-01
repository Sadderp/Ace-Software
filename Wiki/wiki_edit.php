<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../token.php");
    $version ="0.0.2";

    //==============================
    //    Prepared statements
    //==============================

    $sql = "UPDATE service SET title = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_wiki_title, $wiki_id);
    
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
        output_error("'wiki_id' and 'user_id' are not numeric")
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
    }

    // User must be admin or manager
    if(!check_admin($user_id) and !check_manager($user_id)) {
        output_error("You must be an admin or manager to delete a wiki.");
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

    output_ok("Successfully edited wiki title");
?>