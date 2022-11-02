<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

    //==============================
    //    Prepared statements
    //==============================

    $sql = "UPDATE user SET ban = 1 WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$ban_id);

    //==============================
    //    Get variables
    //==============================

    $ban_id = get_if_set('ban_id');
    $ban_reason = get_if_set('ban_reason'); // UNUSED
    $ban_duration = get_if_set('ban_duration'); // UNUSED
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$ban_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'ban_id', 'user_id' and 'token'");
    }

    // ban_id and user_id must be numeric
    if(!is_numeric($ban_id) or !is_numeric($user_id)) {
        output_error("'ban_id' and 'user_id' are not numeric");
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
    //    Ban user
    //==============================

    $stmt->execute();

    if($stmt->affected_rows == 0) {
        output_error("Failed to ban user. Either the user doesn't exist or there was an issue connecting to the database. We're not really sure");
    }

    output_ok("User was banned");
?>