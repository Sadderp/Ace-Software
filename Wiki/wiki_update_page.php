<?php

    /**
     * wiki_update_page.php
     * 
     * Add a new version to the wiki page, along with updated content for it.
     * Old versions and their content is still internally stored and can be accessed in wiki_get_history.php
     */

    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");

    //==============================
    //    Prepared statements
    //==============================

    // Create new version
    $sql = "INSERT INTO wiki_page_version (pageID,num,userID,date) VALUES (?,?,?,now())";
    $stmt_add_version = $conn->prepare($sql);
    $stmt_add_version->bind_param("iii",$page_id,$new_version,$user_id);

    // Add content
    $sql = "INSERT INTO content (pageID,versionID,contents) VALUES (?,?,?)";
    $stmt_add_content = $conn->prepare($sql);
    $stmt_add_content->bind_param("iis",$page_id,$new_version,$content);

    //==============================
    //    Get variables
    //==============================

    $user_id = get_if_set('user_id');
    $token = get_if_set('token');
    $page_id = get_if_set('page_id');
    $content_data = get_if_set('content');

    // Get recent version
    $current_version = get_recent_version($page_id);
    $new_version = $current_version + 1;

    // Get wiki ID
    $wiki_id = get_wiki_from_page($page_id);
    if($wiki_id == 0) {
        output_error("Wiki page not found");
    }

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$user_id or !$page_id or !$token or !$content_data) {
        output_error("Missing input(s) - expected: 'user_id', 'token', 'page_id' and 'content'");
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

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error($page_deleted_error);
    }

    // 'content' must be a valid JSON array
    try {
        $content_data = json_decode($content_data);
    } catch(Exception $e) {
        output_error("'content' is not valid JSON");
    }

    //==============================
    //    Add new version and content to database
    //==============================

    $stmt_add_version->execute();

    if($content_data == null) {
        output_error("'content' is not valid JSON");
    }

    // If "content_data" is a JSON array, add every item in the array to the Content table individually.
    if(is_array($content_data)) {
        foreach($content_data as $c) {
            $content = json_encode($c);
            $stmt_add_content->execute();
        }
    } 
    
    // If not a JSON array, add all content data to a single content row.
    // (JSON encoded to support assoc. arrays, etc.)
    else {
        $content = json_encode($content_data);
        $stmt_add_content->execute();
    }

    if($stmt_add_version->affected_rows == 0) {
        output_error("Failed to add to database");
    }

    // Output
    $output = ["text"=>"Successfully updated wiki page","id"=>$page_id,"version"=>$new_version];
    output_ok($output);
?>