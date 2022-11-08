<?php

    /**
     * wiki_delete_page.php
     * 
     * Mark a wiki page as deleted
     * A 'deleted' page is still stored but cannot be accessed or edited normally.
     * Admins and manager can still view the history of the page and restore it to normal.
     */

    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");

    //==============================
    //    Prepared statements
    //==============================

    // Delete page
    $sql = "UPDATE wiki_page SET deleted = 1 WHERE ID = ?";
    $stmt_delete_page = $conn->prepare($sql);
    $stmt_delete_page->bind_param("i",$page_id);

    // Create deletion version
    $sql = "INSERT INTO wiki_page_version (pageID,num,userID,date,deletion) VALUES (?,?,?,now(),1)";
    $stmt_add_version = $conn->prepare($sql);
    $stmt_add_version->bind_param("iii",$page_id,$new_version,$user_id);

    //==============================
    //    Get variables
    //==============================

    $page_id = get_if_set('page_id');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    $new_version = get_recent_version($page_id) + 1;
    $wiki_id = get_wiki_from_page($page_id);

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$page_id or !$user_id or !$token) {
        output_error("Missing input - expected: 'page_id', 'user_id' and 'page_title'");
    }

    // page_id and user_id must be numeric
    if(!is_numeric($page_id) or !is_numeric($user_id)) {
        output_error($num_error);
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error($token_error);
    }

    // User must not be banned
    if(check_ban($user_id)) {
        output_error($ban_error);
    }

    // User must be admin or manager
    if(!check_admin($user_id) and !check_manager($wiki_id,$user_id)) {
        output_error($permission_error);
    }

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error($page_deleted_error);
    }

    //==============================
    //    Mark page as deleted
    //==============================

    // Page is only *marked* as deleted, since we want to keep its history intact, see who deleted it, and have the ability to restore it.
    $stmt_delete_page->execute();
    $stmt_add_version->execute();

    if($stmt_delete_page->affected_rows == 0 and $stmt_add_version->affected_rows == 0) {
        output_error("Failed to delete page");
    }

    // Output
    $output = ["text"=>"Successfully deleted page","id"=>$page_id];
    output_ok($output);
?>