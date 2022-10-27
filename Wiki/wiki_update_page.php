<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("Functions/wiki_get_recent_version.php");
    require_once("Functions/get_wiki_from_page.php");
    $version = "0.0.8";

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
        error_message("Wiki page not found");
    }

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$user_id or !$page_id or !$token) {
        error_message("Missing input(s) - expected: 'user_id', 'token', 'page_id' and 'content'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        error_message("Token is invalid or expired, please refresh your login.");
    }

    //==============================
    //    Add new version and content to database
    //==============================

    $content_array = json_decode($content_array);

    $stmt_add_version->execute();

    foreach($content_array as $c) {
        $content = $c;
        $stmt_add_content->execute();
    }

    if($stmt_add_version->affected_rows == 0) {
        error_message("Failed to add to database");
    }

    $result = ["Version"=>$version, "Status"=>"OK", "Data"=>"Successfully updated wiki page (v" . $new_version . ")"];

    echo json_encode($result);
?>