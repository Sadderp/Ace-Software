<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("../verify_token.php");
    require_once("Functions/wiki_get_recent_version.php");
    require_once("Functions/check_page_deletion.php");
    $version = "0.0.1";

    //==============================
    //    Prepared statements
    //==============================

    // Delete version
    $sql = "DELETE FROM wiki_page_version WHERE pageID = ? AND num > ?";
    $stmt_delete_version = $conn->prepare($sql);
    $stmt_delete_version->bind_param("ii",$page_id,$rollback_version); 

    // Delete content
    $sql = "DELETE FROM content WHERE pageID = ? AND versionID > ?";
    $stmt_delete_content = $conn->prepare($sql);
    $stmt_delete_content->bind_param("ii",$page_id,$rollback_version); 

    //==============================
    //    Get variables
    //==============================

    $page_id = get_if_set('page_id');
    $rollback_version = get_if_set('rollback_version');
    $user_id = get_if_set('user_id');
    $token = get_if_set('token');

    $current_version = get_recent_version($page_id);

    //==============================
    //    Requirements
    //==============================

    // All input variables must be set
    if(!$page_id or !$rollback_version or !$user_id or !$token) {
        output_error("Missing input(s) - expected: 'page_id', 'rollback_version', 'user_id' and 'token'");
    }

    // Token must be valid
    if(!verify_token($user_id,$token)) {
        output_error("Token is invalid or expired");
    }

    // User must be admin
    if(!check_admin($user_id)) {
        output_error("You must be an admin to delete a wiki.");
    }

    // 'rollback_version' must be a number
    if(!is_numeric($rollback_version)) {
        output_error("'rollback_version' is not a number");
    }

    // Rollback version must be less than current version
    if($rollback_version >= $current_version or $rollback_version < 1) {
        output_error("You cannot rollback to this version");
    }

    // Page must not be deleted
    if(check_page_deletion($page_id)) {
        output_error("Failed to rollback - Page is deleted");
    }
    
    //==============================
    //    Delete versions
    //==============================

    $stmt_delete_version->execute();
    $stmt_delete_content->execute();

    if($stmt_delete_version->affected_rows == 0 and $stmt_delete_content->affected_rows == 0) {
        output_error("Failed to delete");
    }

    output_ok("Successfully rolled back " . ($current_version - $rollback_version) . " versions");
?>

