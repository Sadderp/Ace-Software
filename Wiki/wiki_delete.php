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
        error_message("Missing input(s) - expected: 'wiki_id', 'user_id' and 'token'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        error_message("Token is invalid or expired");
    }

    // User must be admin
    if(!check_admin($user_id)) {
        error_message("You must be an admin to delete a wiki.");
    }

    // Page must be a wiki
    if(!verify_service_type($wiki_id,'wiki')) {
        error_message("Not a wiki");
    }
    
    //=====================================
    //  Deletes from service and end_user
    //=====================================

    $stmt->execute();

    if ($stmt->affected_rows == 0) {
        error_message("Failed to delete");
    }

    $status = "OK";
    $json_result = ["Version"=>$version, "Status"=>$status, "Data"=>"Successfully deleted Wiki (ID " . $wiki_id . ")"];
    echo json_encode($json_result);        

    $stmt->close();
?>