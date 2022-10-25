<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("wiki_get_version.php");
    require_once("wiki_get_recent_version.php");
    $v = "0.0.2";

    /**
     * wiki_get_content.php
     * 
     * Get info on the most recent version of a page, and all the content assigned to it.
     */

    //==============================
    //    Get variables
    //==============================
    $page_id = get_if_set('page_id');

    if(!$page_id) {
        error_message($v,"Missing input(s) - expected: 'page_id'");
    }

    //==============================
    //    Get current version
    //==============================
    $version = get_recent_version($page_id);
    $data = get_version_content($page_id,$version);
    $result = ["version"=>$v, "status"=>"OK", "data"=>$data];
    echo json_encode($result);
?>
