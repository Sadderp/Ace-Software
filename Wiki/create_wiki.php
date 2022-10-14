<?php
    // Creates connection
    require_once("../db.php");

    // Gets user data


    // Creates wiki
    $wiki_name = $_GET['wiki_name'];

    if(!empty($_GET['wiki_name'])) {
        $sql = "INSERT INTO service (title, type) VALUES ('$wiki_name', 'wiki')";

        if ($conn->query($sql) === TRUE) {
            echo "Created wiki ". $wiki_name."!";
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }

    }
?>

