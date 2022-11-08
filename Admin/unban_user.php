<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");

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

    // ban_id and user_id must be numeric
    if(!is_numeric($ban_id) or !is_numeric($user_id)) {
        output_error($num_error);
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error($token_error);
    }

    // User must be admin
    if(!check_admin($user_id)) {
        output_error($permission_error);
    }

    //==============================
    //    Unban user
    //==============================

    $stmt->execute();

    if($stmt->affected_rows == 0) {
        output_error("Failed to unban user. Either the user doesn't exist or there was an issue connecting to the database. We're not really sure");
    }

    // Output
    $output = ["text"=>"User was unbanned","id"=>$ban_id];
    output_ok($output);
?>