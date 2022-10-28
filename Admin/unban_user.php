<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    $version = "0.0.1";

    //==============================
    //    Prepared statements
    //==============================

    $sql = "UPDATE user SET ban = 0 WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$ban_id);

    //==============================
    //    Get variables
    //==============================

    $ban_id = get_if_set('ban_id');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$ban_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'ban_id', 'user_id' and 'token'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired, please refresh your login.");
    }

    // User must be admin
    if(!check_admin($user_id)) {
        output_error("You must be an admin to delete a wiki.");
    }

    //==============================
    //    Unban user
    //==============================

    $stmt->execute();

    if($stmt->affected_rows == 0) {
        output_error("Failed to unban user. Either the user doesn't exist or there was an issue connecting to the database. We're not really sure");
    }

    output_ok("User was unbanned");
?>