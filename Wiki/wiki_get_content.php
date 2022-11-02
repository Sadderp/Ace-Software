<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("wiki_utility.php");

    /**
     * wiki_get_content.php
     * 
     * Get info on the most recent version of a page, and all the content assigned to it.
     */

    //==============================
    //    Get variables
    //==============================
    $page_id = get_if_set('page_id');

    //==============================
    //    Requirements
    //==============================

    // Input must be set
    if(!$page_id) {
        output_error("Missing input - expected: 'page_id'");
    }

    // page_id must be numeric
    if(!is_numeric($page_id)) {
        output_error("'page_id' is not numeric");
    }

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error("Failed to get content - Page is deleted");
    }

    //==============================
    //    Get current version
    //==============================
    $page_v = get_recent_version($page_id);
    $data = get_version_content($page_id,$page_v);
    output_ok($data);
?>
