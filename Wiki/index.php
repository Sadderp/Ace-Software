<?php
require_once "../db.php";

if(!empty($_GET['type']) && !empty($_GET['title'])) {
    $type = $_GET['type'];
    $title = $_GET['title'];

    $sql = "SELECT * FROM service WHERE type='$type' AND title LIKE '%$title%'";
}
else if(!empty($_GET['type'])) {
    $type = $_GET['type'];

    $sql = "SELECT * FROM service WHERE type='$type'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "ID: " . $row["ID"] . " | Title: " . $row["title"] . " | Type: " . $row["type"] . "<br>";
  }
} else {
  echo "0 results";
}
?>