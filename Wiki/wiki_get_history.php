<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");
    $version = "0.0.4";

    /**
     * wiki_get_history.php
     * 
     * Get all the version and content data of a wiki page.
     * Only accessible to wiki end users
     */

    //==============================
    //    Prepared statements
    //==============================
    $sql = "SELECT COUNT(ID) AS 'versions' FROM wiki_page_version WHERE pageID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$page_id);

    //==============================
    //    Get variables
    //==============================
    $page_id = get_if_set('page_id');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    if(!$page_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'page_id', 'user_id' and 'token'");
    }

    //==============================
    //    Check user permissions
    //==============================

    // Token verification
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired, please refresh your login.");
    }

    //==============================
    //    Get page history
    //==============================
    $stmt->execute();
    $v_count = mysqli_fetch_assoc($stmt->get_result())['versions'];

    $data = [];
    for($v=1;$v<=$v_count;$v++) {
        array_push($data,get_version_content($page_id,$v));
    }

    output_ok($data);

    $stmt->close();
?>