<?php
    require_once('../db.php');

    $db = $conn;

    $sel = "DELETE FROM calendar_event WHERE ID=9";
    $seldel = $db->query($sel) or die("Could not search");
    echo json_encode("poof");
?>