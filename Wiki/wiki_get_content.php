<?php
    require_once("../db.php");
    require_once("../utility.php");
    $v = "0.0.1";

    //==============================
    //    Prepared statements
    //==============================

    // Get current version
    $sql = "SELECT num,userID,date FROM wiki_page_version WHERE pageID = ? 
        AND num = (SELECT MAX(num) FROM wiki_page_version WHERE pageID = ?)";
    $stmt_get_version = $conn->prepare($sql);
    $stmt_get_version->bind_param("ii",$page_id,$page_id);

    // Get all content from current version
    $sql = "SELECT * FROM content WHERE pageID = ? AND versionID = ?";
    $stmt_get_content = $conn->prepare($sql);
    $stmt_get_content->bind_param("ii",$page_id,$current_version);

    //==============================
    //    Get variables
    //==============================
    $page_id = get_if_set('page_id');
    

    if(!$page_id) {
        error_message($v,"Missing input(s) - expected: 'page_id'");
    }

    $data = [
        "page_version"=>0,
        "page_last_updated"=>null, 
        "page_updated_by"=>null,
        "page_content"=>[]
    ];

    //==============================
    //    Get current version
    //==============================
    $stmt_get_version->execute();
    $result = mysqli_fetch_assoc($stmt_get_version->get_result());
    if($result) {
        $current_version = $result['num'];
        $data['page_version'] = $current_version;
        $data['page_updated_by'] = $result['userID'];
        $data['page_last_updated'] = $result['date'];
    } 
    
    //==============================
    //    Get content
    //==============================
    $stmt_get_content->execute();
    $result = $stmt_get_content->get_result();

    $content = [];
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($content,$row['contents']);
    }
    $data['page_content'] = $content;

    //==============================
    //    Echo data
    //==============================
    $result = ["version"=>$v, "status"=>"OK", "data"=>$data];
    echo json_encode($result);

    $stmt_get_version->close();
    $stmt_get_content->close();
?>