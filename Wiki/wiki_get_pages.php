<?php
    require_once("../db.php");
    require_once("../utility.php");
    $version = "0.0.2";

    /**
     * wiki_get_pages.php
     * 
     * Return all pages assigned to the given wiki
     */

    //==============================
    //    Prepared statements
    //==============================

    // Get wiki pages
    $sql = "SELECT ID,title FROM wiki_page WHERE serviceID = ?";
    $stmt_get_pages = $conn->prepare($sql);
    $stmt_get_pages->bind_param("i",$wiki_id);

    //==============================
    //    Get variables
    //==============================
    $wiki_id = get_if_set('wiki_id');

    if(!$wiki_id) {
        error_message("Missing input(s) - expected: 'wiki_id'");
    }

    //==============================
    //    Check if service is a wiki
    //==============================
    if(!verify_service_type($wiki_id,'wiki')) {
        error_message("Page is not a wiki");
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

    $result = ["version"=>$version, "status"=>"OK", "data"=>$data];
    echo json_encode($result);

    $stmt_get_pages->close();
?>