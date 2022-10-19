<?php
require_once "../db.php";

if(!empty($_GET['type']) && !empty($_GET['title'])) {
    $type = $_GET['type'];
    $title = "%".$_GET['title']."%";

    $stmt = $conn->prepare("SELECT * FROM service WHERE type=? AND title LIKE ?");
    $stmt->bind_param("ss", $type, $title);

    $stmt->execute();
}
else if(!empty($_GET['type'])) {
    $type = $_GET['type'];

    $stmt = $conn->prepare("SELECT * FROM service WHERE type=?");
    $stmt->bind_param("s", $type);

    $stmt->execute();
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $search = array("ID: "=>$row["ID"],"Title: "=>$row["title"],"Type: "=>$row["type"]);
    echo json_encode($search);
  }
} else {
  echo "0 results";
}
?>