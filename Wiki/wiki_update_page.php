<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("wiki_get_recent_version.php");
    $v = "0.0.4";

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

    // Get wiki from page
    $sql = "SELECT service.ID AS 'wikiID', service.type FROM wiki_page 
        LEFT JOIN service ON service.ID = wiki_page.serviceID 
        WHERE wiki_page.ID = ?";
    $stmt_get_wiki = $conn->prepare($sql);
    $stmt_get_wiki->bind_param("i",$page_id);

    // Check if user has wiki privileges
    $sql = "SELECT * FROM end_user WHERE userID = ? AND serviceID = ?";
    $stmt_check_perms = $conn->prepare($sql);
    $stmt_check_perms->bind_param("ii",$user_id,$wiki_id);

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

    //==============================
    //    Get wiki ID
    //==============================
    $stmt_get_wiki->execute();
    $result = $stmt_get_wiki->get_result();
    $r = mysqli_fetch_assoc($result);

    if($result->num_rows == 0 or $r['type'] != "wiki") {
        error_message($v,"Wiki page not found");
    }

    $wiki_id = $r['wikiID'];

    //==============================
    //    Check user permissions in wiki
    //==============================
    $stmt_check_perms->execute();
    if($stmt_check_perms->get_result()->num_rows == 0) {
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

    $stmt_get_wiki->close();
    $stmt_check_perms->close();
    $stmt_add_version->close();
    $stmt_add_content->close();
?>