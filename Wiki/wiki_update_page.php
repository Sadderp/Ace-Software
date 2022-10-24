<?php
    require_once("../db.php");
    require_once("../utility.php");
    $version = "0.0.1";

    // TEST LINK:
    // http://localhost:8080/webbutveckling/TE4/Ace-Software/wiki/wiki_update_page.php?user_id=1&page_id=1&content=["<h1>Joe Biden</h1>","<p>Joe Biden is the preseident of the United States of America</p>"]





    // ! ! THIS PAGE IS MISSING USER VERIFICATION ! !

    //==============================
    //    Prepared statements
    //==============================

    // Get current version
    $sql1 = "SELECT MAX(num) AS version_num FROM wiki_page_version WHERE pageID = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i",$page_id);

    // Create new version
    $sql2 = "INSERT INTO wiki_page_version (pageID,num,userID,date) VALUES (?,?,?,now())";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("iii",$page_id,$new_version,$user_id);


    // Add content
    $sql3 = "INSERT INTO content (pageID,versionID,contents) VALUES (?,?,?)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("iis",$page_id,$new_version,$content);

    //==============================
    //    Get variables
    //==============================
    $user_id = get_if_set('user_id');
    $page_id = get_if_set('page_id');
    $content_array = get_if_set('content');

    // Give error message if one or more inputs are blank
    if(!$user_id or !$page_id) {
        $result = ["version"=>$version, "status"=>"ERROR", "data"=>"Missing input(s) - expected: 'user_id', 'page_id' and 'content'"];
        die(json_encode($result));
    }

    //==============================
    //    Get current version number
    //==============================
    $stmt1->execute();
    $current_version = mysqli_fetch_assoc($stmt1->get_result())['version_num'];
    if(!$current_version) {
        $current_version = 0;
    }
    $new_version = $current_version + 1;

    //==============================
    //    Get wiki ID
    //==============================
    //$stmt3->execute();
    //$wiki_id = mysqli_fetch_assoc($stmt1->get_result())['wiki'];

    //==============================
    //    idk
    //==============================
    $content_array = json_decode($content_array);

    $stmt2->execute();

    foreach($content_array as $c) {
        $content = $c;
        $stmt3->execute();
    }

    $stmt1->close();
    $stmt2->close();
?>