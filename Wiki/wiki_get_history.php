<?php
    require_once("../db.php");
    require_once("../utility.php");
    require_once("wiki_get_version.php");
    $v = "0.0.1";

    /**
     * wiki_get_history.php
     * 
     * Get all the version and content data of a wiki page.
     * <TODO> Only accessible to wiki end users
     */

    //==============================
    //    Prepared statements
    //==============================
    $sql = "SELECT COUNT(ID) AS 'versions' FROM wiki_page_version WHERE pageID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$page_id);

    //==============================
    //    Get variables
    //==============================
    $page_id = get_if_set('page_id');

    if(!$page_id) {
        error_message($v,"Missing input(s) - expected: 'page_id'");
    }

    //==============================
    //    Get page history
    //==============================
    $stmt->execute();
    $v_count = mysqli_fetch_assoc($stmt->get_result())['versions'];

    $data = [];
    for($version=1;$version<=$v_count;$version++) {
        array_push($data,get_version_content($page_id,$version));
    }

    $result = ["version"=>$v, "status"=>"OK", "data"=>$data];
    echo json_encode($result);

    $stmt->close();
?>