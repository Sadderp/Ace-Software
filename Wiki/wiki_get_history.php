<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");
    $version = "0.0.5";

    /**
     * wiki_get_history.php
     * 
     * Get all the version and content data of a wiki page.
     * Only accessible to admin or wiki manager.
     * Can be done even if page is marked as 'deleted'
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

    //==============================
    //    Requirements
    //==============================

    // All inputs must be set
    if(!$page_id or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'page_id', 'user_id' and 'token'");
    }

    // page_id and user_id must be numeric
    if(!is_numeric($page_id) or !is_numeric($user_id)) {
        output_error("'page_id' and 'user_id' are not numeric")
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired, please refresh your login.");
    }

    // User must be admin or manager
    if(!check_admin($user_id) and !check_manager($wiki_id,$user_id)) {
        output_error("You must be an admin or manager to delete a page.");
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
?>