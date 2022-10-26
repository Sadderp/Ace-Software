<?php
    require_once("../db.php");
    require_once("../utility.php");
    $v = "0.0.1";

    /**
     * wiki_get_pages.php
     * 
     * Return all pages assigned to the given wiki
     */

    //==============================
    //    Prepared statements
    //==============================

    // Check if service is a wiki
    $sql = "SELECT type FROM service WHERE ID = ?";
    $stmt_verify_wiki = $conn->prepare($sql);
    $stmt_verify_wiki->bind_param("i",$wiki_id);

    // Get wiki pages
    $sql = "SELECT ID,title FROM wiki_page WHERE serviceID = ?";
    $stmt_get_pages = $conn->prepare($sql);
    $stmt_get_pages->bind_param("i",$wiki_id);

    //==============================
    //    Get variables
    //==============================
    $wiki_id = get_if_set('wiki_id');

    if(!$wiki_id) {
        error_message($v,"Missing input(s) - expected: 'wiki_id'");
    }

    //==============================
    //    Check if service is a wiki
    //==============================
    $stmt_verify_wiki->execute();
    $service_type = mysqli_fetch_assoc($stmt_verify_wiki->get_result())['type'];

    if(!$service_type) {
        error_message($v,"Service does not exist");
    }

    if($service_type != 'wiki') {
        error_message($v,"Service not a wiki");
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

    $result = ["version"=>$v, "status"=>"OK", "data"=>$data];
    echo json_encode($result);

    $stmt_verify_wiki->close();
    $stmt_get_pages->close();
?>