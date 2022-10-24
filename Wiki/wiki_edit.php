<?php
    require_once("../db.php");
    require_once("../token.php");
    //==============================
    //    Prepared statements
    //==============================

    $stmt = $conn->prepare("SELECT ID FROM service WHERE title = ? AND ");

    $stmt2 = $conn->prepare("UPDATE service SET title = ?");
    $stmt2->bind_param("s", $wiki_title);
    
    $stmt3 = $conn->prepare("DELETE FROM end_user WHERE serviceID = ?");
?>