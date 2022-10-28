<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    $version = "0.0.1";

    //==============================
    //    Prepared statements
    //==============================

    $sql = "UPDATE service SET managerID = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$manager_id,$wiki_id);

    //==============================
    //    Get variables
    //==============================

    $wiki_id = get_if_set('wiki_id');
    $manager_id = get_if_set('manager_id');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$wiki_id or !$manager_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'wiki_id', 'manager_id', 'user_id' and 'token'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired, please refresh your login.");
    }

    // Service must be a wiki
    if(!verify_service_type($wiki_id,'wiki')) {
        output_error("Service is not a wiki!");
    } 

    // User must be admin
    if(!check_admin($user_id) and !check_manager($wiki_id)) {
        output_error("You must be an admin or manager to appoint a manager.");
    }

    // Manager account must exist
    if(!verify_account_existance($manager_id)) {
        output_error("User could not be appointed because their account does not exist");
    }

    // User must not be banned
    if(check_ban_status($manager_id)) {
        output_error("User could not be appointed due to being banned.");
    }

    //==============================
    //    Appoint manager
    //==============================

    $stmt->execute();

    if($stmt->affected_rows == 0) {
        output_error("Failed to appoint manager");
    }

    output_ok("User was successfully appointed.");
?>