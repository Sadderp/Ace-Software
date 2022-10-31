<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("wiki_utility.php");
    $version = "0.1.0";

    // TEST LINK:
    // http://localhost:8080/webbutveckling/TE4/Ace-Software/wiki/wiki_update_page.php?user_id=1&page_id=1&content=["<h1>RobTop</h1>","<p>RobTop is the lead developer of Geometry Dash</p>"]

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
    $content_array = get_if_set('content');

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
    if(!$user_id or !$page_id or !$token) {
        output_error("Missing input(s) - expected: 'user_id', 'token', 'page_id' and 'content'");
    }

    // page_id and user_id must be numeric
    if(!is_numeric($page_id) or !is_numeric($user_id)) {
        output_error("'page_id' and 'user_id' are not numeric")
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired, please refresh your login.");
    }

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error("Page is deleted and cannot be written to");
    }

    // 'content' must be a valid JSON array
    try {
        $content_array = json_decode($content_array);
        if(!is_array($content_array)) {
            output_error("'content' is not a valid JSON array");
        }
    } catch(Exception e) {
        output_error("'content' is not a valid JSON array");
    }

    //==============================
    //    Add new version and content to database
    //==============================

    $stmt_add_version->execute();

    foreach($content_array as $c) {
        $content = $c;
        $stmt_add_content->execute();
    }

    if($stmt_add_version->affected_rows == 0) {
        output_error("Failed to add to database");
    }

    output_ok("Successfully updated wiki page (v" . $new_version . ")");
?>