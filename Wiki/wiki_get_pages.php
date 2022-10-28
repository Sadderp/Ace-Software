<?php
    require_once("../db.php");
    require_once("../utility.php");
    $version = "0.0.3";

    /**
     * wiki_get_pages.php
     * 
     * Return all pages assigned to the given wiki
     */

    //==============================
    //    Prepared statements
    //==============================

    // Get wiki pages
    $sql = "SELECT ID,title FROM wiki_page WHERE serviceID = ? AND deleted = 0";
    $stmt_get_pages = $conn->prepare($sql);
    $stmt_get_pages->bind_param("i",$wiki_id);

    //==============================
    //    Get variables
    //==============================
    $wiki_id = get_if_set('wiki_id');

    if(!$wiki_id) {
        output_error("Missing input(s) - expected: 'wiki_id'");
    }

    //==============================
    //    Check if service is a wiki
    //==============================
    if(!verify_service_type($wiki_id,'wiki')) {
        output_error("Page is not a wiki");
    }

    //==============================
    //    Get pages
    //==============================
    $stmt_get_pages->execute();
    $result = $stmt_get_pages->get_result();

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $p = ["id"=>$row["ID"],"title"=>$row["title"]];
        array_push($data,$p);
    }

    output_ok($data);

    $stmt_get_pages->close();
?>