<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

    //==============================
    //    Prepared statements
    //==============================

    // Create wiki page
    $sql = "INSERT INTO wiki_page (serviceID, title) VALUES (?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is",$wiki_id,$page_title);
    
    //==============================
    //    Get variables
    //==============================

    $wiki_id = get_if_set('wiki_id');
    $page_title = get_if_set('page_title');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$wiki_id or !$page_title or !$user_id or !$token) {
        output_error("Missing input - expected: 'wiki_id', 'page_title', 'user_id' and 'token'");
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

    // Page must be a wiki
    if(!verify_service_type($wiki_id,"wiki")) {
        output_error("Service is not a wiki");
    }
    
    //==============================
    //    Create page
    //==============================

    $stmt->execute();
    
    // Check if operation is successful
    if($stmt->affected_rows == 0) {
        output_error("Failed to add to database");
    }

    output_ok("Successfully added page: " . $page_title);
?>