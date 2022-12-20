<?php
    require_once("db.php");
    require_once("utility.php");
    require_once("verify_token.php");

    //==============================
    //    Prepared statements
    //==============================

    $stmt = $conn->prepare("SELECT * FROM user WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    

    //==============================
    //    Get variables
    //==============================

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$user_id or !$token) {
        output_error("Missing input(s) - expected: 'user_id' and 'token'");
    }

    // ban_id and user_id must be numeric
    if(!is_numeric($user_id)) {
        output_error($num_error);
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error($token_error);
    }


    //==============================
    //    Get users
    //==============================

    $output = [];
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        output_error("Could not get user");
    }

    while($row = $result->fetch_assoc()) {
        $output[] = ['id'=>$row['ID'], 'display_name'=>$row['displayname'], 'username'=>$row['username'], 'banned'=>$row['ban'], 'admin'=>$row['admin']];
    }

    // Output
    output_ok($output);
?>