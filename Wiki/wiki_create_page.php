<?php
    require_once("../db.php");
    require_once("../utility.php");
    $version = "0.0.5";

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
        output_error("Missing input - expected: 'wiki_id', 'user_id' and 'page_title'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
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

    output_ok($page_title);

    $stmt->close();
?>