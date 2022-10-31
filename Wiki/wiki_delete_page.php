<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");
    $version = "0.0.1";

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

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
    }

    // User must be admin or manager
    if(!check_admin($user_id) and !check_manager($wiki_id,$user_id)) {
        output_error("You must be an admin or manager to delete a page.");
    }

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error("Page is already deleted");
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

    output_ok("Page deleted");
?>