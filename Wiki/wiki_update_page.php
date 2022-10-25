<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("wiki_get_recent_version.php");
    require_once("get_wiki_from_page.php");
    $v = "0.0.6";

    // TEST LINK:
    // http://localhost:8080/webbutveckling/TE4/Ace-Software/wiki/wiki_update_page.php?user_id=1&page_id=1&content=["<h1>RobTop</h1>","<p>RobTop is the lead developer of Geometry Dash</p>"]

    // THIS PAGE IS MISSING USER TOKEN VERIFICATION ! !
    // DON'T KNOW HOW TO DO THAT SHIT ! !

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
    $page_id = get_if_set('page_id');
    $content_array = get_if_set('content');

    // Give error message if one or more inputs are blank
    if(!$user_id or !$page_id) {
        error_message($v,"Missing input(s) - expected: 'user_id', 'page_id' and 'content'");
    }

    // Get recent version
    $current_version = get_recent_version($page_id);
    $new_version = $current_version + 1;

    // Get wiki ID
    $wiki_id = get_wiki_from_page($page_id);
    if($wiki_id == 0) {
        error_message($v,"Wiki page not found");
    }

    //==============================
    //    Check user permissions
    //==============================
    if(!check_end_user($user_id,$wiki_id)) {
        error_message($v,"User does not have permission to edit this page");
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

    if($stmt_add_version->affected_rows == 1 and $stmt_add_content->affected_rows >= 1) {
        $result = ["version"=>$v, "status"=>"OK", "data"=>"Successfully updated wiki page (v" . $new_version . ")"];
    } else {
        error_message($v,"Failed to add to database");
    }

    echo json_encode($result);

    $stmt_add_version->close();
    $stmt_add_content->close();
?>