<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
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
        error_message("Missing input - expected: 'wiki_id', 'user_id' and 'page_title'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        error_message("Token is invalid or expired");
    }

    // Page must be a wiki
    if(!verify_service_type($wiki_id,"wiki")) {
        error_message("Service is not a wiki");
    }
    
    //==============================
    //    Create page
    //==============================

    $stmt->execute();
    
    // Check if operation is successful
    if($stmt->affected_rows == 0) {
        error_message("Failed to add to database");
    }

    $result = ["Version"=>$version, "Status"=>"OK", "Data"=>$page_title];
    echo json_encode($result);

    $stmt->close();
?>