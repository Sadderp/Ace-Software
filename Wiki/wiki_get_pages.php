<?php
    require_once("../db.php");
    require_once("../utility.php");

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
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$wiki_id);

    //==============================
    //    Get variables
    //==============================

    $wiki_id = get_if_set('wiki_id');

    //==============================
    //    Requirements
    //==============================

    // Input must be set
    if(!$wiki_id) {
        output_error("Missing input(s) - expected: 'wiki_id'");
    }

    // wiki_id must be numeric
    if(!is_numeric($wiki_id)) {
        output_error($num_error);
    }

    // Service must be a wiki
    if(!verify_service_type($wiki_id,'wiki')) {
        output_error("Page is not a wiki");
    }

    //==============================
    //    Get pages
    //==============================

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $p = ["id"=>$row["ID"],"title"=>$row["title"]];
        array_push($data,$p);
    }

    output_ok($data);
?>