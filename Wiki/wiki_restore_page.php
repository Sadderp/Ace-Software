<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");
    $version = "0.0.1";

    //==============================
    //    Prepared statements
    //==============================

    // Mark page as not deleted
    $sql = "UPDATE wiki_page SET deleted = 0 WHERE ID = ?";
    $stmt_restore_page = $conn->prepare($sql);
    $stmt_restore_page->bind_param("i",$page_id);

    // Create version
    $sql = "INSERT INTO wiki_page_version (pageID,num,userID,date) VALUES (?,?,?,now())";
    $stmt_add_version = $conn->prepare($sql);
    $stmt_add_version->bind_param("iii",$page_id,$new_version,$user_id);

    // Add old content
    $sql = "INSERT INTO content (pageID,versionID,contents) VALUES (?,?,?)";
    $stmt_add_content = $conn->prepare($sql);
    $stmt_add_content->bind_param("iis",$page_id,$new_version,$content);

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
        output_error("You must be an admin or manager to restore a page.");
    }

    // Page must be deleted
    if(!check_page_deletion($page_id)) {
        output_error("Could not restore - Page is not deleted");
    }

    //==============================
    //    Restore page
    //==============================

    $stmt_restore_page->execute();
    $stmt_add_version->execute();

    $old_content = get_version_content($page_id,$new_version - 2)['page_content'];
    foreach($old_content as $c) {
        $content = $c;
        $stmt_add_content->execute();
    }

    if($stmt_restore_page->affected_rows == 0 and $stmt_add_version->affected_rows == 0) {
        output_error("Failed to restore page");
    }

    output_ok("Successfully restored page");
?>